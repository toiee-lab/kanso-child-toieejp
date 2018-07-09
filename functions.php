<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' , array('uikit') );
//	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('uikit') );
}

add_action( 'wp_enqueue_scripts', function(){
	
	$names = array('kanso-general-style', 'base-style-css');
	array_map(function($name){
		if ( wp_style_is( $name ) ) {
	        wp_dequeue_style( $name );
	    }		
	}, $names);
} ,11);

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


class Toc_Shortcode {

	private $add_script = false;
	private $atts = array();

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'toc', array( $this, 'shortcode_content' ) );
		add_action( 'wp_footer', array( $this, 'add_script' ) );
	}

	function enqueue_scripts() {
		if ( !wp_script_is( 'jquery', 'done' ) ) {
			wp_enqueue_script( 'jquery' );
		}
	}

	public function shortcode_content( $atts ) {
		global $post;

		if ( ! isset( $post ) )
			return '';

		$this->atts = shortcode_atts( array(
			'id' => '',
			'class' => 'toc',
			'title' => '目次',
			'toggle' => false,
			'opentext' => '開く',
			'closetext' => '閉じる',
			'close' => false,
			'showcount' => 2,
			'depth' => 2,
			'toplevel' => 1,
			'targetclass' => 'the_content',
			'offset' => '',
			'duration' => 'normal'
		), $atts );

		$this->atts['toggle'] = ( false !== $this->atts['toggle'] && 'false' !== $this->atts['toggle'] ) ? true : false;
		$this->atts['close'] = ( false !== $this->atts['close'] && 'false' !== $this->atts['close'] ) ? true : false;

		$content = $post->post_content;

		$headers = array();
		preg_match_all( '/<([hH][1-6]).*?>(.*?)<\/[hH][1-6].*?>/u', $content, $headers );
		$header_count = count( $headers[0] );
		$counter = 0;
		$counters = array( 0, 0, 0, 0, 0, 0 );
		$current_depth = 0;
		$prev_depth = 0;
		$top_level = intval( $this->atts['toplevel'] );
		if ( $top_level < 1 ) $top_level = 1;
		if ( $top_level > 6 ) $top_level = 6;
		$this->atts['toplevel'] = $top_level;

		// 表示する階層数
		$max_depth = ( ( $this->atts['depth'] == 0 ) ? 6 : intval( $this->atts['depth'] ) );

		$toc_list = '';
		for ( $i = 0; $i < $header_count; $i++ ) {
			$depth = 0;
			switch ( strtolower( $headers[1][$i] ) ) {
				case 'h1': $depth = 1 - $top_level + 1; break;
				case 'h2': $depth = 2 - $top_level + 1; break;
				case 'h3': $depth = 3 - $top_level + 1; break;
				case 'h4': $depth = 4 - $top_level + 1; break;
				case 'h5': $depth = 5 - $top_level + 1; break;
				case 'h6': $depth = 6 - $top_level + 1; break;
			}
			if ( $depth >= 1 && $depth <= $max_depth ) {
				if ( $current_depth == $depth ) {
					$toc_list .= '</li>';
				}
				while ( $current_depth > $depth ) {
					$toc_list .= '</li></ul>';
					$current_depth--;
					$counters[$current_depth] = 0;
				}
				if ( $current_depth != $prev_depth ) {
					$toc_list .= '</li>';
				}
				if ( $current_depth < $depth ) {
					$class = $current_depth == 0 ? ' class="toc-list"' : '';
					$style = $current_depth == 0 && $this->atts['close'] ? ' style="display: none;"' : '';
					$toc_list .= "<ul{$class}{$style}>";
					$current_depth++;
				}
				$counters[$current_depth - 1]++;
				$number = $counters[0];
				for ( $j = 1; $j < $current_depth; $j++ ) {
					$number .= '.' . $counters[$j];
				}
				$counter++;
				$toc_list .= '<li><a href="#toc' . ($i + 1) . '"><span class="contentstable-number">' . $number . '</span> ' . $headers[2][$i] . '</a>';
				$prev_depth = $depth;
			}
		}
		while ( $current_depth >= 1 ) {
			$toc_list .= '</li></ul>';
			$current_depth--;
		}

		$html = '';
		if ( $counter >= $this->atts['showcount'] ) {
			$this->add_script = true;

			$toggle = '';
			if ( $this->atts['toggle'] ) {
				$toggle = ' <span class="toc-toggle">[<a class="internal" href="javascript:void(0);">' . ( $this->atts['close'] ? $this->atts['opentext'] : $this->atts['closetext'] ) . '</a>]</span>';
			}

			$html .= '<div' . ( $this->atts['id'] != '' ? ' id="' . $this->atts['id'] . '"' : '' ) . ' class="' . $this->atts['class'] . '">';
			$html .= '<p class="toc-title">' . $this->atts['title'] . $toggle . '</p>';
			$html .= $toc_list;
			$html .= '</div>' . "\n";
		}

		return $html;
	}

	public function add_script() {
		if ( !$this->add_script ) {
			return false;
		}

		$class = $this->atts['class'];
		$offset = is_numeric( $this->atts['offset'] ) ? (int)$this->atts['offset'] : - 1;
		$duration = is_numeric( $this->atts['duration'] ) ? (int)$this->atts['duration'] : '"' . $this->atts['duration'] . '"';
		$targetclass = trim( $this->atts['targetclass'] );
		if ( $targetclass == '' ) {
			$targetclass = get_post_type();
		}
		$targetclass = ".$targetclass :header";
		$opentext = $this->atts['opentext'];
		$closetext = $this->atts['closetext'];
		?>
<script type="text/javascript">
(function ($) {
  var offset = <?php echo $offset; ?>;
  var idCounter = 0;
  $("<?php echo $targetclass; ?>").each(function () {
    idCounter++;
    this.id = "toc" + idCounter;
  });
  $(".<?php echo $class; ?> a[href^='#']").click(function () {
    var href = $(this).attr("href");
    var target = $(href === "#" || href === "" ? "html" : href);
    var h = (offset === -1 ? $("#wpadminbar").height() + $(".navbar-fixed-top").height() : offset);
    var position = target.offset().top - h - 4;
    $("html, body").animate({scrollTop: position}, <?php echo $duration; ?>, "swing");
    return false;
  });
  $(".toc-toggle a").click(function () {
    var tocList = $(this).parents(".<?php echo $class; ?>").children(".toc-list");
    if (tocList.is(":hidden")) {
      tocList.show();
      $(this).text("<?php echo $closetext; ?>");
    } else {
      tocList.hide();
      $(this).text("<?php echo $opentext; ?>");
    }
  });
})(jQuery);
</script>
		<?php
	}

}

