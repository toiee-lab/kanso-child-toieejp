<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 *
 * Template Name: トライアルクーポン
 */

get_header(); ?>
	<div class="uk-container uk-container-small uk-background-default main-content" style="min-height: 500px;">
		<?php
		while ( have_posts() ) :
			the_post();

			the_title( '<h1 class="main-title">', '</h1>' );
			the_subtitle( '<h2 class="main-subtitle">', '</h2>' );

			the_content();

			/* クーポンコード */
			if ( is_user_logged_in() ) {
				/* クーポンコード履歴をチェック */
				if ( isset( $_POST['_wpnonce'] ) ) {
					check_admin_referer( 'register-coupon_' . get_the_ID() );

					$days   = get_field( 'wc4t_trial_days' );
					$expire = date( 'Y-m-d 23:59:59', time() + $days * 24 * 60 * 60 );

					$product_id   = get_field( 'wc4t_product' );
					$product      = wc_get_product( $product_id );
					$product_name = $product->get_name();
					$my_lib_url   = get_field( 'wcmylib_url', $product_id );

					$user_id   = get_current_user_id();
					$user_meta = array_map(
						function ( $a ) {
							return $a[0];
						},
						get_user_meta( $user_id )
					);
					$user_name = $user_meta['last_name'] . ' ' . $user_meta['first_name'];

					$args = array(
						'post_name'   => $user_id . '-' . $product_id,
						'post_title'  => $user_name . ' - ' . $product_name . ' - ' . $expire,
						'post_status' => 'publish',
						'post_type'   => 'wc4t_trialcpn_order',
					);
					$pid  = wp_insert_post( $args );

					update_field( 'wc4t_user', get_current_user_id(), $pid );
					update_field( 'wc4t_product', $product_id, $pid );
					update_field( 'wc4t_expire', $expire, $pid );
					update_field( 'wc4t_coupon', get_the_ID(), $pid );
					?>
					<p>お申し込み、ありがとうございます。<br>
					以下のページにて、ご覧になれます。</p>
					<ul>
						<li><a href="<?php echo $my_lib_url;?>"><?php echo $product_name; ?></a></li>
						<li><a href="/my-account/my-library/">マイライブラリ</a></li>
					</ul>

					<?php
				} else {
					$pid    = get_field( 'wc4t_product' );
					$args   = array(
						'post_type'  => 'wc4t_trialcpn_order',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wc4t_user',
								'value' => get_current_user_id(),
							),
							array(
								'key'   => 'wc4t_product',
								'value' => $pid,
							),
						),
					);
					$trials = get_posts( $args );

					if ( 0 === count( $trials ) ) {
						?>
						<p>クーポンの利用を開始しますか？</p>
						<form method="post" action="<?php echo get_permalink(); ?>">
							<button type="submit" class="uk-button uk-button-primary">開始する</button>
							<?php wp_nonce_field( 'register-coupon_' . get_the_ID() ); ?>
						</form>
						<?php
					} else {

						$my_lib_url = false;
						foreach( $trials as $t ) {
							$expire = get_field( 'wc4t_expire', $t->ID );
							if ( time() < strtotime( $expire ) ) {
								$product_obj = wc_get_product( get_field( 'wc4t_product', $t->ID ) );
								$my_lib_url  = get_field( 'wcmylib_url', $product_obj->get_id() );
								break;
							}
						}
						if ( $my_lib_url ) {
							?>
					<p>以下のページにて、ご覧になれます。<br>
					（<?php echo $expire; ?> まで）</p>
					<ul>
						<li><a href="<?php echo $my_lib_url;?>"><?php echo $product_obj->get_name(); ?></a></li>
						<li><a href="/my-account/my-library/">マイライブラリ</a></li>
					</ul>
							<?php
						} else {
							?>
							<div class="uk-alert-danger" uk-alert><p>この商品のトライアル（お試し）は、ご利用済みです。<br>ご利用いただけません。</p></div>
							<?php
						}
					}
				}
			} else {
				?>
				<p>クーポンを利用するには、<a href="#" uk-toggle="target: #modal_login_form">ユーザーログイン(登録もこちら)</a>をしてください。</p>
				<?php
			}

						// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>
	</div><!-- .main-content -->


<?php
get_sidebar();
get_footer();
