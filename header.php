<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kanso-general
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
if ( is_front_page() ) :
	?>
	<div id="kns-head" class="uk-background-cover uk-background-center-center">
		<div id="kns-head-nav-front" uk-sticky="animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky kns-navbar-sticky; cls-inactive: uk-navbar-transparent kns-navbar-top-front; show-on-up: true">
			<nav class="uk-navbar-container">
				<div class="uk-container uk-container-expand">
					<div uk-navbar>
						<div class="uk-navbar-left uk-visible@m">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" itemprop="url">
								<img src="https://toiee.jp/wp-content/uploads/2018/03/toiee-lab-logo-1.svg" class="custom-logo" alt="toiee Lab" itemprop="logo" scale="0">
							</a>
						</div>
						<div class="uk-navbar-left uk-hidden@m" style="margin-left:0">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" itemprop="url">
								<img src="https://toiee.jp/wp-content/uploads/2019/03/toiee-logo.png" class="custom-logo" alt="toiee Lab" itemprop="logo" scale="0">
							</a>
						</div>
						<div class="uk-navbar-right">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'menu-1',
									'menu_id'        => 'primary-menu',
									'menu_class'     => 'uk-navbar-nav uk-visible@m',
									'container'      => false,
									'fallback_cb'    => '',
								)
							);
							?>
							<ul class="uk-navbar-nav">
								<li><a href="<?php echo esc_url( home_url( '/' ) );?>?s="><span uk-icon="search"></span></a></li>
								<?php
								/*
								 * ログイン、ログアウトで切り替える
								 */
								if ( is_user_logged_in() ) :
									?>
									<li><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' )); ?>my-library/">マイアカウント</a></li>
								<?php else: ?>
									<li><a href="#" uk-toggle="target: #modal_login_form">ログイン</a></li>
								<?php endif; ?>
							</ul>
							<?php
							if ( ! is_user_logged_in() ) {
								echo get_popup_login_form();
							}
							?>
							<?php
							if ( kns_get_template() === 'sidebar' ) {
								$uk_hidden        = 'uk-hidden@m';
								$nav_height_dummy = 'uk-visible@m';
							} else {
								$uk_hidden        = '';
								$nav_height_dummy = 'uk-hidden';
							}
							?>
							<ul class="uk-navbar-nav <?php echo esc_html( $uk_hidden ); ?>">
								<li class=""><a href="#sidebar" uk-toggle><span uk-navbar-toggle-icon></span> <span class="uk-margin-small-left">Menu</span></a></li>
							</ul>
							<ul class="uk-navbar-nav <?php echo esc_html( $nav_height_dummy ); ?>">
								<li class=""><a href="#sidebar" uk-toggle><span class="uk-margin-small-lef"></span></a></li>
							</ul>
						</div>
					</div>
				</div>
			</nav>
		</div>
		<div id="kns-header">
			<div id="kns-header-text" class="uk-padding-small">
				<h1 id="kanso_general_options_htitle"><?php echo esc_html( get_option( 'kanso_general_options_htitle' ) ); ?></h1>
				<h2 id="kanso_general_options_hsubtitle"><?php echo esc_html( get_option( 'kanso_general_options_hsubtitle' ) ); ?></h2>
			</div>
		</div>
	</div>
	<?php
else :
	?>
	<div id="kns-head-nav" uk-sticky="animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-inactive: kns-navbar-top; cls-active: uk-navbar-sticky kns-navbar-sticky ; show-on-up: true">
		<nav class="uk-navbar-container">
			<div class="uk-container uk-container-expand">
				<div uk-navbar>
					<!-- ロゴ（レスポンシブに対応） -->
					<div class="uk-navbar-left uk-visible@m">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" itemprop="url">
							<img src="https://toiee.jp/wp-content/uploads/2018/03/toiee-lab-logo-1.svg" class="custom-logo" alt="toiee Lab" itemprop="logo" scale="0">
						</a>
					</div>
					<div class="uk-navbar-left uk-hidden@m" style="margin-left:0">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" itemprop="url">
							<img src="https://toiee.jp/wp-content/uploads/2019/03/toiee-logo.png" class="custom-logo" alt="toiee Lab" itemprop="logo" scale="0">
						</a>
					</div>
					<!-- ナビの右側 -->
					<div class="uk-navbar-right">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'menu_class'     => 'uk-navbar-nav uk-visible@m',
								'container'      => false,
								'fallback_cb'    => '',
							)
						);
						?>
						<ul class="uk-navbar-nav">
							<li><a href="<?php echo esc_url( home_url( '/' ) );?>?s="><span uk-icon="search"></span></a></li>
							<?php
							/*
							 * ログイン、ログアウトで切り替える
							 */
							if( is_user_logged_in() ):
								?>
								<li><a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' )); ?>my-library/">マイアカウント</a></li>
							<?php else: ?>
								<?php echo get_popup_login_form(); ?>
								<li><a href="#" uk-toggle="target: #modal_login_form">ログイン</a></li>
							<?php endif; ?>
						</ul>
						<?php
						if ( kns_get_template() === 'sidebar' ) {
							$uk_hidden        = 'uk-hidden@m';
							$nav_height_dummy = 'uk-visible@m';
						} else {
							$uk_hidden        = '';
							$nav_height_dummy = 'uk-hidden';
						}
						?>
						<ul class="uk-navbar-nav <?php echo esc_html( $uk_hidden ); ?>">
							<li class=""><a href="#sidebar" uk-toggle><span uk-navbar-toggle-icon></span> <span class="uk-margin-small-left">Menu</span></a></li>
						</ul>
						<ul class="uk-navbar-nav <?php echo esc_html( $nav_height_dummy ); ?>">
							<li class=""><a href="#sidebar" uk-toggle><span class="uk-margin-small-lef"></span></a></li>
						</ul>
					</div>
				</div>
			</div>
		</nav>
	</div>
	<?php
endif;
?>
<!-- end header -->



