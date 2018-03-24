<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' , array('uikit') );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
}

// といリブ用のサイドバーウィジェットを用意する
add_action( 'widgets_init', function(){
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar for といリブ', 'kanso-general' ),
		'id'            => 'sidebar-lib',
		'description'   => esc_html__( 'Add widgets here.', 'kanso-general' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );	
} );

//テキストウィジェットでショートコードを使用する（目次のショートコードが使えるように）
add_filter('widget_text', 'do_shortcode');