<?php
/**
 * Theme customizer support
 *
 * @package ScreenReaderCheckTheme
 * @since 1.0.0
 */

function srctheme_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'				=> '.site-title a',
			'container_inclusive'	=> false,
			'render_callback'		=> 'srctheme_render_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'				=> '.site-description',
			'container_inclusive'	=> false,
			'render_callback'		=> 'srctheme_render_blogdescription',
		) );
	}
}
add_action( 'customize_register', 'srctheme_customize_register' );

function srctheme_customize_preview_init() {
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_script( 'screen-reader-check-theme-customizer', get_template_directory_uri() . '/assets/dist/js/customize-preview' . $min . '.js', array( 'jquery', 'customize-preview' ), SCREEN_READER_CHECK_THEME_VERSION, true );
}
add_action( 'customize_preview_init', 'srctheme_customize_preview_init' );

function srctheme_render_blogname() {
	bloginfo( 'name' );
}

function srctheme_render_blogdescription() {
	bloginfo( 'description' );
}