new Toc_Shortcode();


//メニューの項目をログイン状態とログインしていない状態でコントロールする（CSSでコントールできるように）
add_action('wp_head','jg_user_nav_visibility');
function jg_user_nav_visibility() {

    if ( is_user_logged_in() ) {
        $output="<style> .nav-login { display: none; } </style>";
    } else {
        $output="<style> .nav-account { display: none; } </style>";
    }

    echo $output;
}


// ----------------------------
// Hero Knowlege Base用
// ----------------------------

// アイコンをサポート
add_theme_support( 'ht-kb-category-icons' );

// サイドバーウィジェットを用意する
add_action( 'widgets_init', function(){
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar for KB', 'kanso-general' ),
		'id'            => 'sidebar-kb',
		'description'   => esc_html__( 'Add widgets here.', 'kanso-general' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );	
} );

// サイドバーウィジェットを用意する
add_action( 'widgets_init', function(){
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar for KB Category', 'kanso-general' ),
		'id'            => 'sidebar-kb-cat',
		'description'   => esc_html__( 'Add widgets here.', 'kanso-general' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );	
} );

// サイドバーウィジェット(LearnDash Lesson用)を用意する
add_action( 'widgets_init', function(){
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar for LD lesson', 'kanso-general' ),
		'id'            => 'sidebar-ld_lesson',
		'description'   => esc_html__( 'Add widgets here.', 'kanso-general' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );	
} );


