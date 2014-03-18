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

# api

$wgAPIModules['allplaces'] = 'MAPKU\ApiAllPlaces';

# classes

$dir = __DIR__ . '/';
$apiDir = $dir . 'includes/api/';
$wgExtensionMessagesFiles['MAPKUDataAPI'] = $dir . 'MAPKUDataAPI.i18n.php';

$wgAutoloadClasses['MAPKU\RemoveDetection'] = $dir . 'RemoveDetection.php';

$wgAutoloadClasses['MAPKU\PlaceSerializer'] = $dir . 'includes/serializer/PlaceSerializer.php';
$wgAutoloadClasses['MAPKU\SortSerializer'] = $dir . 'includes/serializer/SortSerializer.php';

$wgAutoloadClasses['MAPKU\ApiDataBase'] = $apiDir . 'ApiDataBase.php';
$wgAutoloadClasses['MAPKU\ApiAllPlaces'] = $apiDir . 'ApiAllPlaces.php';
$wgAutoloadClasses['MAPKU\ApiAllSorts'] = $apiDir . 'ApiAllSorts.php';



# make it easier for user to add setupMAPKUDataAPI in LocalSettings.php

$wgMAPKUDataAPIStr = array();

function setupMAPKUDataAPI($str) {
  global $wgMAPKUDataAPIStr;
  $wgMAPKUDataAPIStr = array_merge($wgMAPKUDataAPIStr, $str);
}
