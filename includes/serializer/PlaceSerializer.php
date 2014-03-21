<?php

namespace MAPKU;

use Title;
use SMWResultArray;
use SMWQueryProcessor;
use SMW\Serializers\QueryResultSerializer;
use SMW\ExtensionContext;
use SMWDataItem as DataItem;

class PlaceSerializer {

  private static function getImageThumbUrl($title, $imgsize) {
    $file = wfFindFile($title);
    if ($file != null) {
      return wfExpandUrl($file->createThumb($imgsize), PROTO_RELATIVE);
    } else {
      return "";
    }
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
        'name' => $diWikiPage->getTitle()->getText(),
      );

      foreach ( $queryResult->getPrintRequests() as $printRequest ) {
        $resultArray = new SMWResultArray( $diWikiPage, $printRequest, $queryResult->getStore() );
        if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_addr']) {
$result['addr']=get_class_methods($resultArray->getContent()[0]);
          $str = $resultArray->getContent()[0];
          if ($str != null)
            $result['addr'] = $str->getSerialization();
          else
            $result['addr'] = '';
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_place_description']) {
          $str = $resultArray->getContent()[0];
          if ($str != null)
            $result['description'] = $str->getSerialization();
          else
            $result['description'] = '';
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_baidu']) {
          $coord = $resultArray->getContent()[0]->getCoordinateSet();
          $result['baidu_lati'] = $coord['lat'];
          $result['baidu_longi'] = $coord['lon'];
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_google']) {
          $coord = $resultArray->getContent()[0]->getCoordinateSet();
          $result['google_lati'] = $coord['lat'];
          $result['google_longi'] = $coord['lon'];
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_cat']) {
          $result['sorts'] = array();
          foreach ( $resultArray->getContent() as $dataItem ) {
            if ($dataItem->getTitle()->isKnown()) {
              $result['sorts'][] = $dataItem->getTitle()->getFullText();
            }
          }
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_mainimg']) {
          $dataItem = $resultArray->getContent()[0];
          if ($dataItem !== null) {
            $result['image'] = self::getImageThumbUrl($dataItem->getTitle()->getText(), $imgsize);
          } else {
            $result['image'] = '';
          }
        } else if ( $printRequest->getLabel() === '-' . $wgMAPKUDataAPIStr['prop_guide_parent_place']) {
          $result['guides'] = array();
          foreach ( $resultArray->getContent() as $dataItem ) {
            $result['guides'][] = $dataItem->getTitle()->getFullText();
          }
        } else if ( $printRequest->getLabel() === '-' . $wgMAPKUDataAPIStr['prop_image_parent_place']) {
          $result['images'] = array();
          foreach ( $resultArray->getContent() as $dataItem ) {
            $result['images'][] = self::getImageThumbUrl($dataItem->getTitle()->getText(), $imgsize);
          }
        }
      }
      $resultList->addValue(null, null, $result);
    }
  }
}
