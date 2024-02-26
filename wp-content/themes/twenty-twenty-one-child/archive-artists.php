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

$description = get_the_archive_description();
$allPostsWPQuery = new WP_Query(array('post_type'=>'artists', 'post_status'=>'publish', 'posts_per_page'=>-1));
?>
 

<?php if ( $allPostsWPQuery->have_posts() ) : ?>

	<header class="page-header alignwide">
		<h1 class="page-title"><?php echo str_replace("Archives: ", "", get_the_archive_title()); ?></h1>
		<?php if ( $description ) : ?>
			<div class="archive-description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
		<?php endif; ?>
	</header><!-- .page-header -->
	<div class="artists-wrapper">
		<?php while ( $allPostsWPQuery->have_posts() ) : ?>
			<?php $allPostsWPQuery->the_post(); ?>
			
			<div class="artists-item">
				<div class="post-title">
					<h4><?php the_title(); ?></h4>
					<a class="artist_link" href="<?php the_permalink(); ?>">
						<span>more</span> 
						<svg data-bbox="20 86 160 28" viewBox="0 0 200 200" height="200" width="200" xmlns="http://www.w3.org/2000/svg" data-type="shape">
							<g><path d="M164.965 114L180 100l-15.035-14-2.8 2.941 9.275 8.636H20v4.846h151.44l-9.275 8.636 2.8 2.941z"></path></g>
						</svg>
					</a>
				</div>
			</div>
			
		<?php endwhile; ?>
	</div>

<?php else : ?>
	<?php get_template_part( 'template-parts/content/content-none' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
