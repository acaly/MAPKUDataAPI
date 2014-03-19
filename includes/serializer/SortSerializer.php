<?php

namespace MAPKU;

use Title;
use SMWResultArray;

class SortSerializer {
  public static function serializeSortArray($queryResult, $resultList) {
    global $wgMAPKUDataAPIStr;

    $resultList->addValue(null, 'parents', $wgMAPKUDataAPIStr['array_allowed_pcat_parent']);
    $results = array();

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
        if ( $printRequest->getLabel() === $wgMAPKUDataAPIStr['prop_parent_pcat']) {
          $result['parent'] = $resultArray->getContent()[0]->getSerialization();
        }
      }
      $results[] = $result;
    }
    $resultList->addValue(null, 'categories', $results);
  }
}
