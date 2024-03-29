<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */


$mdy_obj            = get_queried_object();
$mdy                = get_fields( $mdy_obj );
$mdy['id']          = $mdy_obj->term_id;
$mdy['url']         = get_term_link( $mdy_obj );
$mdy['title']       = $mdy_obj->name;
$mdy['description'] = $mdy_obj->description;

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
if ( true === $mdy['restrict'] ) {
	$has_access = $wcr_content->check_access( $mdy['restrict_product'] );
	if ( is_super_admin() ) {
		$has_access = true;
	}
}

/*
 *  ユーザー固有のリンクを取得する。大元のテーマでモーダルdivを出力していることを前提にしています。
 *  toiee.jp では、ログインしていない場合、ナビ部分でモーダルdivを出力しています。
 */
if ( $user_logged_in ) {
	$pcast_url        = $mdy['url'] . 'feed/pcast/?wcrtoken=' . $wcr_content->get_user_wcrtoken();
	$pcast_url_app    = str_replace( array( 'https://', 'http://' ), 'podcast://', $pcast_url );
	$button_href_app  = 'href="' . $pcast_url_app . '"';
	$button_href_feed = 'href="' . $pcast_url . '""';
} else {
	$button_href_app  = 'href="#" uk-toggle="target: #modal_login_form"';
	$button_href_feed = $button_href_app;
}

$new_feed = get_field( 'new_feed', $mdy_obj );

get_header();

?>
	<header class="mdy-header">
		<div class="uk-container">
			<div class="uk-padding">
				<?php if( $new_feed ): ?>
					<div class="uk-alert uk-alert-warning" uk-alert>
						<p>この教材は、新しい形式に移動しました。移動先は<a href="<?php echo $new_feed; ?>">こちら<br>
								<?php echo $new_feed; ?></a></p>
					</div>
				<?php endif; ?>
				<p class="uk-h3 uk-margin-remove-top uk-margin-remove-bottom mdy-tagline">耳デミー : スキマ、ながら時間で「耳から学ぶ」</p>
			</div>
		</div>
	</header>
	<div class="mdy-overlap">
		<div class="pkt-content uk-container uk-background-default">
			<div class="uk-margin-top" uk-grid>
				<div class="uk-width-auto"><img src="<?php echo esc_attr( $mdy['image'] ); ?>" width="150" height="150"></div>
				<div class="uk-width-expand">
					<h1 class="uk-h2 uk-margin-remove-bottom"><?php echo esc_html( $mdy['title'] ); ?></h1>
					<p class="uk-text-muted uk-margin-remove-top"><?php echo esc_html( $mdy['subtitle'] ); ?></p>
					<p class="uk-text-small uk-text-muted pkt-description"><?php echo esc_html( $mdy['description'] ); ?></span></p>
				</div>
			</div>
			<ul class="uk-child-width-expand" uk-tab>
				<li class="uk-active"><a href="#">教材</a></li>
				<li class=""><a href="#">補足資料</a></li>
			</ul>
			<ul class="uk-switcher uk-margin uk-margin-bottom">
				<!-- ================= 教材 =================== -->
				<li>
					<?php
					if ( $can_edit ) {
						$setting = array(
							'post_id'            => 'new_post',
							'post_title'         => true,
							'new_post'           => array(
								'post_type'   => 'mdy_episode',
								'post_status' => 'draft',
								'tax_input'   => array( 'mdy_channel' => $mdy['id'] ),
							),
							'fields'             => array( 'hoge' ),
							'submit_value'       => 'エピソードを追加（下書き保存）',
							'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
							'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
							'html_after_fields'  => '<input type="hidden" name="acf[mimidemy]" value="' . $mdy['id'] . '"/>',
						);
						?>
						<button class="uk-button uk-button-default uk-margin-small-right uk-align-right" type="button" uk-toggle="target: #modal-post">投稿する</button>
						<div id="modal-post" uk-modal>
							<div class="uk-modal-dialog uk-modal-body">
								<h2 class="uk-modal-title">Headline</h2>
								<?php acf_form( $setting ); ?>
							</div>
						</div>
						<?php
					}
					?>
					<div class="uk-alert-success" uk-alert>
						<p><a href="#" uk-toggle="target: <?php echo $user_logged_in ? '#modal_offline' : '#modal_login_form' ?>"><span uk-icon="icon: play-circle"></span> オフライン、モバイルで視聴する</a></p>
					</div>
					<div id="modal_offline" class="uk-flex-top" uk-modal>
						<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
							<?php
							if ( $user_logged_in ) {
								if ( $has_access ) {
									$url        = str_replace( array( 'https://', 'http://' ), 'pcast://', $pcast_url );
									$href_pcast = 'href="' . $url . '"';

									$url           = str_replace( array( 'https://', 'http://' ), 'podcast://', $pcast_url );
									$href_podcast  = 'href="' . $url . '"';

									$href_feed     = 'href="' . $pcast_url . '"';


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
										<?php
										if ( isset( $mdy['audiobook'] ) && $mdy['audiobook'] != '' ) {
											$href_download = 'href="' . $mdy['audiobook'] . '" download="' . $mdy['title'] . '.m4b"';
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
					<?php

					$the_episode_player_plyr_ext = 'scrum_episode';

					while ( have_posts() ) :
						the_post();
						require locate_template( 'template-parts/player.php' );
					endwhile;
					?>
				</li>
				<!-- ================= 補足資料 =================== -->
				<li>
					<?php
					$tmp_posts = get_posts(
						array(
							'post_type'      => 'mdy_material',
							'posts_per_page' => 20,
							'meta_query'     => array(
								array(
									'key'   => 'mimidemy',
									'value' => $mdy['id'],
								),
							),
						)
					);

					if ( count( $tmp_posts ) ) {
						/* 最初のものだけ表示 */
						$p = array_pop( $tmp_posts );
						toiee_get_edit_button( $p );
						echo apply_filters( 'the_content', $p->post_content ); // the_content filter を通す

						foreach ( $tmp_posts as $p ) {
							echo $p->post_title; // TODO 過去のレジュメがあったら表示する
						}

					} elseif ( $can_edit ) {
						$setting = array(
							'post_id'            => 'new_post',
							'post_title'         => true,
							'new_post'           => array(
								'post_type'   => 'mdy_material',
								'post_status' => 'draft',
							),
							'fields'             => array( 'hoge' ),
							'submit_value'       => '授業資料を下書き保存',
							'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
							'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
							'html_after_fields'  => '<input type="hidden" name="acf[mimidemy]" value="' . $mdy['id'] . '"/>',
						);

						acf_form( $setting );
					} else {
						?>
						<div uk-alert>
							<p>授業資料はありません。</p>
						</div>
						<?php
					}

					?>
				</li>
			</ul>
		</div>
	</div>

<?php
//var_dump( $mdy );
get_sidebar();
get_footer();
