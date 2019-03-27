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
	<div class="uk-flex uk-flex-center">
		<!-- sidebar -->
		<div class="uk-visible@m">
			<!-- 960px以下で非表示 -->
			<div class="sidebar-wrapper">
				<!-- 210px + 650px(コンテンツ部分) = 860 -->
				<div class="sidebar uk-padding-small" uk-sticky="width-element: .sidebar-wrapper;bottom: #clear-stickey" style="overflow-y: scroll;max-height: 100vh">
					<aside　id="secondary">
						<nav>
							<?php dynamic_sidebar( 'sidebar-lib' ); ?>
						</nav>
					</aside>
				</div>
			</div>
		</div>
		<!-- content -->
		<div>
			<div class="uk-container uk-container-small uk-margin-remove-top uk-padding-small main-content main-content-sidebar">
				<main>
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'page' );

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>
				</main>
			</div>
		</div>
	</div>
<?php
get_sidebar( 'lib' );
get_footer();
