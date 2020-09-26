<?php
require( dirname(__FILE__) . '/../../../wp-config.php' );

/* ---------------------------------------------------------- */
      /* ADD VIDEO TO THE VIMEO SHOWCASE FROM CHANNEL */
/* ------------------------------------------------------- */

function  add_vimeo_showcase_asset(){
             
  $curl = curl_init();
  //$tmp = fopen(dirname(__file__).'/logs_vimeo.txt', "a+");  
  $current_user = wp_get_current_user();       
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.vimeo.com/me/albums",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",             
    CURLOPT_POSTFIELDS => 'user_id=111828492&name=' .$current_user->ID.' Showcase',
    CURLOPT_HTTPHEADER => array(
      "Content-Type: application/x-www-form-urlencoded",
      "Authorization: Bearer " .VIMEO_API_TOKEN
    ),
  ));
  
  // Submit the POST request
  $response = curl_exec($curl);
  //Decodes a JSON string
  $data = json_decode($response,true);
  $vimeorespons = $data['uri'];
  $vm_showcase_id = preg_split("/[\/]+/", $vimeorespons)[4];
  //fwrite($tmp,"\r\n\r\n response  r1: ". print_r($vm_showcase_id, true));
  return  $vm_showcase_id;
}

/* -------------------------------------------- */
    /* ADD DEFAULT VIDEO FROM VIMEO SHOWCASE  */
// /* -------------------------------------------- */

function add_default_video($vm_showcase_id){

  $curl = curl_init();
  
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.vimeo.com/me/albums/".$vm_showcase_id."/videos/441111454",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer " .VIMEO_API_TOKEN
    ),
  ));
  
  $response = curl_exec($curl);
  
  curl_close($curl);
  // return $response;

}

?>