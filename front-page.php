<?php
/**
 * @package ScreenReaderCheckTheme
 * @since 1.0.0
 */

$options = function_exists( 'src_get' ) ? src_get()->tests->get_global_options() : array();
$i_break = intval( ceil( count( $options ) / 2 ) );
$i = 0;

get_header(); ?>

		<div class="container">
			<div class="row">

				<main id="main" class="site-content col-md-12" role="main">

					<form id="screen-reader-check-form" class="screen-reader-check-form" method="post" novalidate>

						<h2><?php _e( 'Check your code now', 'screen-reader-check-theme' ); ?></h2>

						<div id="main-input" class="form-group row" aria-live="polite">
							<div id="screen-reader-check-url-input" class="col-sm-10">
								<input type="url" id="url" name="url" class="form-control form-control-lg" aria-label="<?php _e( 'URL', 'screen-reader-check-theme' ); ?>" aria-describedby="url-description" aria-required="true" required>
								<p id="url-description" class="form-text text-muted">
									<?php _e( 'Enter the URL of the website you would like to check.', 'screen-reader-check-theme' ); ?>
								</p>
							</div>

							<div id="screen-reader-check-html-input" class="col-sm-10" style="display:none;">
								<textarea id="html" name="html" class="form-control" rows="8" aria-label="<?php _e( 'HTML Code', 'screen-reader-check-theme' ); ?>" aria-describedby="html-description"></textarea>
								<p id="html-description" class="form-text text-muted">
									<?php _e( 'Enter the HTML code you would like to check.', 'screen-reader-check-theme' ); ?>
								</p>
							</div>

							<div class="col-sm-2">
								<button id="screen-reader-check-form-submit" type="submit" class="btn btn-primary btn-lg">
									<?php _e( 'Submit', 'screen-reader-check-theme' ); ?>
								</button>
							</div>
						</div>

						<div class="switch-link-wrap">
							<button type="button" id="toggle-main-input" class="btn btn-link btn-sm" data-target="#screen-reader-check-html-input" data-hide="#screen-reader-check-url-input" data-text="<?php _e( 'Or enter the URL of a website', 'screen-reader-check-theme' ); ?>" aria-controls="main-input">
								<?php _e( 'Or enter the HTML code directly', 'screen-reader-check-theme' ); ?>
							</button>
						</div>

						<?php if ( ! empty( $options ) ) : ?>
							<div class="advanced-options card">
								<div class="card-header">
									<?php _e( 'Advanced Options', 'screen-reader-check-theme' ); ?>
									<button type="button" id="advanced-options-toggle" class="advanced-options-toggle btn btn-link btn-sm" data-toggle="collapse" data-target="#advanced-options-content" aria-controls="advanced-options-content" aria-expanded="false" aria-label="<?php _e( 'Toggle advanced options panel', 'screen-reader-check-theme' ); ?>"></button>
								</div>

								<div id="advanced-options-content" class="card-block collapse">
									<p class="card-text"><?php _e( 'Specifying the following options will help the tool provide more accurate results.', 'screen-reader-check-theme' ); ?></p>

									<div class="row">
										<div class="col-md-6">
											<?php while ( $i < $i_break ) : ?>
												<div class="form-group">
													<label for="options-<?php echo $options[ $i ]['slug']; ?>"><?php echo $options[ $i ]['label']; ?></label>
													<input type="<?php echo $options[ $i ]['type']; ?>" id="options-<?php echo $options[ $i ]['slug']; ?>" name="options[<?php echo $options[ $i ]['slug']; ?>]" value="<?php echo $options[ $i ]['default']; ?>" class="form-control form-control-sm" aria-describedby="options-<?php echo $options[ $i ]['slug']; ?>-description">
													<p id="options-<?php echo $options[ $i ]['slug']; ?>-description" class="form-text text-muted"><?php echo $options[ $i ]['description']; ?></p>
												</div>
												<?php $i++; ?>
											<?php endwhile; ?>
										</div>

										<div class="col-md-6">
											<?php while ( $i < count( $options ) ) : ?>
												<div class="form-group">
													<label for="options-<?php echo $options[ $i ]['slug']; ?>"><?php echo $options[ $i ]['label']; ?></label>
													<input type="<?php echo $options[ $i ]['type']; ?>" id="options-<?php echo $options[ $i ]['slug']; ?>" name="options[<?php echo $options[ $i ]['slug']; ?>]" value="<?php echo $options[ $i ]['default']; ?>" class="form-control form-control-sm" aria-describedby="options-<?php echo $options[ $i ]['slug']; ?>-description">
													<p id="options-<?php echo $options[ $i ]['slug']; ?>-description" class="form-text text-muted"><?php echo $options[ $i ]['description']; ?></p>
												</div>
												<?php $i++; ?>
											<?php endwhile; ?>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>

					</form>

					<div class="results card" role="log">
						<h3 class="card-header">
							<?php _e( 'Results', 'screen-reader-check-theme' ); ?>
							<span id="results-loader" class="src-loader" aria-hidden="true"></span>
						</h3>

						<div class="card-block">
							<p><?php _e( 'This area shows results for your code check.', 'screen-reader-check-theme' ); ?></p>

							<ul id="results-log" class="list-unstyled" aria-live="polite" aria-atomic="false"></ul>
						</div>
					</div>

				</main>

			</div>
		</div>

<?php get_footer();
