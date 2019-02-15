<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kanso-general
 */
?>
			<div id="footer" uk-height-viewport="expand: true">
				<div  class="uk-text-left" uk-grid>
	
				    <div class="uk-width-1-2@s">
				        <?php dynamic_sidebar('footer-left'); ?>
				    </div>
				    <div class="uk-width-1-4@s">
	   			        <?php dynamic_sidebar('footer-center'); ?>
				    </div>
				    <div class="uk-width-1-4@s">
	   			        <?php dynamic_sidebar('footer-right'); ?>
				    </div>
				    
				</div>
				<p id="footer-copyright">Copyright &copy; <span class="ownername"><?php echo get_option( 'kanso_general_options_ownername' ); ?></span>, All rights reserved.</p>
			</div><!-- #footer -->
			
	    </div><!-- uk-offcanvas-content -->
	    <div id="sidebar" uk-offcanvas="overlay: true;mode: push">
	        <div class="uk-offcanvas-bar">
	
	            <button class="uk-offcanvas-close" type="button" uk-close></button>

				<aside id="secondary" class="widget-area">
					<?php dynamic_sidebar( 'sidebar-1' ); ?>
				</aside><!-- #secondary -->


	        </div>
	    </div>


