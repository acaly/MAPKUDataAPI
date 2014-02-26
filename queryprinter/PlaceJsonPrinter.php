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
    $params = array();

    $params['limit']->setDefault( 100 );

    $params['prettyprint'] = array(
      'type' => 'boolean',
      'default' => '',
      'message' => 'smw-paramdesc-prettyprint',
    );

    return $params;
  }
}