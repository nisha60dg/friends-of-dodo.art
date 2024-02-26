<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();

global $post;

$currentOpening = $post;

$shows = get_the_terms($currentOpening->ID, 'shows');
$show =$shows[0];

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
$image_id = get_term_meta ( $show->term_id, 'image_id', true );
/* Start the Loop */
while ( have_posts() ) :
	the_post();
	?>

    <header class="page-header alignwide">
	    <div class="show-container artists-outer">
		    <div class="col-md-3">
			    <?php if(empty($image_id)) {?>
                    <div class="show-title">		  
		                <a href="<?=get_term_link($show->term_id)?>"><?=artgallery_trim_show_title($show->name)?></a> 
		            </div>
		        <?php } else { ?>
                    <div class="show_thumbnail">
                        <?= wp_get_attachment_image ( $image_id, 'medium' ); ?>
                    </div>
			    <?php } ?>
            </div>
        		 
        <div class="col-md-9">
            <?php foreach($showOpenings as $opening){
                $artistsCount = count(explode(",",get_post_meta($opening->ID, '_event_artists', true)) );
                ?>
                <div class="opening-item"><?=$opening->post_title?></div>
                <p><?=$artistsCount?> Artists</p>
            <?php } ?>
        </div>
	</div>
    </header>
    <?php get_template_part( 'template-parts/content/content-single' ); ?>
    
	<div class="opening-windows-container">
        <?php 
        for($windowIndex = 1; $windowIndex <= 12; $windowIndex++){ 
            $_window_artist = get_post_meta($currentOpening->ID, '_window_artist_'.$windowIndex, true);
            $_window_artist_image = get_post_meta($currentOpening->ID, '_window_artist_image_'.$windowIndex, true);
            $_window_artist_image = ($_window_artist_image) ? $_window_artist_image : '';
            $image_preview_url = (!empty($_window_artist_image)) ? wp_get_attachment_url($_window_artist_image) : '';
            $artist = get_post($_window_artist);
            
            $artist_artwork_link = get_permalink($currentOpening->ID).'artists/'.$artist->post_name;

            if(!empty($image_preview_url)){ ?>
                <div class="opening-gallery">
					<a class="windowThumbnail" href="<?=$artist_artwork_link?>">
                    	<img src="<?=$image_preview_url?>" />
					</a>
                </div>
            <?php }
        } ?>
    </div>
    <?php
endwhile; // End of the loop.

get_footer();
