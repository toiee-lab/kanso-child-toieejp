<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kanso-general
 */

get_header();

$cats    = get_the_category();
$anc_ids = get_ancestors( $cats[0]->term_id, 'category' );
$fields  = get_fields();

$kind = [
	'workshop-archive', /* ワークショップ・アーカイブのカテゴリスラッグを workshop-archive と想定 */
	'lft',              /* ワークショップ・レジュメのカテゴリスラッグを lft と想定 */
	'mimidemy',         /* 耳デミーのカテゴリスラッグを mimidemy と想定 */
];

$top_nav = [
	'href' => '',
	'text' => ''
];

foreach( $kind as $k ) {
	if ( toiee_in_categories( $k ) ) {
		$term = get_term_by( 'slug', $k, 'category' );
		$top_nav['href'] = get_term_link( $term );
		$top_nav['text'] = $term->name;

		break;
	}
}

/**
 *
 * 教材状態の投稿か、通常の投稿か？で処理を分ける
 *
 */
if ( isset( $fields['tlm_enable'] ) && true === $fields['tlm_enable'] ) {

	$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	$channel   = $fields['tlm_channel'];

	$user_logged_in = is_user_logged_in();

	/* 閲覧制限を取得 */
	global $wcr_content;
	$has_access = true;
	if ( true === $channel['restrict'] ) {
		$has_access = $wcr_content->check_access( $channel['restrict_product'] );
		if ( is_super_admin() ) {
			$has_access = true;
		}
	}

	?>
	<div class="uk-container uk-margin-top" style="max-width:900px">
		<p><a href="" uk-icon="icon: grid"></a><a href="<?php echo $top_nav['href']; ?>" class="uk-link-text"><?php echo $top_nav['text']; ?></a></p>
	<?php
	while ( have_posts() ) {
		the_post();
		?>
		<div uk-grid>
			<div class="uk-width-small"><img src="<?php echo $channel['artwork']; ?>"></div>
			<div class="uk-width-expand">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				<h2 class="main-subtitle"><?php echo $channel['subtitle']; ?></h2>
			</div>
		</div>

		<?php the_content(); ?>

		<div class="uk-alert-success" uk-alert>
			<p><a href="#" uk-toggle="target: <?php echo $user_logged_in ? '#modal_offline' : '#modal_login_form' ?>"><span uk-icon="icon: play-circle"></span> オフライン、モバイルで視聴する</a></p>
		</div>
		<div id="modal_offline" class="uk-flex-top" uk-modal>
			<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
				<?php
				if ( $user_logged_in ) {
					if ( $has_access ) {
						$permalink = get_permalink();
						$pcast_url = $permalink . 'postcast/' . $wcr_content->get_user_wcrtoken();

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
							if ( $channel['audiobook'] != '' ) {
								$href_download = 'href="' . $channel['audiobook'] . '" download="' . get_the_title() . '.m4b"';
								?>
								<dt>オーディオブック形式（m4b）</dt>
								<dd>ダウンロードして視聴できます。iPhoneなどのApple Book、Book
									Player、Androidのオーディオブックアプリなどを利用できます。<br>
									<p uk-margin><a <?php echo $href_download; ?> class="uk-button uk-button-default">ダウンロード</a></p>
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
		ob_start();

		$count = 0;
		$toc   = array();

		foreach( $fields['tlm_items'] as $item ){
			$count++;
			$toc[ $count ] = $item['title'];
			?>
			<h2 id="item-<?php echo $count; ?>" class="uk-heading-line"><span><?php echo $item['title'] ?></span></h2>
			<p class="uk-text-meta"><?php echo $item['subtitle']; ?></p>
			<?php

			$the_episode_player_plyr_ext = 'tlm_input';

			$src   = $item['enclosure'];
			$media = $item['media'];

			if ( $has_access ) {
				the_episode_player_plyr( $src, $media, $the_episode_player_plyr_ext );
			} else {
				$restrict = $item['restrict'];
				if ( $restrict === true ) {
					$restrict = 'restrict';
				} else if ( $restrict === false ) {
					$restrict = 'open';
				}

				switch ( $restrict ) {
					case 'open':
						the_episode_player_plyr( $src, $media, $the_episode_player_plyr_ext );
						break;
					case 'free':
						if ( $user_logged_in ) {
							the_episode_player_plyr( $src, $media, $the_episode_player_plyr_ext );
							break;
						}
					default: /* restrict */
						the_episode_player_dummy( $media );
						break;
				}
			}

			echo $item['note'];

		}

		$content = ob_get_contents();
		ob_clean();
		?>

		<div class="uk-width-2-5@m">
			<ul class="uk-nav uk-nav-default" uk-scrollspy-nav="closest: li; scroll: true; offset: 70">
				<li class="uk-nav-header uk-active">目次</li>
				<li class="uk-nav-divider"></li>
				<?php foreach( $toc as $i => $h ) : ?>
				<li><a href="#item-<?php echo $i; ?>"><?php echo $h;?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php echo $content; ?>
		<hr class="uk-divider-small uk-text-center">
		<?php
		the_post_navigation(
			array(
				'prev_text'          => '&lt; PREVIOUS',
				'next_text'          => 'NEXT &gt;',
				'screen_reader_text' => 'Navigation',
			)
		);

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			?>
			<hr class="uk-margin-large">
			<?php
			comments_template();
		endif;
	}
	?>
	</div>
<?php
} else {

	?>
	<div class="uk-container uk-container-small uk-background-default main-content">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );
			?>
			<hr class="uk-divider-small uk-text-center">
			<?php
			the_post_navigation(
				array(
					'prev_text'          => '&lt; PREVIOUS',
					'next_text'          => 'NEXT &gt;',
					'screen_reader_text' => 'Navigation',
				)
			);

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				?>
				<hr class="uk-margin-large">
				<?php
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</div><!-- .main-content -->
	<?php
}
get_sidebar();
get_footer();
