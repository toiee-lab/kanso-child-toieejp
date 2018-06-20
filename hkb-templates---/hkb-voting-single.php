<?php
/**
* Theme template for voting
*/
?>

<div class="hkb-feedback">
	<h3 class="hkb-feedback__title"><?php _e('Was this article helpful?', 'ht-knowledge-base'); ?></h3>
	<?php do_action('ht_voting_post'); ?>
</div>