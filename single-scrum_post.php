<?php
/**
 * Created by PhpStorm.
 * User: takame
 * Date: 2019-02-28
 * Time: 17:44
 */

$post_id = get_the_ID();
$terms   = wp_get_post_terms( $post_id, 'scrum' );

if ( count( $terms ) ) {
	$scrum         = $terms[0];
	$scrum_fields  = get_fields( $scrum );
	$scrum_url     = get_term_link( $scrum );
	$header_color  = $scrum_fields['title_color'];
	$header_bg_img = $scrum_fields['scrum_headerbg']['url'];
} else {
	wp_die( '必ず、スクラムを指定してくださ（カテゴリ）。' );
}

get_header(); ?>
	<header>
		<div class="uk-section-default">
			<div class="uk-section <?php echo $header_color; ?> uk-background-cover" style="background-image: url(<?php echo $header_bg_img; ?>)">
				<div class="uk-container">
					<p>スクラム・ラーニング <?php echo $scrum->name; ?></p>
					<h1 class="uk-margin-remove-bottom uk-margin-remove-top uk-h2">お知らせブログ</h1>
				</div>
			</div>
		</div>
	</header>
	<div class="uk-container uk-container-small uk-background-default uk-margin-top uk-margin-bottom">
		<?php
		while ( have_posts() ) :
			the_post();
			echo '<p class="uk-text-small"><a href="' . $scrum_url . '">home</a> &gt; ' . get_the_title() . '</p>';
			the_title( '<h1 class="main-title">', '</h1>' );
			the_content();

			?>
			<hr class="uk-divider-small uk-text-center">
			<?php

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				?>
				<hr class="uk-margin-large">
				<?php
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</div><!-- .main-content -->
<?php
get_sidebar();
get_footer();
