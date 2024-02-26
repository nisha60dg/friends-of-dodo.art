<?php
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_setup' );
function enqueue_child_theme_setup(){
	$parenthandle = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', array(),  $theme->parent()->get('Version'));
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),array( $parenthandle ),$theme->get('Version'));
}

/*
 ***************
 Shows Custom Post Type Starts Here
 ***************
*/

add_action('init', function(){
    
	// Openings
	$labels = array(
        "name" => "Openings",
        "singular_name" => "Opening",
		"all_items" => "All Openings",
        "add_new" => "Add New Opening",
        "add_new_item" => "Add New Opening",
        "edit" => "Edit",
        "edit_item" => "Edit Opening",
        "new_item" => "New Opening",
        "view" => "View",
        "view_item" => "View Opening",
        "search_items" => "Search Openings",
        "not_found" => "No Opening Found",
        "not_found_in_trash" => "No Openings Found in Trash",
        "parent" => "Parent Opening",
    );

    $args = array(
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "show_ui" => true,
        "has_archive" => true,
        "show_in_menu" => true,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
		'menu_icon' => 'dashicons-image-filter',
        "hierarchical" => false,
        "rewrite" => array( "slug" => "openings", "with_front" => true ),
        "query_var" => true,
        "supports" => array( "title", "editor", "revisions", "thumbnail" )
    );

    register_post_type( "openings", $args );
	
	// Shows
	$labels = array(
		'name' => _x( 'Shows', 'taxonomy general name' ),
		'singular_name' => _x( 'Show', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Shows' ),
		'all_items' => __( 'All Shows' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Shows' ), 
		'update_item' => __( 'Update Shows' ),
		'add_new_item' => __( 'Add New Show' ),
		'new_item_name' => __( 'New Show Name' ),
		'menu_name' => __( 'Shows' ),
	  );    
	 
	  register_taxonomy('shows',array('openings'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_in_rest' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'shows' ),
	  ));
	  
	  
	// Artists
    $labels = array(
        "name" => "Artists",
        "singular_name" => "Artist",
		"add_new_item" => "Add New Artist",
		"add_new" => "Add New Artist",
    );

    $args = array(
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "show_ui" => true,
        "has_archive" => true,
        "show_in_menu" => true,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
		'menu_icon' => 'dashicons-businessman',
        "hierarchical" => true,
        "rewrite" => array( "slug" => "artists", "with_front" => true ),
        "query_var" => true,
        "supports" => array( "title", "editor", "revisions", "thumbnail","custom" )
    );

    register_post_type( "artists", $args );

});

// Artworks
	$labels = array(
        "name" => "Artworks",
        "singular_name" => "Artwork",
		"all_items" => "All Artworks",
        "add_new" => "Add New Artwork",
        "add_new_item" => "Add New Artwork",
        "edit" => "Edit",
        "edit_item" => "Edit Artwork",
        "new_item" => "New Artwork",
        "view" => "View",
        "view_item" => "View Artwork",
        "search_items" => "Search Artworks",
        "not_found" => "No Artwork Found",
        "not_found_in_trash" => "No Artwork Found in Trash",
        "parent" => "Parent Artwork",
    );

    $args = array(
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "show_ui" => true,
        "has_archive" => true,
        "show_in_menu" => true,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
		'menu_icon' => 'dashicons-layout',
        "hierarchical" => false,
        "rewrite" => array( "slug" => "artworks", "with_front" => true ),
        "query_var" => true,
        "supports" => array( "title", "editor", "thumbnail" )
    );

    register_post_type( "artworks", $args );
	

add_action( 'init', 'create_tag_taxonomies', 0 );

//create two taxonomies, genres and tags for the post type "tag"
function create_tag_taxonomies() 
{
  // Add new taxonomy, NOT hierarchical (like tags)
  $labels = array(
    'name' => _x( 'Tags', 'taxonomy general name' ),
    'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Tags' ),
    'popular_items' => __( 'Popular Tags' ),
    'all_items' => __( 'All Tags' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Tag' ), 
    'update_item' => __( 'Update Tag' ),
    'add_new_item' => __( 'Add New Tag' ),
    'new_item_name' => __( 'New Tag Name' ),
    'separate_items_with_commas' => __( 'Separate tags with commas' ),
    'add_or_remove_items' => __( 'Add or remove tags' ),
    'choose_from_most_used' => __( 'Choose from the most used tags' ),
    'menu_name' => __( 'Tags' ),
  ); 

  register_taxonomy('tag','artworks',array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'artwork_tag' ),
  ));
}

/*
add_action( 'init', 'add_artworks_metaboxes', 0 );
function add_artworks_metaboxes() {
	add_meta_box(
		'wpt_artwork_status',
		'Artwork Status',
		'wpt_artwork_status',
		'artworks',
		'side',
		'default'
	);

}

add_action( 'init', 'wpt_artwork_status', 0 );
function wpt_artwork_status() {
	global $post;

	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'artwork_status_fields' );

	// Get the location data if it's already been entered
	$location = get_post_meta( $post->ID, 'artworks', true );

	// Output the field
	echo '<input type="text" name="status" value="' . esc_textarea( $location )  . '" class="widefat">';

}
*/
	

// Include custom files
require_once get_stylesheet_directory().'/inc/art-functions.php';
require_once get_stylesheet_directory().'/inc/show-functions.php';
require_once get_stylesheet_directory().'/inc/class-restapi.php';

add_action('rest_api_init', 'register_custom_endpoints');
function register_custom_endpoints(){ 
    $custom_endpoints = new Custom_Endpoints();
    $custom_endpoints->register_routes();  
}
?>