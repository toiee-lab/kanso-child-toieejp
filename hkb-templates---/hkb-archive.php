<?php
/**
* Theme template for archive display
*/
?>

<?php get_header(); ?>
	<div class="uk-container uk-container-small uk-background-default main-content">


<?php hkb_get_template_part('hkb-compat', 'archive'); ?>

	</div><!-- .main-content -->

<?php
get_sidebar();
get_footer();
