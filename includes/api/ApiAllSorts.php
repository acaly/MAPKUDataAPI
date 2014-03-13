<?php

//SMW
use SMW\Api\Query;

class ApiAllSorts extends Query {

  public function execute() {
    $this->run();
  }

  public function getCacheMode( $params ) {
    return 'public';
  }

  /**
   * @param $resultPageSet ApiPageSet
   * @return void
   */
  public function executeGenerator( $resultPageSet ) {
    if ( $resultPageSet->isResolvingRedirects() ) {
      $this->dieUsage( 'Use "gapfilterredir=nonredirects" option instead of "redirects" when using allpages as a generator', 'params' );
    }

    $this->run( $resultPageSet );
  }

  /**
   * @param $resultPageSet ApiPageSet
   * @return void
   */
  private function run( $resultPageSet = null ) {
    $cat = wfMessage('mapku-cat-sort')->text();
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[Subcategory of::' . $cat . ']]'
          ),
          false
        );

    $queryResult = $this->getQueryResult( $this->getQuery(
      $queryString,
      $printouts,
      $parameters
    ) );
    
    //TODO: output
  }

  public function getSortListForPlaceApi() {
    $sortList = array();
    $cat = wfMessage('mapku-cat-sort')->text();
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[Subcategory of::' . $cat . ']]'
          ),
          false
        );

    $queryResult = $this->getQueryResult( $this->getQuery(
      $queryString,
      $printouts,
      $parameters
    ) );
    
    foreach ( $queryResult->getResults() as $diWikiPage ) {
      $sortList[] = $diWikiPage->getTitle()->getText();
    }
    return $sortList;
  }

  public function getAllowedParams() {
    return array();
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
