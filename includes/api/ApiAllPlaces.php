<?php

//SMW
use SMW\Api\Query;

class ApiAllPlaces extends Query {

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
    /*
    $this->getResult()->addValue(null, 'test', 'test');

    $dbr = wfGetDB( DB_SLAVE );

    $cat = Title::newFromText( wfMessage('output_to_api')->text() )->getDBkey();
    $res = $dbr->select(
      'categorylinks',
      'cl_from',
      array( 'cl_to' => $cat )
    );

    $resArray = array();
    foreach ( $res as $row ) {
      $id = $row->cl_from;

      $title = $dbr->selectField(
        'page',
        'page_title',
        array( 'page_id' => $id ),
        __METHOD__
      );
      if ($title === null) {
        $resArray[] = array( 'id' => $id, 'missing' );
      } else {
        $resArray[] = array( 'id' => $id,
            'name' => $title );
      }

      //Query SMW for $title
      list( $queryString, $parameters, $printouts ) = 
          SMWQueryProcessor::getComponentsFromFunctionParams(
            array('[[' . $title . ']]', '?author', 'link=none'),
            false
          );

      $queryResult = $this->getQueryResult( $this->getQuery(
        $queryString,
        $printouts,
        $parameters
      ) );
      $resArray['smw'] = $queryResult->toArray()['results'];
    }
    $this->getResult()->addValue(null, 'result', $resArray);
    */

    $cat = wfMessage('output_to_api')->text();
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[Category:' . $cat . ']]',
            '?' . PlaceSerializer::getPropertyAddress(),
            '?' . PlaceSerializer::getPropertyBaiduCoord(),
            '?' . PlaceSerializer::getPropertyGoogleCoord(),
            'link=none'
          ),
          false
        );

    $queryResult = $this->getQueryResult( $this->getQuery(
      $queryString,
      $printouts,
      $parameters
    ) );
    $this->getResult()->addValue(null, 'result', PlaceSerializer::serializePlaceArray($queryResult));
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
    return 'Enumerate all places sequentially';
  }

  public function getPossibleErrors() {
    return array_merge( parent::getPossibleErrors(), array() );
  }

  public function getExamples() {
    return array(
      'api.php?action=allplaces' => array(
        'Simple Use'
      )
    );
  }

  public function getHelpUrls() {
    return 'http://pkuwiki.pkucada.org/wiki/%E5%B8%AE%E5%8A%A9:%E6%95%B0%E6%8D%AE%E6%8E%A5%E5%8F%A3';
  }
}