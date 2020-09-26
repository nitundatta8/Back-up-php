<?php
//require_once __DIR__.'/vendor/autoload.php';
require_once '/home1/ugrow/stagingone.ugrow.tv/wp-content/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

function save_channel_info($channelId,$userId,$assetId,$liveStreamAssetId,$packageId,$vimeoshowcaseId,$live_streamId,$post_title,$antmedia_id){
   $dbname = 'Channels';
  $factory = (new Factory)->withServiceAccount(__DIR__.'/secret');
  //$tmp = fopen(dirname(__file__).'/logs.txt', "a+");
  $database = $factory->createDatabase();
  var_dump($database);
  //fwrite($tmp,"\r\n\r\n database  r1: ". print_r($database , true));
  $data =[
    $userId.'/channelId' => $channelId, 
    $userId.'/userId' => $userId,
    $userId.'/assetId' => $assetId,
    $userId.'/liveStreamAssetId' => $liveStreamAssetId,
    $userId.'/packageId' => $packageId,
    $userId.'/vimeoshowcaseId' => $vimeoshowcaseId,
    $userId.'/livestreamId' => $live_streamId,
    $userId.'/post_title' => $post_title,
    $userId.'/antmediaId' => $antmedia_id
  ];
  foreach ($data as $key => $value){
    $database->getReference()->getChild($dbname)->getChild($key)->set($value);
  }
 
}
//save_channel_info(001,1,'Gym','a1','s1','v1');

?>