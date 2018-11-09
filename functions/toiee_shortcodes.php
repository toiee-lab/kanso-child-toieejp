<?php
	
/**
 * toiee.jp 専用のショートコード
 *
 */
 
 //! 商品プレビューを出すためのショートコード（二回め！）
 add_shortcode( 'toiee_preview_list' , function ( $atts, $content = null ) {
	 
	extract( 
		shortcode_atts (
			array(
				'series' => '',
				'open' => '1-4',
				'free' => '5-7'
			),
			$atts
		)
	); 
	
	// parameter check
	$no = array();
	foreach( array('open', 'free' ) as $key ) {
		if( preg_match('/^([0-9]+)-([0-9]+)$/', $$key , $matches) ) {
			$no[ $key ]['s'] = $matches[1];
			$no[ $key ]['e'] = $matches[2];
		}
		else{
			return '<p>invalid number of '.$$key.'. like this (1-5)</p>';
		}
	}
	
//	return "<pre>" .print_r($no, true). "</pre>";
	
	
	// termのチェック
	if( $series == '' ){ return "<p>please set series slug or id</p>"; }
	
	if( is_numeric( $series ) ) {
		$term = get_term_by('id', $series, 'series', ARRAY_A);
	}
	else {
		$term = get_term_by('slug', $series, 'series', ARRAY_A);
		if( is_wp_error( $term ) ){
			$term = get_term_by('name', $series, 'series', ARRAY_A);
		}
	}
	
	if( is_wp_error( $term ) ){ return "<p>not found series</p>"; }
	
//	return "<pre>" .print_r($term, true). "</pre>";
	
	// post の取得
	$posts = get_posts( array(
		'post_type' => 'podcast',
		'posts_per_page'   => -1,
		'order'     => 'ASC',
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'series',
				'field'    => 'term_id',
				'terms'    => $term['term_id'],
			),
		)
	));
	
