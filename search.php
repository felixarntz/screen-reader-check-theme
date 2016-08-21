<?php
/**
 * @package ScreenReaderCheckTheme
 * @since 1.0.0
 */

get_header(); ?>

		<div class="container">
			<div class="row">

				<main id="main" class="site-content col-md-<?php echo ( is_active_sidebar( 'primary' ) ? 9 : 12 ); ?>" role="main">

					<?php if( have_posts() ) : ?>

						<header>
							<h1 class="main-title"><?php printf( esc_html__( 'Search Results for: %s', '_s' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
						</header>

						<?php

						while( have_posts() ) : the_post();

							$slug = 'content';
							$name = get_post_type();
							/*if ( 'post' === $name ) {
								$slug .= '-post';
								$name = get_post_format();
							}*/
							get_template_part( 'template-parts/' . $slug, $name );

						endwhile;

						?>
					<?php else : ?>
						<?php get_template_part( 'template-parts/content', 'none' ); ?>
					<?php endif; ?>

				</main>

				<?php get_sidebar( 'primary' ); ?>

			</div>
		</div>

<?php get_footer();
