<?php
/**
 * The template for displaying shows archives
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();

$show = get_queried_object(); 

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
$image_id = get_term_meta ( $show->term_id, 'image_id', true );
?>

<?php if ( have_posts() ) : ?>

	<header class="page-header alignwide">
	    <div class="show-container artists-outer ">
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
	</header><!-- .page-header -->
	<?php foreach ($showOpenings as $index=>$opening ){ 
        $artists = array_merge($artists, explode(",",get_post_meta($opening->ID, "_event_artists", true) ) );
        ?>
		
		<p class="mg-0"><a href="<?=get_permalink($opening->ID)?>"><?php echo $opening->post_title ?></a></p>
	<?php } 
    
    if(!empty($artists)){ ?>
        <div class="show-artists-container">
            <h2>Participating Artists</h2>
            <?php
            $artists = array_filter(array_unique($artists));
            foreach($artists as $artistid){
                $artist = get_post($artistid);
                ?>
                <div class="artist-item text-center">
                    <p><a href="<?=get_permalink($artist->ID);?>"><?=$artist->post_title?></a></p>
                </div>
            <?php } ?>
        </div>
    <?php }
    ?>

<?php else : ?>
	<?php get_template_part( 'template-parts/content/content-none' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
