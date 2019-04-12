<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kanso-general
 */

$can_edit = false;
if ( current_user_can( 'edit_posts' ) ) {
	acf_form_head();
	wp_deregister_style( 'wp-admin' );
	$can_edit = true;
}

$feedback_mode = false;
$token         = get_query_var( 'pktftoken', '' );
if ( $token === md5( get_the_id() ) ) {
	$feedback_mode = true;
}


get_header(); ?>
	<header class="pkt-header">
		<div class="uk-container">
			<div class="uk-padding">
				<p class="uk-h3 uk-margin-remove-top uk-margin-remove-bottom pkt-tagline">ポケてら 開催レポート</p>
			</div>
		</div>
	</header>
	<div class="pkt-overlap">
		<div class="uk-container uk-container-small uk-background-default main-content">
			<?php
			while ( have_posts() ) :
				the_post();

				$pkt_ch_id   = get_field( 'pocketera' );
				$pkt         = get_term_by( 'id', $pkt_ch_id, 'pkt_channel' );
				$feedbak_url = trailingslashit( get_permalink() ) . '?pktftoken=' . md5( get_the_id() );

				/* =========== アンケートフォームを表示 ============== */
				if ( $feedback_mode ) {
					?>
				<section class="uk-section">
					<h1><?php echo esc_html( $pkt->name ); ?> フィードバックアンケート</h1>
					<?php
					$groups = acf_get_field_groups( array( 'post_type' => 'pkt_feedback' ) );

					acf_form(
						array(
							'post_id'              => 'new_post',
							'new_post'             => array(
								'post_type'   => 'pkt_feedback',
								'post_status' => 'publish',
							),
							'field_groups'         => array( 'group_5cb014460eb0a' ),
							'html_submit_button'   => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
							'html_after_fields'    => '<input type="hidden" name="acf[pkt_report]" value="' . get_the_id() . '"/>',
							'submit_value'         => '送信する',
							'html_updated_message' => '<div class="uk-alert-success" uk-alert><p>アンケートありがとうございました。</p></div>',
						)
					);
					?>
				</section>
					<?php
					/* =========== LFTが閲覧できる ============== */
				} elseif ( $can_edit ) {


					?>
			<h1 class="uk-h2 uk-margin-top">ポケてら <a
						href="<?php echo get_term_link( $pkt ); ?>"><?php echo esc_html( $pkt->name ); ?></a><br>開催レポート
			</h1>
			<div uk-alert class="pkt-report-meta">
				<ul>
					<li>タイトル : <?php the_title( '', '' ); ?></li>
					<li>記載日時 : <?php the_date(); ?></li>
					<li>記載者 : <?php the_author(); ?></li>
					<li>フィードバック用URL : <a href="<?php echo esc_url( $feedbak_url ); ?>" target="_blank">このリンクを参加者に配布してください</a></li>
				</ul>
			</div>
			<ul class="uk-child-width-expand" uk-tab>
				<li class="uk-active"><a href="#">LFTレポート</a></li>
				<li class=""><a href="#">参加者フィードバック</a></li>
			</ul>
			<ul class="uk-switcher uk-margin uk-margin-bottom">
				<li>
					<a href="<?php echo esc_url( admin_url( 'post.php?post=' . get_the_ID() . '&action=edit' ) ) ?>" class="uk-button uk-button-default uk-margin-small-right uk-align-right">編集する</a>
					<?php

							the_content();
					?>
				</li>
				<li>
					<?php
					$tmp_posts = get_posts(
						array(
							'post_type'      => 'pkt_feedback',
							'posts_per_page' => - 1,
							'meta_query'     => array(
								array(
									'key'   => 'pkt_report',
									'value' => get_the_ID(),
								),
							),
						)
					);
					if ( count( $tmp_posts ) ) {
						?>
						<ul class="uk-list uk-list-striped pkt-feedbak">
							<?php
							foreach ( $tmp_posts as $p ) {
								?>
								<li>
									<?php echo apply_filters( 'the_content', $p->post_content ); ?>
								</li>
								<?php
							}
							?>
						</ul>
						<?php
					} else {
						?>
						<p>参加者フィードバックはありません</p>
						<?php
					}
					?>
				</li>
			</ul>
					<?php


					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						?>
						<hr class="uk-margin-large">
						<?php
						comments_template();
					}

					break;
					/* =========== 一般の人は閲覧させない ============== */
				} else {
					?>
				<section class="uk-section"><p>ご覧いただけません。</p></section>
					<?php
				}
			endwhile; // End of the loop.

			?>
		</div><!-- .main-content -->
	</div>
<?php
get_sidebar();
get_footer();
