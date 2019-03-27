<?php


// ----------------------------
// Hero Knowlege Base用
// ----------------------------
// アイコンをサポート
add_theme_support( 'ht-kb-category-icons' );

// サイドバーウィジェットを用意する
add_action(
	'widgets_init',
	function() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Sidebar for KB', 'kanso-general' ),
				'id'            => 'sidebar-kb',
				'description'   => esc_html__( 'Add widgets here.', 'kanso-general' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
);

// サイドバーウィジェットを用意する
add_action(
	'widgets_init',
	function() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Sidebar for KB Category', 'kanso-general' ),
				'id'            => 'sidebar-kb-cat',
				'description'   => esc_html__( 'Add widgets here.', 'kanso-general' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
);

