<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */


$magazine        = get_queried_object();
$magazine_id     = $magazine->term_id;
$magazine_url    = get_term_link( $magazine );
$magazine_fields = get_fields( $magazine );

acf_form_head();
get_header();
?>
<div class="header-bg">
	<header>
		<div class="uk-section-default">
			<div class="uk-section <?php echo $header_color; ?> uk-background-cover" style="background-image: url(<?php echo $header_bg_img; ?>)">
				<div class="uk-container">
					<p>スクラム・ラーニング</p>
					<h1 class="uk-margin-remove-bottom"><?php echo $magazine->name; ?></h1>
					<p class="uk-text-lead uk-margin-remove-top"><?php echo $magazine_fields['scrum_subtitle']; ?></p>
				</div>
			</div>
		</div>
	</header>

	<div class="uk-flex uk-flex-center">
		<!-- content -->
		<div>
			<div class="uk-container uk-container-small uk-margin-remove-top uk-padding-small main-content main-content-sidebar">
				<main>
					<?php
					var_dump( $magazine_fields );
					?>
					<?php
					while ( have_posts() ) :
						the_post();
						// get_template_part( 'template-parts/content', 'page' );
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>
				</main>
			</div>
		</div>
		<!-- sidebar -->
		<div class="uk-visible@m">
			<!-- 960px以下で非表示 -->
			<div class="sidebar-wrapper">
				<!-- 210px + 650px(コンテンツ部分) = 860 -->
				<div class="sidebar uk-padding-small" uk-sticky="width-element: .sidebar-wrapper;bottom: #clear-stickey" style="overflow-y: scroll;max-height: 100vh">
					<aside　id="secondary">
					<nav>
						<?php dynamic_sidebar( 'sidebar-1' ); ?>
					</nav>
					</aside>
				</div>
			</div>
		</div>
	</div>
	<div id="clear-stickey" class="uk-margin-medium-bottom"></div>
</div>
<?php


get_sidebar();
get_footer();
