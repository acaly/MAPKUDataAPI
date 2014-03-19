<?php

namespace MAPKU;

use ApiBase;
use SMWQueryProcessor;

class ApiAllPCats extends ApiDataBase {

  /**
   * @param $resultPageSet ApiPageSet
   * @return void
   */
  public function run( $resultPageSet = null ) {
    global $wgMAPKUDataAPIStr;
    $cat = $wgMAPKUDataAPIStr['cat_sort'];
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[Category:' . $cat . ']]',
            '?' . $wgMAPKUDataAPIStr['prop_parent_pcat'],
          ),
          false
        );

    $queryResult = $this->getQueryResult( $this->getQuery(
      $queryString,
      $printouts,
      $parameters
    ) );

    SortSerializer::serializeSortArray($queryResult, $this->getResult());
  }

  public function getAllowedParams() {
    return array(
      'format' => array(
        ApiBase::PARAM_DFLT => 'json',
        ApiBase::PARAM_TYPE => array( 'json', 'jsonfm' ),
      )
    );
  }

  public function getParamDescription() {
    return array();
  }

  public function getResultProperties() {
    return array(
      '' => array(
        //'pageid' => 'integer',
        //'ns' => 'namespace',
        //'title' => 'string'
      )
    );
  }

  public function getDescription() {
    return 'Enumerate all sorts sequentially';
  }

  public function getPossibleErrors() {
    return array_merge( parent::getPossibleErrors(), array() );
  }

  public function getExamples() {
    return array(
      'api.php?action=allsorts' => array(
        'Simple Use'
      )
    );
  }

  public function getHelpUrls() {
    return 'http://pkuwiki.pkucada.org/wiki/%E5%B8%AE%E5%8A%A9:%E6%95%B0%E6%8D%AE%E6%8E%A5%E5%8F%A3';
  }
}
