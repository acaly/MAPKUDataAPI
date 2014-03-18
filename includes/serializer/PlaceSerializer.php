<?php

namespace MAPKU;

use SMW\Serializers\QueryResultSerializer;
use SMWDataItem as DataItem;
use SMW\ExtensionContext;

class PlaceSerializer {
  # private static $sortList = null;

  public static $prop_addr;
  public static $prop_baidu;
  public static $prop_google;
  public static $prop_cat;
  public static $prop_mainimg;
  public static $cat_guide;

  public static function initStrings() {
    global $wgMAPKUDataAPIStr;
    self::$prop_addr     = $wgMAPKUDataAPIStr('prop-address');
    self::$prop_baidu    = $wgMAPKUDataAPIStr('prop-baidu');
    self::$prop_google   = $wgMAPKUDataAPIStr('prop-google');
    self::$prop_cat      = $wgMAPKUDataAPIStr('prop-category');
    self::$prop_mainimg  = $wgMAPKUDataAPIStr('prop-mainimg' );
    self::$cat_guide     = $wgMAPKUDataAPIStr('cat-guide');
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

  private static function getImageThumbUrl($title, $imgsize) {
    $file = wfFindFile($title);
    if ($file != null) {
      return wfExpandUrl($file->createThumb($imgsize), PROTO_RELATIVE);
    } else {
      return "";
    }
  }

  public static function getPlaceCategoryContent($name, & $result, $imgsize) {
    global $wgMAPKUDataAPIStr;
    $result['images'] = array();
    $result['guides'] = array();

    # Guides
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[' . $wgMAPKUDataAPIStr['prop_guide_parent_place'] . '::' . $name . ']]',
            '[[Category:' . $wgMAPKUDataAPIStr['cat_guide'] . ']]'
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
      $result['guides'][] = $diWikiPage->getTitle()->getText();
    }

    # Images
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[' . $wgMAPKUDataAPIStr['prop_image_parent_place'] . '::' . $name . ']]',
            '[[Category:' . $wgMAPKUDataAPIStr['cat_image'] . ']]'
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
      $result['images'][] = $diWikiPage->getTitle()->getText();
    }

    return;
    # Old code
    //There is the place page, images, guides, and maybe some other things.
    $printRequest = $queryResult->getPrintRequests()[0]; //category
    foreach ( $queryResult->getResults() as $diWikiPage ) {
      if ( ($diWikiPage->getNamespace() === NS_FILE ) ) {
        //an image.

        $result['images'][] = self::getImageThumbUrl($diWikiPage->getTitle()->getText(), $imgsize);
      } else {
        //check if there is a category named guides
        $resultArray = new SMWResultArray( $diWikiPage, $printRequest, $queryResult->getStore() );
        foreach ( $resultArray->getContent() as $dataItem ) {
          if ( explode(':', $dataItem->getTitle()->getFullText())[1] === $wgMAPKUDataAPIStr['cat_guide'])
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
    global $wgMAPKUDataAPIStr;

    foreach ( $queryResult->getResults() as $diWikiPage ) {
      if ( !($diWikiPage->getTitle() instanceof Title ) ) {
        continue;
      }
      if ( ($diWikiPage->getNamespace() !== NS_MAIN ) ) {
        continue;
      }

      $result = array(
//        '_id' => $diWikiPage->getDBkey(),
        'name' => $diWikiPage->getTitle()->getText(),
      );

      foreach ( $queryResult->getPrintRequests() as $printRequest ) {
        $resultArray = new SMWResultArray( $diWikiPage, $printRequest, $queryResult->getStore() );
        if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_addr']) {
          $result['addr'] = $resultArray->getContent()[0]->getSerialization();
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_baidu']) {
          $coord = $resultArray->getContent()[0]->getCoordinateSet();
          $result['baidu_lati'] = $coord['lat'];
          $result['baidu_longi'] = $coord['lon'];
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_google']) {
          $coord = $resultArray->getContent()[0]->getCoordinateSet();
          $result['google_lati'] = $coord['lat'];
          $result['google_longi'] = $coord['lon'];
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_cat']) {
          foreach ( $resultArray->getContent() as $dataItem ) {
            //$sort_title = explode(':', $dataItem->getTitle()->getFullText())[1];
            //if ( self::isCategoryPlaceSort( $sort_title ) ) {
              $result['sorts'][] = $dataItem->getTitle()->getFullText();//$sort_title;
            //}
          }
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_mainimg']) {
          $dataItem = $resultArray->getContent()[0];
          if ($dataItem !== null) {
            $result['image'] = self::getImageThumbUrl($dataItem->getTitle()->getText(), $imgsize);
          } else {
            $result['image'] = '';
          }
        }
      }
      self::getPlaceCategoryContent($result['name'], $result, $imgsize);
      $resultList->addValue(null, null, $result);
    }
  }
}
