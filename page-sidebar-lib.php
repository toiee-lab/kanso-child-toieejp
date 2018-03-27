<?php
/**
 *	
 * Template Name: といリブ用(サイドバー付き)
 * 
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
 */

get_header(); ?>

		<div uk-grid class="uk-grid-match uk-flex-center">
			<!-- sidebar -->
			<div class="uk-width-auto">
				<div class="main-content-lib main-content-sidebar uk-container uk-container-auto">

					<?php
					while ( have_posts() ) : the_post();
		
						get_template_part( 'template-parts/content', 'page' );
		
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
		
					endwhile; // End of the loop.
					?>

				</div><!-- .main-content -->
			</div>
			<div id="content-sidebar" class="uk-visible@m uk-flex-first">
				<nav>
					<aside id="secondary" class="widget-area uk-padding-left">
						<?php dynamic_sidebar( 'sidebar-lib' ); ?>
					</aside><!-- #secondary -->
				</nav>
			</div><!-- .sidebar -->
		</div>	
<?php
get_sidebar('lib');
get_footer();
