<?php
/**
 * The template for displaying all single artwork
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();

	/* Start the Loop */
	while ( have_posts() ):
		the_post();
		global $post;
		//get_template_part( 'template-parts/content/content-single' );
		$_artist_opensea = '';
		$_artist_twitter = '';
		$_artist_instagram = '';

		$artist_id = get_post_meta($post->ID, '_artwork_artist', true);
		if(!empty($artist_id)){
			$artist = get_post( $artist_id );
			$_artist_opensea = get_post_meta( $artist->ID, '_artist_opensea', true );
			$_artist_twitter = get_post_meta( $artist->ID, '_artist_twitter_username', true );
			$_artist_instagram = get_post_meta( $artist->ID, '_artist_instagram_username', true );
		}
		?>
		
		<div id="post-<?php the_ID(); ?>" class="single-artworks-wrapper">		
			<div class="artwork-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div>
			<?php /*<div class="artworks-title">
				<?php the_title(); ?>
			</div> */ ?>
			<div class="artworks-description">
				<?php the_content(); ?>
			</div>

			<?php if($_artwork_price = get_post_meta($post->ID, '_artwork_price', true) ){?>
				<div class="artworks-price">
					<strong>Price:</strong> <?=$_artwork_price?>
				</div>
			<?php } ?>

			<div class="artworks-status">
				<strong>Gallery:</strong>  twelveartists.berlin
			</div>

			<?php if($_artowrk_status = get_post_meta($post->ID, '_artwork_status', true) ){?>
				<div class="artworks-status">
					<strong>Status:</strong> <?=$_artowrk_status?>
				</div>
			<?php } ?>

			<?php 
			$posttags = get_the_terms( $post->ID, 'tag' );
			if ($posttags) { ?>
				<div class="artwork-tags">
					<strong>Categories:</strong>				
					<?php foreach($posttags as $index=>$tag) {
							echo (count($posttags) == ($index+1) ) ? $tag->name : $tag->name . ', '; 
						}
					?>
				</div>
			<?php } ?>

			<?php if(!empty($_artist_opensea)){ ?>
				<div class="artwork-opensea-link">
					<strong>Opensea:</strong> <?=$_artist_opensea?>
				</div>
			<?php } ?>

			<div class="artwork-twitter-link">
				<strong>Twitter:</strong>
				<ul>
					<li>@friends.of.dodo</li>
					<li>@twelveartists</li>
					<?php if(!empty($_artist_twitter)){ ?>
						<li><?=(strpos($_artist_twitter, "@") > -1 ) ? $_artist_twitter : "@".$_artist_twitter ?></li>
					<?php } ?>
				</ul>
			</div>

			<div class="artwork-insta-link">
				<strong>Instagram:</strong>
				<ul>
					<li>@friends.of.dodo</li>
					<li>@twelveartists</li>
					<?php if(!empty($_artist_instagram)){ ?>
						<li><?=(strpos($_artist_instagram, "@") > -1 ) ? $_artist_instagram : "@".$_artist_instagram ?></li>
					<?php } ?>
				</ul>
			</div>
			
		</div>
		<?	
		
	endwhile;
	/* Loop Ends here */

get_footer();