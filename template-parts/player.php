<?php

the_title( '<h2 class="uk-h3">', '</h2>' );

$src = get_field( 'enclosure' );
$type = get_field( 'media' );

if ( $has_access ) {
	the_episode_player( $src, $type );
} else {
	$restrict = get_field( 'restrict' );
	switch ( $restrict ) {
		case 'open':
			the_episode_player( $src, $type );
			break;
		case 'free':
			if ( $user_logged_in ) {
				the_episode_player( $src, $type );
				break;
			} else {
				echo '<p class="uk-text-small uk-text-success">無料会員登録することで、ご覧いただけます。</p>';
				the_episode_player_dummy( $type );
				break;
			}
		default: /* restrict */
			the_episode_player_dummy( $type );
			break;
	}
}
the_excerpt();
?>
<p><a href="<?php the_permalink(); ?>">詳細を読む</a></p>
<hr>