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
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if( is_tax('scrum') || get_post_type() == 'scrum_post' ){ echo '<meta name="robots" content="noindex" />'; } ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="uk-offcanvas-content">
			<?php 
				
				$kns_header_text_color_cls = get_option('kanso_general_options_hcolor_front', 'light');
				$kns_color_set = kns_get_color_set( get_option( 'kanso_general_options_colors', 'simple' ) );
				
/*				if( is_front_page() ) { // トップページだけ、ナビをヘッダー画像の上にのせる
					$uk_sticky_add = 'cls-inactive: uk-navbar-transparent uk-'.$kns_header_text_color_cls.';';
					$uk_nav_color  = $kns_header_text_color_cls;
				}
				else {
*/					
					$uk_sticky_add = '';
					$uk_nav_color  = $kns_color_set['color'];
// 				}
			?>	
		    <div id="kns-head-nav" uk-sticky="animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky uk-<?php echo $kns_color_set['color'];?>; <?php echo $uk_sticky_add;?> show-on-up: true" class="uk-<?php echo $uk_nav_color; ?>">
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
                                    /*
                                     * レスポンシブで消えるナビ
                                     */
									wp_nav_menu( array(
										'theme_location' => 'menu-1',
										'menu_id'        => 'primary-menu',
										'menu_class'     => 'uk-navbar-nav uk-visible@m',
										'container'      => false,
									) );
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
									/*
									 * 固定で表示するメニュー
									 */
									// ハンバーガーメニューの表示、非表示
									// - page-sidebar.php が指定してある場合は非表示
									// - デフォルトレイアウトでサイドバーが指定されている
									// サイドバーが設定してある場合
									$uk_visible = '';
									if ( kns_get_template() == 'sidebar' ) {
										$uk_visible = 'uk-hidden@m';
									}
								?>
			                    <ul class="uk-navbar-nav <?php echo $uk_visible; ?>"  data-debug="<?php echo $_SERVER['HTTP_USER_AGENT']; ?>">
			                        <li><a href="#sidebar" uk-toggle><span uk-navbar-toggle-icon></span> <span class="uk-margin-small-left">Menu</span></a></li>
			                    </ul>			                    
			                </div>
		                </div>
		            </div>
		        </nav>
		    </div>
   			<?php 
				if( is_front_page() ):  // トップページだけ、ナビをヘッダー画像の上にのせる
					
					$slide_header_path = get_stylesheet_directory().'/template-parts/slide-header.php';
					$ua = mb_strtolower($_SERVER['HTTP_USER_AGENT']);  //すべて小文字にしてユーザーエージェントを取得
					if (strpos($ua,'msie') !== false || strpos($ua,'trident') !== false) {
						$is_ie = true;
					}
					else{
					    $is_ie = false;
                    }
					
					if(	file_exists( $slide_header_path ) && !$is_ie ){
						require( $slide_header_path );
					}
					else
					{
			?>
		<div id="kns-head" class="uk-background-cover uk-background-center-center">
		    <div id="kns-header" class="uk-<?php echo $kns_header_text_color_cls; ?>" style="">
			    <div id="kns-header-text" class="uk-padding-small">
				    <h1 id="kanso_general_options_htitle"><?php echo get_option( 'kanso_general_options_htitle' ); ?></h1>
				    <h2 id="kanso_general_options_hsubtitle"><?php echo get_option( 'kanso_general_options_hsubtitle' ); ?></h2>
			    </div>
		    </div>
		</div><!-- #kns-head -->
		    <?php 
			    	}
			    endif; ?>
