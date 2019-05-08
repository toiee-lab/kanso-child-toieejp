<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kanso-general
 */

$post_id   = get_the_ID();
$scrum_chs = wp_get_post_terms( $post_id, 'scrum_channel' );

if ( count( $scrum_chs ) ) {
	$scrum_ch        = $scrum_chs[0];
	$scrum_ch_id     = $scrum_ch->term_id;
	$scrum_ch_link   = get_term_link( $scrum_ch );
	$scrum_ch_fields = get_fields( $scrum_ch );

	$args = array(
		'hide_empty' => false,
		'taxonomy'   => 'scrum',
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key'     => 'updates_news_podcast',
				'value'   => $scrum_ch_id,
				'compare' => '=',
			),
			array(
				'key'     => 'updates_archive_podcast',
				'value'   => $scrum_ch_id,
				'compare' => '=',
			),
		),
	);
	$scrums = get_terms( $args );

	if ( count( $scrums ) ) {
		$scrum         = $scrums[0];
		$scrum_fields  = get_fields( $scrum );
		$scrum_url     = get_term_link( $scrum );
		$header_color  = $scrum_fields['title_color'];
		$header_bg_img = $scrum_fields['scrum_headerbg']['url'];
		$scrum_url     = get_term_link( $scrum );
	} else {
		wp_die( '所属する scrum がありません。指定してください。' );
	}
} else {
	wp_die( '所属する scrum_channel がありません。指定してください。' );
}

$fields = get_fields();

global $wcr_content;
$has_access = true;
if ( true === $scrum_ch_fields['restrict'] ) {
	$has_access = $wcr_content->check_access( $scrum_ch_fields['restrict_product'] );
	if ( is_super_admin() ) {
		$has_access = true;
	}
}

get_header(); ?>
	<header>
		<div class="uk-section-default">
			<div class="uk-section <?php echo $header_color; ?> uk-background-cover" style="background-image: url(<?php echo $header_bg_img; ?>)">
				<div class="uk-container">
					<p>スクラム・ラーニング <?php echo $scrum->name; ?></p>
					<h1 class="uk-margin-remove-bottom uk-margin-remove-top uk-h2"><?php echo $scrum_ch->name; ?></h1>
				</div>
			</div>
		</div>
	</header>
	<div class="uk-container uk-container-small uk-background-default main-content">
		<div class="kns-breadcrumb">
			<ul itemscope itemtype="http://schema.org/BreadcrumbList">
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a href="<?php echo esc_url( site_url() ); ?>" itemprop="item"><span itemprop="name">home</span><meta itemprop="position" content="1"></a>
				</li>
				<li class="bc-divider">&gt;</li>
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a href="<?php echo esc_url( $scrum_url ); ?>" itemprop="item"><span itemprop="name">スクラム : <?php echo $scrum->name; ?></span><meta itemprop="position" content="2"></a>
				</li>
				<li class="bc-divider">&gt;</li>
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a href="<?php echo esc_url( $scrum_ch_link ); ?>" itemprop="item"><span itemprop="name">Podcast : <?php echo esc_html( $scrum_ch->name ); ?></span><meta itemprop="position" content="2"></a>
				</li>
			</ul>
		</div>
		<?php
		while ( have_posts() ) :
			the_post();

			the_title( '<h1 class="main-title">', '</h1>' );

			$src   = get_field( 'enclosure' );
			$media = get_field( 'media' );

			if ( $has_access ) {
				the_episode_player( $src, $media );
			} else {
				$restrict = get_field( 'restrict' );
				if ( $restrict === true ) {
					$restrict = 'restrict';
				} else if ( $restrict === false ) {
					$restrict = 'open';
				}

				switch ( $restrict ) {
					case 'open':
						the_episode_player( $src, $media );
						break;
					case 'free':
						if ( $user_logged_in ) {
							the_episode_player( $src, $media );
							break;
						}
					default: /* restrict */
						the_episode_player_dummy( $media );
						break;
				}
			}

			the_content();
			?>
			<hr class="uk-divider-small uk-text-center">
			<?php if ( 'serial' === $scrum_ch_fields['episode_type'] ) : ?>
			<ul class="uk-pagination">
				<li><a href="#"><?php previous_post_link( '%link', 'Previous', true, ' ', 'scrum_channel' ); ?></li>
				<li class="uk-margin-auto-left"><?php next_post_link( '%link', 'Next', true, ' ', 'scrum_channel' ); ?></li>
			</ul>
			<?php else : ?>
			<ul class="uk-pagination">
				<li class="uk-margin-auto-left"><?php next_post_link( '%link', 'Previous', true, ' ', 'scrum_channel' ); ?></li>
				<li><a href="#"><?php previous_post_link( '%link', 'Next', true, ' ', 'scrum_channel' ); ?></li>
			</ul>
			<?php endif; ?>
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
