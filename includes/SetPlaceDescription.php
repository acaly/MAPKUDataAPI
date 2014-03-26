<?php

namespace MAPKU;

class SetPlaceDescription {
  public static function onParserBeforeStrip( &$parser, &$text, &$strip_state ) {
    global $wgMAPKUDataAPIStr;
    
    $reg = '/\{\{' .
        $wgMAPKUDataAPIStr['str_place_template_name'] .
        '[^\{\}]*(\{([^\{\}]|(?1))*\})*[^\{\}]*\}\}\n*(([^=]=?)*[^=][^\n])\n*==/s';

    if ( preg_match($reg, $text, $match) !== 1 ) return;

    $description = preg_replace(
      array("/'/", "/{/", "/}/"),
      "",
      $match[3]
    );

    $text = $text . "\n{{#set:" . $wgMAPKUDataAPIStr['prop_place_description']
        . '=' . $description . '}}';

    # Set description for subobjects

    $reg_find_sub = '/\{\{' . $wgMAPKUDataAPIStr['str_sub_place_template_name']
        . '[ \n]*\|[ \n]*' . $wgMAPKUDataAPIStr['str_sub_place_template_param_name'] 
        . '[ \n]*=[ \n]*([^\|\n ]*)/';

    if ( preg_match_all($reg_find_sub, $text, $matches) == FALSE ) return;
    foreach ( $matches[1] as $sub_place ) {
      $reg_find_sub_des = '/\n===? *' . $sub_place . ' *===?\n*((=?[^=]+)*[^\n=])/s';
      if ( preg_match($reg_find_sub_des, $text, $match) !== 1 ) continue;
      $description = preg_replace(
        array("/'/", "/{/", "/}/"),
        "",
        $match[1]
      );
      $text = $text . "\n{{#subobject:" . $sub_place . '|' . $wgMAPKUDataAPIStr['prop_place_description']
          . '=' . $description . '}}';
    }
  }
}
