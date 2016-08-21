<?php
/**
 * @package ScreenReaderCheckTheme
 * @version 1.0.0
 */

?>
		<footer id="footer" class="site-footer" role="contentinfo">

			<div class="container">

				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<nav role="navigation">
						<?php srctheme_nav_menu( array( 'theme_location' => 'footer', 'menu_class' => 'list-inline' ) ); ?>
					</nav>
				<?php endif; ?>

				<p class="copyright">
					&copy; <?php echo date( 'Y' ); ?>
					<a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a>
				</p>

			</div>

		</footer><!-- #footer -->

		<?php wp_footer(); ?>
	</body>
</html>
