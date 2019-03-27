<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kanso-general
 */
?>
<div id="sidebar" uk-offcanvas="overlay: true;mode: push">
	<div class="uk-offcanvas-bar">

		<button class="uk-offcanvas-close" type="button" uk-close></button>

		<aside id="secondary" class="widget-area">
			<?php dynamic_sidebar( 'sidebar-kb' ); ?>
		</aside><!-- #secondary -->


	</div>
</div>


