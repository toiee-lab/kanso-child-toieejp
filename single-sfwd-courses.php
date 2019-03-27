<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kanso-general
 */

get_header(); ?>
	<div class="uk-container uk-container-small uk-background-default main-content-lib">

		<?php
		while ( have_posts() ) :
			the_post();

			$the_id = get_the_ID();

			$terms = get_the_terms( $the_id, 'ld_course_category' );
			if ( isset( $terms[0] ) ) {
				$cat_name = $terms[0]->name;
			} else {
				$cat_name = '';
			}

			$bc_cat = '';

			if ( ! is_null( $terms ) ) {
				$term_link = get_term_link( $terms[0]->slug, 'ld_course_category' );
				$bc_cat    = <<<EOD
					<li class="bc-divider">&gt;</li>
					<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
					<a href="{$term_link}" itemprop="item"><span itemprop="name">{$cat_name}</span><meta itemprop="position" content="3"></a>
					</li>				
EOD;
			}

			?>
			<div class="kns-breadcrumb">
				<ul itemscope="" itemtype="http://schema.org/BreadcrumbList">
					<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
					<a href="https://toiee.jp" itemprop="item"><span itemprop="name">home</span><meta itemprop="position" content="1"></a>
					</li>
					<li class="bc-divider">&gt;</li>
					<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
					<a href="https://toiee.jp/project/toieelib/" itemprop="item"><span itemprop="name">といリブ</span><meta itemprop="position" content="2"></a>
					</li>
					<?php echo $bc_cat; ?>
				</ul>
			</div>
		
			<?php the_title( '<h1>', '</h1>' ); ?>
			<h2 class="main-subtitle"><?php echo get_post_meta( $the_id, 'kns_lead', true ); ?></h2>
			
			<?php the_content(); ?>
			
			
			<hr class="uk-divider-small uk-text-center">
			<?php
			the_post_navigation(
				array(
					'prev_text'          => '&lt; PREVIOUS',
					'next_text'          => 'NEXT &gt;',
					'screen_reader_text' => 'Navigation',
				)
			);

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				?>
			<hr class="uk-margin-large">
				<?php
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</div><!-- .main-content -->
<?php
get_sidebar();
get_footer();
