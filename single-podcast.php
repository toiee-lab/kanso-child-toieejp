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

		<?php
		if ( have_posts() ) :
			?>

			<header class="page-header">
				<?php
					// the_archive_title( '<h1 class="page-title">', '</h1>' );
					// the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				// 原則 1つのseriesに登録することを想定している
				$rets         = wp_get_post_terms( get_the_ID(), 'series' );
				$series       = $rets[0];
				$series_title = $series->name;
				$series_url   = get_term_link( $series );

				// 制限するための準備
				global $ss_podcasting;
				global $wcr_content;
				global $wcr_ssp;

				$series_id       = $series->term_id;
				$wcr_content_ssp = get_field( 'series_limit', $series );

				if ( $wcr_content_ssp ) {
					$ret           = $wcr_ssp->get_access_and_product_url( '', '', $series_id );
					$restrict_pass = $ret['access'];
				}



				?>
				<p class="uk-text-small" style=""><a href="<?php echo $series_url; ?>"><?php echo $series_title; ?></a> &gt; here </p>
				<?php
				the_title( '<h1 class="entry-title">', '</h1>' );
				?>
				<h2 class="main-subtitle"><?php echo get_post_meta( get_the_ID(), 'kns_lead', true ); ?></h2>
	
				<div class="entry-meta uk-text-right uk-margin">
					<?php kanso_general_posted_on(); ?>
				</div><!-- .entry-meta -->
			
				<?php

				// get audio file
				$audio_file = $ss_podcasting->get_enclosure( get_the_ID() );
				if ( get_option( 'permalink_structure' ) ) {
					$enclosure = $ss_podcasting->get_episode_download_link( get_the_ID() );
				} else {
					$enclosure = $audio_file;
				}
				$enclosure = apply_filters( 'ssp_feed_item_enclosure', $enclosure, get_the_ID() );

				// get type (audio or video)
				$episode_type = $ss_podcasting->get_episode_type( get_the_ID() );
				if ( $episode_type == 'audio' ) {
					$html_player = do_shortcode( '[audio src="' . $enclosure . '" /]' );
				} else {
					if ( preg_match( '|https://player.vimeo.com/external/([0-9]+)|', $audio_file, $matches ) ) {
						$vid = $matches[1];
						// $html_player = '<iframe src="https://player.vimeo.com/video/'.$vid.'" width="640" height="480" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
						$html_player = '<div style="padding:56.25% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/' . $vid . '?title=0&byline=0&portrait=0" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>';
					} else {
						$html_player = do_shortcode( '[video src="' . $audio_file . '" /]' );
					}
				}

				$episode_restrict = get_post_meta( get_the_ID(), 'wcr_ssp_episode_restrict', 'disable' );
				if ( $restrict_pass || ( $episode_restrict != 'enable' ) ) {
					// 表示
					echo <<<EOD
                    <div class="uk-margin-medium-top uk-margin-small-bottom">
		                {$html_player}
					</div>
EOD;
				} else {
					// 非表示にする
					if ( $episode_type == 'audio' ) {
						echo $wcr_ssp->add_wcr_ssp_shortcode(
							array(
								'id'                => $series_id,
								'template_name'     => 'on_episode_audio',
								'label_trial'       => '',
								'label_offer_trial' => '',
								'redirect_url'      => $series_url,
							)
						);
					} else {
						echo $wcr_ssp->add_wcr_ssp_shortcode(
							array(
								'id'                => $series_id,
								'template_name'     => 'on_episode_video',
								'label_trial'       => '',
								'label_offer_trial' => '',
								'redirect_url'      => $series_url,
							)
						);
					}
				}
				?>

				<?php the_content(); ?>
				<?php
			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</div><!-- .main-content -->

<?php
get_sidebar();
get_footer();
