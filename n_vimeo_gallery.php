<?php
/**
 * Plugin Name: n_vimeo_gallery
 * Plugin URI: #
 * Description: Show video from vimeo showcase
 * Version: 1.0
 * Author: Ugrow
 * Author URI: #
 */
require( dirname(__FILE__) . '/../../../wp-config.php' );


function show_vimeo_gallery(){

  $assetId = get_post_meta(get_the_ID(), 'wpcf-embed-channel', true); 

  setcookie("ik", INPLAYER_ACCESS_KEY, time() + (86400 * 30), '/'); // 86400 = 1 day
  
 echo "

 <script type='text/javascript' src='https://assets.inplayer.com/paywall/v3/paywall.min.js'></script>
 <div id='inplayer-".$assetId."' class='inplayer-paywall'></div>
 <script>

 function getData(cname) {
  var name = cname + '=';
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return '';
}

 var a=getData('ik');
   var paywall = new InplayerPaywall(a, [{
     id: ".$assetId."
   }], {
     oauthAppKey:a
   });
 </script>";
 }

add_shortcode( 'vimeo_gallery', 'show_vimeo_gallery' );


?>