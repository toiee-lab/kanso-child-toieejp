<?php
/**
* Theme template for display the voting feedback form
*/
global $post_id, $voting_nonce, $feedback_nonce;

$new_vote = ht_kb_voting_get_new_vote();

$votes = ht_voting_get_post_votes($post_id);

$allow_anon = ht_kb_voting_enable_anonymous();

$vote_enabled_class = ( !$allow_anon && !is_user_logged_in() ) ? 'disabled' : 'enabled';

$user_vote_direction = ht_kb_voting_get_users_post_vote_direction($post_id);

?>

<?php if(!$allow_anon && !is_user_logged_in()): ?>	
	<div class="voting-login-required" data-ht-voting-must-log-in-msg="<?php _e('You must log in to vote', 'ht-knowledge-base'); ?>">
		<?php _e('You must log in to vote', 'ht-knowledge-base'); ?>
	</div>
<?php endif; ?>
<div class="ht-voting-links ht-voting-<?php echo $user_vote_direction; ?>">
	<a class="ht-voting-upvote <?php echo $vote_enabled_class; ?>" rel="nofollow" role="button" data-direction="up" data-type="post" data-nonce="<?php echo $voting_nonce; ?>" data-id="<?php echo $post_id; ?>" data-allow="<?php echo $allow_anon; ?>" data-display="standard" href="<?php echo '#'; // $this->vote_post_link('up', $post_id, $allow); ?>">
		<i class="hkb-upvote-icon"></i>
		<span><?php _e('Yes', 'ht-knowledge-base'); ?></span>
	</a>
	<a class="ht-voting-downvote <?php echo $vote_enabled_class; ?>" rel="nofollow" role="button" data-direction="down" data-type="post" data-nonce="<?php echo $voting_nonce; ?>" data-id="<?php echo $post_id; ?>" data-allow="<?php echo $allow_anon; ?>" data-display="standard" href="<?php echo '#'; // $this->vote_post_link('down', $post_id, $allow); ?>">
		<i class="hkb-upvote-icon"></i>
		<span><?php _e('No', 'ht-knowledge-base'); ?></span>
	</a>
</div>
<?php if(empty($new_vote)): ?>
	<!-- no new vote -->
<?php elseif( ht_kb_voting_show_feedback_form() ): ?>
	<div class="ht-voting-comment <?php echo $vote_enabled_class; ?>" data-nonce="<?php echo $feedback_nonce; ?>"  data-vote-key="<?php echo $new_vote->key; ?>" data-id="<?php echo $post_id; ?>">
		<textarea class="ht-voting-comment__textarea" rows="4" cols="50" placeholder="<?php _e('Thanks for your feedback, add a comment here to help improve the article', 'ht-knowledge-base'); ?>"><?php if(isset($new_vote->comments)) $new_vote->comments; ?></textarea>
		<button class="ht-voting-comment__submit" type="button" role="button"><?php _e('Send Feedback', 'ht-knowledge-base'); ?></button>
	</div>
<?php else: ?>
    	<div class="ht-voting-thanks"><?php _e('Thanks for your feedback', 'ht-knowledge-base'); ?></div>
<?php endif;//vote_key ?>	
