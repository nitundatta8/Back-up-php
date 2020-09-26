<?php
/**
 * Plugin Name: n_antmedia_info
 * Plugin URI: #
 * Description: Handle Antmedia info
 * Version: 1.0
 * Author: Ugrow
 * Author URI: #
 */

function show_antmedia_info(){
  $tmp = fopen(dirname(__file__).'/logs.txt', "a+");
  
  $inPlayer_Livestream_Id = get_post_meta(get_the_ID(), 'wpcf-live-stream-id', true); 
  setcookie("ik", INPLAYER_ACCESS_KEY, time() + (86400 * 30), '/'); // 86400 = 1 day
 
  fwrite($tmp,"\r\n\r\n live_streamId  inPlayer_Livestream_Id: ".$inPlayer_Livestream_Id);
  echo "

  <script type='text/javascript' src='https://assets.inplayer.com/paywall/v3/paywall.min.js'></script>
  <div id='inplayer-".$inPlayer_Livestream_Id."' class='inplayer-paywall'></div>
  <script>
    var paywall = new InplayerPaywall('key', [{
      id: ".$inPlayer_Livestream_Id."
    }], {
      oauthAppKey: 'key'
    });
  </script>";

  
}
add_shortcode( 'antmedia_info', 'show_antmedia_info' );
?>