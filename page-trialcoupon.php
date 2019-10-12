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
 * Template Name: トライアルクーポン
 */

get_header(); ?>
	<div class="uk-container uk-container-small uk-background-default main-content" style="min-height: 500px;">

		<?php
		while ( have_posts() ) :
			the_post();

			the_title( '<h1 class="main-title">', '</h1>' );
			the_subtitle( '<h2 class="main-subtitle">', '</h2>' );

			the_content();
			?>
			<p>
				<input id="coupon_id" class="uk-input uk-width-1-2" type="text" placeholder="クーポンID"> <button id="btn_send" class="uk-button uk-button-default">送信</button>
			</p>
			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	<script>
		document.getElementById('btn_send').addEventListener(
			'click',
			function() {
				let coupon = document.getElementById('coupon_id').value;
				let url    = location.protocol + '//' + location.hostname + location.pathname + coupon;
				location.href = url;
			},
			false
		);
	</script>
	</div><!-- .main-content -->


<?php
get_sidebar();
get_footer();
