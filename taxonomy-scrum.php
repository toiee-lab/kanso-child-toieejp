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

$user_logged_in = is_user_logged_in();
$can_edit       = false;
if ( current_user_can( 'edit_posts' ) ) {
	acf_form_head();
	wp_deregister_style( 'wp-admin' );
	$can_edit = true;
}

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

		<ul id="main-tab" uk-tab>
			<li <?php echo $tab_active['getting_start']; ?>><a href="#" onclick="location.hash='getting_start'">はじめての方へ</a></li>
			<li <?php echo $tab_active['updates']; ?>><a href="#" onclick="location.hash='updates'">お知らせ一覧</a></li>
			<li <?php echo $tab_active['materials']; ?>><a href="#" onclick="location.hash='materials'">教材一覧</a></li>
			<?php if ( $can_edit ) : ?>
			<li><a href="#" onclick="location.hash='admin'">管理者</a></li>
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

				/* scrum_post、scrum_episode (pre_get_posts で調整済み) を取得 */
				$updates            = array();
				$archive_podcast_id = $scrum_fields['updates_archive_podcast'];
				$news_podcast_id    = $scrum_fields['updates_news_podcast'];

				/*
				 * Start the Loop
				 */
				while ( have_posts() ) :
					the_post();
					$p = get_post();

					$terms = wp_get_post_terms( $p->ID, 'scrum_channel' );
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

					echo kanso_get_post_navigation();

					?>
				</ul>

			</li>
			<!-- 教材一覧 -->
			<li>
				<?php echo $scrum_fields['materials-body']; ?>
				<h3>スクラムPodcast</h3>
				<?php
				$arr = array( $scrum_fields['updates_news_podcast'], $scrum_fields['updates_archive_podcast'] );

				echo w4t_podcast_grid_display( $arr, 'scrum_channel' );

				if ( is_array( $scrum_fields['materials_tlm'] ) && count( $scrum_fields['materials_tlm'] ) ) {
					?>
				<h3>スクラム教材</h3>
					<?php
					echo w4t_podcast_grid_display( $scrum_fields['materials_tlm'], 'tlm' );
				}

				if ( is_array( $scrum_fields['materials_mimidemy'] ) && count( $scrum_fields['materials_mimidemy'] ) ) {
					?>
				<h3>耳デミー（ながら時間で聴き流して、インプット）</h3>
					<?php
					echo w4t_podcast_grid_display( $scrum_fields['materials_mimidemy'], 'mdy_channel' );
				}

				if ( is_array( $scrum_fields['materials_pocketera'] ) && count( $scrum_fields['materials_pocketera'] ) ) {
					?>
					<h3>耳デミー（ながら時間で聴き流して、インプット）</h3>
					<?php
					echo w4t_podcast_grid_display( $scrum_fields['materials_pocketera'], 'pkt_channel' );
				}
				?>
			</li>
			<!-- 管理者用 -->
			<?php if ( $can_edit ) : ?>
			<li>
				<ul uk-tab>
					<li><a href="#">メインPodcast投稿</a></li>
					<li><a href="#">アーカイブPodcast投稿</a></li>
					<li><a href="#">ブログ投稿</a></li>
				</ul>
				<ul class="uk-switcher uk-margin uk-margin-bottom">
					<li>
						<h3>メインPodcastに投稿する</h3>
						<p>エピソードを「即」公開します</p>
						<?php
						if ( $scrum_fields['updates_news_podcast'] ) {
							$setting = array(
								'post_id'            => 'new_post',
								'post_title'         => true,
								'post_content'       => true,
								'new_post'           => array(
									'post_type'   => 'scrum_episode',
									'post_status' => 'publish',
									'tax_input'   => array( 'scrum_channel' => $news_podcast_id ),
								),
								'submit_value'       => 'エピソードを「メインPodcast」に追加（公開）',
								'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
								'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
							);
							acf_form( $setting );
						} else {
							?>
							<div class="uk-alert-warning" uk-alert><p>SCRUMポッドキャストチャンネルが設定されていません。</p></div>
							<?php
						}
						?>
					</li>
					<li>
						<h3>アーカイブPodcastに投稿する</h3>
						<p>エピソードを「即」公開します</p>
						<?php
						if ( $scrum_fields['updates_archive_podcast'] ) {
							$setting = array(
								'post_id'            => 'new_post',
								'post_title'         => true,
								'post_content'       => true,
								'new_post'           => array(
									'post_type'   => 'scrum_episode',
									'post_status' => 'publish',
									'tax_input'   => array( 'scrum_channel' => $archive_podcast_id ),
								),
								'submit_value'       => 'エピソードを「アーカイブPodcast」に追加（公開）',
								'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
								'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
							);
							acf_form( $setting );
						} else {
							?>
							<div class="uk-alert-warning" uk-alert><p>SCRUMポッドキャストチャンネルが設定されていません。</p></div>
							<?php
						}
						?>
					</li>
					<li>
						<h3>ブログ投稿</h3>
						<p>下書き保存し、編集ページに移動します。管理画面（Gutenberg）で編集し、公開してください。</p>
						<?php
						$setting = array(
							'post_id'            => 'new_post',
							'post_title'         => true,
							'new_post'           => array(
								'post_type'   => 'scrum_post',
								'post_status' => 'draft',
								'tax_input'   => array( 'scrum' => $scrum_id ),
							),
							'submit_value'       => 'エピソードを追加（下書き保存）',
							'return'             => admin_url( '/post.php?post=%post_id%&action=edit' ),
							'html_submit_button' => '<input type="submit" class="uk-button uk-button-secondary" value="%s" />',
						);
						acf_form( $setting );
						?>
					</li>
				</ul>
			</li>
			<?php endif; ?>
		</ul>

	</div><!-- .main-content -->
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
        if( location.hash == "#getting_start" ) {
            UIkit.tab('#main-tab').show(0);
        } else if( location.hash == "#updates" ) {
            UIkit.tab('#main-tab').show(1);
        } else if( location.hash == "#materials" ) {
            UIkit.tab('#main-tab').show(2);
        } else if( location.hash == "#admin" ) {
            UIkit.tab('#main-tab').show(3);
        }
	</script>
<?php


get_sidebar();
get_footer();
