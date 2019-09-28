<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 *
 * Template Name: コンテンツのみ(幅広)
 */

get_header(); ?>
	<div class="uk-container uk-container-large uk-background-default main-content">

		<?php
		while ( have_posts() ) :
			the_post();

			the_title( '<h1 class="main-title">', '</h1>' );

			the_content();

		endwhile; // End of the loop.
		?>


	</div><!-- .main-content -->


<?php
get_sidebar();
get_footer();
