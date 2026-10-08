<?php
/**
 * Plugin Name: CEREDIS Core
 * Plugin URI: https://ceredis.net/
 * Description: Network-wide foundation for the CEREDIS WordPress distribution.
 * Version: 0.1.0
 * Author: CEREDIS
 * Author URI: https://ceredis.net/
 * License: GPL-2.0-or-later
 * Requires at least: 7.1
 * Requires PHP: 8.3
 */

defined( 'ABSPATH' ) || exit;

define( 'CEREDIS_CORE_VERSION', '0.1.0' );

/**
 * Add a stable CEREDIS marker to the front-end body classes.
 */
function ceredis_core_body_class( array $classes ): array {
    $classes[] = 'ceredis-network';

    if ( is_multisite() ) {
        $classes[] = 'ceredis-multisite';
    }

    return $classes;
}
add_filter( 'body_class', 'ceredis_core_body_class' );

/**
 * Expose the distribution version in the HTML generator meta only in development.
 */
function ceredis_core_generator_meta(): void {
    if ( defined( 'WP_ENVIRONMENT_TYPE' ) && 'production' !== WP_ENVIRONMENT_TYPE ) {
        printf(
            '<meta name="generator" content="CEREDIS WordPress %s" />' . "\n",
            esc_attr( CEREDIS_CORE_VERSION )
        );
    }
}
add_action( 'wp_head', 'ceredis_core_generator_meta', 1 );
