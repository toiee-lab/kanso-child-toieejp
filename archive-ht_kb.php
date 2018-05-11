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

			<header class="page-header">
				<h1 class="page-title">ナレッジベース</h1>
				<?php
					the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php hkb_get_template_part('hkb-compat', 'archive'); ?>

	</div><!-- .main-content -->

<?php
get_sidebar();
get_footer();
