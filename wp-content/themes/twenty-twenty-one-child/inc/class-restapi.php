<?php
class Custom_Endpoints extends WP_REST_Controller {

    public function register_routes() {

        /**
         * Get List of All Shows and its openings categoriesed by ongoing and upcoming shows
         * 
         */
        register_rest_route( 'twenty-twenty-one-child/v2', 'shows/',array(
            'methods'  => 'GET', 
            'callback' => [$this, 'wp_artgallery_get_shows_list']
        ));
        
        /**
         * Get Details of Show
         */
        register_rest_route( 'twenty-twenty-one-child/v2', 'shows/(?P<show>[a-zA-Z0-9-]+)',array(
            'methods'  => 'GET', 
            'callback' => [$this, 'wp_artgallery_get_show_details']
        ));

        /**
         * Get List of all Openings
         * 
         */
        register_rest_route( 'twenty-twenty-one-child/v2', 'openings/',array(
            'methods'  => 'GET', 
            'callback' => [$this, 'wp_artgallery_get_openings']
        ));

        /**
         * Get details of a Opening
         * 
         */
        register_rest_route( 'twenty-twenty-one-child/v2', 'openings/(?P<opening>[a-zA-Z0-9-]+)',array(
            'methods'  => 'GET', 
            'callback' => [$this, 'wp_artgallery_get_opening_details']
        ));


        /**
         * Get List of all artists
         * 
         */
        register_rest_route( 'twenty-twenty-one-child/v2', 'artists/',array(
            'methods'  => 'GET', 
            'callback' => [$this, 'wp_artgallery_get_artists']
        ));

        /**
         * Get details of a artist
         * 
         */
        register_rest_route( 'twenty-twenty-one-child/v2', 'artists/(?P<artist>[a-zA-Z0-9-]+)',array(
            'methods'  => 'GET', 
            'callback' => [$this, 'wp_artgallery_get_artist_details']
        ));
        
        /**
         * Get List of all artworks
         * 
         */
        register_rest_route( 'twenty-twenty-one-child/v2', 'artworks/',array(
            'methods'  => 'GET', 
            'callback' => [$this, 'wp_artgallery_get_artworks']
        ));

        /**
         * Get details of a artwork
         * 
         */
        register_rest_route( 'twenty-twenty-one-child/v2', 'artworks/(?P<artwork>[a-zA-Z0-9-]+)',array(
            'methods'  => 'GET', 
            'callback' => [$this, 'wp_artgallery_get_artwork_details']
        )); 
    }

    /**
     * Callback function to get list of all shows with openings
     * 
     */
    public function wp_artgallery_get_shows_list($request) {        

        if(isset($request['artist']) && !empty($request['artist'])){
            $args = [
                'meta_query' => [
                    [
                        'key'   =>  '_event_artists',
                        'value'   => '(^|,)'.$request['artist'].'(,|$)',
                        'compare'  =>  'REGEXP'
                        ]
                    ]
            ];
            $artistOpenings = artgallery_get_posts('openings', $args);
            $showsArray = $artistShows = [];
            if(!empty($artistOpenings)){
                foreach($artistOpenings as $showOpening){
                    $openingShows = get_the_terms($showOpening->ID, 'shows');
                    foreach($openingShows as $showObject){
                        if(!in_array($showObject->term_id, $showsArray)){
                            $artistShows[] = $showObject;
                            $showsArray[] = $showObject->term_id;
                        }
                    }
                }
            }

            $showsOpenings = [];
            if(!empty($artistShows)){
                foreach($artistShows as $show){
                    if($isOnGoing = artgallery_get_shows_openings($show, 'ongoing') ){
                        $showsOpenings[] = $isOnGoing;
                    }
                }
            }

        }else{
            $shows = get_terms( array(
                'taxonomy' => 'shows',
                'hide_empty' => true,
            ) );
        
            // pr($shows);
            $showsOpenings = $onGoingShows = $upcomingShows = [];
            if(!empty($shows)){
                foreach($shows as $show){ 
                    $image_id = get_term_meta ( $show->term_id, 'image_id', true );
                    if(!empty($image_id)){
                        $show->thumbnail = wp_get_attachment_url( $image_id, 'medium' );
                    }
                    if($isOnGoing = artgallery_get_shows_openings($show, 'ongoing') ){
                        $onGoingShows[] = $isOnGoing;
                    }else if($isUpcoming = artgallery_get_shows_openings($show, 'upcoming') ){
                        $upcomingShows[] = $isUpcoming;
                    }
                }
            }

            if(!empty($onGoingShows)){
                $showsOpenings['ongoing'] = $onGoingShows;
            }

            if(!empty($upcomingShows)){
                $showsOpenings['upcoming'] = $upcomingShows;
            }

            if (empty($showsOpenings)) {
                return new WP_Error( 'empty_shows', 'there is not any ongoing or upcoming show exists', array('status' => 404) );
            }
        }

        
        $response = array('status' =>  true, 'message' => 'success', 'data'    =>  $showsOpenings);
        $response = new WP_REST_Response($response);
        $response->set_status(200); 

        return $response;
    }

