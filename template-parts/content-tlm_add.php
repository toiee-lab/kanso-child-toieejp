<?php

$p     = get_post();
$terms = wp_get_post_terms( $p->ID, 'tlm' );
$term  = count( $terms ) ? $terms[0] : false;

if ( current_user_can( 'edit_posts' ) ) {

	if ( false === $term ) {
		?>
		<div class="uk-alert-warning" uk-alert>関連する「toiee教材」が、存在しません。</div>
		<?php
	} else {
		$link = get_term_link( $term ) . "#tlm_add";
		?>
		<div class="uk-alert-success" uk-alert>
			<h3>toiee教材・関連ナレッジ</h3>
			<p>実際に表示される場所は、<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term->name ); ?></a>です。</p>
		</div>
		<?php
	}

	the_title( '<h1>', '</h1>' );
	the_content();

} else {
	if ( false === $term ) {
		?>
		<div class="uk-alert-warning" uk-alert>関連する「toiee教材」が、存在しません。</div>
		<?php
	} else {
		$link = get_term_link( $term ) . "#tlm_add";
		?>
		<p><a href="<?php echo esc_html( $link ); ?>">こちらのページ</a>でご覧ください。</p>
		<script>
            location.href = '<?php echo esc_html( $link ); ?>';
		</script>
		<?php
	}
}