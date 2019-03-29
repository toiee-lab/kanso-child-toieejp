<?php
/**
 * Created by PhpStorm.
 * User: takame
 * Date: 2019-03-27
 * Time: 14:50
 */


/*
 * ソースコードとしては、最悪の結果だが、目を瞑る。いつか綺麗にしよう。
 * なぜ、こうなっているのか？というと、属するタクソノミーごとにデザインを設定しようとしているから
 * もしかしたら、もっといい方法があるかもしれないが、今はわからないので、無理やり解決することとする。
 */

$current_post_id = false;

/* ナビ部分のコンテンツを生成 */
if ( is_tax( 'magazine' ) ) {
	/* magazineのタクソノミーを表示しているときの処理 */

	/* サイドバー */
	ob_start();

	while ( have_posts() ) :
		the_post();

		if ( false === $current_post_id ) {
			$current_post_id = get_the_ID();
		}
		get_template_part( 'template-parts/mag_nav_item' );
	endwhile;
	rewind_posts();

	$side_nav_content = ob_get_contents();
	ob_end_clean();


} else {
	/* それ以外（post_type == mag_post）の場合、データを取得する */

	$current_post_id = get_the_ID();

	$my_order = Toiee_Magazine_Post::get_order( $mag['order'] );
	$args     = array(
		'post_type' => 'mag_post',
		'tax_query' => array(
			array(
				'taxonomy' => 'magazine',
				'field'    => 'term_id',
				'terms'    => $magazine->term_id,
			),
		),
		'order'     => $my_order['order'],
		'orderby'   => $my_order['orderby'],
	);

	$q = new WP_Query( $args );

	/* サイドバー */
	if ( $q->have_posts() ) {
		ob_start();

		while ( $q->have_posts() ) {
			$q->the_post();
			setup_postdata( $q->post );
			get_template_part( 'template-parts/mag_nav_item' );
		}
		wp_reset_postdata();

		$side_nav_content = ob_get_contents();
		ob_end_clean();
	}
}

$side_nav_content = str_replace( 'nav-item-' . $current_post_id, 'nav-item-active', $side_nav_content );

?>
<header class="mag-header">
	<div class="uk-section">
		<div class="uk-container">
			<p class="uk-margin-remove-top uk-margin-remove-bottom">toiee Lab magazine</p>
			<h1 class="uk-margin-remove-bottom uk-margin-small-top mag-h1"><a href="<?php echo esc_attr( $magazine_url ); ?>"><?php echo esc_html( $mag['title'] ); ?></a></h1>
			<p class="uk-text-lead uk-margin-remove-top mag-lead"><a href="<?php echo esc_attr( $magazine_url ); ?>"><?php echo esc_html( $mag['subtitle'] ); ?></a></p>
		</div>
	</div>
</header>
<div class="mag-overlap">
	<div class="uk-flex uk-flex-center">
		<!-- content -->
		<div class="uk-background-default">
			<div class="uk-container uk-container-small uk-margin-remove-top uk-padding-small main-content main-content-sidebar">
				<main class="uk-margin-top">
					<?php if ( '' !== trim( $mag['header'] ) ) : ?>
					<div class="uk-padding-small mag-notice-header uk-hidden@m">
						<?php echo "hoge" . $mag['header']; ?>
					</div>
					<?php endif; ?>
					<div uk-alert class="uk-hidden@m">
						<p><a href="#footer-nav" class="uk-link-text" uk-scroll><span uk-icon="table"></span> コンテンツ一覧</a></p>
					</div>

					<?php
					while ( have_posts() ) :
						the_post();

						the_title( '<h1 class="mag-post-title">', '</h1>' );
						the_date( 'Y.m.d D', '<div class="uk-text-right uk-text-muted uk-text-small">', '</div>' );
						the_content();


						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

						break;
					endwhile; // End of the loop.
					?>
					<?php if ( '' !== trim( $mag['footer'] ) ) : ?>
					<div class="uk-padding-small mag-notice-header">
						<?php echo $mag['footer']; ?>
					</div>
					<?php endif; ?>
				</main>
			</div>
		</div>
		<!-- sidebar -->
		<div class="uk-visible@m uk-background-default uk-margin-left">
			<!-- 960px以下で非表示 -->
			<div class="sidebar-wrapper">
				<!-- 210px + 650px(コンテンツ部分) = 860 -->
				<div class="sidebar uk-padding-small" uk-sticky="width-element: .sidebar-wrapper;bottom: #clear-stickey" style="overflow-y: scroll;max-height: 100vh">
					<aside　id="secondary">
					<nav>
						<?php if ( '' !== trim( $mag['header'] ) ) : ?>
						<div class="uk-padding-small mag-notice-header">
							<?php echo $mag['header']; ?>
						</div>
						<?php endif; ?>
						<h3>コンテンツ一覧</h3>
						<ul class="uk-nav navigation">
						<?php
							echo $side_nav_content;
						?>
						</ul>
					</nav>
					</aside>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-nav uk-hidden@m uk-padding">
		<div uk-alert class="uk-hidden@m">
			<p><span uk-icon="table"></span> コンテンツ一覧</p>
		</div>
		<aside>
			<a id="footer-nav"></a>
			<nav>
				<ul class="uk-nav">
				<?php echo $side_nav_content; ?>
				</ul>
			</nav>
		</aside>
	</div>
	<div id="clear-stickey" class="uk-height-small uk-visible@m"></div>
</div>

