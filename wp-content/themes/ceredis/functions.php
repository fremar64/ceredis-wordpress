<?php
/**
 * CEREDIS theme functions and definitions.
 *
 * @package CEREDIS
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the theme stylesheet on the front end.
 */
function ceredis_enqueue_styles(): void {
	wp_enqueue_style(
		'ceredis-theme-style',
		get_stylesheet_uri(),
		array(),
		(string) filemtime( get_stylesheet_directory() . '/style.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'ceredis_enqueue_styles' );

/**
 * Enqueue the floating back-to-top control script.
 */
function ceredis_enqueue_back_to_top(): void {
	wp_enqueue_script(
		'ceredis-back-to-top',
		get_stylesheet_directory_uri() . '/assets/js/back-to-top.js',
		array(),
		(string) filemtime( get_stylesheet_directory() . '/assets/js/back-to-top.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'ceredis_enqueue_back_to_top' );

/**
 * Keep aria-expanded in sync only on pages that render core/navigation.
 * The native WordPress toggle exposes aria-haspopup but no expanded state.
 */
function ceredis_enqueue_navigation_state( string $block_content, $block ): string {
	if ( ! wp_script_is( 'ceredis-navigation-state', 'enqueued' ) ) {
		$script_path = get_stylesheet_directory() . '/assets/js/navigation-state.js';
		wp_enqueue_script(
			'ceredis-navigation-state',
			get_stylesheet_directory_uri() . '/assets/js/navigation-state.js',
			array(),
			(string) filemtime( $script_path ),
			true
		);
	}

	return $block_content;
}
add_filter( 'render_block_core/navigation', 'ceredis_enqueue_navigation_state', 10, 2 );

/**
 * Add favicon to the head.
 */
function ceredis_add_favicon(): void {
	$favicon_path = get_theme_file_path( 'assets/images/favicon.png' );

	if ( ! file_exists( $favicon_path ) ) {
		return;
	}

	echo '<link rel="icon" href="' .
		esc_url( get_theme_file_uri( 'assets/images/favicon.png' ) ) .
		'" type="image/png" sizes="any" />' . "\n";
}
add_action( 'wp_head', 'ceredis_add_favicon' );

/**
 * Render the floating back-to-top button.
 */
function ceredis_render_back_to_top(): void {
	echo '<button type="button" class="ceredis-back-to-top" aria-label="Retour en haut de la page" aria-hidden="true" title="Retour en haut de la page"><span aria-hidden="true">↑</span></button>';
}
add_action( 'wp_footer', 'ceredis_render_back_to_top' );
