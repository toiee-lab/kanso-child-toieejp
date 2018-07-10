<?php
	
/**
 * toiee.jp 専用のショートコード
 *
 */

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

