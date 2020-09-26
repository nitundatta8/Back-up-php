<?php

/* -------------------------------------------- */
      /*      CREATE WP LIVESTEAM  */
// /* -------------------------------------------- */


function create_wp_livestream($antmedia_id,$livestream_asset_id,$post_title){
  
 $tmp = fopen(dirname(__file__).'/logswp.txt', "a+");
 $current_user = wp_get_current_user();
 $my_post = array(
   'post_title'   => $post_title,
   //'post_content' => $_POST['post_content'],
   'post_status'  => 'publish',
   'post_author'  => $current_user->ID,
   'post_type' => 'live-stream'
 );
 $post_id = wp_insert_post( $my_post );
 $status=update_post_meta( $post_id, 'wpcf-live-stream', $antmedia_id);
 update_post_meta( $post_id, 'wpcf-inplayer-live-stream-id', $livestream_asset_id);
 
 //  fwrite($tmp,"\r\n\r\n live_streamId  status: ".$status.",post_id:".$post_id." livestream_asset_id: ".$livestream_asset_id);

 return $post_id;
}

?>