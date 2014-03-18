<?php

namespace MAPKU;

use SMW\Api\Query;

class ApiDataBase extends Query {

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

  public function executeGenerator( $resultPageSet ) {
    $this->run( $resultPageSet );
  }
}