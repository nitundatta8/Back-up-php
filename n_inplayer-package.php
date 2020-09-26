<?php
/**
 * Plugin Name: n_inplayer-package
 * Plugin URI: #
 * Description: Making API calls with cURL
 * Version: 1.0
 * Author: Ugrow
 * Author URI: #
 */
require( dirname(__FILE__) . '/../../../wp-load.php' );

require('n_wpfirebase.php'); 
require('n-inplayer-asset-api.php');
require('n-antmedia-api.php'); 
require('n-vm-showcase-api.php');
require('n-wp-livestream.php');

/* ------------------------------------------------------ */
      /* CREATE INPLAYER ASSETS AND PACKAGE 
      NOTE: cred_save_data()-- This Toolset hook allows
      doing a custom action when post data is saved to database.
    */
/* -------------------------------------------------------- */



function add_inplayer_package(){
    add_action('cred_save_data', 'create_ugrow_package',10,2);
    function create_ugrow_package($post_id,$form_data){
      if ( is_user_logged_in() ) {
        if ($form_data['id']=='386') {
         
           $tmp = fopen(dirname(__file__).'/logsdata.txt', "a+");
          try {
            
              $post_title = get_the_title( $post_id );
              $current_user = wp_get_current_user(); 
              
              $antmedia_id =create_antmedia_id();
              $vm_showcase_id = add_vimeo_showcase_asset();
              $gallery_asset_id = create_gallery($vm_showcase_id,$post_title);
              $livestream_asset_id= create_livestream($vm_showcase_id,$antmedia_id,$post_title );
              $packageId = create_package($vm_showcase_id,$post_title);
              fwrite($tmp,"\r\n\r\n log-data-inplayer: ".$gallery_asset_id. " livestream: ".$livestream_asset_id);
              
              add_item_in_package_api($packageId,$gallery_asset_id,$livestream_asset_id);
              add_package_price($packageId);
              
              //Add default video for new channel. 
              add_default_video($vm_showcase_id);
             
             //Dynamically add antmedia_id,livestream_id into the wp Livestream post.
              $live_streamId=create_wp_livestream($antmedia_id,$livestream_asset_id,$post_title);
              
              //Store channel info into Firebase
              save_channel_info($post_id, $current_user->ID,$gallery_asset_id,$livestream_asset_id,$packageId,$vm_showcase_id,$live_streamId,$post_title,$antmedia_id);
              
              //Update InPlayer gallery_asset_id and livestream_asset_id into wp database.
              update_post_meta( $post_id, 'wpcf-embed-channel', $gallery_asset_id);
              update_post_meta( $post_id, 'wpcf-live-stream-id', $livestream_asset_id);
             
            } catch (Exception $e) {
              
            fwrite($tmp,"\r\n\r\n Error".$e->getMessage());
          }
        }
        
      }
    
    }
}

add_shortcode( 'ugrow_inplayer_package', 'add_inplayer_package' );


?>