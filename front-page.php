<?php
/**
 * @package ScreenReaderCheckTheme
 * @since 1.0.0
 */

get_header(); ?>

		<div class="container">
			<div class="row">

				<main id="main" class="site-content col-md-12" role="main">

					<form id="screen-reader-check-form" class="screen-reader-check-form" method="post" novalidate>

						<div class="main-input">

							<h2><?php _e( 'Check your code now', 'screen-reader-check-theme' ); ?></h2>

							<div id="screen-reader-check-url-input">
								<div class="form-group row">
									<label for="url" class="screen-reader-text">
										<?php _e( 'URL', 'screen-reader-check-theme' ); ?>
									</label>
									<div class="col-sm-10">
										<input type="url" id="url" class="form-control form-control-lg" aria-describedby="url-description">
										<p id="url-description" class="form-text text-muted">
											<?php _e( 'Enter the URL of the website you would like to check.', 'screen-reader-check-theme' ); ?>
										</p>
									</div>
									<div class="col-sm-2">
										<button type="submit" class="btn btn-primary btn-lg">
											<?php _e( 'Submit', 'screen-reader-check-theme' ); ?>
										</button>
									</div>
								</div>
								<div class="switch-link-wrap">
									<button id="show-screen-reader-check-html-input" class="btn btn-link btn-sm">
										<?php _e( 'Or enter the HTML code directly', 'screen-reader-check-theme' ); ?>
									</button>
								</div>
							</div>

							<div id="screen-reader-check-html-input" style="display:none;">
								<div class="form-group row">
									<label for="html" class="screen-reader-text">
										<?php _e( 'HTML Code', 'screen-reader-check-theme' ); ?>
									</label>
									<div class="col-sm-10">
										<textarea id="html" class="form-control" rows="8" aria-describedby="html-description"></textarea>
										<p id="html-description" class="form-text text-muted">
											<?php _e( 'Enter the HTML code you would like to check.', 'screen-reader-check-theme' ); ?>
										</p>
									</div>
									<div class="col-sm-2">
										<button type="submit" class="btn btn-primary btn-lg">
											<?php _e( 'Submit', 'screen-reader-check-theme' ); ?>
										</button>
									</div>
								</div>
								<div class="switch-link-wrap">
									<button id="show-screen-reader-check-url-input" class="btn btn-link btn-sm">
										<?php _e( 'Or enter the URL of a website', 'screen-reader-check-theme' ); ?>
									</button>
								</div>
							</div>

						</div>

						<div class="advanced-options" style="display:none;">

							<h2><?php _e( 'Advanced Options', 'screen-reader-check-theme' ); ?></h2>

						</div>

					</form>

				</main>

			</div>
		</div>

<?php get_footer();
