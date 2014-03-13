<?php
use SMW\Serializers\QueryResultSerializer;
use SMWDataItem as DataItem;
use SMW\ExtensionContext;

class PlaceSerializer {
  private static $sortList = null;

  public static $prop_addr;
  public static $prop_baidu;
  public static $prop_google;
  public static $prop_cat;
  public static $cat_guide;

  public static function initStrings() {
    self::$prop_addr = wfMessage('mapkuprop-address')->text();
    self::$prop_baidu = wfMessage('mapkuprop-baidu')->text();
    self::$prop_google = wfMessage('mapkuprop-google')->text();
    self::$prop_cat = wfMessage('mapkuprop-category')->text();
    self::$cat_guide = wfMessage('mapkucat-guide')->text();
  }

  public static function getSerialization( $dataItem, $printRequest = null ) {
    $result = array();

    switch ( $dataItem->getDIType() ) {
      case DataItem::TYPE_WIKIPAGE:
        $title = $dataItem->getTitle();
        $result = array(
          'fulltext' => $title->getFullText(),
          'fullurl' => $title->getFullUrl(),
          'namespace' => $title->getNamespace(),
          'exists' => $title->isKnown()
        );
        break;
      case DataItem::TYPE_NUMBER:
        // dataitems and datavalues
        // Quantity is a datavalue type that belongs to dataitem
        // type number which means in order to identify the correct
        // unit, we have re-factor the corresponding datavalue otherwise
        // we will not be able to determine the unit
        // (unit is part of the datavalue object)
        if ( $printRequest !== null && $printRequest->getTypeID() === '_qty' ) {
          $diProperty = $printRequest->getData()->getDataItem();
          $dataValue = DataValueFactory::getInstance()->newDataItemValue( $dataItem, $diProperty );

          $result = array(
            'value' => $dataValue->getNumber(),
            'unit' => $dataValue->getUnit()
          );
        } else {
          $result = $dataItem->getNumber();
        }
        break;
      case DataItem::TYPE_GEO:
        $result = $dataItem->getCoordinateSet();
        break;
      case DataItem::TYPE_TIME:
        $result = $dataItem->getMwTimestamp();
        break;
      default:
        $result = $dataItem->getSerialization();
        break;
    }

    return $result;
  }

  public static function getPlaceCategoryContent($name, & $result, $imgsize) {
    $result['images'] = array();
    $result['guides'] = array();
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[Category:' . $name . ']]',
            '?' . self::$prop_cat,
          ),
          false
        );

    $content = new ExtensionContext();
    $queryResult = $content->getStore()->getQueryResult( SMWQueryProcessor::createQuery(
      $queryString,
      SMWQueryProcessor::getProcessedParams( $parameters, $printouts ),
      SMWQueryProcessor::SPECIAL_PAGE,
      '',
      $printouts
    ) );

    //There is the place page, images, guides, and maybe some other things.
    $printRequest = $queryResult->getPrintRequests()[0]; //category
    foreach ( $queryResult->getResults() as $diWikiPage ) {
      if ( ($diWikiPage->getNamespace() === NS_FILE ) ) {
        //an image.
        $image = wfFindFile($diWikiPage->getTitle()->getText());

        $result['images'][] = wfExpandUrl($image->createThumb($imgsize), PROTO_RELATIVE);
      } else {
        //check if there is a category named guides
        $resultArray = new SMWResultArray( $diWikiPage, $printRequest, $queryResult->getStore() );
        foreach ( $resultArray->getContent() as $dataItem ) {
          if ( explode(':', $dataItem->getTitle()->getFullText())[1] === self::$cat_guide)
            $result['guides'][] = $diWikiPage->getTitle()->getText();
        }
      }
    }
  }

  public static function initSortList() {
    $sortList = array();
    
    $cat = wfMessage('mapku-cat-sort')->text();
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[Subcategory of::' . $cat . ']]'
          ),
          false
        );

    $content = new ExtensionContext();
    $queryResult = $content->getStore()->getQueryResult( SMWQueryProcessor::createQuery(
      $queryString,
      SMWQueryProcessor::getProcessedParams( $parameters, $printouts ),
      SMWQueryProcessor::SPECIAL_PAGE,
      '',
      $printouts
    ) );

    foreach ( $queryResult->getResults() as $diWikiPage ) {
      $sortList[] = $diWikiPage->getTitle()->getText();
    }
    self::$sortList = $sortList;
  }

  public static function isCategoryPlaceSort($name) {
    if (self::$sortList === null) {
      self::initSortList();
    }
    return in_array($name, self::$sortList);
  }

  public static function serializePlaceArray($queryResult, $resultList, $imgsize) {

    foreach ( $queryResult->getResults() as $diWikiPage ) {
      if ( !($diWikiPage->getTitle() instanceof Title ) ) {
        continue;
      }
      if ( ($diWikiPage->getNamespace() !== NS_MAIN ) ) {
        continue;
      }

      $result = array(
        '_id' => $diWikiPage->getDBkey(),
        'name' => $diWikiPage->getTitle()->getText(),
      );

      self::getPlaceCategoryContent($result['name'], $result, $imgsize);

      foreach ( $queryResult->getPrintRequests() as $printRequest ) {
        $resultArray = new SMWResultArray( $diWikiPage, $printRequest, $queryResult->getStore() );
        if ( $printRequest->getLabel() === self::$prop_addr) {
          $result['addr'] = self::getSerialization(
            $resultArray->getContent()[0],
            $printRequest
          );
        } else if ( $printRequest->getLabel() === self::$prop_baidu) {
          $coord = self::getSerialization(
            $resultArray->getContent()[0],
            $printRequest
          );
          $result['baidu_lati'] = $coord['lat'];
          $result['baidu_longi'] = $coord['lon'];
        } else if ( $printRequest->getLabel() === self::$prop_google) {
          $coord = self::getSerialization(
            $resultArray->getContent()[0],
            $printRequest
          );
          $result['google_lati'] = $coord['lat'];
          $result['google_longi'] = $coord['lon'];
        } else if ( $printRequest->getLabel() === self::$prop_cat) {
          $values = array();
          foreach ( $resultArray->getContent() as $dataItem ) {
            $sort_title = explode(':', $dataItem->getTitle()->getFullText())[1];
            if ( self::isCategoryPlaceSort( $sort_title ) ) {
              $values[] = $sort_title;
            }
          }
          $result['sorts'] = $values;
        }
      }
      $resultList->addValue(null, null, $result);
    }
  }
}

PlaceSerializer::initStrings();
