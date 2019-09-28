<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */


$tlm_obj            = get_queried_object();
$tlm                = get_fields( $tlm_obj );
$tlm['id']          = $tlm_obj->term_id;
$tlm['url']         = get_term_link( $tlm_obj );
$tlm['title']       = $tlm_obj->name;
$tlm['description'] = $tlm_obj->description;

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
if ( true === $tlm['restrict'] ) {
	$has_access = $wcr_content->check_access( $tlm['restrict_product'] );
	if ( is_super_admin() ) {
		$has_access = true;
	}
}

$elements = array();
while ( have_posts() ) {
	the_post();
	$p = get_post();

	$ptype = $p->post_type;
	if ( ! isset( $elements[ $ptype ] ) ) {
		$elements[ $ptype ] = array();
	}

	$elements[ $ptype ][] = $p;
}

$new_feed = get_field( 'new_feed', $tlm_obj );

get_header();

?>
	<header class="tlm-header">
		<div class="uk-section">
			<div class="uk-container">
				<?php if ( $new_feed ) : ?>
					<div class="uk-alert uk-alert-warning" uk-alert>
						<p>この教材は、新しい形式に移動しました。移動先は<a href="<?php echo $new_feed; ?>">こちら<br>
								<?php echo $new_feed; ?></a></p>
					</div>
				<?php endif; ?>
				<p class="uk-margin-remove-top uk-margin-remove-bottom tlm-tagline uk-h3">toiee教材</p>
				<p class="uk-margin-remove-top">Love to learn together</p>
			</div>
		</div>
	</header>
	<div class="tlm-overlap">
		<div class="tlm-content uk-container uk-background-default">
			<div class="uk-margin-top" uk-grid>
				<div class="uk-width-auto"><img src="<?php echo esc_attr( $tlm['image'] ); ?>" width="150" height="150"></div>
				<div class="uk-width-expand">
					<h1 class="uk-h2 uk-margin-remove-bottom"><?php echo esc_html( $tlm['title'] ); ?></h1>
					<p class="uk-text-muted uk-margin-remove-top"><?php echo esc_html( $tlm['subtitle'] ); ?></p>
					<div class="uk-margin">
						<p class="uk-text-small uk-text-muted tlm-description"><?php echo esc_html( $tlm['description'] ); ?></span></p>
					</div>

				</div>
			</div>
			<ul class="uk-child-width-expand" uk-tab id="main-tab">
				<li><a href="#" onclick="location.hash='tlm_ws'"><span class="uk-visible@s">ワークショップ</span><span class="uk-hidden@s">WS</span></a></li>
				<li><a href="#" onclick="location.hash='tlm_archive'"><span class="uk-visible@s">ワークショップ録画</span><span class="uk-hidden@s">WS録画</span></a></li>
				<li><a href="#" onclick="location.hash='tlm_add'"><span class="uk-visible@s">関連ナレッジ</span><span class="uk-hidden@s">関連</span></a></li>
			</ul>
			<ul class="uk-switcher uk-margin uk-margin-bottom">
				<li><!-- ================= ワークショップ =================== -->
					<?php
					$has_input = isset( $elements['tlm_in'] );
					?>
					<ul uk-tab id="tlm_ws_tab">
						<li class="uk-active"><a href="#" onclick="location.hash='tlm_ws'">ワーク</a></li>
						<li><a href="#" onclick="location.hash='tlm_ws_aid'">受講資料</a></li>
						<li><a href="#" onclick="location.hash='tlm_ws_lft'">LFT</a></li>
						<?php if ( $has_input ) : ?>
							<li><a href="#" onclick="location.hash='tlm_in'">インプット</a></li>
						<?php endif; ?>
					</ul>
					<ul class="uk-switcher uk-margin uk-margin-bottom">
						<!-- ================= ビデオ =========== -->
						<li>
							<?php
							if ( isset( $elements['tlm_ws'] ) ) {
								usort(
									$elements['tlm_ws'],
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

								$the_episode_player_plyr_ext = '';

								global $post;
								foreach ( $elements['tlm_ws'] as $post ) {
									setup_postdata( $post );
									require locate_template( 'template-parts/player.php' );
								}
								wp_reset_postdata();

							} else {
								?>
								<p>現在、ワークショップビデオがありません。</p>
								<?php
							}
							?>
						</li>
						<!-- ================= 資料 =========== -->
						<li>
							<?php

							if ( isset( $elements['tlm_ws_aid'] ) ) {
								/* 最初のものだけ表示 */
								$p = array_pop( $elements['tlm_ws_aid'] );

								if ( $can_edit ) {
									toiee_get_edit_button( $p );
								}

								echo apply_filters( 'the_content', $p->post_content );
							} else {
								$setting = array(
									'post_id'            => 'new_post',
									'post_title'         => true,
									'new_post'           => array(
										'post_type'   => 'tlm_ws_aid',
										'post_status' => 'draft',
										'tax_input'   => array( 'tlm' => $tlm['id'] ),
									),
									'fields'             => array( 'hoge' ),
									'submit_value'       => '授業資料を下書き保存',
									'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
									'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
									'html_after_fields'  => '<input type="hidden" name="acf[pocketera]" value="' . $tlm['id'] . '"/>',
								);

								acf_form( $setting );
							}
							?>
						</li>
						<li><!-- ================= LFT =========== -->
							<?php
							if ( isset( $elements['tlm_ws_lft'] ) ) {
								/* 最初のものだけ表示 */
								$p = array_pop( $elements['tlm_ws_lft'] );

								if ( $can_edit ) {
									toiee_get_edit_button( $p );
								}

								echo apply_filters( 'the_content', $p->post_content );

							} else {
								$setting = array(
									'post_id'            => 'new_post',
									'post_title'         => true,
									'new_post'           => array(
										'post_type'   => 'tlm_ws_lft',
										'post_status' => 'draft',
										'tax_input'   => array( 'tlm' => $tlm['id'] ),
									),
									'fields'             => array( 'hoge' ),
									'submit_value'       => 'LFTレジュメを下書き保存',
									'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
									'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
								);

								acf_form( $setting );
							}
							?>
						</li>
						<!-- ================= インプット =================== -->
						<?php if ( $has_input ) : ?>
							<li>
								<div class="uk-alert-success" uk-alert>
									<p><a href="#" uk-toggle="target: <?php echo $user_logged_in ? '#modal_offline' : '#modal_login_form'; ?>"><span uk-icon="icon: play-circle"></span> オフライン、モバイルで視聴する</a></p>
								</div>
								<div id="modal_offline" class="uk-flex-top" uk-modal>
									<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
										<?php
										if ( $user_logged_in ) {
											if ( $has_access ) {
												$pcast_url = $tlm['url'] . 'feed/pcast/?wcrtoken=' . $wcr_content->get_user_wcrtoken();

												$url        = str_replace( array( 'https://', 'http://' ), 'pcast://', $pcast_url );
												$href_pcast = 'href="' . $url . '"';

												$url          = str_replace( array( 'https://', 'http://' ), 'podcast://', $pcast_url );
												$href_podcast = 'href="' . $url . '"';

												$href_feed = 'href="' . $pcast_url . '"';

												?>
												<h3 class="uk-h4"><span uk-icon="icon: play-circle"></span> オフライン、モバイルで視聴する</h3>
												<dl class="uk-description-list">
													<dt>Podcast形式</dt>
													<dd>以下のボタンをクリックし、即視聴できます。iPhone、Apple WatchのPodcastアプリ、AndroidのPodcastアプリ、MacのMusic(iTuens)、WindowsのiTunesなどで視聴可能です。<br>
														<p uk-margin>
															<a <?php echo $href_podcast; ?> class="uk-button uk-button-default">iPhone、iPad、Apple Watch</a>
															<a <?php echo $href_pcast; ?> class="uk-button uk-button-default">iTunes、Android</a>
															<a <?php echo $href_feed; ?> class="uk-button uk-button-text">フィードURL</a>
														</p>
													</dd>
													<?php
													if ( $tlm['audiobook'] != '' ) {
														$href_download = 'href="' . $tlm['audiobook'] . '" download="' . $tlm['title'] . '.m4b"';
														?>
													<dt>オーディオブック形式（m4b）</dt>
													<dd>ダウンロードして視聴できます。iPhoneなどのApple Book、Book
														Player、Androidのオーディオブックアプリなどを利用できます。<br>
														<p uk-margin><a <?php echo $href_download; ?>
																	class="uk-button uk-button-default">ダウンロード</a></p>
													</dd>
														<?php
													}
													?>
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
								usort(
									$elements['tlm_in'],
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

								$the_episode_player_plyr_ext = 'tlm_input';

								global $post;
								foreach ( $elements['tlm_in'] as $post ) {
									setup_postdata( $post );
									require locate_template( 'template-parts/player.php' );
								}
								wp_reset_postdata();
								?>
							</li>
						<?php endif; ?>
					</ul>
				</li>
				<li><!-- ================= 録画 =========== -->
					<?php
					if ( $can_edit ) {
						$setting = array(
							'post_id'            => 'new_post',
							'post_title'         => true,
							'new_post'           => array(
								'post_type'   => 'tlm_archive',
								'post_status' => 'draft',
								'tax_input'   => array( 'tlm' => $tlm['id'] ),
							),
							'fields'             => array( 'hoge' ),
							'submit_value'       => 'エピソードを追加（下書き保存）',
							'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
							'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
						);
						?>
						<button class="uk-button uk-button-default uk-margin-small-right uk-align-right" type="button" uk-toggle="target: #modal-post">投稿する</button>
						<div id="modal-post" uk-modal>
							<div class="uk-modal-dialog uk-modal-body">
								<h2 class="uk-modal-title">アーカイブを追加</h2>
								<?php acf_form( $setting ); ?>
							</div>
						</div>
						<?php
					}

					if ( isset( $elements['tlm_archive'] ) ) {

						$the_episode_player_plyr_ext = 'tlm_archive';

						global $post;
						foreach ( $elements['tlm_archive'] as $post ) {
							setup_postdata( $post );
							require locate_template( 'template-parts/player.php' );
						}
						wp_reset_postdata();
					} else {
						?>
						<p>現在、ワークショップのアーカイブビデオがありません。</p>
						<?php
					}
					?>
				</li>
				<li><!-- ================= 関連ナレッジ =================== -->
					<?php
					if ( isset( $elements['tlm_add'] ) ) {
						$p = array_pop( $elements['tlm_add'] );

						echo apply_filters( 'the_content', $p->post_content );

						if ( $can_edit ) {
							toiee_get_edit_button( $p );
						}
					} else {
						$setting = array(
							'post_id'            => 'new_post',
							'post_title'         => true,
							'new_post'           => array(
								'post_type'   => 'tlm_add',
								'post_status' => 'draft',
								'tax_input'   => array( 'tlm' => $tlm['id'] ),
							),
							'fields'             => array( 'hoge' ),
							'submit_value'       => '関連ナレッジを下書き保存',
							'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
							'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
						);

						acf_form( $setting );
					}
					?>
				</li>
			</ul>
		</div>
	</div>
	<script>
		/*
		* - input ( tlm_in )
		*   - オーディオ (tlm_in)
		*   - ワークショップ録画 (tlm_archive)
		* - workshop ( tlm_ws )
		*   - ワークショップ (tlm_ws)
		*   - 資料（tlm_ws_aid)
		*   - ヒント (tlm_ws_hint)
		* - related (tlm_add)
		*
		* */
		let index = 0;
		if( location.hash == "#tlm_in" ) {
			UIkit.tab('#main-tab').show(0);
			UIkit.tab('#tlm_ws_tab').show(3);
		} else if( location.hash == "#tlm_ws" ) {
			UIkit.tab('#main-tab').show(0);
		} else if( location.hash == "#tlm_ws_aid" ) {
			UIkit.tab('#main-tab').show(0);
			UIkit.tab('#tlm_ws_tab').show(1);
		} else if( location.hash == "#tlm_ws_lft" ) {
			UIkit.tab('#main-tab').show(0);
			UIkit.tab('#tlm_ws_tab').show(2);
		} else if( location.hash == "#tlm_archive" ) {
			UIkit.tab('#main-tab').show(1);
		} else if( location.hash == "#tlm_add" ) {
			UIkit.tab('#main-tab').show(2);
		}
	</script>
<?php

get_sidebar();
get_footer();
