<?php
/**
 * Created by PhpStorm.
 * User: takame
 * Date: 2019-03-27
 * Time: 16:25
 */

?>
<li class="uk-margin-bottom mag-nav-list">
	<a href="<?php the_permalink(); ?>" style="display:block" class="uk-link-text nav-item-<?php echo get_the_ID(); ?>">
	<div class="uk-grid-collapse uk-child-width-expand@s" uk-grid>
		<div class="uk-width-expand">
			<?php
			the_title( '<p class="mag-nav-title">', '</p>' );
			$attr = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
			?>
		</div>
		<div class="uk-width-auto">
			<div class="uk-background-cover mag-thumbnail" style="background-image:url(<?php echo $attr[0]; ?>);">
			</div>
		</div>
	</div>
	</a>
</li>
