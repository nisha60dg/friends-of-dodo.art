<?php
/** 
 * define custom constants
 * 
 * 
 * 
*/
define('STYLESHEET_DIR_ROOT', get_stylesheet_directory().'/inc/');
define('STYLESHEET_URI_PATH', get_stylesheet_directory_uri().'/inc/');
global $wpdb;
$table_name = $wpdb->prefix . "artist_galleries";
define('ARTIST_GALLERY_TABLE', $table_name);
