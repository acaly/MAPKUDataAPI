<?php

namespace MAPKU;

use ApiBase;
use SMWQueryProcessor;

class ApiAllPlaces extends ApiDataBase {

  /**
   * @param $resultPageSet ApiPageSet
   * @return void
   */
  public function run( $resultPageSet = null ) {
    global $wgMAPKUDataAPIStr;
    $cat = $wgMAPKUDataAPIStr['cat_place'];
    list( $queryString, $parameters, $printouts ) = 
        SMWQueryProcessor::getComponentsFromFunctionParams(
          array(
            '[[Category:' . $cat . ']]',
            '?' . $wgMAPKUDataAPIStr['prop_addr'],
            '?' . $wgMAPKUDataAPIStr['prop_baidu'],
            '?' . $wgMAPKUDataAPIStr['prop_google'],
            '?' . $wgMAPKUDataAPIStr['prop_cat'],
            '?' . $wgMAPKUDataAPIStr['prop_mainimg'],
            '?' . $wgMAPKUDataAPIStr['prop_place_description'],
            '?-'. $wgMAPKUDataAPIStr['prop_guide_parent_place'],
            '?-'. $wgMAPKUDataAPIStr['prop_image_parent_place'],
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
