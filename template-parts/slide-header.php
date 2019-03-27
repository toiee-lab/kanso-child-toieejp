<?php
/**

	トップページのスライド。
	- bg は背景画像
	- msg はテキスト（改行を適宜入れる）
	- lead は案内文書（a タグで記事にリンクすると良い）
 */
$toiee_slids = array(
	array(
		'bg'   => 'https://i2.wp.com/toiee.jp/wp-content/uploads/2016/08/MG_0622-1-1-e1539853788673.jpg?zoom=2&fit=2000%2C1333&ssl=1', // Background image url FQDN
		'msg'  => '違う意見、考え方を理解し合うプロセスから、<br>良い学びは生まれる', // メッセージ
		'lead' => '私たちが大切にしていること。<a href="#">詳しくはこちら</a>', // リード文
	),
	array(
		'bg'   => 'https://toiee.jp/wp-content/uploads/2017/10/1_w9QP9kfo3FDLdSqOSKy6kw.jpeg', // Background image url FQDN
		'msg'  => 'ワクワクする気持ちを大切にする<br>それが可能性が始まる場所', // メッセージ
		'lead' => '私たちが大切にしていること。<a href="#">詳しくはこちら</a>', // リード文
	),
	array(
		'bg'   => 'https://toiee.jp/wp-content/uploads/2017/09/IMG_4806.jpg', // Background image url FQDN
		'msg'  => '難しいことを探求するワクワクこそ<br>学ぶ醍醐味', // メッセージ
		'lead' => '私たちが大切にしていること。<a href="#">詳しくはこちら</a>', // リード文
	),
	array(
		'bg'   => 'https://toiee.jp/wp-content/uploads/2018/05/1*rp5Cq6xGmy-qOaQw-rV0Yw.jpeg', // Background image url FQDN
		'msg'  => '今日集まった人は、必然であり<br>良い学びに不可欠な存在である', // メッセージ
		'lead' => 'ラーニングファシリテーターマインドセットより（詳しくはこちら）', // リード文
	),
	array(
		'bg'   => 'https://toiee.jp/wp-content/uploads/2018/09/header.jpg', // Background image url FQDN
		'msg'  => '違う意見、考え方を理解し合うプロセスから、<br>良い学びは生まれる', // メッセージ
		'lead' => '私たちが大切にしていること。詳しくはこちら', // リード文
	),
	array(
		'bg'   => 'https://toiee.jp/wp-content/uploads/2018/05/832770C0-4FE4-4376-83A7-02ECEAF79218.jpg', // Background image url FQDN
		'msg'  => '違う意見、考え方を理解し合うプロセスから、良い学びは生まれる', // メッセージ
		'lead' => '私たちが大切にしていること。詳しくはこちら', // リード文
	),
	array(
		'bg'   => 'https://toiee.jp/wp-content/uploads/2018/04/IMG_1343.jpg', // Background image url FQDN
		'msg'  => '違う意見、考え方を理解し合うプロセスから、良い学びは生まれる', // メッセージ
		'lead' => '私たちが大切にしていること。詳しくはこちら', // リード文
	),
	array(
		'bg'   => 'https://toiee.jp/wp-content/uploads/2018/04/8EA38CA9-CBA0-4A81-B9B6-4F5DD07A8E35-1.jpg', // Background image url FQDN
		'msg'  => '違う意見、考え方を理解し合うプロセスから、良い学びは生まれる', // メッセージ
		'lead' => '私たちが大切にしていること。詳しくはこちら', // リード文
	),
);
?><div class="uk-position-relative uk-visible-toggle uk-light" uk-slideshow="max-height: 600; animation: fade; autoplay: true; autoplay-interval: 6000;pause-on-hover: false">

	<ul class="uk-slideshow-items" uk-height-viewport="offset-top: true; offset-bottom: 30; min-height: 400">
		<?php
			shuffle( $toiee_slids );
		foreach ( $toiee_slids as $slide ) :
			?>
		<li>
			<div class="uk-position-cover uk-animation-kenburns uk-animation-reverse uk-transform-origin-center-left">
				<img src="<?php echo $slide['bg']; ?>" alt="" uk-cover>
			</div>
			<div class="uk-position-center uk-position-small uk-text-center"  style="text-shadow: 1px 1px 3px black;">
				<h2 uk-slideshow-parallax="y: -50,0,0;" class="kns-msg-h2"><?php echo $slide['msg']; ?></h2>
				<p uk-slideshow-parallax="y: 50,0,0;" class="kns-msg-p"><?php echo $slide['lead']; ?></p>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>

	<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
	<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
</div>
