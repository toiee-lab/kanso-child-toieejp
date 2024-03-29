<?php
/**
 * 様々な設定を行う
 *
 * @package hoge
 */

/*
 * これって、なんとためだったかなー・・・
 * 多分、同じ css を２度読んでいるから抑制するためだったと思うが・・・
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		wp_enqueue_style(
			'parent-style',
			get_template_directory_uri() . '/style.css',
			array( 'uikit' )
		);
		wp_enqueue_style(
			'child-style',
			get_stylesheet_directory_uri() . '/style.css',
			array( 'parent-style' )
		);

		$names = array( 'kanso-general-style', 'base-style' );
		array_map(
			function( $name ) {
				if ( wp_style_is( $name ) ) {
					wp_dequeue_style( $name );
				}
			},
			$names
		);
	},
	11
);

/* メニューの項目をログイン状態とログインしていない状態でコントロールする（CSSでコントールできるように） */
add_action(
	'wp_head',
	function () {

		if ( is_user_logged_in() ) {
			$output = '<style> .nav-login { display: none; } </style>';
		} else {
			$output = '<style> .nav-account { display: none; } </style>';
		}

		echo $output;
	}
);


/* 管理画面を見やすく修正 */
add_action(
	'admin_bar_menu',
	function( $wp_admin_bar ) {
		$wp_admin_bar->remove_menu( 'wp-logo' );
		$wp_admin_bar->remove_menu( 'updates' );
		$wp_admin_bar->remove_menu( 'view' );
	},
	201
);

add_action(
	'admin_menu',
	function() {
		if ( ! is_super_admin() ) {
			remove_menu_page( 'edit.php?post_type=podcast' );
			remove_menu_page( 'edit.php?post_type=gift_card' );
			remove_menu_page( 'edit.php?post_type=woocustomemails' );
			remove_menu_page( 'post_type=yith-wccos-ostatus' );
			remove_menu_page( 'edit.php?post_type=yith-wccos-ostatus' );
		}
	}
);

/* テキストウィジェットでショートコードを使用する（目次のショートコードが使えるように） */
add_filter( 'widget_text', 'do_shortcode' );


/**
 * テンプレートの切り替え設定
 */
function kns_get_template() {

	if ( is_page_template( 'page-sidebar-lib.php' ) ) {
		return 'sidebar';
	}

	if ( is_singular( 'sfwd-lessons' ) ) {
		return 'sidebar';
	}

	if ( is_singular( 'sfwd-topic' ) ) {
		return 'sidebar';
	}

	if ( is_page_template( 'page-sidebar.php' ) ) {
		return 'sidebar';
	}

	if ( is_page_template( 'page-content.php' ) ) {
		return 'content';
	}

	if ( is_front_page() ) {
		return 'content';
	}

	if ( is_home() ) {
		return 'content';
	}

	$options = get_option( 'kns_options' );
	if ( isset( $options['kns_default_layout'] ) && ( $options['kns_default_layout'] == 'sidebar' ) ) {
		return 'sidebar';
	} else {
		return 'content';
	}
}


/* heroic knowledge base setting */
require_once 'functions/kb_settings.php';

/* toiee lib 関連の設定 */
require_once 'functions/toieelib_settings.php';

/* learndash 関連の設定 */
require_once 'functions/learndash_settings.php';


/* toiee.jp 専用のショートコードを格納 */
require_once 'functions/toiee_shortcodes.php';


/*
 woocommerce の動作変更 */
/*require_once( 'functions/woocommerce_settings.php' );*/


if ( ! function_exists( '_log' ) ) {
	function _log( $message, $prefix = '' ) {
		$prefix = ( $prefix == '' ) ? '' : $prefix . "\n";
		if ( WP_DEBUG === true ) {
			if ( is_array( $message ) || is_object( $message ) ) {
				error_log( $prefix . print_r( $message, true ) );
			} else {
				error_log( $prefix . $message );
			}
		}
	}
}


// 検索結果のラベルを変更する
function kanso_get_post_label() {

	$post_type = get_post_type();
	switch ( $post_type ) {
		case 'podcast':
			$rets       = wp_get_post_terms( get_the_ID(), 'series' );
			$series     = $rets[0];
			$post_label = $series->name != '' ? $series->name : 'ポッドキャスト';
			break;

		case 'page':
			$post_label = 'ページ';
			break;

		case 'post':
			$post_label = 'ブログ';
			break;

		case 'scrum_post':
			$terms      = wp_get_post_terms( get_the_ID(), 'scrum' );
			$scrum      = $terms[0];
			$post_label = 'スクラム : ' . $scrum->name;
			break;

		default:
			$post_label = esc_html( get_post_type_object( get_post_type() )->label );
	}

	return $post_label;
}


add_filter( 'upload_mimes', function ( $mime_types ) {
	unset( $mime_types[ 'mp3|m4a|m4b' ] );
	

	$mime_types[ 'mp3' ] = 'audio/mpeg';
	$mime_types[ 'm4a' ] = 'audio/mpeg';
    $mime_types[ 'm4b' ] = 'video/mp4';

    return $mime_types;
} );

add_filter( 'the_title', function( $title ) {
	if( is_singular( array('post') ) && in_the_loop() ) {
		if( true === get_field('display_none_title') ) {
			return '';
		} else {
			return $title;
		}
	}
	
	return $title;
});

add_filter( 'the_subtitle', function( $title ) {
	if( is_singular( array('post') ) && in_the_loop() ) {
		if( true === get_field('display_none_title') ) {
			return '';
		} else {
			return $title;
		}
	}
	
	return $title;
});
