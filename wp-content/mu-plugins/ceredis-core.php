<?php
/**
 * CEREDIS Core — MU-plugin loader.
 *
 * WordPress automatically loads PHP files directly inside mu-plugins/.
 * The actual CEREDIS Core implementation lives in its own subdirectory.
 */

defined( 'ABSPATH' ) || exit;

require_once WPMU_PLUGIN_DIR . '/ceredis-core/ceredis-core.php';
