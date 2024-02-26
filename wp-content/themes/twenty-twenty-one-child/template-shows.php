<?php
/**
 * Template Name: Shows Listing
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();

/* Start the Loop */
while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/content-page' );

	// If comments are open or there is at least one comment, load up the comment template.
	$shows = get_terms( array(
        'taxonomy' => 'shows',
        'hide_empty' => true,
    ) );

    // pr($shows);
    $onGoingShows = $upcomingShows = [];
    if(!empty($shows)){
        foreach($shows as $show){ 
            if($isOnGoing = artgallery_get_shows_openings($show, 'ongoing') ){
                $onGoingShows[] = $isOnGoing;
            }else if($isUpcoming = artgallery_get_shows_openings($show, 'upcoming') ){
                $upcomingShows[] = $isUpcoming;
            }
        }
    }
	
    // pr($onGoingShows, true);

    if(!empty($onGoingShows)){ ?>
        <h2>On going Shows</h2>
    <?php }

     foreach($onGoingShows as $show){ 
	    $image_id = get_term_meta ( $show->term_id, 'image_id', true );	
	 ?>
	 
        <div class="show-container">
            <div class="col-md-3">
				<?php if(empty($image_id)) {?>
           <div class="show-title">		  
		   <a href="<?=get_term_link($show->term_id)?>"><?=artgallery_trim_show_title($show->name)?></a> 
		   </div>
		   <?php } else { ?>
		   <a href="<?=get_term_link($show->term_id)?>" class="show_thumbnail">
			<?php echo wp_get_attachment_image ( $image_id, 'medium' ); ?>
			</a>
			<?php } ?>
              
            </div>
            <div class="col-md-9">
                <?php foreach($show->showOpenings as $opening){
                    $artistsCount = count(explode(",",get_post_meta($opening->ID, '_event_artists', true)) );
                    ?>
                    <div class="opening-item"><?=$opening->post_title?></div>
                    <p><?=$artistsCount?> Artists</p>
                <?php } ?>
            </div>
        </div>
     <?php }


    if(!empty($upcomingShows)){ ?>
        <h2>Upcoming Shows</h2>
    <?php }

    foreach($upcomingShows as $show){
	$image_id = get_term_meta ( $show->term_id, 'image_id', true );
	?>
        <div class="show-container">
			
			<div class="col-md-3">
				<?php if(empty($image_id)) {?>
           <div class="show-title">		  
		   <a href="javascript:;"><?=artgallery_trim_show_title($show->name)?></a> 
		   </div>
		   <?php } else { ?>
		   <a href="javascript:;" class="show_thumbnail">
			<?= wp_get_attachment_image ( $image_id, 'medium' ); ?>
			</a>
			<?php } ?>
              
            </div>
			
            <div class="col-md-9">
                <?php foreach($show->showOpenings as $opening){
                    $artistsCount = count(explode(",",get_post_meta($opening->ID, '_event_artists', true)) );
                    ?>
                    <div class="opening-item"><?=$opening->post_title?></div>
                    <p><?=$artistsCount?> Artists</p>
                <?php } ?>
            </div>
        </div>
    <?php }
endwhile; // End of the loop.

get_footer();
