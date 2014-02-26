<?php
use SMW\JsonResultPrinter;
class PlaceJSONPrinter extends JsonResultPrinter {
  /**
   * Returns human readable label for this printer
   * @codeCoverageIgnore
   *
   * @return string
   */
  public function getName() {
    return $this->msg( 'mapkudataapi-placejson' )->text();
  }

  public function getParamDefinitions( array $definitions ) {

    $definitions['prettyprint'] = array(
      'type' => 'boolean',
      'default' => '',
      'message' => 'smw-paramdesc-prettyprint',
    );

    return $definitions;
  }
  protected function getResultText( SMWQueryResult $res, $outputmode ) {

    if ( $outputmode == SMW_OUTPUT_FILE ) {

      // No results, just bailout
      if ( $res->getCount() == 0 ){
        return $this->params['default'] !== '' ? $this->params['default'] : '';
      }

      // Serialize queryResult
      $result = FormatJSON::encode(
        $this->filterJson($res->serializeToArray()),
        $this->params['prettyprint']
      );

    } else {
      // Create a link that points to the JSON file
      $result = $this->getLink( $res, $outputmode )->getText( $outputmode, $this->mLinker );

      // Code can be viewed as HTML if requested, no more parsing needed
      $this->isHTML = $outputmode == SMW_OUTPUT_HTML;
    }

    return $result;
  }

  protected function filterJson( $json ) {
    $result = array(
      'name' => '',
      'addr' => '',
      'baidu_lati' => '',
      'baidu_longi' => '',
      'google_lati' => '',
      'google_longi' => '',
      'email' => '',
      'opening_hours' => '',
      'tel' => ''
    );

    foreach ( $json['results'] as $k => $v ) {
      $result['name'] = $k;
      foreach ( $v['printouts'] as $vk => $vv) {
        if ( substr($vk, 0, 5) == 'Baidu' ) {
          $result['baidu_lati'] = $vv[0]['lat'];
          $result['baidu_longi'] = $vv[0]['lon'];
        } else if ( substr($vk, 0, 6) == 'Google' ) {
          $result['google_lati'] = $vv[0]['lat'];
          $result['google_longi'] = $vv[0]['lon'];
        }
      }
    }
    return $result;
  }
}
