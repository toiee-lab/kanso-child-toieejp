<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' , array('uikit') );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
}

function kns_get_template() {
	
	if( is_page_template( 'page-sidebar-lib.php' ) ){
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


