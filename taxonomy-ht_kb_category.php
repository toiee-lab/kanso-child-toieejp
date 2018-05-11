<?php
/**
* Theme template for ht_kb_category
*/
get_header(); ?>

		<div uk-grid class="uk-grid-match uk-flex-center">
			<!-- sidebar -->
			<div class="uk-width-auto">
				<div class="main-content-kb-cat main-content-sidebar uk-container uk-container-auto">

					<h1 class="page-title"><?php echo single_cat_title( '', false ); ?> ナレッジ</h1>
					<?php the_archive_description( '<div class="archive-description">', '</div>' );	?>	
					<?php hkb_get_template_part('hkb-compat', 'taxonomy'); ?>
					
				</div><!-- .main-content -->
			</div>
			<div id="content-sidebar" class="uk-visible@m uk-flex-first">
				<nav>
					<aside id="secondary" class="widget-area uk-padding-left">
						<?php dynamic_sidebar( 'sidebar-kb-cat' ); ?>
					</aside><!-- #secondary -->
				</nav>
			</div><!-- .sidebar -->
		</div>	
<?php
get_sidebar('kb-cat');
get_footer();