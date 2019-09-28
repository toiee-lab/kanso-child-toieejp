<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kanso-general
 */

$p_id  = get_the_ID();
$terms = wp_get_post_terms( $p_id, 'pkt_channel' );

if ( count( $terms ) ) {
	$mdy          = get_fields( $terms[0] );
	$mdy['url']   = get_term_link( $terms[0] );
	$mdy['title'] = $terms[0]->name;
}

$fields = get_fields();

global $wcr_content;
$has_access = true;
if ( true === $mdy['restrict'] ) {
	$has_access = $wcr_content->check_access( $mdy['restrict_product'] );
	if ( is_super_admin() ) {
		$has_access = true;
	}
}

get_header(); ?>
	<div class="uk-container uk-container-small uk-background-default main-content">
		<div class="kns-breadcrumb">
			<ul itemscope itemtype="http://schema.org/BreadcrumbList">
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a href="<?php echo esc_url( site_url() ); ?>" itemprop="item"><span itemprop="name">home</span><meta itemprop="position" content="1"></a>
				</li>
				<li class="bc-divider">&gt;</li>
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a href="<?php echo esc_url( $mdy['url'] ); ?>" itemprop="item"><span itemprop="name">ポケてら : <?php echo esc_html( $mdy['title'] ); ?></span><meta itemprop="position" content="2"></a>
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
				the_episode_player_plyr( $src, $media );
			} else {
				$restrict = get_field( 'restrict' );
				if ( $restrict === true ) {
					$restrict = 'restrict';
				} elseif ( $restrict === false ) {
					$restrict = 'open';
				}

				switch ( $restrict ) {
					case 'open':
						the_episode_player_plyr( $src, $media );
						break;
					case 'free':
						if ( $user_logged_in ) {
							the_episode_player_plyr( $src, $media );
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
			<?php if ( 'serial' === $mdy['episode_type'] ) : ?>
			<ul class="uk-pagination">
				<li><a href="#"><?php previous_post_link( '%link', 'Previous', true, ' ', 'mmdmy' ); ?></li>
				<li class="uk-margin-auto-left"><?php next_post_link( '%link', 'Next', true, ' ', 'mmdmy' ); ?></li>
			</ul>
			<?php else : ?>
			<ul class="uk-pagination">
				<li class="uk-margin-auto-left"><?php next_post_link( '%link', 'Previous', true, ' ', 'mmdmy' ); ?></li>
				<li><a href="#"><?php previous_post_link( '%link', 'Next', true, ' ', 'mmdmy' ); ?></li>
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