// サイドバーウィジェット(LearnDash Lesson用)を用意する
add_action( 'widgets_init', function(){
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar for LD topic(work)', 'kanso-general' ),
		'id'            => 'sidebar-ld_topic',
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

// admin bar
add_action('admin_bar_menu', function($wp_admin_bar){
	$wp_admin_bar->remove_menu( 'wp-logo' );
	$wp_admin_bar->remove_menu( 'updates' );
	$wp_admin_bar->remove_menu( 'view' );
	$wp_admin_bar->remove_menu( 'new-content' );
	$wp_admin_bar->remove_menu( 'my-account' );
}, 201);


// といてらイベントテーブル出力
add_shortcode('toiee_event', function ( $atts, $content = null ) {
	
    extract( shortcode_atts( array(
        'class' => 'uk-table uk-table-striped',
    ), $atts ) );

	// データの解析、配列にする    
    $events = array();
	$tmparr = explode( "===" , wp_strip_all_tags($content) );
	foreach($tmparr as $dat){
		
		preg_match_all("/(.*?):(.*)/", $dat, $matches);
		
		$evt = array();		
		foreach($matches[1] as $i => $v)
		{
			$evt[ trim($v) ] = trim( $matches[2][$i] );
		}
		$events[] = $evt;
	}
	
	$table = 
'<table class="uk-table uk-table-striped">
    <thead>
        <tr>
            <th>日時</th>
            <th>内容</th>
            <th>ファシリテーター</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
';

	foreach( $events as $e )
	{
		$s_tag = ''; $e_tag = ''; $expire = false;

		//日付をチェックし、打ち消し線を設置
		$e_time = strtotime( $e[ 'date' ] );
		if( $e_time < ( time() - 24*60*60 ) ){
			$s_tag = '<del class="uk-text-muted">';
			$e_tag = '</del>';
			$url = '終了しました';
		}
		else{
			$s_tag = '';
			$e_tag = '';
			$url = '<a href="'.$e['url'].'" class="uk-button uk-button-primary uk-button-small" target="_blank">詳細</a>';
		}
		
		$week = ['日', '月', '火', '水', '木', '金', '土'];
		$w = $week[ date('w', $e_time) ];
		$date = date( "Y年n月j日($w)", $e_time );
		
		$table .= "
        <tr>
        	<td>{$s_tag}{$date}<br>{$e['time']}{$e_tag}</td>
        	<td>{$s_tag}{$e['title']}{$e_tag}</td>
        	<td>{$s_tag}{$e['lft']}{$e_tag}</td>
        	<td>{$url}</td>
        </tr>
";
		
	}
	
	$table .= 
'	</tbody>
</table>';

	    
    return $table;
});


// といリブイベント一覧
add_shortcode('toiee_lib_list', function ( $atts, $content = null ) {
	
    extract( shortcode_atts( array(
        'class' => 'uk-table uk-table-striped',
    ), $atts ) );
	// データの解析、配列にする    
    $data = array();
	$tmparr = explode( "===" , wp_strip_all_tags($content) );
	foreach($tmparr as $dat){
		
		preg_match_all("/(.*?):(.*)/", $dat, $matches);
		
		$d = array();		
		foreach($matches[1] as $i => $v)
		{
			$d[ trim($v) ] = trim( $matches[2][$i] );
		}
		$data[] = $d;
	}
	
	$table = 
'<p class="uk-text-right" style="font-size:0.7rem;">※ <span uk-icon="icon: calendar;"></span>進行表、 <span uk-icon="icon: file-edit;"></span>受講者資料</p>
<table class="uk-table uk-table-striped uk-table-middle">
    <thead>
        <tr>
            <th>タイトル</th>
            <th>ver</th>
            <th>内容</th>
            <th><span uk-icon="icon: calendar;"></span></th>
            <th><span uk-icon="icon: file-edit;"></span></th>
        </tr>
    </thead>
    <tbody>
';
	foreach( $data as $d )
	{
		$lft_text = preg_match('/^https:/', $d['lft']) ? "<a href=\"{$d['lft']}\" download=\"lft-text.pdf\" target=\"_blank\" class=\"\" uk-icon=\"icon: download\"></a>" : "--";
		
		$table .= "
        <tr>
        	<td style=\"font-size:0.8rem;\"><a href='{$d['url']}'>{$d['title']}</a></td>
        	<td style=\"font-size:0.8rem;\">{$d['ver']}</td>
        	<td style=\"font-size:0.8rem;\">{$d['desc']}</td>
        	<td><span style=\"font-size:0.8rem;\">{$lft_text}</span></td>
        	<td><a href=\"{$d['user']}\" download=\"materials.pdf\" target=\"_blank\" class=\"\" uk-icon=\"icon: download\"></a></td>
        </tr>
";
		
	}
	
	$table .= 
'	</tbody>
</table>
';
	    
    return $table;
});



/* ========================================================================
 * //! WooCommerce の登録フォーム変更
 *
 */	

 add_action( 'woocommerce_register_form_start', function () {?>
		<p class="form-row form-row-first">
			<label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
			<input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
		</p>
		<p class="form-row form-row-last">
			<label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?></label>
			<input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
		</p>		
		<div class="clear"></div>
       <?php
 } );
 
/**

* register fields Validating.

*/
add_action( 'woocommerce_register_post', function ( $username, $email, $validation_errors ) {
	if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
		$validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
	
	}
	return $validation_errors;
}, 10, 3 );


/**
* Below code save extra fields.
*/
add_action( 'woocommerce_created_customer', function ( $customer_id ) {
      if ( isset( $_POST['billing_first_name'] ) ) {
             //First name field which is by default
             update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
             // First name field which is used in WooCommerce
             update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
      }
      if ( isset( $_POST['billing_last_name'] ) ) {
             // Last name field which is by default
             update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
             // Last name field which is used in WooCommerce
             update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
      }

} );
