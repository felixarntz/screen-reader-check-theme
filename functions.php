<?php
/**
 * Theme functions
 *
 * @package ScreenReaderCheckTheme
 * @since 1.0.0
 */

define( 'SCREEN_READER_CHECK_THEME_VERSION', '1.0.0' );

function srctheme_setup() {
	load_theme_textdomain( 'screen-reader-check-theme', get_template_directory() . '/languages' );

	add_editor_style( 'assets/dist/css/editor' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) . '.css' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-header', array(
		'default-image'          => '',
		'default-text-color'     => '333333',
		'width'                  => 1280,
		'height'                 => 350,
		'flex-height'            => true,
		'wp-head-callback'       => 'srctheme_header_style',
	) );
	add_theme_support( 'custom-background', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) );
	add_theme_support( 'custom-logo', array(
		'width'       => 100,
		'height'      => 100,
		'flex-height' => true,
	) );

	set_post_thumbnail_size( 1280, 720 );

	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'screen-reader-check-theme' ),
		'footer'  => __( 'Footer Navigation', 'screen-reader-check-theme' ),
	) );
}
add_action( 'after_setup_theme', 'srctheme_setup' );

function srctheme_content_width() {
	$GLOBALS['content_width'] = 720;
}
add_action( 'after_setup_theme', 'srctheme_content_width', 0 );

function srctheme_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Primary Sidebar', 'screen-reader-check-theme' ),
		'id'            => 'primary',
		'description'   => __( 'This sidebar is shown beside the main content.', 'screen-reader-check-theme' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'srctheme_widgets_init' );

function srctheme_enqueue_scripts() {
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	$vars = array(
		'name'			=> __( 'Screen Reader Check Theme', 'screen-reader-check-theme' ),
		'description'	=> __( 'Theme for the Screen Reader Check plugin, based on Bootstrap.', 'screen-reader-check-theme' ),
		'version'		=> SCREEN_READER_CHECK_THEME_VERSION,
		'ajax'			=> array(
			'url'			=> admin_url( 'admin-ajax.php' ),
			'nonce'			=> wp_create_nonce( 'screen-reader-check-theme' ),
		),
		'settings'		=> array(
			'init_tooltips'	=> false,
			'init_popovers'	=> false,
			'init_fancybox'	=> true,
			'wrap_embeds'	=> true,
		),
		'i18n'			=> array(
			'plugin_missing'              => __( 'Screen Reader Check plugin is missing!', 'screen-reader-check-theme' ),
			'an_error_occurred'           => __( 'An error occurred!', 'screen-reader-check-theme' ),
			'wcag_guideline'              => __( 'WCAG 2.0 Guideline', 'screen-reader-check-theme' ),
			'further_reading'             => __( 'Further Reading', 'screen-reader-check-theme' ),
			'close'                       => __( 'Close', 'screen-reader-check-theme' ),
			'action_required'             => __( 'Your action is required.', 'screen-reader-check-theme' ),
			'action_required_description' => __( 'In order to complete this test, you need to provide some more information.', 'screen-reader-check-theme' ),
			'submit'                      => __( 'Submit', 'screen-reader-check-theme' ),
			'test_description'            => __( 'Test Description', 'screen-reader-check-theme' ),
			'test_results'                => __( 'Test Results', 'screen-reader-check-theme' ),
		),
	);

	wp_enqueue_script( 'tether', get_template_directory_uri() . '/assets/vendor/tether/dist/js/tether' . $min . '.js', array(), '1.3.4', true );
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/vendor/bootstrap/dist/js/bootstrap' . $min . '.js', array( 'jquery', 'tether' ), '4.0.0', true );

	wp_enqueue_style( 'fancybox', get_template_directory_uri() . '/assets/vendor/fancybox/source/jquery.fancybox.css', array(), '2.1.5' );
	wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/assets/vendor/fancybox/source/jquery.fancybox.pack.js', array( 'jquery' ), '2.1.5', true );

	$dependencies = array( 'jquery', 'wp-util', 'fancybox' );
	if ( is_front_page() ) {
		$dependencies[] = 'screen-reader-check';
	}

	wp_enqueue_style( 'screen-reader-check-theme', get_template_directory_uri() . '/assets/dist/css/app' . $min . '.css', array(), SCREEN_READER_CHECK_THEME_VERSION, 'all' );
	wp_enqueue_script( 'screen-reader-check-theme', get_template_directory_uri() . '/assets/dist/js/app' . $min . '.js', $dependencies, SCREEN_READER_CHECK_THEME_VERSION, true );
	wp_localize_script( 'screen-reader-check-theme', 'wp_theme', $vars );

	if ( is_singular() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'srctheme_enqueue_scripts' );

function srctheme_header_style() {
	$header_text_color = get_header_textcolor();

	if ( HEADER_TEXTCOLOR === $header_text_color ) {
		return;
	}

	?>
	<style type="text/css">
	<?php if ( ! display_header_text() ) : ?>
		.site-title,
		.site-description {
			position: absolute;
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php else : ?>
		.site-title a,
		.site-description {
			color: #<?php echo esc_attr( $header_text_color ); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}

require_once get_template_directory() . '/inc/template-tags.php';

require_once get_template_directory() . '/inc/customizer.php';
