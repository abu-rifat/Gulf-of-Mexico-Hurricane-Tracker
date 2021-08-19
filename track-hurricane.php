<?php
/*
 * Plugin Name:       Track Hurricane
 * Plugin URI:        aburifat.com
 * Description:       Just use this shortcode [track-hurricane] and boom.
 * Version:           1.0.0
 * Author:            Abu Rifat M.
 * Author URI:        aburifat.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       track-hurricane
 */
// If this file is called directly, abort.
if(!defined('WPINC')){
    die;
}
    
add_shortcode( 'track-hurricane' , 'track_hurricane' );
#namespace Track_Hurricane;
global $body;
global $header;

function get_rss_data() {
    WP_DEBUG ? error_log("track_hurricane Plugin ::get_rss_data called") : '';
    global $body;
    $response = wp_remote_get("https://www.nhc.noaa.gov/index-at.xml");
    if ( is_array( $response ) ) {
        $header = $response['headers'];
        $body = $response['body'];
    }
}

function track_hurricane($atts, $content = '') {
    global $body;
    $build_output_string = '';
    $graphics_output_string='';
    get_rss_data();
    $xml = simplexml_load_string($body);
    foreach($xml->channel->item as $item){
        if(strpos($item->title ,'Graphics')){
            $graphics_output_string.="<div class='th-data'>";
            $graphics_output_string.="<div class='th-title'>".$item->title."</div>";
            $graphics_output_string.="<div class='th-date'>".$item->pubDate."</div>";
            $graphics_output_string.="<div class='th-description'>".$item->description."</div>";
            $graphics_output_string .= "</div>";
        }else{
            $build_output_string.="<div class='th-data'>";
            $build_output_string.="<div class='th-title'>".$item->title."</div>";
            $build_output_string.="<div class='th-date'>".$item->pubDate."</div>";
            $build_output_string.="<div class='th-description'>".$item->description."</div>";
            $build_output_string .= "</div>";
        }
    }
    $build_output_string=$graphics_output_string.$build_output_string;
    return do_shortcode($build_output_string);
}
    
function hurricane_enqueue_scripts(){
    //wp_enqueue_script( 'hurricane-script', plugin_dir_url( __FILE__) . '/hurricane.js' );
    wp_enqueue_style( 'hurricane', plugin_dir_url( __FILE__) . '/hurricane.css' );
    //wp_enqueue_style('dashicons');
}

add_action( 'wp_enqueue_scripts', "hurricane_enqueue_scripts" );
