<article id="post-<?php the_ID(); ?>" class="hkb-article" <?php //post_class(); ?>>

    <h3 class="hkb-article__title" itemprop="headline">
        <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
    </h3>
    <?php if( hkb_show_search_excerpt() && hkb_get_the_excerpt() ) : ?>
        <div class="hkb-article__excerpt"><?php hkb_the_excerpt(); ?></div>
    <?php endif; ?>

    <?php hkb_get_template_part( 'hkb-article-meta', 'search' ); ?>

</article>