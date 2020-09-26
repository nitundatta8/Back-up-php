<?php

/*-------------------------------------------*/
       /*CREATE PACKAGE WITH ASSETES */
/* -------------------------------------------- */

require( dirname(__FILE__) . '/../../../wp-config.php' );


  function create_gallery($vimeoshowcaseId,$post_title){
   
       
    $gallery_content='<div id="output"><input type="hidden" id="vimeoshowcaseId" name="vimeoshowcaseId" value="'.$vimeoshowcaseId.'"></div>
    <script type="text/javascript" src="http://stagingone.ugrow.tv/wp-content/themes/vimeo/gallery/bundleV10.js"></script>';
    return create_item("html_asset", "Gallery",$vimeoshowcaseId,$gallery_content,$post_title);
    } 
          
          
  function create_livestream($vimeoshowcaseId,$antmedia_id,$post_title){
    $livestream_content='<iframe width="1280" height="720" src="//yougrowto.be:/WebRTCAppEE/play.html?name='.$antmedia_id.'" frameborder="0" allowfullscreen></iframe>';
    return create_item("html_asset", "Live Stream",$vimeoshowcaseId,$livestream_content,$post_title );
    }
        
  function create_package($vimeoshowcaseId,$post_title){
    $val=" ";
    return create_item("package", "Package",$vimeoshowcaseId,$val,$post_title);
    }     
      
  
  function create_item($item_type,$title,$vimeoshowcaseId,$content,$post_title){
    $new_title = $post_title.'-'.$title;
    $response =  create_item_api($item_type, $new_title,$vimeoshowcaseId,$content );
    return $response['id'];
  }
  
  /*-------------------------------------------------------*/
     /*   MAKE API CALL TO CREATE INPLAYER ASSETS         */
     /*    1 - Paid ("name")
           2 - Code ("id")
           3 - Auth ("auth") 
           access_control_type_id=3 */
  /*-------------------------------------------------------*/
  function  create_item_api($item_type,$title,$vimeoshowcaseId,$content){
    $tmp = fopen(dirname(__file__).'/logs_gallery.txt', "a+");
    $curl = curl_init();
  
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://services.inplayer.com/v2/items",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,     
      CURLOPT_CUSTOMREQUEST => "POST",             
      CURLOPT_POSTFIELDS => 'item_type='.$item_type.'&title=' .$title.'&access_control_type_id=3&content='.$content,
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " .INPLAYER_API_TOKEN,
        "Content-Type: application/x-www-form-urlencoded"
      ),
    ));
    fwrite($tmp,"\r\n\r\n inplayer response: ". "Authorization: Bearer".INPLAYER_API_TOKEN);
    // Submit the POST request
    $response = curl_exec($curl);
    
    $data = json_decode($response,true);
    return $data;
  }

  /*---------------------- -------------------------------*/
      /*          ADD ITEM TO THE PACKAGE       */
/*-----------------------------------------------------*/
function add_item_in_package_api($packageId,$gallery_asset_id,$livestream_asset_id){
  $curl = curl_init();
            
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://services.inplayer.com/v2/items/packages/".$packageId."/bulk",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "PATCH ",
    CURLOPT_POSTFIELDS => 'add_asset_ids[0]='.$gallery_asset_id.'&add_asset_ids[1]='.$livestream_asset_id,
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer " .INPLAYER_API_TOKEN,
      "Content-Type: application/x-www-form-urlencoded"
    ),
  ));
  
  $response = curl_exec($curl);
  $data = json_decode($response,true);
  return $data;
  
}


/*--------------------------------------------------------------*/
          /*        PACKAGE WITH PRICE     */
/*--------------------------------------------------------------*/

function add_package_price($packageId){
  $curl = curl_init();
  
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://services.inplayer.com/v2/items/".$packageId."/access-fees",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS =>'access_type_id=3&amount=20&currency=USD&description=Simple Access Fee',
                          
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6ImVkNDFjNjU3LTk5ZjAtNGQ3ZC05N2RhLTgyYzliNTU2NGZhYSJ9.eyJhdWQiOiIzYjM5YjVhYi1iNWZjLTRiYTMtYjc3MC03MzE1NWQyMGU2MWYiLCJqdGkiOiJlZDQxYzY1Ny05OWYwLTRkN2QtOTdkYS04MmM5YjU1NjRmYWEiLCJpYXQiOjE1OTgwMjQ3NjgsIm5iZiI6MTU5ODAyNDc2OCwiZXhwIjoxNjAwNjIwMzY4LCJzdWIiOiJpbmZvQGVhc3lpbnRlcmZhY2UuaW8iLCJzY29wZXMiOltdLCJtaWQiOjEsImFpZCI6MzU3MzQ1OCwibXVpIjoiM2IzOWI1YWItYjVmYy00YmEzLWI3NzAtNzMxNTVkMjBlNjFmIiwiY3R4IjpbIm1lcmNoYW50Il0sInRpZCI6MzU3MzQ1OCwidHV1aWQiOiJlZDQxYzY1Ny05OWYwLTRkN2QtOTdkYS04MmM5YjU1NjRmYWEiLCJvaWQiOjB9.CU0laAJpuKqE0e3-gwraT0sGbCj6P_VAceiSiZ1S034",
      "Content-Type: application/x-www-form-urlencoded"
    ),
  ));
  $response = curl_exec($curl);
  $data = json_decode($response,true);
  return $data;
}
  
?>