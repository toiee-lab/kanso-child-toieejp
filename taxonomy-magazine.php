<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */


$magazine        = get_queried_object();
$magazine_id     = $magazine->term_id;
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

acf_form_head();
get_header();

require locate_template( 'template-parts/magazine.php' );

get_sidebar();
get_footer();
