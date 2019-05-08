<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */


$pkt_obj            = get_queried_object();
$pkt                = get_fields( $pkt_obj );
$pkt['id']          = $pkt_obj->term_id;
$pkt['url']         = get_term_link( $pkt_obj );
$pkt['title']       = $pkt_obj->name;
$pkt['description'] = $pkt_obj->description;

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
if ( true === $pkt['restrict'] ) {
	$has_access = $wcr_content->check_access( $pkt['restrict_product'] );
	if ( is_super_admin() ) {
		$has_access = true;
	}
}

/*
 *  ユーザー固有のリンクを取得する。大元のテーマでモーダルdivを出力していることを前提にしています。
 *  toiee.jp では、ログインしていない場合、ナビ部分でモーダルdivを出力しています。
 */
if ( $user_logged_in ) {
	$pcast_url        = $pkt['url'] . 'feed/pcast/?wcrtoken=' . $wcr_content->get_user_wcrtoken();
	$pcast_url_app    = str_replace( array( 'https://', 'http://' ), 'podcast://', $pcast_url );
	$button_href_app  = 'href="' . $pcast_url_app . '"';
	$button_href_feed = 'href="' . $pcast_url . '""';
} else {
	$button_href_app  = 'href="#" uk-toggle="target: #modal_login_form"';
	$button_href_feed = $button_href_app;
}

/*
 *
 */

get_header();

