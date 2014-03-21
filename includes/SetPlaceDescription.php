<?php

namespace MAPKU;

class SetPlaceDescription {
  public static function onParserBeforeStrip( &$parser, &$text, &$strip_state ) {
    global $wgMAPKUDataAPIStr;
    
    //if (strpos($text, '!TEST API!') !== 0) return;
    
    $reg = '/\{\{' .
        $wgMAPKUDataAPIStr['str_place_template_name'] .
        '[^\{\}]*(\{([^\{\}]|(?1))*\})*\}\}\n*(([^=]=?)*[^=])==/s';

    if ( preg_match($reg, $text, $match) !== 1 ) return;

    $description = preg_replace(
      array("/'/", "/{/", "/}/"),
      "",
      $match[3]
    );

    $text = $text . "\n{{#set:" . $wgMAPKUDataAPIStr['prop_place_description']
        . '=' . $description . '}}';
  }
}
