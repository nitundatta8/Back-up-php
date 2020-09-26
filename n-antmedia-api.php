<?php

/*--------------------------------------------------------------*/
          /*       ANTMEDIA API     */
/*--------------------------------------------------------------*/

function create_antmedia_id(){
  $tmp = fopen(dirname(__file__).'/logs_antmedia.txt', "a+");
  
  $current_user = wp_get_current_user();
  $post_title_wp = get_the_title( $post_id );
  
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://yougrowto.be/WebRTCAppEE/rest/v2/broadcasts/create",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    //CURLOPT_POSTFIELDS => 'add_asset_ids[0]='.$galleryAssetId.'&add_asset_ids[1]='.$liveStreamAssetId,
    //CURLOPT_POSTFIELDS =>$current_user->ID.'&name='.$post_title_wp,
    CURLOPT_POSTFIELDS =>"{\n    \"streamId\": \"{$current_user->ID}\",\n    \"status\": \"finished\",\n    \"type\": \"liveStream\",\n    \"name\": \"{$post_title_wp}\"\n}",
    CURLOPT_HTTPHEADER => array(
      "Content-Type: application/json"
    ),
  ));

  $response = curl_exec($curl);
  //echo($response);
  fwrite($tmp,"\r\n\r\n antmedia response  r1: ". print_r($response, true));
  $json_res = json_decode($response, true);
  fwrite($tmp,"\r\n\r\n antmedia json_res  r1: ". print_r($current_user->ID, true));
  //echo $json_res['streamId'];
  //add_antmedia_id($json_res['streamId'], $current_user->ID);
  //curl_close($curl);
  return $json_res['streamId'];
}

?>