    /**
     * Callback function to get show details
     * 
     */
    public function wp_artgallery_get_show_details($request) {

        $show = get_term_by('slug',$request['show'], 'shows');

        if (empty($show)) {
            return new WP_Error( 'empty_show', 'Invalid Show! Please try again.', array('status' => 404) );
        }

        $image_id = get_term_meta ( $show->term_id, 'image_id', true );
        if(!empty($image_id)){
            $show->thumbnail = wp_get_attachment_url( $image_id, 'medium' );
        }

        $response = array('status' =>  true, 'message' => 'success', 'data'    =>  $show);
        $response = new WP_REST_Response($response);
        $response->set_status(200); 

        return $response;
    }


    /**
     * callback function to get the list of all openings or filter by show 
     * 
     */
    public function wp_artgallery_get_openings($request){

        $args = [];
        
        if(isset($request['show']) && !empty($request['show'])){
            
            $show = get_term_by('slug',$request['show'], 'shows');
            if (empty($show)) {
                return new WP_Error( 'empty_show', 'Invalid Show! Please try again.', array('status' => 404) );
            }else{
                $args = array(
                    'tax_query' =>  array(
                        array(
                            'taxonomy'  =>  'shows',
                            'field'  =>  'term_id',
                            'terms' =>  $show->term_id
                        )
                    )
                );
            }
        }

        $showOpenings = artgallery_get_posts('openings', $args);
        // pr($showOpenings); die;
        $artists = [];
        foreach($showOpenings as $index=>$opening){
            $showOpenings[$index]->artistsCount = count(explode(",",get_post_meta($opening->ID, '_event_artists', true)) );
        }

        $response = array('status' =>  true, 'message' => 'success', 'data'    =>  $showOpenings);
        $response = new WP_REST_Response($response);
        $response->set_status(200); 

        return $response;
    }
    
    /**
     * callfunction to get the details of any opening
     * 
     */
    public function wp_artgallery_get_opening_details($request){

        $opening = artgallery_get_posts('openings',['name'=>$request['opening'] ]);
        if (empty($opening)) {
            return new WP_Error( 'empty_show', 'There is no opening found with the given details. Please try again later.', array('status' => 404) );
        }

        $opening = $opening[0];
        $shows = get_the_terms($opening->ID, 'shows');
        if(!empty($shows)){
            $image_id = get_term_meta ( $shows[0]->term_id, 'image_id', true );
            if(!empty($image_id)){
                $shows[0]->thumbnail = wp_get_attachment_url( $image_id, 'medium' );
            }
            $opening->show =$shows[0];
        }

        $opening->artistsCount = count(explode(",",get_post_meta($opening->ID, '_event_artists', true)) );

        $openingWindows = [];
        for($windowIndex = 1; $windowIndex <= 12; $windowIndex++){ 
            $_window_artist = get_post_meta($opening->ID, '_window_artist_'.$windowIndex, true);
            $_window_artist_image = get_post_meta($opening->ID, '_window_artist_image_'.$windowIndex, true);
            $image_preview_url = (!empty($_window_artist_image)) ? wp_get_attachment_url($_window_artist_image) : '';
            $artist = get_post($_window_artist);
            if(!empty($image_preview_url)){
                $openingWindows[] = array("artist" => $artist, "window_image" => $image_preview_url );
            }
        }

        $opening->openingWindows = $openingWindows;
        $opening->post_meta = get_post_meta($opening->ID);

        $response = array('status' =>  true, 'message' => 'success', 'data'    =>  $opening );
        $response = new WP_REST_Response($response);
        $response->set_status(200); 

        return $response;
    }