?>
	<header class="pkt-header">
		<div class="uk-section">
			<div class="uk-container">
				<p class="uk-margin-remove-top uk-margin-remove-bottom pkt-tagline">ポケてら : 探求、発見、驚き、楽しさ</p>
			</div>
		</div>
	</header>
	<div class="pkt-overlap">
		<div class="pkt-content uk-container uk-background-default">
			<div class="uk-margin-top" uk-grid>
				<div class="uk-width-auto"><img src="<?php echo esc_attr( $pkt['image'] ); ?>" width="150" height="150"></div>
				<div class="uk-width-expand">
					<h1 class="uk-h2 uk-margin-remove-bottom"><?php echo esc_html( $pkt['title'] ); ?></h1>
					<p class="uk-text-muted uk-margin-remove-top"><?php echo esc_html( $pkt['subtitle'] ); ?></p>
					<p>
						<a <?php echo $button_href_app; ?> class="uk-button uk-button-default uk-box-shadow-small" style="text-transform:none;">Podcast登録</a>
						<a <?php echo $button_href_feed; ?> class="uk-button uk-button-text" style="text-transform:none;" onclick="copyToClipboard()">フィードURL</a>
					</p>
				</div>
			</div>
			<div class="uk-margin">
				<p class="uk-text-small uk-text-muted pkt-description"><?php echo esc_html( $pkt['description'] ); ?></span></p>
			</div>
			<ul class="uk-child-width-expand" uk-tab>
				<li class="uk-active"><a href="#">教材</a></li>
				<li class=""><a href="#"><span class="uk-visible@s">受講資料</span><span class="uk-hidden@s">資料</span></a></li>
				<li class=""><a href="#"><span class="uk-visible@s">関連ナレッジ</span><span class="uk-hidden@s">関連</span></a></li>
				<li class=""><a href="#"><span class="uk-visible@s">ファシリテーター</span><span class="uk-hidden@s">LFT</span></a></li>
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
								'post_type'   => 'pkt_episode',
								'post_status' => 'draft',
								'tax_input'   => array( 'pkt_channel' => $pkt['id'] ),
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
								<h2 class="uk-modal-title">教材を追加する</h2>
								<?php acf_form( $setting ); ?>
							</div>
						</div>
						<?php
					}

					while ( have_posts() ) :
						the_post();
						require locate_template( 'template-parts/player.php' );
					endwhile;
					?>
				</li>
				<!-- ================= 受講者資料 =================== -->
				<li>
					<?php
					$tmp_posts = get_posts(
						array(
							'post_type'      => 'pkt_material',
							'posts_per_page' => 20,
							'meta_query'     => array(
								array(
									'key'   => 'pocketera',
									'value' => $pkt['id'],
								),
							),
						)
					);

					if ( count( $tmp_posts ) ) {
						/* 最初のものだけ表示 */
						$p = array_pop( $tmp_posts );

						if ( $can_edit ) :
							?>
							<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $p->ID . '&action=edit' ) ) ?>" class="uk-button uk-button-default uk-margin-small-right uk-align-right">編集する</a>
						<?php
						endif;
						echo apply_filters( 'the_content', $p->post_content ); // the_content filter を通す

						foreach ( $tmp_posts as $p ) {
							echo $p->post_title; // TODO 過去のレジュメがあったら表示する
						}

					} else {
						$setting = array(
							'post_id'            => 'new_post',
							'post_title'         => true,
							'new_post'           => array(
								'post_type'   => 'pkt_material',
								'post_status' => 'draft',
							),
							'fields'             => array( 'hoge' ),
							'submit_value'       => '授業資料を下書き保存',
							'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
							'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
							'html_after_fields'  => '<input type="hidden" name="acf[pocketera]" value="' . $pkt['id'] . '"/>',
						);

						acf_form( $setting );
					}
					?>
				</li>
				<!-- ================= 関連ナレッジ =================== -->
				<li>
					<?php
					$tmp_posts = get_posts(
						array(
							'post_type'      => 'toiee_knowledge',
							'posts_per_page' => 20,
							'orderby'        => 'meta_value',
							'meta_key'       => 'like',
							'meta_query'     => array(
								array(
									'key'     => 'pocketera',
									'value'   => serialize( (string)$pkt['id'] ),
									'compare' => 'LIKE',
								),
							),
						)
					);

					if ( count( $tmp_posts ) ) {
						$attr = array(
							'class' => 'uk-align-center uk-align-right@m uk-margin-remove-adjacent uk-width-medium',
						);
						foreach ( $tmp_posts as $p ) {
							setup_postdata( $p );
							?>
							<div>
								<?php echo get_the_post_thumbnail( $p->ID, 'medium', $attr ); ?>
								<h3><?php echo $p->post_title; ?></h3>
								<p><?php echo strip_tags( mb_substr( get_the_content(), 0, 200 ) );?></p>
								<p class="uk-text-meta"><?php echo get_post_meta( $p->ID, 'like', true); ?> likes, update <?php the_modified_date(); ?>, created <?php the_date(); ?></p>
								<p><a href="<?php echo get_permalink( $p->ID ); ?>">詳細を読む</a></p>
							</div>
							<hr style="clear:both">
							<?php
						}
						wp_reset_postdata();
					} else {
						?>
						<div uk-alert>
							<p>関連ナレッジはありません。</p>
						</div>
						<?php
					}

					if ( $can_edit ) {
						$setting = array(
							'post_id'            => 'new_post',
							'post_title'         => true,
							'new_post'           => array(
								'post_type'   => 'toiee_knowledge',
								'post_status' => 'draft',
							),
							'fields'             => array( 'hoge' ),
							'submit_value'       => '関連ナレッジを作成（下書き保存）',
							'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
							'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
							'html_after_fields'  => '<input type="hidden" name="acf[pocketera]" value="' . $pkt['id'] . '"/>',
						);

						echo '<div uk-alert class="uk-margin-medium-top"><h3>関連ナレッジを追加する</h3>';
						acf_form( $setting );
						echo '</div>';
					}
					?>
				</li>
				<!-- ================= レジュメ =================== -->
				<li>
					<ul class="uk-tab-bottom uk-flex-right" uk-tab>
						<li class="uk-active"><a href="#">レジュメ</a></li>
						<li><a href="#">LFTノート</a></li>
						<li><a href="#">開催レポート</a></li>
					</ul>
					<ul class="uk-switcher uk-margin uk-margin-bottom">
						<li>
					<?php
					$tmp_posts = get_posts(
						array(
							'post_type'      => 'pkt_resume',
							'posts_per_page' => 20,
							'meta_query'     => array(
								array(
									'key'   => 'pocketera',
									'value' => $pkt['id'],
								),
							),
						)
					);

					if ( count( $tmp_posts ) ) {
						/* 最初のものだけ表示 */
						$p = array_pop( $tmp_posts );

						echo apply_filters( 'the_content', $p->post_content ); // the_content filter を通す

						toiee_get_edit_button( $p );

						foreach ( $tmp_posts as $p ) {
							echo $p->post_title; // TODO 過去のレジュメがあったら表示する
						}
					} else {
						$setting = array(
							'post_id'            => 'new_post',
							'post_title'         => true,
							'new_post'           => array(
								'post_type'   => 'pkt_resume',
								'post_status' => 'draft',
							),
							'fields'             => array( 'hoge' ),
							'submit_value'       => '授業資料を下書き保存',
							'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
							'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
							'html_after_fields'  => '<input type="hidden" name="acf[pocketera]" value="' . $pkt['id'] . '"/>',
						);

						acf_form( $setting );
					}
					?>
						</li>
						<li>
							<!-- ================= LFTノート =========== -->
							<?php

							$tmp_posts = get_posts(
								array(
									'post_type'      => 'pkt_lftnote',
									'posts_per_page' => 20,
									'meta_query'     => array(
										array(
											'key'   => 'pocketera',
											'value' => $pkt['id'],
										),
									),
								)
							);
							if ( count( $tmp_posts ) ) {
								/* 最初のものだけ表示 */
								$p = array_pop( $tmp_posts );

								if ( $can_edit ) {
									toiee_get_edit_button( $p );
								}

								echo apply_filters( 'the_content', $p->post_content ); // the_content filter を通す

								foreach ( $tmp_posts as $p ) {
									echo $p->post_title; // TODO 過去のレジュメがあったら表示する
								}
							} else {
								$setting = array(
									'post_id'            => 'new_post',
									'post_title'         => true,
									'new_post'           => array(
										'post_type'   => 'pkt_lftnote',
										'post_status' => 'draft',
									),
									'fields'             => array( 'hoge' ),
									'submit_value'       => '授業資料を下書き保存',
									'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
									'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
									'html_after_fields'  => '<input type="hidden" name="acf[pocketera]" value="' . $pkt['id'] . '"/>',
								);

								acf_form( $setting );
							}
							?>
						</li>
						<li>
							<!-- ================= 開催報告 & フィードバック =================== -->
							<?php
							if ( $can_edit ) {
								$setting = array(
									'post_id'            => 'new_post',
									'post_title'         => true,
									'new_post'           => array(
										'post_type'   => 'pkt_report',
										'post_status' => 'draft',
									),
									'fields'             => array( 'hoge' ),
									'submit_value'       => '開催レポートを追加',
									'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
									'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
									'html_after_fields'  => '<input type="hidden" name="acf[pocketera]" value="' . $pkt['id'] . '"/>',
									'html_before_fields' => '<div class="uk-alert-primary" uk-alert><p>期待する結果、プロセス・姿勢、前提を記入し「必ず、公開」を行ってから、参加者にフィードバック用のURLを配布してください。</p></div>',
								);
								?>
								<button class="uk-button uk-button-default uk-margin-small-right uk-align-right" type="button" uk-toggle="target: #modal-post-report">開催レポート、フィードバックフォームを作る</button>
								<div id="modal-post-report" uk-modal>
									<div class="uk-modal-dialog uk-modal-body">
										<h2 class="uk-modal-title">開催レポート作成</h2>
										<?php acf_form( $setting ); ?>
									</div>
								</div>
								<?php
							}
							?>
							<h2>開催レポート一覧</h2>
							<?php
							$tmp_posts = get_posts(
								array(
									'post_type'      => 'pkt_report',
									'posts_per_page' => 20,
									'meta_query'     => array(
										array(
											'key'   => 'pocketera',
											'value' => $pkt['id'],
										),
									),
								)
							);
							if ( count( $tmp_posts ) ) {
								?>
								<ul class="uk-list uk-list-striped">
									<?php
									foreach ( $tmp_posts as $p ) {
										$fnum = get_post_meta( $p->ID, 'feedback_num', true );
										?>
									<li><a href="<?php echo get_permalink( $p ); ?>"><?php echo get_the_date( '', $p->ID ) . ' ' . $p->post_title; ?> <span uk-icon="comments"></span> <?php echo $fnum; ?></a></li>
										<?php
									}
									?>
								</ul>
								<?php
							} else {
								?>
								<p>no item</p>
								<?php
							}
							?>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
<?php

get_sidebar();
get_footer();
