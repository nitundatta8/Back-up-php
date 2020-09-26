<?php
/**
 * Plugin Name: inplayer_api_plugin_subcribers
 * Plugin URI: #
 * Description: Learning plugin use
 * Version: 1.0
 * Author: Nitun
 * Author URI: #
 */

require( dirname(__FILE__) . '/../../../wp-config.php' );

/* -------------------------------------------- */
      /* GET NUMBER OF SUBCRIBER FOR A PACKAGES */
/* -------------------------------------------- */

//$ugrow_user = get_current_user_id();
function get_Inplayer_subcribers($attr) {
    shortcode_atts(array('name'=>""),$attr);
  // return "Hello " .$attr['name'] ."!";  
  
  //InPlayer api call begins
  $curl = curl_init();
                    
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://services.inplayer.com/v2/analytics/subscriptions?grouped_by=item&action_type=recurrent",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer " .INPLAYER_API_TOKEN,
    ),
  ));
  
  // Submit the POST request
  $response = curl_exec($curl);
  //[{"country_code":"","country_name":"","grouped_by":"item","total_subscriptions":0,"subscriptions_grouped_by":null}]

  $data = json_decode($response,true);
  $total_subscriptions=$data[0]['total_subscriptions'];
  // Close cURL session handle
  curl_close($curl);
  return "Total Subcribers : " .$total_subscriptions;
}

add_shortcode( 'inplayer_subcribers', 'get_Inplayer_subcribers' );




?>