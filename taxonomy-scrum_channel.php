<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */

$scrum_ch_obj            = get_queried_object();
$scrum_ch                = get_fields( $scrum_ch_obj );
$scrum_ch['id']          = $scrum_ch_obj->term_id;
$scrum_ch['url']         = get_term_link( $scrum_ch_obj );
$scrum_ch['title']       = $scrum_ch_obj->name;
$scrum_ch['description'] = $scrum_ch_obj->description;

/* 所属する scrum を検索 */
$args   = array(
	'hide_empty' => false,
	'taxonomy'   => 'scrum',
	'meta_query' => array(
		'relation' => 'OR',
		array(
			'key'     => 'updates_news_podcast',
			'value'   => $scrum_ch['id'],
			'compare' => '=',
		),
		array(
			'key'     => 'updates_archive_podcast',
			'value'   => $scrum_ch['id'],
			'compare' => '=',
		),
	),
);
$scrums = get_terms( $args );

if ( count( $scrums ) ) {
	$scrum         = $scrums[0];
	$scrum_fields  = get_fields( $scrum );
	$scrum_url     = get_term_link( $scrum );
	$header_color  = $scrum_fields['title_color'];
	$header_bg_img = $scrum_fields['scrum_headerbg']['url'];
	$scrum_url     = get_term_link( $scrum );
} else {
	wp_die( '所属する scrum がありません。指定してください。' );
}


/* 表示の準備 */
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
if ( true === $scrum_ch['restrict'] ) {
	$has_access = $wcr_content->check_access( $scrum_ch['restrict_product'] );
	if ( is_super_admin() ) {
		$has_access = true;
	}
}

/*
 *  ユーザー固有のリンクを取得する。大元のテーマでモーダルdivを出力していることを前提にしています。
 *  toiee.jp では、ログインしていない場合、ナビ部分でモーダルdivを出力しています。
 */
if ( $user_logged_in ) {
	$pcast_url        = $scrum_ch['url'] . 'feed/pcast/?wcrtoken=' . $wcr_content->get_user_wcrtoken();
	$pcast_url_app    = str_replace( array( 'https://', 'http://' ), 'podcast://', $pcast_url );
	$button_href_app  = 'href="' . $pcast_url_app . '"';
	$button_href_feed = 'href="' . $pcast_url . '"';
} else {
	$button_href_app  = 'href="#" uk-toggle="target: #modal_login_form"';
	$button_href_feed = $button_href_app;
}

get_header();

?>
	<header>
		<div class="uk-section-default">
			<div class="uk-section <?php echo $header_color; ?> uk-background-cover" style="background-image: url(<?php echo $header_bg_img; ?>)">
				<div class="uk-container">
					<p>スクラム <?php echo $scrum->name; ?></p>
					<h1 class="uk-margin-remove-bottom uk-margin-remove-top uk-h2"><?php echo $scrum_ch_obj->name; ?></h1>
				</div>
			</div>
		</div>
	</header>
	<div class="pkt-content uk-container uk-background-default">
		<div class="kns-breadcrumb">
			<ul itemscope itemtype="http://schema.org/BreadcrumbList">
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a href="<?php echo esc_url( site_url() ); ?>" itemprop="item"><span itemprop="name">home</span><meta itemprop="position" content="1"></a>
				</li>
				<li class="bc-divider">&gt;</li>
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a href="<?php echo esc_url( $scrum_url ); ?>" itemprop="item"><span itemprop="name">スクラム : <?php echo $scrum->name; ?></span><meta itemprop="position" content="2"></a>
				</li>
			</ul>
		</div>
		<div class="uk-margin-top" uk-grid>
			<div class="uk-width-auto"><img src="<?php echo esc_attr( $scrum_ch['image'] ); ?>" width="150" height="150"></div>
			<div class="uk-width-expand">
				<h1 class="uk-h2 uk-margin-remove-bottom"><?php echo esc_html( $scrum_ch['title'] ); ?></h1>
				<p class="uk-text-muted uk-margin-remove-top"><?php echo esc_html( $scrum_ch['subtitle'] ); ?></p>
				<p>
					<a <?php echo $button_href_app; ?> class="uk-button uk-button-default uk-box-shadow-small" style="text-transform:none;">Podcast App</a>
					<a <?php echo $button_href_feed; ?> class="uk-button uk-button-text" style="text-transform:none;">その他(フィードURL)</a>
				</p>
			</div>
		</div>
		<div class="uk-margin">
			<p class="uk-text-small uk-text-muted pkt-description"><?php echo esc_html( $scrum_ch['description'] ); ?></p>
		</div>
			<?php
			if ( $can_edit ) {
				$setting = array(
					'post_id'            => 'new_post',
					'post_title'         => true,
					'new_post'           => array(
						'post_type'   => 'mdy_episode',
						'post_status' => 'draft',
						'tax_input'   => array( 'mdy_channel' => $scrum_ch['id'] ),
					),
					'fields'             => array( 'hoge' ),
					'submit_value'       => 'エピソードを追加（下書き保存）',
					'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
					'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
					'html_after_fields'  => '<input type="hidden" name="acf[mimidemy]" value="' . $scrum_ch['id'] . '"/>',
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
			<?php
			$the_episode_player_plyr_ext = 'scrum_episode';
			while ( have_posts() ) :
				the_post();
				require locate_template( 'template-parts/player.php' );
			endwhile;

			echo kanso_get_post_navigation();
			?>
	</div>

<?php
// var_dump( $scrum_ch );
get_sidebar();
get_footer();
