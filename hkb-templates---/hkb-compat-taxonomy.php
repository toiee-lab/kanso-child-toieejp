<?php
/**
* Compat template for displaying heroic knowledgebase category archive content
*/
?>

<!-- #ht-kb -->
<div id="hkb" class="hkb-template-category">

    <?php hkb_get_template_part('hkb-searchbox', 'taxonomy'); ?>

    <?php hkb_get_template_part('hkb-breadcrumbs', 'taxonomy'); ?>

	<?php hkb_get_template_part('hkb-subcategories', 'taxonomy'); ?>

    <?php if ( have_posts() ) : ?>
    
        <?php while ( have_posts() ) : the_post(); ?>

    		<?php hkb_get_template_part('hkb-content-article', 'taxonomy'); ?>
        
        <?php endwhile; ?>

        <?php posts_nav_link(); ?>
        
    <?php else : ?>

        <?php $subcategories = hkb_get_subcategories(hkb_get_term_id()); ?>
        <?php if ( !$subcategories ): ?>
            <h2><?php _e('No articles in this category.', 'ht-knowledge-base'); ?></h2>
        <?php endif; ?>
        
    <?php endif; ?>

</div>
<!-- /#ht-kb -->