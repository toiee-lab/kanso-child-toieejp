<?php
/**
 * Created by PhpStorm.
 * User: takame
 * Date: 2019-03-27
 * Time: 14:50
 */


/*
 * ソースコードとしては、最悪の結果だが、目を瞑る。いつか綺麗にしよう。
 * なぜ、こうなっているのか？というと、属するタクソノミーごとにデザインを設定しようとしているから
 * もしかしたら、もっといい方法があるかもしれないが、今はわからないので、無理やり解決することとする。
 */

?>
<header class="mag-header">
	<div class="uk-section">
		<div class="uk-container">
			<p class="uk-margin-remove-top uk-margin-remove-bottom">toiee Lab magazine</p>
			<h1 class="uk-margin-remove-bottom uk-margin-small-top mag-h1"><a href="<?php echo esc_attr( $magazine_url ); ?>"><?php echo esc_html( $mag['title'] ); ?></a></h1>
			<p class="uk-text-lead uk-margin-remove-top mag-lead"><a href="<?php echo esc_attr( $magazine_url ); ?>"><?php echo esc_html( $mag['subtitle'] ); ?></a></p>
		</div>
	</div>
</header>
<div class="mag-overlap">
	<div class="uk-container uk-container-small uk-background-default" style="min-height: 600px;">
		<div class="main-content">
			<main class="uk-margin-top">
				<?php echo $mag['close_msg']; ?>
			</main>
		</div>
	</div>
</div>

