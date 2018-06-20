<?php
/**
* Compat template for displaying search results
*/
?>

<!-- #ht-kb -->
<div id="hkb" class="hkb-template-search">

<?php hkb_get_template_part('hkb-searchbox', 'search'); ?>

<?php hkb_get_template_part('hkb-breadcrumbs', 'search'); ?>

        <?php if ( have_posts() ) : ?>
          <?php while ( have_posts() ) : the_post(); ?>

            <?php hkb_get_template_part('hkb-content-article', 'search'); ?>

          <?php endwhile; ?>

          <?php posts_nav_link(); ?>

        <?php else : ?>

          <div class="hkb-search-noresults">
            <h2 class="hkb-search-noresults__title">
              <?php _e('No Results', 'ht-knowledge-base'); ?>
            </h2>
            <p><?php printf( __('Your search for "%s" returned no results. Perhaps try something else?',  'ht-knowledge-base'), get_search_query() ); ?></p>
          </div>

        <?php endif; ?>

</div>
<!-- /#ht-kb -->