<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kanso-general
 */

$fields = get_fields();

get_header(); ?>
	<header class="tkb-header">
		<div class="uk-container">
			<div class="uk-padding">
				<p class="uk-h3 uk-margin-remove-top uk-margin-remove-bottom tkb-tagline">関連ナレッジ : 興味を探求しよう</p>
			</div>
		</div>
	</header>
	<div class="tkb-overlap">
		<div class="uk-container uk-container-small uk-background-default main-content">
			<div class="kns-breadcrumb">
				<ul itemscope itemtype="http://schema.org/BreadcrumbList">
					<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a href="<?php echo site_url(); ?>" itemprop="item"><span itemprop="name">home</span><meta itemprop="position" content="1"></a>
					</li>
					<li class="bc-divider">&gt;</li>
					<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a href="<?php echo get_post_type_archive_link( 'toiee_knowledge' ); ?>" itemprop="item"><span itemprop="name">関連ナレッジ</span><meta itemprop="position" content="2"></a>
					</li>
				</ul>
			</div>
			<?php
			while ( have_posts() ) :
				the_post();

				the_title('<h1 class="main-title">', '</h1>');
				the_content();

				$relation = [
					'mimidemy'  => [
						'title' => '関連する耳デミー',
						'tax'   => 'mmdmy',
					],
					'pocketera' => [
						'title' => '関連するポケてら',
						'tax'   => 'pkt_channel',
					],
				];

				foreach ( $relation as $f => $v ) :
					?>
				<h2><?php echo esc_html( $v['title'] ); ?></h2>
				<ul>
					<?php
					foreach ( $fields[ $f ] as $tax_id ) :
						$url = get_term_link( $tax_id, $v['tax'] );
						$pkt = get_term_by( 'term_id', $tax_id, $v['tax'] );

						if ( false !== $term ) :
							?>
					<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $pkt->name ); ?></a></li>
							<?php
						endif;
					endforeach;
					?>
				</ul>
					<?php
				endforeach;
				?>
				<hr class="uk-divider-small uk-text-center">
				<?php


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
	</div>
<?php
get_sidebar();
get_footer();
