<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */

get_header(); ?>

	<div class="uk-container uk-container-middle uk-background-default uk-margin-medium-top uk-margin-medium-bottom">
		<?php
		if ( have_posts() ) :
			$a_title = get_the_archive_title();
			$a_title = explode( ':', $a_title );
			?>
			<header class="page-header">
				<?php
				echo '<h1 class="page-title">' . esc_html( trim( $a_title[1] ) ) . '</h1>';
				the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .page-header -->
			<div class="uk-child-width-1-3@m uk-child-width-1-2@s" uk-grid uk-height-match="target: > div > .uk-card">
				<?php
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				?>
			</div>
			<?php
			echo kanso_get_post_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</div><!-- .main-content -->

<?php
get_sidebar();
get_footer();
