<?php
/**
 * Loader
 *
 * @package GamiPress\Community\Loader
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Plugin version
define( 'GAMIPRESS_BP_VER', '1.3.7' );

// Plugin file
define( 'GAMIPRESS_BP_FILE', __FILE__ );

// Plugin path
define( 'GAMIPRESS_BP_DIR', plugin_dir_path( __FILE__ ) );

// Plugin URL
define( 'GAMIPRESS_BP_URL', plugin_dir_url( __FILE__ ) );

require_once GAMIPRESS_BP_DIR . 'includes/admin.php';
require_once GAMIPRESS_BP_DIR . 'includes/functions.php';
require_once GAMIPRESS_BP_DIR . 'includes/listeners.php';
require_once GAMIPRESS_BP_DIR . 'includes/requirements.php';
require_once GAMIPRESS_BP_DIR . 'includes/rules-engine.php';
require_once GAMIPRESS_BP_DIR . 'includes/scripts.php';
require_once GAMIPRESS_BP_DIR . 'includes/triggers.php';

// Since the multisite feature we need an extra check here to meet if BuddyPress is active on current site
if ( class_exists( 'BuddyPress' ) ) {

    require_once GAMIPRESS_BP_DIR . 'includes/components/gamipress-achievements-bp-component.php';
    require_once GAMIPRESS_BP_DIR . 'includes/components/gamipress-points-bp-component.php';
    require_once GAMIPRESS_BP_DIR . 'includes/components/gamipress-ranks-bp-component.php';

    // Profile
    require_once GAMIPRESS_BP_DIR . 'includes/bp-members.php';

    // Activity
    require_once GAMIPRESS_BP_DIR . 'includes/bp-activity.php';

}