<?php
/**
 * 
 * 
 * 
 */

function render_artist_gallery_html($attr = []){
    $artist_id = (isset($attr['id'])) ? $attr['id'] : '';

    if(empty($artist_id))
        return false;

    // artgallery_get_artist_gallery
}
add_shortcode('artist_gallery', 'render_artist_gallery_html');
