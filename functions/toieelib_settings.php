<?php

/*
 * toiee lib のための設定
 *
 *
 */



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



// といリブのレジュメのための「文字列置換」ビデオを表す画像のところに、video を埋め込む
add_filter('the_content', 'replace_video_timer', 12);
function replace_video_timer( $content )
{	
	global $template;
	$temp_name = basename($template);
	
	if( $temp_name == 'page-sidebar-lib.php' )
	{
	
		$content = preg_replace_callback(
			'|<figure(.*?)<img(.*?)"(.*?)"(.*?)>(.*?)</figure>|s', 
			function($matches){
				
				if( preg_match('|id=(.*)$|', $matches[3], $sub_matches) )
				{
					//var_dump( $sub_matches[1] );
					return '<div style="padding:56.25% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/'.$sub_matches[1].'" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>';
				}
				else if( preg_match('|t=(.*)$|', $matches[3], $sub_matches) ){
					
					list($t1, $t2, $t3) = explode('-', $sub_matches[1]);
					return '<div class="video"><iframe  src="https://d.toiee.jp/timer/#t1='.$t1.'&t2='.$t2.'&t3='.$t3.'&m=Start%20work!" frameborder="0"></iframe></div>';				
				}
				else{
					return $matches[0];
				}
			},
			$content);
			
			$content .= '--';
	}
	
	return $content;
	
}