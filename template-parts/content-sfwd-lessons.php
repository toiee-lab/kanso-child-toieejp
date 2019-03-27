<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */

?>
<?php
if ( is_home() || is_front_page() ) {
	// do nothing
} else {
	$the_id = get_the_ID();
	get_template_part( 'template-parts/breadcrumb', 'page' );

	if ( get_post_meta( $the_id, 'kns_hidetitle', true ) != '1' ) {
		the_title( '<h1>', '</h1>' );
		?>
			<h2 class="main-subtitle"><?php echo get_post_meta( $the_id, 'kns_lead', true ); ?></h2>
		<?php
	}

	if ( get_post_meta( $the_id, 'kns_hidethumb', true ) != '1' ) {
		the_post_thumbnail( 'full', array( 'class' => 'uk-margin-medium-bottom' ) );
	}
}
?>
			<div class="the_content">
			<?php
				$the_content = get_the_content();
			if ( $the_content != '' ) {
				the_content();
			} else {
				echo '<div uk-alert class="uk-alert-none uk-margin-large-top"><h3><span uk-icon="icon: info"></span> お知らせ</h3>
<p>このページは作成中です。今しばらくお待ちください。</p>
</div>';
			}
			?>
			</div>
			
			<?php

			if ( ! is_front_page() && ! is_home() && ( get_post_meta( $the_id, 'exclude_menu', true ) ) != '1' ) :

				$pagelist = get_pages(
					array(
						'meta_key'   => 'exclude_menu',
						'meta_value' => '1',
					)
				);
				$ex_pages = array();
				foreach ( $pagelist as $page ) {
					$ex_pages[] = $page->ID;
				}
				$ex_pages[] = get_option( 'page_on_front' ); // front page を除外
				$ex_pages[] = get_option( 'page_for_posts' ); // blog 一覧ページを除外

				$pagelist = get_pages(
					array(
						'sort_column' => 'menu_order',
						'sort_order'  => 'asc',
						'exclude'     => implode( ',', $ex_pages ),
					)
				);

				$pages = array();
				foreach ( $pagelist as $page ) {
					$pages[] += $page->ID;
				}

				$current = array_search( get_the_ID(), $pages );
				$prevID  = isset( $pages[ $current - 1 ] ) ? $pages[ $current - 1 ] : '';
				$nextID  = isset( $pages[ $current + 1 ] ) ? $pages[ $current + 1 ] : '';
				?>
			<hr class="uk-margin-large-top">
			<div class="uk-clearfix uk-margin-medium-top kns-footer-page-nav">
				<div class="uk-float-right">
				<?php if ( ! empty( $nextID ) ) : ?>
					<a href="<?php echo get_permalink( $nextID ); ?>" 
			 title="<?php echo get_the_title( $nextID ); ?>"><span><?php echo get_the_title( $nextID ); ?> &gt;</span></a>
				<?php endif; ?>		        
				</div>
				<div class="uk-float-left">
				<?php if ( ! empty( $prevID ) ) : ?>
					<a href="<?php echo get_permalink( $prevID ); ?>"
					  title="<?php echo get_the_title( $prevID ); ?>"><span>&lt; <?php echo get_the_title( $prevID ); ?></span></a>
				<?php endif; ?>
				</div>
			</div><!-- .kns-footer-page-nav -->
				<?php
				endif;
			?>
