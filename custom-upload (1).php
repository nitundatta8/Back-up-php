<?php
/**
 * Plugin Name: Custom Upload
 * Plugin URI: #
 * Description: Handle video upload to S3
 * Version: 1.0
 * Author: Ugrow
 * Author URI: #
 */

require( dirname(__FILE__) . '/../../../wp-load.php' );
require( dirname(__FILE__) . '/../../../wp-config.php' );
include_once( plugin_dir_path( __FILE__ ).'/lib/aws/aws-autoloader.php' );
use Aws\S3\S3Client;

use Aws\Exception\AwsException;

add_shortcode( 'ugrow_uploader', 'custom_upload_callback' );

function custom_upload_callback(){
    // verify user is logged in
    if ( !is_user_logged_in() ) {
        auth_redirect();
    } 
    //ob_start();
    upload_form_function();
    //return ob_get_clean();
}

function upload_form_function() {
    if (isset($_POST['video_submit'])) {
        // process
        handle_video_upload();
    }

    video_upload_html_form();
}

function video_upload_html_form() {
    global $user_prov_name;
    global $form_error;
    global $success_upload;

    echo '<div><form action="'. get_permalink() . '" method="post" enctype="multipart/form-data">';
    if ( $form_error ) {
        error_log("there are errors in form - ". $form_error);
        echo '<div style="color:red;margin:2em;">';
        echo $form_error . '<br/>';
        echo '</div>';
    } elseif (isset($user_prov_name)) {
        if ($success_upload) {
            echo '<p style="color:green;margin:2em;">Your video named <b>'.$user_prov_name. '</b> was successfully uploaded! Upload some more....</p>';
        } else {
            echo '<p style="color:red;margin:2em;">We encountered failure uploading '.$user_prov_name. '! Please try again!</p>';
        }
    }
    
    echo '<div style="margin:2em;">
            <label for="file-upload" class="custom-file-upload">
                <i class="fa fa-cloud-upload" width="450" height="auto"></i> Upload Video File
            </label>
            <input class="file-class" id="file-upload" type="file" name="custom_video" size="45" />
           <br/>
        </div>
        <div style="margin:2em;">
        <input type="text" name="custom_video_name" size="24" placeholder="Enter Video Name" value="' . (  $form_error ? $user_prov_name : null ) . '"/>
        <br/>
        </div>
        <div style="margin:2em;">
        <input type="submit" name="video_submit" value="Submit" />
        </div>
        </form></div>';
}

function handle_video_upload() {
    // Make the WP_Error object global    
    global $form_error;
    global $user_prov_name;
    global $success_upload;

    $success_upload = false;
    // instantiate the class
    $form_error = '';

    $wordpress_upload_dir = wp_upload_dir();
    // $wordpress_upload_dir['path'] is the full server path
    $video = $_FILES['custom_video'];
    if( empty( $_FILES['custom_video'] ) ) {
        $form_error.= 'video must be provided';
        return;
    }
     
    if( $_FILES['custom_video']['error'] ) {
        $form_error.='Video must be provided'; 
        return;
    }
     
    if( $_FILES['custom_video']['size'] > wp_max_upload_size() ) {
        $form_error.= 'Video is too large to be uploaded.'; 
        return;
    }

    $new_file_mime = mime_content_type( $_FILES['custom_video']['tmp_name'] );
    if (!(strncmp($new_file_mime, 'video', 5) === 0)) {
        $form_error.='This type of file is not supported, only video files are supported.'; 
        return;
    }

    $new_file_name = md5(time());
    $user_prov_name = $_FILES['custom_video']['name'];
    if ( isset( $_POST['custom_video_name'] ) and trim($_POST['custom_video_name']) != '' ) {
        $user_prov_name = $_POST['custom_video_name'];
        error_log("User provided custom name for file - ". $user_prov_name);
    } else {
        error_log("Using original name for file - ". $user_prov_name);
    }

    //Create a S3Client
    $s3 = new Aws\S3\S3Client([
        'version' => 'latest',
        'region' => 'us-east-1',
        'credentials' => array(
            'key'    => AWS_ACCESS_KEY,
            'secret' => AWS_SECRET_KEY,
        )
    ]);

    $current_user_id = get_current_user_id();
    error_log("Uploading file for user - ".$current_user_id. " file = ".$video['tmp_name']. " name = ". $user_prov_name. " mime = ". $new_file_mime);
     
    // looks like everything is OK
    $key = $current_user_id.'/'.$user_prov_name;
    error_log("Uploading file to s3 key - ".$key);
    try {
        $result = $s3->headObject([
            'Bucket' => VIDEO_BUCKET,
            'Key' => $key,
        ]);
        error_log("File already exists in s3 key - ".$key. " result - ". $result);
        if ($result['ContentLength'] > 0) {
            $form_error.='File with name <b>'. $user_prov_name.'</b> already exists, please use a different name.'; 
            return;
        }
    } catch(Exception $e) {
        // ignore
    }
    $result = $s3->putObject([
        'Bucket' => VIDEO_BUCKET,
        'Key' => $key,
        'SourceFile' => $video['tmp_name'],
        'ContentType' => $new_file_mime,
    ]);
    // Show the uploaded file in browser
    //wp_redirect( '/index.php/upload-test/' );
    $success_upload = true;
}


?>