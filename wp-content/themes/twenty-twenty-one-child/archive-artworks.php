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
$allPostsWPQuery = new WP_Query(array('post_type'=>'artworks', 'post_status'=>'publish', 'posts_per_page'=>-1));

?>

 <?php  ?>
<ul class="cat-list" style="display: flex; list-style: none; justify-content: space-evenly; align-items: center; background: #000; padding: 10px;  max-width: 900px; margin: 20px auto;">
  <li><a class="cat-list_item active" href="#!" data-slug="">All Artworks</a></li>
  <li>
	<label>Filter By Show</label>
	<select>
		<option>TwelveOne</option>
		<option>TwelveTwo</option>
		<option>TwelveThree</option>
		<option>TwelveFour</option>
		<option>TwelveFive</option>
	</select>
  </li>
  <li>  
	<label>Filter By Artist</label>
	<select>
		<option>TwelveOne</option>
		<option>TwelveTwo</option>
		<option>TwelveThree</option>
		<option>TwelveFour</option>
		<option>TwelveFive</option>
	</select>
  </li> 
</ul>


<?php if ( $allPostsWPQuery->have_posts() ) : ?>

	<header class="page-header alignwide">
		<h1 class="page-title"><?php echo str_replace("Archives: ", "", get_the_archive_title()); ?></h1>
		<?php if ( $description ) : ?>
			<div class="archive-description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
		<?php endif; ?>
	</header><!-- .page-header -->
	<div class="artworks-wrapper">
		<?php while ( $allPostsWPQuery->have_posts() ) : ?>
			<?php $allPostsWPQuery->the_post(); ?>
			
			<div class="artworks-item">
				<div class="artwork-thumbnail">
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
						<?php the_post_thumbnail(); ?>
					</a>
				</div>								
			</div>
			
		<?php endwhile; ?>
	</div>

<?php else : ?>
	<?php get_template_part( 'template-parts/content/content-none' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
