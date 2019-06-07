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

/*
 *  ユーザー固有のリンクを取得する。大元のテーマでモーダルdivを出力していることを前提にしています。
 *  toiee.jp では、ログインしていない場合、ナビ部分でモーダルdivを出力しています。
 */
if ( $user_logged_in ) {
	$pcast_url        = $tlm['url'] . 'feed/pcast/?wcrtoken=' . $wcr_content->get_user_wcrtoken();
	$pcast_url_app    = str_replace( array( 'https://', 'http://' ), 'podcast://', $pcast_url );
	$button_href_app  = 'href="' . $pcast_url_app . '"';
	$button_href_feed = 'href="' . $pcast_url . '"';
} else {
	$button_href_app  = 'href="#" uk-toggle="target: #modal_login_form"';
	$button_href_feed = $button_href_app;
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
get_header();

?>
	<header class="tlm-header">
		<div class="uk-section">
			<div class="uk-container">
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
					<p>
						<a <?php echo $button_href_app; ?> class="uk-button uk-button-default uk-box-shadow-small" style="text-transform:none;">Podcast登録</a>
						<a <?php echo $button_href_feed; ?> class="uk-button uk-button-text" style="text-transform:none;" onclick="copyToClipboard()">フィードURL</a>
					</p>
				</div>
			</div>
			<div class="uk-margin">
				<p class="uk-text-small uk-text-muted tlm-description"><?php echo esc_html( $tlm['description'] ); ?></span></p>
			</div>
			<ul class="uk-child-width-expand" uk-tab id="main-tab">
				<li><a href="#" onclick="location.hash='tlm_in'">インプット</a></li>
				<li><a href="#" onclick="location.hash='tlm_ws'"><span class="uk-visible@s">ワークショップ</span><span class="uk-hidden@s">WS</span></a></li>
				<li><a href="#" onclick="location.hash='tlm_archive'"><span class="uk-visible@s">ワークショップ録画</span><span class="uk-hidden@s">WS録画</span></a></li>
				<li><a href="#" onclick="location.hash='tlm_add'"><span class="uk-visible@s">関連ナレッジ</span><span class="uk-hidden@s">関連</span></a></li>
			</ul>
			<ul class="uk-switcher uk-margin uk-margin-bottom">
				<!-- ================= インプット =================== -->
				<li>
					<?php if ( ! $user_logged_in ) : ?>
					<div class="uk-alert-primary" uk-alert>
						スクラム教材・インプットは「会員登録」することで、<b>すべて無料</b>でご覧いただけます。<br>
						<a href="#" onclick="UIkit.modal('#modal_login_form').show();UIkit.tab('#modal_login_form_tab').show(1);">会員登録する</a>
					</div>
					<?php endif; ?>
					<?php
					if ( isset( $elements['tlm_in'] ) ) {

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
					} else {
						?>
						<p>現在、インプット教材がありません。</p>
						<?php
					}
					?>
				</li>
				<li><!-- ================= ワークショップ =================== -->
					<ul uk-tab id="tlm_ws_tab">
						<li class="uk-active"><a href="#" onclick="location.hash='tlm_ws'">ワーク</a></li>
						<li><a href="#" onclick="location.hash='tlm_ws_aid'">受講資料</a></li>
						<li><a href="#" onclick="location.hash='tlm_ws_lft'">LFT</a></li>
					</ul>
					<ul class="uk-switcher uk-margin uk-margin-bottom">
						<!-- ================= ビデオ =========== -->
						<li>
							<?php
							if ( isset( $elements['tlm_ws']) ) {
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
        } else if( location.hash == "#tlm_ws" ) {
            UIkit.tab('#main-tab').show(1);
        } else if( location.hash == "#tlm_ws_aid" ) {
            UIkit.tab('#main-tab').show(1);
            UIkit.tab('#tlm_ws_tab').show(1);
        } else if( location.hash == "#tlm_ws_lft" ) {
            UIkit.tab('#main-tab').show(1);
            UIkit.tab('#tlm_ws_tab').show(2);
        } else if( location.hash == "#tlm_archive" ) {
            UIkit.tab('#main-tab').show(2);
        } else if( location.hash == "#tlm_add" ) {
            UIkit.tab('#main-tab').show(3);
        }
	</script>
<?php

get_sidebar();
get_footer();
