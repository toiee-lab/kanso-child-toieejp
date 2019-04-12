<?php
	$pkt_ch_id = get_field( 'pocketera' );
	$pkt       = get_term_by( 'id', $pkt_ch_id, 'pkt_channel' );
	$term_url  = get_term_link( $pkt ) . '#material';
?>
<p><a href="<?php echo esc_html( $term_url ); ?>">こちらのページ</a>でご覧ください。</p>
<script>
	location.href = '<?php echo esc_html( $term_url ); ?>';
</script>