//	return "<pre>" .print_r( $posts ,true). "</pre>";
	
	global $wcr_ssp;
	global $ss_podcasting;
	
	
	// user check
	if( is_user_logged_in() ) {
		$user_logined = true;
	}
	else {
		$user_logined = false;
		$modal = $wcr_ssp->get_wc_login_form_modal();	
	}
	
	$cnt = 1; $content = '';
	foreach( $posts as $e ) {
		
		if( $no['open']['s'] <= $cnt && $cnt <= $no['open']['e'] ) {
			$status = 'open';
		}
		else if( $no['free']['s'] <= $cnt && $cnt <= $no['free']['e'] ) {
			if( $user_logined ){
				$status = 'open';
			}
			else {
				$status = 'free';
			}
		}
		else{
			$status = 'close';
		}
		
		// get type (audio or video)
        $episode_type = $ss_podcasting->get_episode_type( $e->ID );
                        		
		switch ( $status ) {
			
			// プレイヤーを表示
			case 'open':
								// get audio file
				$audio_file = $ss_podcasting->get_enclosure( $e->ID );
                if ( get_option( 'permalink_structure' ) ) {
                    $enclosure = $ss_podcasting->get_episode_download_link( $e->ID );
                } else {
                    $enclosure = $audio_file;
                }		
                $enclosure = apply_filters( 'ssp_feed_item_enclosure', $enclosure, $e->ID );
            
                if ( $episode_type == 'audio' ) {
					$shortcode = '[audio src="'.$enclosure.'" /]';
				}
				else {
					$shortcode = '[video src="'.$audio_file.'" /]';
				}
				
				$content .= "<h4>{$e->post_title}</h4>\n"
						. do_shortcode( $shortcode )
						. apply_filters( 'the_content', $e->post_content );
			
				break;
				
			
			
			
			// ダミーを表示
			case 'free':
				$content .= "<h4>{$e->post_title}</h4>\n"
						. $wcr_ssp->get_dummy_player( $episode_type )
						. '<p style="font-size:0.8rem;">無料登録(あるいはログイン)することで、ご覧いただけます。<br>
						<a href="#" uk-toggle="target: #modal_login_form">無料登録する or ログインする場合は、こちらをクリック</a></p>';
			
				break;
				
			default:
				$content .= "<p>{$e->post_title}</p>\n";
		}
		
		$content .= "<hr>";

		$cnt++;
	}
	
	return $content.$modal;
	
} );
 
 
 
 
 //! Podcastの一覧を出力する
 // [toiee_list_series] で、ポケてらを検索して表示
 // [toiee_list_series search="^耳デミー"] で、耳デミーを検索して表示
 // 一応、num="3" とかで適当にします
 add_shortcode( 'toiee_list_series', function ( $atts, $content = null ) {
	 
	extract( 
		shortcode_atts (
			array(
				'search' => '^ポケてら',
				'num' => 4
			),
			$atts
		)
	);
	 
	$terms = get_terms( 'series', array( 'hide_empty=0' ) );
	
	if( is_wp_error( $terms ) ){
		return 'this is error : '. print_r($terms , true);
	}
	
	//マッチするものだけ残す
	$terms = array_filter( $terms, function( $term ) use( $search ) { return preg_match( "/{$search}/", $term->name ); }  );
	
	$terms_a = array();
	$content = '<div class="uk-grid-small uk-child-width-1-'.$num.'@s uk-flex-left uk-text-center" uk-grid>'."\n";
	foreach( $terms as $k=>$term ){
		
		$name = $term->name;
		$plink = get_term_link( $term->term_id, 'series' );
		$series_image = get_option( 'ss_podcasting_data_image_' . $term->term_id, 'no-image' );
		$terms_a[ ] = array(
			'name' => $name,
			'link' => $plink,
			'img'  => $series_image,
		);
		
		$content .= '<div><a href="'.$plink.'" title="'.$name.'" class="uk-display-block uk-box-shadow-small"><img src="'.$series_image.'" alt="'.$name.'"></a></div>'."\n";
		
	}
	$content .= '</div>';
	
	return  $content;
	 
 } );
 
 
 //! 商品一覧画像を出力する
 // [toiee_list_product cat="耳デミー"] で「耳デミー」の一覧を出す
 // [toiee_list_product cat="ポケてら"] で「ポケてら」の一覧を出す
 add_shortcode( 'toiee_list_product', function ( $atts, $content = null ) {
	extract( 
		shortcode_atts (
			array(
				'cat' => '耳デミー',
				'num' => 4
			),
			$atts
		)
	);	
	
	$products = array();
	
	$$term = get_term_by('name', $cat, 'product_cat' );
	
	$args = array(
		'post_type' => 'product',
		'orderby'   => 'title',
		'tax_query' => array(
			array(
				'taxonomy'  => 'product_cat',
				'field'     => 'id',
				'terms'     => $$term->term_id
			),
		),
		'posts_per_page' => -1,
		'post_status' => 'publish'
	);
	$featured_query = new WP_Query( $args );
	
	$content = '<div class="uk-grid-small uk-child-width-1-'.$num.'@s uk-flex-left uk-text-center" uk-grid>'."\n";	
	while ($featured_query->have_posts()) :
		$featured_query->the_post();
		$product = get_product( $featured_query->post->ID );
		// By doing this, we will be able to fetch all information related to single WooCommerce Product
		
		$name = $product->get_name();
		$img  = get_the_post_thumbnail_url( $product->get_id(), 'full' );
		$url  = get_permalink( $product->get_id() );
		
		$products[] = array(
			'name' => $name,
			'img' => $img,
			'url' => $url,
			
		);
		
		$content .= '<div><a href="'.$url.'" title="'.$name.'"><img src="'.$img.'" alt="'.$name.'" class="uk-display-block uk-box-shadow-small"></a></div>'."\n";
		
	endwhile;
	wp_reset_query();
	$content .= '</div>';
	
	return $content;
	 
 } );
 

//! といてらイベントテーブル出力
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


