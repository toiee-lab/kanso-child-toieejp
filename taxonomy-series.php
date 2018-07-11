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
		if ( have_posts() ) : ?>

			<header class="page-header uk-margin-medium-bottom">
				<div uk-grid>
				<?php
					global $ss_podcasting;
					global $wcr_content;
					global $wcr_ssp;
					
					$series       = get_queried_object();
					$series_id    = $series->term_id;
					$series_url   = get_term_link( $series );
					$series_image = get_option( 'ss_podcasting_data_image_' . $series_id, 'no-image' );
					
					// 制限ありのpodcastなのかフラグ
					$series_allow = true;
					$restrict_pass  = get_option( 'ss_podcasting_wc_restrict_ssp_' . $series_id, false );  // デフォルトは fals				
					if( $restrict_pass == 'restrict_enable' ) {
						$ret = $wcr_ssp->get_access_and_product_url('', '', $series_id);
						$restrict_pass = $ret['access'];
					}
					
					// - - - - - - 
										
					// podcast 登録案内ボタンの取得
					$pcast_info = $wcr_ssp->add_wcr_ssp_shortcode(array(
									'id'            => $series_id,
									'template_name' => 'on_archive',
									'redirect_url'  => $series_url
									));
				?>
					<div class="uk-width-medium@m">
						<img src="<?php echo $series_image; ?>">
					</div>
					<div class="uk-width-expand@m">
				<?php

					//the_archive_title( '<h1 class="uk-heading-primaryr">', '</h1>' );
					$series_title = get_option( 'ss_podcasting_data_title_' . $series_id, '' );
					echo '<h1 class="uk-h2">'.$series_title.'</h2>';

					the_archive_description( '<p>', '</p>' );
					echo $pcast_info;
					
				?>
					</div>
				</div>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				//get_template_part( 'template-parts/content', get_post_format() );
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
							$shortcode = '[audio src="'.$enclosure.'" /]';
						}
						else {
							$shortcode = '[video src="'.$audio_file.'" /]';
						}
						
						$episode_restrict = get_post_meta( get_the_ID(), 'wcr_ssp_episode_restrict', 'disable' );
						if( $restrict_pass || ($episode_restrict != 'enable' ) ){
							// 表示
						?>

					<div class="uk-margin-medium-top uk-margin-small-bottom">
		                <?php echo do_shortcode( $shortcode ); ?>
					</div>					
						
						<?php	
						}
						else{
							// 非表示にする
			                if ( $episode_type == 'audio' ) {
								echo $wcr_ssp->add_wcr_ssp_shortcode(array(
									'id'            => $series_id,
									'template_name' => 'on_episode_audio',
									'label_trial'   => '',
									'label_offer_trial' => '',
									'redirect_url'  => $series_url
									));
							}
							else {
								echo $wcr_ssp->add_wcr_ssp_shortcode(array(
									'id'            => $series_id,
									'template_name' => 'on_episode_video',
									'label_trial'   => '',
									'label_offer_trial' => '',									
									'redirect_url'  => $series_url
									));
							}
						}
						
					?>
					<div>
						<p><a href="#" uk-toggle="target: #desc<?php the_id(  );?>">説明を表示する</a></p>
						<div id="desc<?php the_id(  );?>" hidden>
					<?php
						the_content( );
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

			//the_posts_navigation();<ul class='page-numbers'>
			$pagenation = get_the_posts_pagination( array(
				'type'          => 'list',
				'prev_text'     => '<span uk-pagination-previous></span></a>',
				'next_text'     => '<span uk-pagination-next></span></a>',
				'mid_size'      => 3
			) );
			$pagenation = str_replace("<ul class='page-numbers'>", "<ul class='uk-pagination' uk-margin>", $pagenation);
			echo $pagenation;
			

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

	</div><!-- .main-content -->

<?php
get_sidebar();
get_footer();
