<?php
# MediaWiki MAPKUDataAPI extension v0.1

$wgExtensionCredits['semantic'][] = array(
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
$apiDir = $dir . 'includes/api/';

$wgExtensionMessagesFiles['MAPKUDataAPI'] = $dir . 'MAPKUDataAPI.i18n.php';

$wgAutoloadClasses['PlaceSerializer'] = $dir . 'includes/PlaceSerializer.php';
$wgAutoloadClasses['ApiAllPlaces'] = $apiDir . 'ApiAllPlaces.php';

$wgAPIModules['allplaces'] = 'ApiAllPlaces';
