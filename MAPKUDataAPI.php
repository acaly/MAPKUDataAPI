<?php
# MediaWiki MAPKUDataAPI extension v0.1

$wgExtensionCredits['specialpage'][] = array(
  'path' => __FILE__,
  'name' => 'MAPKUDataAPI',
  'author' => array(
    'Acaly'
  ),
  'version' => '0.1',
  'url' => 'https://github.com/Acaly/MAPKUDataAPI',
  'descriptionmsg' => 'mapkudataapi-desc',
);

$dir = __DIR__ . '/';
$wgExtensionMessagesFiles['MAPKUDataAPI'] = $dir . 'MAPKUDataAPI.i18n.php';
//$wgAutoloadClasses['PlaceJSONPrinter'] = $dir . 'queryprinter/PlaceJSONPrinter.php';
$smwgResultFormats['jsonplace'] = 'PlaceJSONPrinter';