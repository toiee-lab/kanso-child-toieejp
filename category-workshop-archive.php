<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package kanso-general
 */

get_header(); ?>
<?php
$term_obj  = get_queried_object();
$term_link = get_term_link( $term_obj );
$fields    = get_fields( $term_obj );
?>
	<div class="uk-section-default uk-text-center">
		<div class="uk-section uk-light uk-background-cover" style="background-image: url(<?php echo $fields['bg_image'] ?>)">
			<div class="uk-container">
				<h1 class="uk-text-bold" style="text-shadow:0px 0px 3px #000000;color:<?php echo $fields['font_color'] ?>;"><a href="<?php echo $term_link; ?>"><?php the_archive_title(); ?></a></h1>
				<p class="uk-text-bold" style="text-shadow:0px 0px 3px #000000;color:<?php echo $fields['font_color'] ?>"><?php echo $fields['cat_subtitle'] ?></p>
				<form class="uk-form-stacked" method="get" action="<?php home_url(); ?>">
					<div class="uk-margin">
						<div class="uk-form-controls">
							<div class="uk-inline">
								<span class="uk-form-icon" uk-icon="icon: search" style="color: rgba(2, 2, 2, 0.7);"></span>
								<input class="uk-input uk-form-width-large uk-form-large" name="s" type="text" style="background-color: rgba(255, 255, 255, 0.8);color: rgba(2, 2, 2, 0.7);">
								<input type="hidden" name="cat" value="<?php echo $term_obj->term_id; ?>" />
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="uk-container uk-margin-medium">
		<div uk-filter="target: .js-filter">
		<?php
			if ( have_posts() ) :

				$uk_filter = array();
				ob_start();
			?>
				<ul class="js-filter uk-child-width-1-2 uk-child-width-1-4@m uk-text-center" uk-grid uk-height-match="target: > li > .uk-card">
					<?php
					/* Start the Loop */
					$no_cat_slug = 'a9bkkebibeb13bd';
					$no_cat_name = '未分類';
					$no_cat_exits = false;
					$cat_filter = array();

					while ( have_posts() ) :
						the_post();

						$cats       = get_the_category();
						if ( isset( $cats[0] ) ) {
							$cat_name = $cats[0]->name;
							$cat_slug = $cats[0]->slug;
							$cat_filter[ $cat_slug ] = $cat_name;
						} else {
							$cat_name = $no_cat_name;
							$cat_slug = $no_cat_slug;
							$no_cat_exits = true;
						}
						?>
					<li data-color="<?php echo $cat_slug; ?>">
						<div class="uk-card uk-card-default uk-grid-small uk-card-small uk-box-shadow-small">
							<div class="uk-card-badge uk-label tm-card-badge"><?php echo $cat_name; ?></div>
							<div class="uk-card-media-top uk-cover-container tm-postcast-card">
								<img src="<?php echo kanso_general_get_thumnail_url(); ?>" uk-cover>
								<a href="<?php the_permalink(); ?>"></a>
							</div>
							<div class="uk-card-body">
								<h2 class="uk-h5 uk-margin-remove uk-link-text uk-text-bold"><a href="<?php the_permalink(); ?>" ><?php the_title(); ?></a></h2>
								<p class="uk-margin-small-top uk-text-small uk-text-muted"><?php
									the_excerpt(
										sprintf(
											wp_kses(
											/* translators: %s: Name of current post. Only visible to screen readers */
												__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'kanso-general' ),
												array(
													'span' => array(
														'class' => array(),
													),
												)
											),
											get_the_title()
										)
									);
									?></p>
							</div>
						</div>
					</li>
					<?php endwhile; ?>
				</ul>
				<?php
				$content = ob_get_contents();
				ob_clean();

				if ( $no_cat_exits ) {
					$cat_filter[ $no_cat_slug ] = $no_cat_name;
				}

				?>
				<ul class="uk-subnav uk-subnav-pill">
					<li class="uk-active" uk-filter-control><a href="#">All</a></li>
					<?php foreach ( $cat_filter as $key => $value ) : ?>
					<li uk-filter-control="[data-color='<?php echo $key; ?>']"><a href="#"><?php echo $value; ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php
			echo $content;
			echo kanso_get_post_navigation();

			else :

			get_template_part( 'template-parts/content', 'none' );

			endif;
			?>
		</div>
	</div><!-- .main-content -->

<?php
get_sidebar();
get_footer();
