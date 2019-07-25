<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */


$kdy_obj            = get_queried_object();
$kdy                = get_fields( $kdy_obj );
$kdy['id']          = $kdy_obj->term_id;
$kdy['url']         = get_term_link( $kdy_obj );
$kdy['title']       = $kdy_obj->name;
$kdy['description'] = $kdy_obj->description;

$user_logged_in = is_user_logged_in();

$can_edit = false;
if ( current_user_can( 'edit_posts' ) ) {
	acf_form_head();
	wp_deregister_style( 'wp-admin' );
	$can_edit = true;
}

/* 閲覧制限を取得 */
global $wcr_content;
$has_access = true;
if ( true === $kdy['restrict'] ) {
	$has_access = $wcr_content->check_access( $kdy['restrict_product'] );
	if ( is_super_admin() ) {
		$has_access = true;
	}
}
get_header();

?>
	<header class="kdy-header">
		<div class="uk-section">
			<div class="uk-container">
				<p class="uk-margin-remove-top uk-margin-remove-bottom kdy-tagline uk-h3">かめデミー</p>
				<p class="uk-margin-remove-top">学ぶこと自体を楽しみ、もっと先へ</p>
			</div>
		</div>
	</header>
	<div class="kdy-overlap">
		<div class="kdy-content uk-container uk-background-default">
			<div class="uk-margin-top" uk-grid>
				<div class="uk-width-auto"><img src="<?php echo esc_attr( $kdy['image'] ); ?>" width="150" height="150"></div>
				<div class="uk-width-expand">
					<h1 class="uk-h2 uk-margin-remove-bottom"><?php echo esc_html( $kdy['title'] ); ?></h1>
					<p class="uk-text-muted uk-margin-remove-top"><?php echo esc_html( $kdy['subtitle'] ); ?></p>
					<div class="uk-margin">
						<p class="uk-text-small uk-text-muted kdy-description"><?php echo esc_html( $kdy['description'] ); ?></span></p>
					</div>

				</div>
			</div>


					<?php if ( ! $user_logged_in ) : ?>
					<div class="uk-alert-primary" uk-alert>
						スクラム教材・インプットは「会員登録」することで、<b>すべて無料</b>でご覧いただけます。<br>
						<a href="#" onclick="UIkit.modal('#modal_login_form').show();UIkit.tab('#modal_login_form_tab').show(1);">会員登録する</a>
					</div>
					<?php endif; ?>
					<?php

					if ( have_posts() ) {

						?>
					<div class="uk-alert-success" uk-alert>
						<p><a href="#" uk-toggle="target: <?php echo $user_logged_in ? '#modal_offline' : '#modal_login_form' ?>"><span uk-icon="icon: play-circle"></span> オフライン、モバイルで視聴する</a></p>
					</div>
					<div id="modal_offline" class="uk-flex-top" uk-modal>
						<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
							<?php
							if ( $user_logged_in ) {
								if ( $has_access ) {
									$pcast_url = $kdy['url'] . 'feed/pcast/?wcrtoken=' . $wcr_content->get_user_wcrtoken();

									$url        = str_replace( array( 'https://', 'http://' ), 'pcast://', $pcast_url );
									$href_pcast = 'href="' . $url . '"';

									$url           = str_replace( array( 'https://', 'http://' ), 'podcast://', $pcast_url );
									$href_podcast  = 'href="' . $url . '"';

									$href_feed     = 'href="' . $pcast_url . '"';

									if ( $kdy['audiobook'] != '' ) {
										$href_download = 'href="' . $kdy['audiobook'] . '" download="' . $kdy['title'] . '.m4b"';
									} else {
										$href_download = 'href="#" uk-toggle="target: #modal_not_audiobook"';
									}
									?>
									<h3 class="uk-h4"><span uk-icon="icon: play-circle"></span> オフライン、モバイルで視聴する</h3>
									<dl class="uk-description-list">
										<dt>Podcast形式</dt>
										<dd>以下のボタンをクリックし、即視聴できます。iPhone、Apple WatchのPodcastアプリ、AndroidのPodcastアプリ、MacのMusic(iTuens)、WindowsのiTunesなどで視聴可能です。<br>
											<p uk-margin>
												<a <?php echo $href_podcast;?> class="uk-button uk-button-default">iPhone、iPad、Apple Watch</a>
												<a <?php echo $href_pcast;?> class="uk-button uk-button-default">iTunes、Android</a>
												<a <?php echo $href_feed;?> class="uk-button uk-button-text">フィードURL</a>
											</p>
										</dd>
										<dt>オーディオブック形式（m4b）</dt>
										<dd>ダウンロードして視聴できます。iPhoneなどのApple Book、Book Player、Androidのオーディオブックアプリなどを利用できます。<br>
											<p uk-margin><a <?php echo $href_download; ?> class="uk-button uk-button-default">ダウンロード</a></p>
										</dd>
									</dl>

									<?php
								} else {
									?>
									<h2>ご利用いただけません</h2>
									<p>Podcastあるいは、ダウンロードを利用するには、「スクラム」に参加するか、「スクラム教材定期購読の申し込み」が必要です。</p>
									<p><a href="">詳しくはこちら</a></p>
								<?php
								}
							} else {
								?>
								<h2>ログインしてください</h2>
								<?php
							}
							?>

						</div>
					</div>
						<div id="modal_not_audiobook" class="uk-flex-top" uk-modal>
							<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
								<h2>オーディオブックがありません</h2>
								<p>この教材にはオーディオブックがありません。順次追加中です。しばらくお待ちください。</p>
							</div>
						</div>
						<?php

						$elements = array();
						while ( have_posts() ) {
							the_post();
							$elements[] = get_post();
						}

						if( 'serial' === $kdy['episode_type'] ) {
							usort(
								$elements,
								function ( $a, $b ) {
									if ( $a->post_date === $b->post_date ) {
										return 0;
									} elseif ( $a->post_date < $b->post_date ) {
										return - 1;
									} else {
										return 1;
									}
								}
							);
						}

						$the_episode_player_plyr_ext = 'tlm_input';

						global $post;
						foreach ( $elements as $post ) {
							setup_postdata( $post );
							require locate_template( 'template-parts/player.php' );
						}
						wp_reset_postdata();
					} else {
						?>
						<p>現在、教材がありません。</p>
						<?php
					}
					?>

		</div>
	</div>
<?php

get_sidebar();
get_footer();
