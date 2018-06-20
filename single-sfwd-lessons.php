<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package kanso-general
 */

get_header(); ?>

		<div uk-grid class="uk-grid-match uk-flex-center uk-grid-divider">
			<div class="uk-width-xxlarge">
				<div class="main-content-lib main-content-sidebar uk-container uk-container-auto">

					<?php
					while ( have_posts() ) : the_post();
					
						$the_id = get_the_ID();
						
						// コース情報の取得
						$course_id = learndash_get_course_id();
						$cource_link = get_permalink( $course_id );
						$cource_title = get_the_title( $course_id );
						
						// コースカテゴリの取得						
						$terms = get_the_terms( $course_id , 'ld_course_category' );
						$bc_cat = '';
									
						if( ! is_null( $terms )){
							$term_link = get_term_link( $terms[0]->slug, 'ld_course_category' );
							$bc_cat = <<<EOD
								<li class="bc-divider" style="display: none">&gt;</li>
								<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" style="display: none">
								<a href="{$term_link}" itemprop="item"><span itemprop="name">{$terms[0]->name}</span><meta itemprop="position" content="3"></a>
								</li>				
EOD;
						}
						

					?>
					
					
						<div class="kns-breadcrumb">
							<ul itemscope="" itemtype="http://schema.org/BreadcrumbList">
								<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" style="display: none">
								<a href="https://toiee.jp" itemprop="item"><span itemprop="name">home</span><meta itemprop="position" content="1"></a>
								</li>
								<li class="bc-divider" style="display: none">&gt;</li>
								<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" style="display: none">
								<a href="https://toiee.jp/project/toieelib/" itemprop="item"><span itemprop="name">といリブ</span><meta itemprop="position" content="2"></a>
								</li>
								<?php echo $bc_cat; ?>
								<li class="bc-divider" style="display: none">&gt;</li>
								<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
								<a href="<?php echo $cource_link; ?>" itemprop="item"><span itemprop="name"><?php echo $cource_title; ?></span><meta itemprop="position" content="4"></a>
								</li>
								<li class="bc-divider">&gt;</li>
								<li><?php echo the_title(); ?></li>
							</ul>
						</div>
					
						<?php echo the_title('<h1>', '</h1>'); ?>
						<h2 class="main-subtitle"><?php echo get_post_meta( $the_id, 'kns_lead', true );?></h2>
						
						<?php
							$the_content = get_the_content(); 
							if( $the_content != '' ){
								the_content();
							}
							else{
								echo '<div uk-alert class="uk-alert-none uk-margin-large-top"><h3><span uk-icon="icon: info"></span> お知らせ</h3>
									<p>このページは作成中です。今しばらくお待ちください。</p>
										</div>';
							}
						?>
						
						
						<hr class="uk-divider-small uk-text-center">
					<?php
						the_post_navigation(array(
						    'prev_text'           => '&lt; PREVIOUS',
						    'next_text'           => 'NEXT &gt;',
						    'screen_reader_text'  => 'Navigation',
						));
			
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
			<!-- sidebar -->
			<div id="___content-sidebar" class="uk-visible@m">
				<nav>
					<aside id="secondary" class="widget-area uk-padding-left">
						<?php dynamic_sidebar( 'sidebar-ld_lesson' ); ?>
					</aside><!-- #secondary -->
				</nav>
			</div><!-- .sidebar -->
		</div>	

<?php
get_sidebar( 'ld_lesson' );
get_footer();
