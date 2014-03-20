<?php

namespace MAPKU;

class SetPlaceDescription {
  public static function onParserBeforeStrip( &$parser, &$text, &$strip_state ) {
    global $wgMAPKUDataAPIStr;
    
    if (strpos($text, '!TEST API!') !== 0) return;
    
    $reg = '/\{\{' .
        $wgMAPKUDataAPIStr['str_place_template_name'] .
        '[^\{\}]*(\{([^\{\}]|(?1))*\})*\}\}\n?(([^=]=?)*[^=])==/s';

    $description = preg_match($reg, $text, $match, )

    $text = $text . '"' . $description . '"' . '{{#set:Is test=true}}';
  }
}