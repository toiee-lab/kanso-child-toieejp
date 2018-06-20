<?php
/**
* Theme template for single page
*/
get_header(); ?>

		<div uk-grid class="uk-grid-match uk-flex-center">
			<!-- sidebar -->
			<div class="uk-width-auto">
				<div class="main-content-lib main-content-sidebar uk-container uk-container-auto">

					<?php the_title('<h1>', '</h1>'); ?>
		

					<?php hkb_get_template_part('hkb-compat', 'single'); ?>
					
					
				</div><!-- .main-content -->
			</div>
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
