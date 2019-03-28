<?php
/**
 * Created by PhpStorm.
 * User: takame
 * Date: 2019-02-28
 * Time: 17:44
 */

$terms = wp_get_post_terms( get_the_ID(), 'magazine' );

if ( count( $terms ) ) {
	$magazine        = $terms[0];
	$magazine_url    = get_term_link( $magazine );
	$magazine_fields = get_fields( $magazine );

	$mag = array(
		'title'     => $magazine->name,
		'subtitle'  => $magazine_fields['mag_subtitle'],
		'header'    => $magazine_fields['mag_header_notice'],
		'footer'    => $magazine_fields['mag_footer_notice'],
		'order'     => $magazine_fields['mag_order'],
		'status'    => $magazine_fields['mag_status'],
		'close_msg' => $magazine_fields['mag_close_notice'],
	);
} else {
	wp_die( '必ず、所属する雑誌（magazine taxonomy)を指定してください。' );
}

get_header();


require locate_template( 'template-parts/magazine.php' );


get_sidebar();
get_footer();
