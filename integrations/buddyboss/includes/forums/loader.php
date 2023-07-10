<?php
/**
 * Loader
 *
 * @package GamiPress\Forums\Loader
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Plugin version
define( 'GAMIPRESS_BBP_VER', '1.1.9' );

// Plugin path
define( 'GAMIPRESS_BBP_DIR', plugin_dir_path( __FILE__ ) );

// Plugin URL
define( 'GAMIPRESS_BBP_URL', plugin_dir_url( __FILE__ ) );

require_once GAMIPRESS_BBP_DIR . 'includes/admin.php';
require_once GAMIPRESS_BBP_DIR . 'includes/filters.php';
require_once GAMIPRESS_BBP_DIR . 'includes/listeners.php';
require_once GAMIPRESS_BBP_DIR . 'includes/scripts.php';
require_once GAMIPRESS_BBP_DIR . 'includes/triggers.php';