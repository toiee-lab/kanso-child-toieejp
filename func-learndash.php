<?php
/**
	
product のアップデートを検知して、LearnDashの変更があれば、それを反映して修正する。
	
*/
add_action('post_updated', function ($post_id, $post_after, $post_before ){
			
	$post_type = get_post_type( $post_ID );
	
	// 'product' の投稿でなければ、更新しない。
    if ( "product" != $post_type )
        return;
        
    // 前後を比較する
    // $post_after のコースと、 $post_after のコースを比較
    // もし、変化があれば、以下を実行する
    //    この プロダクトのオーダーを探す
    //    見つかったオーダーの持ち主のコースを作り直す
    
    var_dump( $post_before, $post_after );
    exit;
	
} , 15 );