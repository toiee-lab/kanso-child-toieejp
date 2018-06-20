<?php
/**
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
			<div class="uk-width-auto">
				<div class="main-content-kb main-content-sidebar uk-container uk-container-auto">

					<?php the_title('<h1>', '</h1>'); ?>
		

					<?php hkb_get_template_part('hkb-compat', 'single'); ?>
					
					
				</div><!-- .main-content -->
			</div>
			<!-- sidebar -->
			<div id="content-sidebar" class="uk-visible@m uk-flex-first">
				<nav>
					<aside id="secondary" class="widget-area uk-padding-left">
						<?php dynamic_sidebar( 'sidebar-kb' ); ?>
					</aside><!-- #secondary -->
				</nav>
			</div><!-- .sidebar -->
		</div>	
<?php
get_sidebar('kb');
get_footer();
