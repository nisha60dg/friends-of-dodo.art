<?php
require_once get_stylesheet_directory().'/inc/constants.php';
require_once get_stylesheet_directory().'/inc/art-actions.php';
require_once get_stylesheet_directory().'/inc/art-shortcodes.php';
 
// require_once get_stylesheet_directory().'/inc/art-filters.php';

function art_gallery_admin_url($type = '', $id = '', $query_args = array() ) {
    
    if(empty($id))
        return admin_url( 'admin.php' );

    $type = (empty($type)) ? 'page' : $type;
    
    $admin_query_args = array_merge( array( $type => $id ), $query_args );

    $url = add_query_arg( $admin_query_args, admin_url( 'admin.php' ) );

    return apply_filters( 'art_gallery_admin_url', $url, $query_args );
}

function artigallery_get_artists($args = [] ){

    $default_args = [
        'post_type' =>  'artists',
        'posts_per_page'    =>  '-1',
        'post_status'    =>  'publish',
    ];

    $args = array_merge($default_args, $args);

    return get_posts($args);
}

function artgallery_get_posts($post_type = '', $args = []){
    if(empty($post_type))
        return false;

    $default_args = [
        'post_type' =>  $post_type,
        'posts_per_page'    =>  '-1',
        'post_status'    =>  'publish',
    ];

    $args = array_merge($default_args, $args);
    
    return get_posts($args);
}

function artgallery_get_artist_gallery($artist_id = '', $event_id = ''){
    if(empty($artist_id))
        return false;
    
    global $wpdb;
    return $wpdb->get_row("select * from ".ARTIST_GALLERY_TABLE." where artist_id='".$artist_id."' and event_id = '".$event_id."' ");
}

function artgallery_show_date( $date = ''){
    return ($date) ? date("d M, Y", strtotime($date)) : ''; 
}

function artgallery_trim_show_title( $show_title = ''){
    if(empty($show_title))
        return false;

    $show_title_array = explode(" ",$show_title);
    $first_title = $show_title_array[0];
    unset($show_title_array[0]);
    $last_title = implode(" ", $show_title_array);

    return $first_title.' <span class="title-last-half">'.$last_title.'</span>';
}

function artgallery_get_shows_openings( $show = '', $type = ''){

    if(empty($type) || empty($show))
        return false;

    $args = array(
        'tax_query' =>  array(
            array(
                'taxonomy'  =>  'shows',
                'field'  =>  'term_id',
                'terms' =>  $show->term_id
            )
        )
    );
    $show->showOpenings = artgallery_get_posts('openings', $args);
    
    $current_date = date("Y-m-d");
    $onGoingShows = $upcomingShows = '';
    $end_date = $start_date = '';
    if(!empty($show->showOpenings)){
        foreach($show->showOpenings as $opening){
            $opening->artistsCount = count(explode(",",get_post_meta($opening->ID, '_event_artists', true)) ); 
            $opening_date = get_post_meta($opening->ID,'_event_date', true);
            if(empty($start_date)){
                $start_date = $opening_date;
            }else if( strtotime($opening_date) < strtotime($start_date)){
                $start_date = $opening_date;
            }
            
            if(empty($end_date)){
                $end_date = $opening_date;
            }else if( strtotime($opening_date) > strtotime($end_date)){
                $end_date = $opening_date;
            }
        }
    }
    if($start_date <= $current_date && $end_date >= $current_date){
        $onGoingShows = $show;
    }else if($start_date > $current_date){
        $upcomingShows = $show;
    }

    return ($type == "upcoming") ? $upcomingShows : $onGoingShows;
}

function pr($array = [], $stop = false){
    echo '<pre>'; print_r($array); echo  '<pre>';

    if($stop == true)
        die;
}