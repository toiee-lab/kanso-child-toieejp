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

if ( is_user_logged_in() || current_user_can( 'edit_posts' ) ) {
	acf_form_head();
	wp_deregister_style( 'wp-admin' );
}

get_header();

?>
	<header class="pkt-header">
		<div class="uk-section">
			<div class="uk-container">
				<p class="uk-margin-remove-top uk-margin-remove-bottom">ポケてら</p>
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
						<button class="uk-button uk-button-secondary uk-button-small">Secondary</button>
						<button class="uk-button uk-button-secondary uk-button-small">Secondary</button>
						<button class="uk-button uk-button-secondary uk-button-small">Secondary</button>
					</p>
				</div>
			</div>
			<div class="uk-margin">
				<p class="uk-text-small uk-text-muted pkt-description"><?php echo esc_html( $pkt['description'] ); ?></span></p>
			</div>
			<ul class="uk-child-width-expand" uk-tab>
				<li class="uk-active"><a href="#">教材</a></li>
				<li class=""><a href="#">受講資料</a></li>
				<li class=""><a href="#">関連ナレッジ</a></li>
				<li class=""><a href="#">レジュメ</a></li>
			</ul>
			<ul class="uk-switcher uk-margin uk-margin-bottom">
				<!-- ================= 教材 =================== -->
				<li>
					<?php
					$current_user = wp_get_current_user();
					if ( is_admin() ) {
						?>
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
						foreach ( $tmp_posts as $p ) {
							echo apply_filters( 'the_content', $p->post_content ) . "<br>\n";
						}
					} else {
						?>
						<p>no item</p>
						<?php
					}
					?>
				</li>
				<!-- ================= レジュメ =================== -->
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
						foreach ( $tmp_posts as $p ) {
							echo $p->post_title . "<br>\n";
						}
					} else {
						?>
						<p>no item</p>
						<?php
					}
					?>
					<!-- ================= 開催報告 & フィードバック =================== -->
					<?php

					// get_post_meta( $report_id, 'feedback_num', true ); でフィードバック数が取得できる
					//
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
						foreach ( $tmp_posts as $p ) {
							echo $p->post_title . "<br>\n";
						}
					} else {
						?>
						<p>no item</p>
						<?php
					}
					?>
				</li>
			</ul>
		</div>
	</div>
<?php

get_sidebar();
get_footer();
