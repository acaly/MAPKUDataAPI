<?php
# MediaWiki MAPKUDataAPI extension v0.1

# extension declaration
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


# hook

$wgHooks['CategoryAfterPageRemoved'][] = 'MAPKU\RemoveDetection::onCategoryAfterPageRemoved';
$wgHooks['ParserBeforeStrip'][] = 'MAPKU\SetPlaceDescription::onParserBeforeStrip';

# api

$wgAPIModules['allplaces'] = 'MAPKU\ApiAllPlaces';
$wgAPIModules['allpcats'] = 'MAPKU\ApiAllPCats';

# classes

$dir = __DIR__ . '/';
$apiDir = $dir . 'includes/api/';
$wgExtensionMessagesFiles['MAPKUDataAPI'] = $dir . 'MAPKUDataAPI.i18n.php';

$wgAutoloadClasses['MAPKU\RemoveDetection'] = $dir . 'includes/RemoveDetection.php';
$wgAutoloadClasses['MAPKU\SetPlaceDescription'] = $dir . 'includes/SetPlaceDescription.php';

$wgAutoloadClasses['MAPKU\PlaceSerializer'] = $dir . 'includes/serializer/PlaceSerializer.php';
$wgAutoloadClasses['MAPKU\SortSerializer'] = $dir . 'includes/serializer/SortSerializer.php';

$wgAutoloadClasses['MAPKU\ApiDataBase'] = $apiDir . 'ApiDataBase.php';
$wgAutoloadClasses['MAPKU\ApiAllPlaces'] = $apiDir . 'ApiAllPlaces.php';
$wgAutoloadClasses['MAPKU\ApiAllPCats'] = $apiDir . 'ApiAllPCats.php';



# make it easier for user to add setupMAPKUDataAPI in LocalSettings.php

$wgMAPKUDataAPIStr = array();

function setupMAPKUDataAPI($str) {
  global $wgMAPKUDataAPIStr;
  $wgMAPKUDataAPIStr = array_merge($wgMAPKUDataAPIStr, $str);
}