//! といリブ　教材一覧
add_shortcode('toiee_lib_list', function ( $atts, $content = null ) {
	
    extract( shortcode_atts( array(
        'class'  => 'uk-table uk-table-striped',
		'id'     => '',
		'sub_id' => '',
		'mem_id' => '',
		'wcr_id' => '',
		'lp_url' => 'https://toiee.jp/lp/toieelib-teacher-plan/',

    ), $atts ) );
    
    // アクセス制限パラメタを整理
	// 複数のidが指定されていることを想定
	$product_ids = explode(',', $id);
	$sub_ids = explode(',', $sub_id);
	$mem_ids = explode(',', $mem_id);

	// WC Restrict Post type からデータを取り出して、$product_ids, $sub_ids, $mem_ids に加える
	if( $wcr_id != '' && is_numeric($wcr_id) ){
		$wcr_dat  = get_post_meta($wcr_id, 'wcr_param', true);
		$wcr_arr = unserialize( $wcr_dat );
		
		$tmp_arr = array( 'product_ids', 'sub_ids', 'mem_ids' );
		foreach($tmp_arr as $v){
			$$v = array_merge($$v, $wcr_arr['wcr_'.$v] );
		}
	}

    
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
	
	
	// アクセスのチェック
	global $wcr_content;
	$access = $wcr_content->has_access($product_ids, $sub_ids, $mem_ids);

	// もしなかったら、鍵アイコンにして、クリックすると申し込みのモーダルが表示される
	// error message がある場合、モーダルウィンドウを表示する
	ob_start();
		wc_print_notices();
		$wc_notices = ob_get_contents();
	ob_end_clean();
	
	if( $wc_notices != ''){
		$js = <<<EOD
<script>			
el = document.getElementById('toieelib-list-modal');
UIkit.modal(el).show();
</script>
EOD;
	}
	else{
		$js = '';
	}
	
	// ログイン画面生成
	if( ! is_user_logged_in( ) ) {
		ob_start();
			echo $wc_notices;
			woocommerce_login_form( array('redirect'=> get_permalink()) );
			echo $js;
			$login_form = ob_get_contents();
		ob_end_clean();		
	
		$modal_window = <<<EOD
<div id="toieelib-list-modal" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
	    <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">会員ログインが必要です</h2>
        {$login_form}
    </div>
</div>	
EOD;
	}
	else {
		$modal_window = <<<EOD
<div id="toieelib-list-modal" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
	    <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">お申し込みが必要です</h2>
        <p><a href="{$lp_url}">資料のダウンロードには、「といリブ」のお申し込みが必要です。<br>詳しくは、こちら</a></p>
    </div>
</div>
EOD;
	}
	
	
	$table =  
'<p class="uk-text-right" style="font-size:0.7rem;">※ <span uk-icon="icon: calendar;"></span>進行表、 <span uk-icon="icon: file-edit;"></span>受講者資料</p>
<table class="uk-table uk-table-striped uk-table-middle">
    <thead>
        <tr>
            <th>タイトル</th>
            <th>ver</th>
            <th>内容</th>
            <th style="width:1rem;"><span uk-icon="icon: calendar;"></span></th>
            <th style="width:1rem;"><span uk-icon="icon: file-edit;"></span></th>
        </tr>
    </thead>
    <tbody>
';
	foreach( $data as $d )
	{
		
		$page = preg_match('/^http/', $d['url']) ?  "<a href='{$d['url']}'>{$d['title']}</a>" : $d['title'].'(ビデオなし)';
		
		$lft_text = preg_match('/^https:/', $d['lft']) ? "<a href=\"{$d['lft']}\" download=\"lft-text.pdf\" target=\"_blank\" class=\"\" uk-icon=\"icon: download\"></a>" : "--";
		$user_text = "<a href=\"{$d['user']}\" download=\"materials.pdf\" target=\"_blank\" class=\"\" uk-icon=\"icon: download\"></a>";
		
		if( $access == false) {
			$lft_text = '<a href="" uk-icon="icon: lock" uk-toggle="target: #toieelib-list-modal"></a>';
			$user_text = '<a href="" uk-icon="icon: lock" uk-toggle="target: #toieelib-list-modal"></a>';
		}
		
		$table .= "
        <tr>
        	<td style=\"font-size:0.8rem;\">{$page}</td>
        	<td style=\"font-size:0.8rem;\">{$d['ver']}</td>
        	<td style=\"font-size:0.8rem;\">{$d['desc']}</td>
        	<td style=\"font-size:0.8rem;\">{$lft_text}</td>
        	<td style=\"font-size:0.8rem;\">{$user_text}</td>
        </tr>
";
	}
	
	$table .= 
'	</tbody>
</table>
';
	    
    return $table . $modal_window;
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


/* user_login_check */
add_shortcode('toiee_user_logined', function ( $atts, $content = null ) {

	if ( is_user_logged_in() ) {
		return  do_shortcode( $content );
	}

	return '';
});
