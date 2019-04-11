<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */


$scrum        = get_queried_object();
$scrum_id     = $scrum->term_id;
$scrum_url    = get_term_link( $scrum );
$scrum_fields = get_fields( $scrum );

$header_color  = $scrum_fields['title_color'];
$header_bg_img = $scrum_fields['scrum_headerbg']['url'];

$tab_active = array(
	'getting_start' => '',
	'updates'       => '',
	'materials'     => '',
);
$tab_active[ $scrum_fields['first_tag_type'] ] = 'class="uk-active"';

acf_form_head();
get_header();
?>
<header>
	<div class="uk-section-default">
		<div class="uk-section <?php echo $header_color; ?> uk-background-cover" style="background-image: url(<?php echo $header_bg_img; ?>)">
			<div class="uk-container">
				<p>スクラム・ラーニング</p>
				<h1 class="uk-margin-remove-bottom"><?php echo $scrum->name; ?></h1>
				<p class="uk-text-lead uk-margin-remove-top"><?php echo $scrum_fields['scrum_subtitle']; ?></p>
			</div>
		</div>
	</div>
</header>

	<div class="uk-container uk-container-small uk-background-default uk-margin-medium-top">

		<ul uk-tab>
			<li <?php echo $tab_active['getting_start']; ?>><a href="#">はじめての方へ</a></li>
			<li <?php echo $tab_active['updates']; ?>><a href="#">お知らせ一覧</a></li>
			<li <?php echo $tab_active['materials']; ?>><a href="#">教材一覧</a></li>
			<?php if ( current_user_can( 'edit_published_posts' ) ) : ?>
			<li><a href="#">管理者</a></li>
			<?php endif; ?>
		</ul>

		<ul class="uk-switcher uk-margin uk-margin-bottom">
			<!-- はじめての方へ -->
			<li>
				<?php echo $scrum_fields['getting-start-body']; ?>
			</li>
			<!-- お知らせ一覧 -->
			<li>
				<?php echo $scrum_fields['updates_body']; ?>
				<?php

				/* scrum_post、podcast (pre_get_posts で調整済み) を取得 */
				$updates            = array();
				$archive_podcast_id = $scrum_fields['updates_archive_podcast'];
				$news_podcast_id    = $scrum_fields['updates_news_podcast'];

				/*
				 * Start the Loop
				 */
				while ( have_posts() ) :
					the_post();
					$p = get_post();

					$terms = wp_get_post_terms( $p->ID, 'series' );
					if ( count( $terms ) ) {
						if ( $archive_podcast_id === $terms[0]->term_id ) {
							$ptype = 'podcast_archive';
						} else {
							$ptype = 'podcast_news';
						}
					} else { /* scrum_post と仮定する */
						$ptype = 'scrum_post';
					}

					$updates[ $p->ID ] = array(
						'ID'         => $p->ID,
						'post_date'  => $p->post_date,
						'post_title' => $p->post_title,
						'post_type'  => $ptype,
						'permalink'  => get_the_permalink(),
						'time'       => strtotime( $p->post_date ),
					);
				endwhile;
				?>
				<ul class="uk-list uk-list-divider">
					<?php
					echo implode(
						array_map(
							function( $dat ) {
								switch ( $dat['post_type'] ) {
									case 'podcast_news':
										$label      = 'success';
										$label_text = 'Podcast';
										break;
									case 'podcast_archive':
										$label      = 'warning';
										$label_text = 'アーカイブ';
										break;
									default:
										$label_text = 'ブログ記事';
										$label      = 'default';
								}

								$date = date( 'n月d日', $dat['time'] );
								return "<li><span class=\"uk-label uk-label-{$label}\">{$label_text}</span> <a href=\"{$dat['permalink']}\"><span class='uk-text-muted'>{$date}</span> {$dat['post_title']}</a></li>\n";
							},
							$updates
						)
					);
					?>
				</ul>

			</li>
			<!-- 教材一覧 -->
			<li>
				<?php echo $scrum_fields['materials-body']; ?>
				<h3>Featured (注目)</h3>
				<?php
				$arr = array( $scrum_fields['updates_news_podcast'], $scrum_fields['updates_archive_podcast'] );

				echo w4t_podcast_grid_display( array_merge( $arr, $scrum_fields['materials_featured'] ) );
				?>

				<h3>耳デミー（ながら時間で聴き流して、インプット）</h3>
				<?php echo w4t_podcast_grid_display( $scrum_fields['materials_mimidemy'] ); ?>

				<h3>ポケてら（ワークショップ教材で、体験から学ぶ）</h3>
				<?php echo w4t_podcast_grid_display( $scrum_fields['materials_pocketera'] ); ?>
			</li>
			<!-- 管理者用 -->
			<?php if ( current_user_can( 'edit_published_posts' ) ) : ?>
			<li>
				<?php echo $scrum_fields['admin-body']; ?>

				<hr>

				<h3>記事の投稿</h3>
				<p>タイトルを入れてボタンを押してください。すぐに記事作成画面が開きます。</p>
				<?php
				acf_form(
					array(
						'post_id'            => 'new_post',
						'post_title'         => true,
						'new_post'           => array(
							'post_type'   => 'scrum_post',
							'post_status' => 'draft',
							'tax_input'   => array( 'scrum' => $scrum_id ),
						),
						'submit_value'       => '下書き保存',
						'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
						'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
					)
				);
				?>

				<p><a href="<?php echo admin_url( '/edit.php?scrum=' . $scrum->slug . '&post_type=scrum_post&post_status=draft' ); ?>" target="_blank">下書き一覧はこちら</a>。<a href="<?php echo $scrum_url; ?>rss">記事のRSSフィード</a></p>
				
				<h3>Podcastの投稿</h3>
				<ul>
					<li><a href="https://toiee.jp/toiee-admin/post-podcast/?wpf25655_21=<?php echo $scrum_fields['updates_news_podcast']; ?>" target="_blank">お知らせPodcastを投稿する</a></li>
					<li><a href="https://toiee.jp/toiee-admin/post-podcast/?wpf25655_21=<?php echo $scrum_fields['updates_archive_podcast']; ?>" target="_blank">アーカイブPodcastを投稿する</a></li>
				</ul>
			</li>
			<?php endif; ?>
		</ul>

	</div><!-- .main-content -->
<?php


get_sidebar();
get_footer();
