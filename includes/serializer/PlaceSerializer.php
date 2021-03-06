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

  private static function serializeContact($dataItem) {
    if ($dataItem == null) return array();
    $list = explode(',', $dataItem->getString());
    $results = array();
    foreach ($list as $entry) {
      $results[] = explode(';', trim($entry));
    }
    return $results;
  }

  private static function serializeOpeningHours($dataItem) {
    if ($dataItem == null) return array();
    $list = explode(',', $dataItem->getString());
    $results = array();
    foreach ($list as $entry) {
      $results[] = explode(';', trim($entry));
    }
    return $results;
  }

  public static function serializeSubPlaces($queryResult, $resultList) {
    global $wgMAPKUDataAPIStr;
    $results = array();

    foreach ( $queryResult->getResults() as $diWikiPage ) {
      if ( !($diWikiPage->getTitle() instanceof Title ) ) {
        continue;
      }

      $result = array(
        'name' => $diWikiPage->getTitle()->getFragment(),
      );
      $parent_name = null;

      foreach ( $queryResult->getPrintRequests() as $printRequest ) {
        $resultArray = new SMWResultArray( $diWikiPage, $printRequest, $queryResult->getStore() );
        if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_addr']) {
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
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_cat']) {
          $result['sorts'] = array();
          foreach ( $resultArray->getContent() as $dataItem ) {
            if ($dataItem->getTitle()->isKnown()) {
              $result['sorts'][] = $dataItem->getTitle()->getFullText();
            }
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
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_contact']) {
          $result['contact'] = self::serializeContact($resultArray->getContent()[0]);
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_opening_hours']) {
          $result['opening_hours'] = self::serializeOpeningHours($resultArray->getContent()[0]);
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_sub_place_parent_place']) { # ? Why no '-' here?
          $parent_name = $resultArray->getContent()[0]->getTitle()->getText();
        }
      }
      if ($parent_name !== null) $results[$parent_name][] = $result;
    }

    return $results;
  }

  public static function serializePlaceArray($queryResult, $resultList, $imgsize, $sub_places_result) {
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
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_contact']) {
          $result['contact'] = self::serializeContact($resultArray->getContent()[0]);
        } else if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_opening_hours']) {
          $result['opening_hours'] = self::serializeOpeningHours($resultArray->getContent()[0]);
        }
      }
      $result['subplaces'] = $sub_places_result[$result['name']];
      if ( $result['subplaces'] === null ) {
        $result['subplaces'] = array();
      }
      $resultList->addValue(null, null, $result);
    }
  }
}
