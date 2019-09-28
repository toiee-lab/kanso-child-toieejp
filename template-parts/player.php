<?php

the_title( '<h2 class="uk-h3">', '</h2>' );

$src   = get_field( 'enclosure' );
$media = get_field( 'media' );

if ( ! isset( $the_episode_player_plyr_ext ) ) {
	$the_episode_player_plyr_ext = '';
}

if ( $has_access ) {
	the_episode_player_plyr( $src, $media, $the_episode_player_plyr_ext );
} else {
	$restrict = get_field( 'restrict' );
	if ( $restrict === true ) {
		$restrict = 'restrict';
	} elseif ( $restrict === false ) {
		$restrict = 'open';
	}

	switch ( $restrict ) {
		case 'open':
			the_episode_player_plyr( $src, $media, $the_episode_player_plyr_ext );
			break;
		case 'free':
			if ( $user_logged_in ) {
				the_episode_player_plyr( $src, $media, $the_episode_player_plyr_ext );
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
