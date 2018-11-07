<?php

// これって、なんとためだったかなー・・・
// 多分、同じ css を２度読んでいるから抑制するためだったと思うが・・・
add_action( 'wp_enqueue_scripts', function(){

	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' , array('uikit') );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
	
	$names = array('kanso-general-style', 'base-style');
	array_map(function($name){
		if ( wp_style_is( $name ) ) {
	        wp_dequeue_style( $name );
	    }		
	}, $names);

} ,11);

//メニューの項目をログイン状態とログインしていない状態でコントロールする（CSSでコントールできるように）
add_action('wp_head',function () {

    if ( is_user_logged_in() ) {
        $output="<style> .nav-login { display: none; } </style>";
    } else {
        $output="<style> .nav-account { display: none; } </style>";
    }

    echo $output;
});


// 管理画面を見やすく修正
add_action('admin_bar_menu', function($wp_admin_bar){
	$wp_admin_bar->remove_menu( 'wp-logo' );
	$wp_admin_bar->remove_menu( 'updates' );
	$wp_admin_bar->remove_menu( 'view' );
	$wp_admin_bar->remove_menu( 'new-content' );
	$wp_admin_bar->remove_menu( 'my-account' );
}, 201);

//テキストウィジェットでショートコードを使用する（目次のショートコードが使えるように）
add_filter('widget_text', 'do_shortcode');


//テンプレートの切り替え設定
function kns_get_template() {
	
	if( is_page_template( 'page-sidebar-lib.php' ) ){
		return 'sidebar';
	}

	if( is_singular( 'sfwd-lessons' ) ){
		return 'sidebar';
	}

	if( is_singular( 'sfwd-topic' ) ){
		return 'sidebar';
	}
		
	if( is_page_template( 'page-sidebar.php' ) ){
		return 'sidebar';
	}
	
	if( is_page_template( 'page-content.php' ) ){
		return 'content';
	}
	
	//デフォルトの場合
	
	if( is_front_page() ){
		return 'content';   //指定がなければ、コンテンツのみを採用する
	}
	
	if( is_home() ){
		return 'content';   //投稿ページはサイドバーはないので、コンテンツレイアウトとして設定(headerで必要)
	}
	
	// デフォルトレイアウト値を戻す
	$options = get_option('kns_options');
	if( isset( $options['kns_default_layout'] ) && ($options['kns_default_layout'] == 'sidebar') ){
		return 'sidebar';
	}
	else{
		return 'content';
	}
}


// heroic knowledge base setting
require_once( 'functions/kb_settings.php' );

// toiee lib 関連の設定
require_once( 'functions/toieelib_settings.php' );

// learndash 関連の設定
require_once( 'functions/learndash_settings.php' );


// toiee.jp 専用のショートコードを格納
require_once( 'functions/toiee_shortcodes.php' );


// woocommerce の動作変更
require_once( 'functions/woocommerce_settings.php' );


if(!function_exists('_log')){
	function _log($message, $prefix = '') {
		$prefix = ( $prefix == '' ) ? '' : $prefix."\n";
		if (WP_DEBUG === true) {
			if (is_array($message) || is_object($message)) {
				error_log($prefix . print_r($message, true));
			} else {
				error_log($prefix . $message);
			}
		}
	}
}
