<?php
/**
* Theme template for displaying related articles
*/ 
?>

<?php $related_articles = hkb_get_related_articles(); ?>

<?php if( !empty($related_articles) && $related_articles->have_posts() ): ?>
<!-- .hkb-article__related -->     
    <section class="hkb-article-related">
        <h3 class="hkb-article-related__title"><?php _e('Related Articles', 'ht-knowledge-base'); ?></h3>
        <ul class="hkb-article-list">
        <?php   while( $related_articles->have_posts() ) {
                $related_articles->the_post(); ?>
                
            <li class="hkb-article-list__<?php hkb_post_format_class(); ?>">
                <a href="<?php the_permalink()?>"><?php the_title(); ?></a>
                <?php hkb_get_template_part( 'hkb-article-meta', 'related' ); ?>
            </li>

        <?php } ?>
        </ul>
    </section>
<!-- /.hkb-article__related -->
<?php endif; ?>

<?php 
    //important - reset the post
    hkb_after_releated_post_reset(); 
?>