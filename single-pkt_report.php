<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kanso-general
 */

$fields = get_fields();

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

				$pkt_ch_id = get_field( 'pocketera' );
				$pkt       = get_term_by( 'id', $pkt_ch_id, 'pkt_channel' );

				?>
			<h1 class="uk-h2 uk-margin-top">ポケてら <a href="<?php echo get_term_link( $pkt ); ?>"><?php echo esc_html( $pkt->name ); ?></a><br>開催レポート</h1>
			<div uk-alert>
				<ul>
					<li>タイトル : <?php the_title( '', '' ); ?></li>
					<li>記載日時 : <?php the_date(); ?></li>
					<li>記載者   : <?php the_author(); ?></li>
				</ul>
			</div>
			<ul class="uk-child-width-expand" uk-tab>
				<li class="uk-active"><a href="#">LFTレポート</a></li>
				<li class=""><a href="#">参加者フィードバック</a></li>
			</ul>
			<ul class="uk-switcher uk-margin uk-margin-bottom">
				<li>
					<?php
					the_content();
					?>
				</li>
				<li>
					<?php
					$tmp_posts = get_posts(
						array(
							'post_type'      => 'pkt_feedback',
							'posts_per_page' => -1,
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
						<ul class="uk-list uk-list-striped">
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
				if ( comments_open() || get_comments_number() ) :
					?>
					<hr class="uk-margin-large">
					<?php
					comments_template();
				endif;

				break;
			endwhile; // End of the loop.
			?>

		</div><!-- .main-content -->
	</div>
<?php
get_sidebar();
get_footer();
