<?php

//SMW
use SMW\Api\Query;

class ApiAllPlaces extends Query {

  /**
   * Override built-in handling of format parameter.
   * Only JSON is supported.
   *
   * @return ApiFormatBase
   */
  public function getCustomPrinter() {
    $params = $this->extractRequestParams();
    $format = $params['format'];
    $allowed = array( 'json', 'jsonfm' );
    if ( in_array( $format, $allowed ) ) {
      return $this->getMain()->createPrinterByName( $format );
    }
    return $this->getMain()->createPrinterByName( $allowed[0] );
  }

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
    $cat = wfMessage('mapku-cat')->text();
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[Category:' . $cat . ']]',
            '?' . PlaceSerializer::$prop_addr,
            '?' . PlaceSerializer::$prop_baidu,
            '?' . PlaceSerializer::$prop_google,
            '?' . PlaceSerializer::$prop_cat,
            '?' . PlaceSerializer::$prop_mainimg,
            'link=none'
          ),
          false
        );

    $queryResult = $this->getQueryResult( $this->getQuery(
      $queryString,
      $printouts,
      $parameters
    ) );

    $imgsize = $this->extractRequestParams()['imgsize'];
    switch ($imgsize) {
      case 'l':
        $imgsize = 480;
        break;
      case 'm':
        $imgsize = 240;
        break;
      case 's':
        $imgsize = 120;
        break;
    }

    PlaceSerializer::serializePlaceArray($queryResult, $this->getResult(), $imgsize);
  }

  public function getAllowedParams() {
    return array(
      'format' => array(
        ApiBase::PARAM_DFLT => 'json',
        ApiBase::PARAM_TYPE => array( 'json', 'jsonfm' ),
      ),
      'imgsize' => array(
        ApiBase::PARAM_DFLT => 'm',
        ApiBase::PARAM_TYPE => array( 's', 'm', 'l' ),
      ),
    );
  }

  public function getParamDescription() {
    return array(
      'format' => 'Output format, only json and jsonfm are allowed.',
      'imgsize' => 'Size of the image url returned. The default is m(middle).',
    );
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
