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
/*
  //Name of Categories
  'mapku-cat' => '导出到数据接口',
  'mapku-cat-sort' => '按功能分类',
  'mapku-cat-guide' => '攻略',

  //Property name
  'mapkuprop-address' => '地理位置',
  'mapkuprop-baidu' => 'Baidu经纬度',
  'mapkuprop-google' => 'Google经纬度',
  'mapkuprop-category' => '分类',
  'mapkuprop-mainimg' => '条目图片',

//new
  'prop-address' => '地理位置',
  'prop-baidu' => 'Baidu经纬度',
  'prop-google' => 'Google经纬度',
  'prop-category' => '地点分类',
  'prop-mainimg' => '条目图片',
  'prop-guide-parent-place' => '关联地点',
  'prop-image-parent-place' => '关联地点',

  'cat-place' => '地点',
  'cat-image' => '地点图片'
  'cat-guide' => '攻略',
  'cat-sort' => '地点分类',

*/