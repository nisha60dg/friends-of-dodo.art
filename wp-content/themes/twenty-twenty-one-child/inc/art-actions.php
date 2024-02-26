<?php 
/**
 * Custom Theme Supports
 * 
 */

function artgallery_add_artists_meta_box() {
    add_meta_box('opening_artists',
        __( 'Opening Details', 'artgallery' ),
        'render_opening_artists_content',
        'openings',
        'advanced',
        'high'
    );

    add_meta_box('artist_meta_details',
        __( 'More Details', 'artgallery' ),
        'render_artist_details_content',
        'artists',
        'normal',
        'high'
    );
	
	add_meta_box(
		'wpt_artwork_status',
		'Artwork Details',
		'wpt_artwork_status',
		'artworks',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'artgallery_add_artists_meta_box' );

function render_artist_details_content( $post ) {
    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'artist_meta_box', 'artist_meta_box_nonce' );

    // Use get_post_meta to retrieve an existing value from the database.
    $_artist_opensea = get_post_meta( $post->ID, '_artist_opensea', true );
    $_artist_twitter_username = get_post_meta( $post->ID, '_artist_twitter_username', true );
    $_artist_instagram_username = get_post_meta( $post->ID, '_artist_instagram_username', true );
    ?>
    <div class="wp-meta-container">
        <div class="form-group">
            <label for="_artist_opensea">Opensea</label>
            <div class="form-input">
                <input class="form-control" type="text" name="_artist_opensea" value="<?=$_artist_opensea?>" />
            </div>
        </div>
        <div class="form-group">
            <label for="_artist_twitter_username">Twitter Username</label>
            <div class="form-input">
                <input class="form-control" type="text" name="_artist_twitter_username" value="<?=$_artist_twitter_username?>" />
            </div>
        </div>
        <div class="form-group">
            <label for="_artist_instagram_username">Instagram Username</label>
            <div class="form-input">
                <input class="form-control" type="text" name="_artist_instagram_username" value="<?=$_artist_instagram_username?>" />
            </div>
        </div>
    </div>
    <?php
}


function artgallery_save_artists_meta_box($post_id){
    // Check if our nonce is set.
    if ( ! isset( $_POST['artist_meta_box_nonce'] ) ) {
        return $post_id;
    }

    $nonce = $_POST['artist_meta_box_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'artist_meta_box' ) ) {
        return $post_id;
    }

    /*
        * If this is an autosave, our form has not been submitted,
        * so we don't want to do anything.
        */
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    
    // Check the user's permissions.
    if ( 'artists' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
    }

    /* OK, it's safe for us to save the data now. */
    update_post_meta( $post_id, '_artist_twitter_username', sanitize_text_field( $_POST['_artist_twitter_username'] ) );
    update_post_meta( $post_id, '_artist_instagram_username', sanitize_text_field( $_POST['_artist_instagram_username'] ) );
    update_post_meta( $post_id, '_artist_opensea', sanitize_text_field( $_POST['_artist_opensea'] ) );
}
add_action( 'save_post', 'artgallery_save_artists_meta_box' );

