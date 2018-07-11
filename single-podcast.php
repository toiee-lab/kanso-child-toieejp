<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */

get_header(); ?>

	<div class="uk-container uk-container-small uk-background-default main-content">

		<?php
		if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					//the_archive_title( '<h1 class="page-title">', '</h1>' );
					//the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();
				the_title( '<h1 class="entry-title">', '</h1>' );
			?>
				<h2 class="main-subtitle"><?php echo get_post_meta(get_the_ID(), 'kns_lead', true);?></h2>
	
				<div class="entry-meta uk-text-right uk-margin">
					<?php kanso_general_posted_on(); ?>
				</div><!-- .entry-meta -->
			
				<?php kanso_general_post_thumbnail(); ?>
				<?php the_content(); ?>
		   <?php
			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

	</div><!-- .main-content -->

<?php
get_sidebar();
get_footer();
