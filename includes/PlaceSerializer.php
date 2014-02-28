<?php
use SMW\Serializers\QueryResultSerializer;
use SMWDataItem as DataItem;

class PlaceSerializer {
  public static function getPropertyAddress() {
    return wfMessage('mapkuprop-address')->text();
  }
  public static function getPropertyBaiduCoord() {
    return wfMessage('mapkuprop-baidu')->text();
  }
  public static function getPropertyGoogleCoord() {
    return wfMessage('mapkuprop-google')->text();
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

  public static function serializePlaceArray($queryResult, $resultList) {

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

      foreach ( $queryResult->getPrintRequests() as $printRequest ) {
        $resultArray = new SMWResultArray( $diWikiPage, $printRequest, $queryResult->getStore() );
        //$dataItems = $resultArray->getContent();

        if ( $printRequest->getLabel() === self::getPropertyAddress()) {
          $result['addr'] = self::getSerialization(
            $resultArray->getContent()[0],
            $printRequest
          );
        } else if ( $printRequest->getLabel() === self::getPropertyBaiduCoord()) {
          $coord = self::getSerialization(
            $resultArray->getContent()[0],
            $printRequest
          );
          $result['baidu_lati'] = $coord['lat'];
          $result['baidu_longi'] = $coord['lon'];
        } else if ( $printRequest->getLabel() === self::getPropertyGoogleCoord()) {
          $coord = self::getSerialization(
            $resultArray->getContent()[0],
            $printRequest
          );
          $result['google_lati'] = $coord['lat'];
          $result['google_longi'] = $coord['lon'];
        }
      }
      $resultList.addValue(null, null, $result);
    }
  }
}