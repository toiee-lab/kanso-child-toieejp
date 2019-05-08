<?php

the_title( '<h2 class="uk-h3">', '</h2>' );

$src = get_field( 'enclosure' );
$media = get_field( 'media' );

if ( $has_access ) {
	the_episode_player( $src, $media );
} else {
	$restrict = get_field( 'restrict' );
	if ( $restrict === true ) {
		$restrict = 'restrict';
	} else if ( $restrict === false ) {
		$restrict = 'open';
	}

	switch ( $restrict ) {
		case 'open':
			the_episode_player( $src, $media );
			break;
		case 'free':
			if ( $user_logged_in ) {
				the_episode_player( $src, $media );
				break;
			}
		default: /* restrict */
			the_episode_player_dummy( $media );
			break;
	}
}
the_excerpt();
?>
<p><a href="<?php the_permalink(); ?>">詳細を読む</a></p>
<hr>