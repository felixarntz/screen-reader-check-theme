<?php
/**
 * @package ScreenReaderCheckTheme
 * @since 1.0.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'screen-reader-check-theme' ); ?></a>

		<header id="header" class="site-header" role="banner">

			<div class="container">

				<h1 class="site-title"><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>

				<div class="row">
					<div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2">
						<p class="site-description"><?php bloginfo( 'description' ); ?></p>
					</div>
				</div>

				<?php if ( function_exists( 'the_custom_logo' ) ) : ?>
					<?php the_custom_logo(); ?>
				<?php endif; ?>

				<?php if ( has_nav_menu( 'primary' ) ) : ?>
					<nav class="navbar navbar-light" role="navigation">
						<h2 class="screen-reader-text"><?php _e( 'Primary Navigation', 'screen-reader-check-theme' ); ?></h2>
						<button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#primary-navigation" aria-controls="primary-navigation" aria-expanded="false" aria-label="<?php _e( 'Toggle navigation', 'screen-reader-check-theme' ); ?>"><span aria-hidden="true">&#9776;</span></button>
						<div id="primary-navigation" class="collapse navbar-toggleable-xs">
							<?php srctheme_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav navbar-nav' ) ); ?>
						</div>
					</nav>
				<?php endif; ?>

			</div>

		</header><!-- #header -->
