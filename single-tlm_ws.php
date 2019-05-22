<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kanso-general
 */

$tlms = wp_get_post_terms( get_the_ID(), 'tlm' );

if ( count( $tlms ) ) {
	$tlm_obj            = $tlms[0];
	$tlm                = get_fields( $tlm_obj );
	$tlm['id']          = $tlm_obj->term_id;
	$tlm['url']         = get_term_link( $tlm_obj );
	$tlm['title']       = $tlm_obj->name;
	$tlm['description'] = $tlm_obj->description;
} else {
	wp_die( '所属する scrum_channel がありません。指定してください。' );
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
$user_logged_in = is_user_logged_in();
if ( $user_logged_in ) {
	$pcast_url        = $tlm['url'] . 'feed/pcast/?wcrtoken=' . $wcr_content->get_user_wcrtoken();
	$pcast_url_app    = str_replace( array( 'https://', 'http://' ), 'podcast://', $pcast_url );
	$button_href_app  = 'href="' . $pcast_url_app . '"';
	$button_href_feed = 'href="' . $pcast_url . '""';
} else {
	$button_href_app  = 'href="#" uk-toggle="target: #modal_login_form"';
	$button_href_feed = $button_href_app;
}

$can_edit = false;
if ( current_user_can( 'edit_posts' ) ) {
	$can_edit = true;
}

get_header(); ?>
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
			<div class="uk-margin">
				<ul class="uk-breadcrumb">
					<li><a href="<?php echo esc_url( $tlm['url'] ); ?>"><?php echo esc_html( $tlm['title'] ); ?></a></li>
					<li><a href="<?php echo esc_url( $tlm['url'] ) . '#tlm_ws'; ?>">ワーク一覧</a></li>
					<li><span>here</span></li>
				</ul>
			</div>
			<?php
			while ( have_posts() ) :
				the_post();

				the_title( '<h1 class="main-title">', '</h1>' );

				$src   = get_field( 'enclosure' );
				$media = get_field( 'media' );

				if ( $has_access ) {
					the_episode_player( $src, $media );
				} else {
					$restrict = get_field( 'restrict' );
					if ( $restrict === true ) {
						$restrict = 'restrict';
					} else if ( $restrict === false ) {
						$restrict = 'open';
					}

					switch ( $restrict ) {
						case 'open':
							the_episode_player( $src, $media );
							break;
						case 'free':
							if ( $user_logged_in ) {
								the_episode_player( $src, $media );
								break;
							}
						default: /* restrict */
							the_episode_player_dummy( $media );
							break;
					}
				}

				the_content();
				?>
				<hr class="uk-divider-small uk-text-center">
				<ul class="uk-pagination">
					<li><a href="#"><?php previous_post_link( '%link', 'Previous', true, ' ', 'tlm' ); ?></li>
					<li class="uk-margin-auto-left"><?php next_post_link( '%link', 'Next', true, ' ', 'tlm' ); ?></li>
				</ul>
				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					?>
					<hr class="uk-margin-large">
					<?php
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>
		</div>
	</div>
<?php
get_sidebar();
get_footer();