    /**
     * callback function to get the list of all artists or filter by show 
     * 
     */
    public function wp_artgallery_get_artists($request){

        $args = [];
        
        if(isset($request['opening']) && !empty($request['opening'])){
            $opening = get_post($request['opening']);
            if (empty($opening)) {
                return new WP_Error( 'invalid_opening', 'Invalid Opening! Please try again.', array('status' => 404) );
            }else{
                $args = array(
                    'meta_query' =>  array(
                        array(
                            'key'  =>  '_opening_artists',
                            'value'  =>  '',
                            'compare' =>  '',
                        )
                    )
                );
            }
        }else if(isset($request['show']) && !empty($request['show'])){
            $show = get_term_by('slug',$request['show'], 'shows');
            if (empty($show)) {
                return new WP_Error( 'empty_show', 'Invalid Show! Please try again.', array('status' => 404) );
            }else{
                $args = array(
                    'tax_query' =>  array(
                        array(
                            'taxonomy'  =>  'shows',
                            'field'  =>  'term_id',
                            'terms' =>  $show->term_id
                        )
                    )
                );
                
                $showOpenings = artgallery_get_posts('openings', $args);
                $artists = [];
                foreach($showOpenings as $index=>$opening){
                    $artists = array_merge($artists, explode(",",get_post_meta($opening->ID, "_event_artists", true) ) );
                }
                
                if(!empty($artists)){ 
                    $artists = array_filter(array_unique($artists));
                    $args = array("include"    => $artists);
                }
            }
        }

        $openingArtists = artgallery_get_posts('artists', $args);

        $response = array('status' =>  true, 'message' => 'success', 'data'    =>  $openingArtists);
        $response = new WP_REST_Response($response);
        $response->set_status(200); 

        return $response;
    }
    
    /**
     * callfunction to get the details of any artist
     * 
     */
    public function wp_artgallery_get_artist_details($request){

        $artist = artgallery_get_posts('artists',['name'=>$request['artist'] ]);
        if (empty($artist)) {
            return new WP_Error( 'empty_show', 'There is no artist found with the given details. Please try again later.', array('status' => 404) );
        }

        $artist = $artist[0];
        $artist->thumbnail = wp_get_attachment_url( get_post_thumbnail_id($artist->ID) );
        $artist->post_meta = get_post_meta($artist->ID);

        $response = array('status' =>  true, 'message' => 'success', 'data'    =>  $artist);
        $response = new WP_REST_Response($response);
        $response->set_status(200); 

        return $response;
    }

    /**
     * callback function to get the list of all artworks or filter by show 
     * 
     */
    public function wp_artgallery_get_artworks($request){
        
        $args = [];
        
        if( isset($request['opening']) && !empty($request['opening'])){
            $args['meta_query'] = [
                    'RELATION'	=>	'AND',
                    [
                        'key'   =>  '_artwork_opening',
                        'value'   => $request['opening'],
                        'compare'  =>  '='
                    ],
                ];
        }  
        if( isset($request['artist']) && !empty($request['artist'])){
            if(empty($args) || !isset($args['meta_query'])){
                $args['meta_query'] = [
                        'RELATION'	=>	'AND',
                        [
                            'key'   =>  '_artwork_artist',
                            'value'   => $request['artist'],
                            'compare'  =>  '='
                        ]
                    ];
            }else{
                $args['meta_query'][] = [
                    'key'   =>  '_artwork_artist',
                    'value'   => $request['artist'],
                    'compare'  =>  '='
                ];
            }
        }
        
        $artistArtworks = artgallery_get_posts('artworks', $args);
        foreach($artistArtworks as $index=>$artwork){
            $artistArtworks[$index]->thumbnail = wp_get_attachment_url( get_post_thumbnail_id($artwork->ID)); 
            $artistArtworks[$index]->post_meta = get_post_meta($artwork->ID);
        }

        $response = array('status' =>  true, 'message' => 'success', 'data'    =>  $artistArtworks);
        $response = new WP_REST_Response($response);
        $response->set_status(200); 

        return $response;
    }
    
    /**
     * callfunction to get the details of any artwork
     * 
     */
    public function wp_artgallery_get_artwork_details($request){

        $artwork = artgallery_get_posts('artworks',['name'=>$request['artwork'] ]);
        if (empty($artwork)) {
            return new WP_Error( 'empty_show', 'There is no artwork found with the given details. Please try again later.', array('status' => 404) );
        }

        $artwork = $artwork[0];
        $artwork->thumbnail = wp_get_attachment_url( get_post_thumbnail_id($artwork->ID) );
        $artist_id = get_post_meta($artwork->ID, '_artwork_artist', true);
        if(!empty($artist_id)){
            $artwork->artist = get_post( $artist_id );    
            $artwork->artist->post_meta = get_post_meta($artist_id);
        }
        $artwork->posttags = get_the_terms( $artwork->ID, 'tag' );
        $artwork->post_meta = get_post_meta($artwork->ID);

        $response = array('status' =>  true, 'message' => 'success', 'data'    =>  $artwork);
        $response = new WP_REST_Response($response);
        $response->set_status(200); 

        return $response;
    }
    
} 