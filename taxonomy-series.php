<?php
/**
 * Podcast の Series の表示（一覧）
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */
get_header(); ?>

	<div class="uk-container uk-container-small uk-background-default" style="margin-top:3rem; margin-bottom: 3rem;">

		<?php
		if ( have_posts() ) :

			global $ss_podcasting;
			global $wcr_content;
			global $wcr_ssp;

			$series          = get_queried_object();
			$series_id       = $series->term_id;
			$series_url      = get_term_link( $series );
			$series_image    = get_option( 'ss_podcasting_data_image_' . $series_id, 'no-image' );
			$series_material = get_field( 'series_material', $series );

			if ( get_term_meta( $series_id, 'pcast_moving', true) ) {
				$moving_to_web = get_term_meta( $series_id, 'pcast_moving_to_web', true);

				if ( ! is_super_admin() ) {
					?>
					<script>
						location.href = '<?php echo esc_url( $moving_to_web ); ?>';
					</script>
					<?php
				}
				?>
			<div uk-alert class="uk-alert uk-alert-warning">
				<h3>重要なお知らせ</h3>
				<p>このPodcastチャンネルは移動しました。 <a href="<?php echo esc_url($moving_to_web); ?>"><?php echo esc_url($moving_to_web); ?></a></p>
			</div>
				<?php
			}
			?>

			<header class="page-header uk-margin-medium-bottom">
				<div uk-grid>
				<?php

					// 制限ありのpodcastなのかフラグ
					$wcr_content_ssp = get_field( 'series_limit', $series );

				if ( $wcr_content_ssp ) {
					$ret           = $wcr_ssp->get_access_and_product_url( '', '', $series_id );
					$restrict_pass = $ret['access'];
				}

					// - - - - - -
					// podcast 登録案内ボタンの取得
					$pcast_info = $wcr_ssp->add_wcr_ssp_shortcode(
						array(
							'id'            => $series_id,
							'template_name' => 'on_archive',
							'redirect_url'  => $series_url,
						)
					);
				?>
					<div class="uk-width-medium@m">
						<img src="<?php echo $series_image; ?>">
					</div>
					<div class="uk-width-expand@m series-header">
				<?php

					the_archive_title( '<h1 class="uk-heading-primaryr">', '</h1>' );
					the_archive_description( '<p>', '</p>' );
					echo $pcast_info;
					echo $series_material;

				?>
					</div>
				</div>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				// get_template_part( 'template-parts/content', get_post_format() );
				?>
			<article id="post-<?php the_ID(); ?>">
				<?php
				if ( is_singular() ) :
					the_title( '<h1 class="">', '</h1>' );
					else :
						the_title( '<h2 class="uk-h3">', '</h2>' );
					endif;
					?>
				<div class="">

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
					<div>
						<div id="desc<?php the_id(); ?>" >
					<?php
						the_content();
					?>
						</div>
						<div class="entry-meta uk-text-right uk-margin">
							<?php kanso_general_posted_on(); ?>
						</div><!-- .entry-meta -->					

					</div>
				</div><!-- .entry-content -->
			
				<footer class="entry-footer">
					<?php kanso_general_entry_footer(); ?>
				</footer><!-- .entry-footer -->
			</article><!-- #post-<?php the_ID(); ?> -->
			<hr class="uk-margin-medium-top uk-margin-medium-bottom">

				<?php
			endwhile;

			// the_posts_navigation();<ul class='page-numbers'>
			$pagenation = get_the_posts_pagination(
				array(
					'type'      => 'list',
					'prev_text' => '<span uk-pagination-previous></span></a>',
					'next_text' => '<span uk-pagination-next></span></a>',
					'mid_size'  => 3,
				)
			);
			$pagenation = str_replace( "<ul class='page-numbers'>", "<ul class='uk-pagination' uk-margin>", $pagenation );
			echo $pagenation;


		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</div><!-- .main-content -->

<?php
get_sidebar();
get_footer();
