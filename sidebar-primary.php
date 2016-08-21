<?php
/**
 * @package ScreenReaderCheckTheme
 * @since 1.0.0
 */

?>
			<?php if ( is_active_sidebar( 'primary' ) ) : ?>
				<aside id="sidebar" class="site-sidebar col-md-3" role="complementary">
					<?php dynamic_sidebar( 'primary' ); ?>
				</aside>
			<?php endif; ?>