function render_opening_artists_content( $post ) {
 
    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'opening_artists_box', 'opening_artists_box_nonce' );

    // Use get_post_meta to retrieve an existing value from the database.
    $_event_date = get_post_meta( $post->ID, '_event_date', true );
    $_event_artists = explode(",",get_post_meta( $post->ID, '_event_artists', true ) );

    $artists = artgallery_get_posts('artists');
    // Display the form, using the current value.
    ?>
    <div class="form-group">
        <label for="_event_date"><?php _e( 'Event Date', 'textdomain' ); ?></label>
        <div class="form-input">
            <input class="form-control" type="date" id="_event_date" name="_event_date" value="<?php echo esc_attr( $_event_date ); ?>" style="width:80%;" />
            <small style="width:100%;display:inline-block;font-style: italic;">Please select Event Date</small>
        </div>
    </div>
    <div class="form-group">
        <?php for($windowIndex = 1; $windowIndex <= 12; $windowIndex++){ 
            $_window_artist = get_post_meta($post->ID, '_window_artist_'.$windowIndex, true);
            $_window_artist_image = get_post_meta($post->ID, '_window_artist_image_'.$windowIndex, true);
            $_window_artist_image = ($_window_artist_image) ? $_window_artist_image : '';
            $image_preview_url = (!empty($_window_artist_image)) ? wp_get_attachment_url($_window_artist_image) : '';
            ?>
            <div class="window-artlist-item">
                <div class="window-content">  
                    <div class="form-group">
                        <label for="_event_artists">
                            <?php _e( 'Window '.$windowIndex.' Artist', 'textdomain' ); ?>
                        </label>
                    </div>
                    <div class="form-group">
                        <img class="_window_image_preview <?=(empty($image_preview_url)) ? 'd-none': ''?>" src="<?=$image_preview_url?>" />
                        <input class="_window_image" type="hidden" size="36" name="_window_artist_images[]" value="<?=$_window_artist_image?>" style="width:100%;" />
                        <input class="upload_artist_image_button button button-primary" data-id="<?=$windowIndex?>" type="button" value="Upload Image" />
                    </div>
                    <div class="form-group">
                        <select class="form-control" id="_window_artist_<?=$windowIndex?>" name="_window_artists[]" style="width:100%;">
                            <option value=""> Select Artist </option>
                            <?php if(!empty($artists)){ ?>
                                <?php foreach($artists as $artist){ ?>
                                    <option <?=($artist->ID == $_window_artist) ? 'selected' : ''?> value="<?=$artist->ID?>"> <?=$artist->post_title?> </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php if($windowIndex%4 == 0){ ?>
                <div class="clearfix divider"></div>
            <?php } ?>
        <?php } ?>
    </div>
    <?php
}

function artgallery_save_openings_meta_box($post_id){
    /*
        * We need to verify this came from the our screen and with proper authorization,
        * because save_post can be triggered at other times.
        */

    // Check if our nonce is set.
    if ( ! isset( $_POST['opening_artists_box_nonce'] ) ) {
        return $post_id;
    }

    $nonce = $_POST['opening_artists_box_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'opening_artists_box' ) ) {
        return $post_id;
    }

    /*
        * If this is an autosave, our form has not been submitted,
        * so we don't want to do anything.
        */
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    
    // Check the user's permissions.
    if ( 'openings' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Sanitize the user input.
    $_event_date = sanitize_text_field( $_POST['_event_date'] );
    update_post_meta( $post_id, '_event_date', date("Y-m-d", strtotime($_event_date) ) );

    $_event_artists = [];
    foreach($_POST['_window_artist_images'] as $index=>$_window_artist_image){
        if(empty($_window_artist_image) && empty($_POST['_window_artists'][$index])){
            continue;
        }else{
            if(!empty($_window_artist_image)){
                update_post_meta( $post_id, '_window_artist_image_'.($index+1), $_window_artist_image );
            }
            
            update_post_meta( $post_id, '_window_artist_'.($index+1), $_POST['_window_artists'][$index] );
            if(!empty($_POST['_window_artists'][$index])){
                $_event_artists[]=$_POST['_window_artists'][$index];
            }
        }
    }
    // Update the meta field.
    $_event_artists = sanitize_text_field( implode(",",$_event_artists) );
    update_post_meta( $post_id, '_event_artists', $_event_artists );
}
add_action( 'save_post', 'artgallery_save_openings_meta_box' );


function wpt_artwork_status() {
	global $post;

	// Add an nonce field so we can check for it later.
    wp_nonce_field( 'artwork_meta_box', 'artwork_meta_box_nonce' );

	$_artwork_title = get_post_meta( $post->ID, '_artwork_title', true );
	$_artwork_status = get_post_meta( $post->ID, '_artwork_status', true );
	$_artwork_price = get_post_meta( $post->ID, '_artwork_price', true );
	$_artwork_artist = get_post_meta( $post->ID, '_artwork_artist', true );
	$_artwork_opening = get_post_meta( $post->ID, '_artwork_opening', true );
	
	$openings = [];
	if(!empty($_artwork_opening)){
		$openings[] = get_post($_artwork_opening);
	}
	
	$artists = artgallery_get_posts('artists');
	?>
	<div class="form-group">
        <label for="_artwork_title"><?php _e( 'Artwork Title', 'textdomain' ); ?></label>
        <div class="form-input">
            <input class="form-control" type="text" id="_artwork_title" name="_artwork_title" value="<?php echo esc_attr( $_artwork_title ); ?>" style="width:100%;" />
        </div>
    </div>
	
	<div class="form-group">
        <label for="_artwork_status"><?php _e( 'Status', 'textdomain' ); ?></label>
        <div class="form-input">
            <input class="form-control" type="text" id="_artwork_status" name="_artwork_status" value="<?php echo esc_attr( $_artwork_status ); ?>" style="width:100%;" />
        </div>
    </div>
	
	<div class="form-group">
        <label for="_artwork_price"><?php _e( 'Price', 'textdomain' ); ?></label>
        <div class="form-input">
            <input class="form-control" type="text" id="_artwork_price" name="_artwork_price" value="<?php echo esc_attr( $_artwork_price ); ?>" style="width:100%;" />
        </div>
    </div>
	
	<div class="form-group">
		<label for="_artwork_artist"><?php _e( 'Artist', 'textdomain' ); ?></label>
		<div class="form-input">
			<select class="form-control _artwork_artist" id="_artwork_artist" name="_artwork_artist" style="width:100%;">
				<option value=""> Select Artist </option>
				<?php if(!empty($artists)){ ?>
					<?php foreach($artists as $artist){ ?>
						<option <?=($artist->ID == $_artwork_artist) ? 'selected' : ''?> value="<?=$artist->ID?>"> <?=$artist->post_title?> </option>
					<?php } ?>
				<?php } ?>
			</select>
		</div>
	</div>
	
	<div class="form-group">
		<label for="_artwork_opening"><?php _e( 'Opening', 'textdomain' ); ?></label>
		<div class="form-input">
			<select class="form-control _artwork_opening" id="_artwork_opening" name="_artwork_opening" style="width:100%;">
				<option value=""> Select Opening </option>
				<?php if(!empty($openings)){ ?>
					<?php foreach($openings as $opening){ ?>
						<option <?=($opening->ID == $_artwork_opening) ? 'selected' : ''?> value="<?=$opening->ID?>"> <?=$opening->post_title?> </option>
					<?php } ?>
				<?php } ?>
			</select>
		</div>
	</div>
	<?php
}


function artgallery_save_artwork_meta_box($post_id){
    // Check if our nonce is set.
    if ( ! isset( $_POST['artwork_meta_box_nonce'] ) ) {
        return $post_id;
    }

    $nonce = $_POST['artwork_meta_box_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'artwork_meta_box' ) ) {
        return $post_id;
    }

    /*
        * If this is an autosave, our form has not been submitted,
        * so we don't want to do anything.
        */
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    
    // Check the user's permissions.
    if ( 'artworks' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
    }

    /* OK, it's safe for us to save the data now. */

    // Sanitize the artworks input.
    update_post_meta( $post_id, '_artwork_title', sanitize_text_field( $_POST['_artwork_title'] ) );
	update_post_meta( $post_id, '_artwork_status', sanitize_text_field( $_POST['_artwork_status'] ) );
	update_post_meta( $post_id, '_artwork_price', sanitize_text_field( $_POST['_artwork_price'] ) );
    update_post_meta( $post_id, '_artwork_artist', sanitize_text_field( $_POST['_artwork_artist'] ) );
	update_post_meta( $post_id, '_artwork_opening', sanitize_text_field( $_POST['_artwork_opening'] ) );
}
add_action( 'save_post', 'artgallery_save_artwork_meta_box' );


add_action('admin_menu' , 'art_add_custom_admin_pages'); 

function art_add_custom_admin_pages(){
    add_submenu_page("edit.php?post_type=artists", "Manage Artist Gallery", "Artist Gallery", 'edit_posts', 'artist-gallery', 'artist_gallery_content', null );
}
function artist_gallery_content(){

    $event = $event_id = $artist = $artist_id = null;

	if ( isset( $_GET['artist_id'] ) && '-1' !== $_GET['artist_id'] ) {
		$artist_id = $_GET['artist_id'];
        $artist = get_post($artist_id);
	}

    if ( isset( $_GET['event_id'] ) && !empty($_GET['event_id']) ) {
		$event_id = $_GET['event_id'];
        $event = get_post($event_id);
	}

	if ( !empty($artist) && !empty($event) ) {
		include STYLESHEET_DIR_ROOT . 'admin/art-gallery/artist-gallery.php';
	}else{
        
        $artists = artigallery_get_artists();
        include STYLESHEET_DIR_ROOT . 'admin/art-gallery/index.php';
    }
}


add_action('init', 'artgallery_create_custom_table');
function artgallery_create_custom_table(){
    global $wpdb;
 
    $charset_collate = $wpdb->get_charset_collate();
 
    $sql = "CREATE TABLE IF NOT EXISTS ".ARTIST_GALLERY_TABLE." (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      artist_id bigint(20) UNSIGNED NOT NULL,
      event_id bigint(20) UNSIGNED NOT NULL,
      event_date DATE NOT NULL,
      gallery_images TEXT NOT NULL,
      created_at datetime NOT NULL,
      updated_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
      PRIMARY KEY id (id)
    ) $charset_collate;";
 
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function artgallery_enqueue_admin_script( $hook ) {
    // echo $hook; die;
    $screen = get_current_screen(); 
    
    if(in_array($screen->post_type, array("openings","artworks") ) ){
        wp_enqueue_style( 'select2', "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css", array(), '1.0.0', false );
        wp_enqueue_script( 'select2', "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js", array(), '1.0.0', true );
    }
    
    wp_enqueue_script( 'commonjs', STYLESHEET_URI_PATH."/assets/js/common.js", array(), '1.0.0', true );
    wp_enqueue_style( 'custom-admin', STYLESHEET_URI_PATH . '/assets/css/admin-styles.css');

    if ( 'artists_page_artist-gallery' != $hook && !in_array($screen->post_type, array("openings","artworks") )) {
        return;
    }
    
    wp_enqueue_media(); 
    wp_register_script( 'artgallery-script', STYLESHEET_URI_PATH . '/assets/js/artgallery.js', [ 'jquery' ] );
    wp_localize_script( 'artgallery-script', 'artgallery', [ 'ajaxurl' => admin_url('admin-ajax.php' ) ] );        
    wp_enqueue_script( 'artgallery-script' );
}
add_action( 'admin_enqueue_scripts', 'artgallery_enqueue_admin_script' );

add_action('wp_ajax_artgallery_get_artists_events', 'process_artgallery_get_artists_events');
add_action('wp_ajax_nopriv_artgallery_get_artists_events', 'process_artgallery_get_artists_events');
function process_artgallery_get_artists_events(){
    
    $response = array();
    $data = $_POST;
    if(!isset($data['artist_id']) || empty($data['artist_id'])){
        $response = array('status' => false, 'message' => 'Artist ID is missing');
    }else{
        $args = [
            'meta_query' => [
                [
                    'key'   =>  '_event_artists',
                    'value'   => '(^|,)'.$data['artist_id'].'(,|$)',
                    'compare'  =>  'REGEXP'
                    ]
                ]
        ];
        $openings = artgallery_get_posts('openings', $args);
        
        if(empty($openings)){
            $response = array('status' => false, 'message' => 'Artist has not been associated with any Event.', );
        }else{
            ob_start();
            include STYLESHEET_DIR_ROOT.'templates/partials/artist-event-dates.php';
            $content = ob_get_clean();
            
            $response = array('status' => true, 'message' => 'success', 'data' => $openings, 'content' => $content); 
        }
    }

    echo json_encode($response); exit();
}
function process_artgallery_save_artist_gallery_attachments(){
    $response = array();
    $data = $_POST;
    if(!isset($data['artist_id']) || empty($data['artist_id'])){
        $response = array('status' => false, 'message' => 'Artist ID is missing');
    }else if(!isset($data['gallery_attachments']) || empty($data['gallery_attachments'])){
        $response = array('status' => false, 'message' => 'Please add some Images to gallery');
    }else{
        global $wpdb;
        $artist_gallery = artgallery_get_artist_gallery($data['artist_id'], $data['event_id']);
        $gallery_images = implode(",",array_filter(explode(",",$data['gallery_attachments']) ) );
        $insertArray = [
            'gallery_images' =>  $gallery_images,
            'updated_at'    =>  date("Y-m-d H:i:s")
        ]; 
        if(!empty($artist_gallery)){
            $gallery_id = $wpdb->update(ARTIST_GALLERY_TABLE, $insertArray, ['id' => $artist_gallery->id]); 
        }else{
            $insertArray = array_merge($insertArray, [
                'artist_id' =>  $data['artist_id'],
                'event_id' =>  $data['event_id'],
                'event_date' =>  date("Y-m-d", strtotime($data['event_date'])),
                'created_at'    =>  date("Y-m-d H:i:s")
            ]); 
            $gallery_id = $wpdb->insert(ARTIST_GALLERY_TABLE, $insertArray);
        }
        if($gallery_id){
            $response = array('status' => true, 'message' => 'success'); 
        }else{
            $response = array('status' => true, 'message' => 'Something went wrong. Please try again later.'); 
        }
    }

    echo json_encode($response); exit();
}
add_action('wp_ajax_artgallery_save_artist_gallery_attachments', 'process_artgallery_save_artist_gallery_attachments');
add_action('wp_ajax_nopriv_artgallery_save_artist_gallery_attachments', 'process_artgallery_save_artist_gallery_attachments');



// Setup rewrite rules
add_action( 'init', 'rewrites_init' );
function rewrites_init() {
    add_rewrite_rule(
        'openings/([-a-zA-Z0-9]+)/artists/([-a-zA-Z0-9]+)$',
        'index.php?openings=$matches[1]&artists=$matches[2]',
        'top' );
}

// Add variables
add_filter('query_vars', 'add_query_vars', 0);
function add_query_vars($vars) {
    $vars[] = 'openings';
    $vars[] = 'artists';
    return $vars;
}

// catch the request for this page
add_action('parse_request', 'parse_requests', 0);
function parse_requests() {
    global $wp, $wp_query;
    if(isset($wp->query_vars['openings']) && isset($wp->query_vars['artists'])) {
        // find the artists post
        $posts = new WP_Query( array(
            'post_type' => 'artists',
            'name' => $wp->query_vars['artists'],
            'post_status' => 'publish'
        ));
        if(!empty($posts) ) {
            // set the global query or set your own variable
            $wp_query = $posts;
            // set the openings variable to use in your template
            $openings = get_page_by_path( $wp->query_vars['openings'], OBJECT, 'openings' );
            // include your custom post type template
            if (include_once get_stylesheet_directory().'/single-artists.php' ) {
                exit();
            }
        } else {
            // handle error
            $wp_query->set_404();
            status_header(404);
            locate_template('404.php', true);
            exit;
        }
    }
}