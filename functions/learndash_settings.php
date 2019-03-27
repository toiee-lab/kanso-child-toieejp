<?php

/**
 *
 * LearnDash の設定
 */
// サイドバーウィジェット(LearnDash Lesson用)を用意する
add_action(
	'widgets_init',
	function() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Sidebar for LD lesson', 'kanso-general' ),
				'id'            => 'sidebar-ld_lesson',
				'description'   => esc_html__( 'Add widgets here.', 'kanso-general' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
);


// サイドバーウィジェット(LearnDash Lesson用)を用意する
add_action(
	'widgets_init',
	function() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Sidebar for LD topic(work)', 'kanso-general' ),
				'id'            => 'sidebar-ld_topic',
				'description'   => esc_html__( 'Add widgets here.', 'kanso-general' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
);
