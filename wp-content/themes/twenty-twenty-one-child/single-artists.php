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

/* Start the Loop */
while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/content/content-single' );
	$_artist_twitter_username = get_post_meta( $post->ID, '_artist_twitter_username', true );
    $_artist_instagram_username = get_post_meta( $post->ID, '_artist_instagram_username', true );
	
	$args = [
		'meta_query' => [
			[
				'key'   =>  '_event_artists',
				'value'   => '(^|,)'.$post->ID.'(,|$)',
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

	$onGoingShows = [];
	if(!empty($artistShows)){
		foreach($artistShows as $show){
			if($isOnGoing = artgallery_get_shows_openings($show, 'ongoing') ){
				$onGoingShows[] = $isOnGoing;
			}
		}
	}

	if(!empty($onGoingShows)){ ?>
		<div class="artist-show-container">
			<h2>Shows</h2>
			<div class="artist-shows-list">
				<?php foreach($onGoingShows as $show){ ?> 
					<div class="artist-shows-item">
						<div class="show-title">		  
							<a href="<?=get_term_link($show->term_id);?>"><?=$show->name?></a> 
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php }
	
	if(isset($openings) && !empty($openings)){
		$args = [
			'meta_query' => [
				'RELATION'	=>	'AND',
				[
					'key'   =>  '_artwork_opening',
					'value'   => $openings->ID,
					'compare'  =>  '='
				],
				[
					'key'   =>  '_artwork_artist',
					'value'   => $post->ID,
					'compare'  =>  '='
				]
			]
		];
		
		$artistArtworks = artgallery_get_posts('artworks', $args); ?>
		<div class="artist-artwork-wrapper">
			<?php if(!empty($artistArtworks)){ ?>
				<!---<h2>Artworks</h2>--->
				<div class="artist-artwork-gallery">				
					<?php foreach($artistArtworks as $index=>$artowrk){ ?>				
						<?php 
						$gallery_image_src = wp_get_attachment_url( get_post_thumbnail_id($artowrk->ID)); 
						$_artowrk_title = get_post_meta( $artowrk->ID, '_artwork_title', true )
						?>
						<a href="<?=get_permalink($artowrk->ID)?>" class="gallery-item"  title="<?=$_artowrk_title?>"><img src="<?=$gallery_image_src?>" class="artist-artwork-img" alt="<?=$_artowrk_title?>" />
						</a>
					<?php } ?>
				</div>
			<?php }else{ ?>
				<div class="alert alert-danger">No Artwork added for this Artist.</div>
			<?php } ?>
		</div>
	<?php }
endwhile; // End of the loop.

get_footer();
