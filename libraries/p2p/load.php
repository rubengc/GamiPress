<?php
/**
 * Post2Post
 *
 * @package     GamiPress\Post2Post
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Include SCB Framework
require_once dirname( __FILE__ ) . '/wp-scb-framework/load.php';

/**
 * Load P2P.
 *
 * @since 1.0.0
 */
function _gamipress_p2p_load() {
	if ( function_exists( 'p2p_register_connection_type' ) )
		return;

	define( 'P2P_PLUGIN_VERSION', '1.6.5' );
	define( 'P2P_TEXTDOMAIN', 'posts-to-posts' );

	require_once dirname( __FILE__ ) . '/wp-lib-posts-to-posts/autoload.php';

	P2P_Storage::init();

	P2P_Query_Post::init();
	P2P_Query_User::init();

	P2P_URL_Query::init();

	//P2P_Widget::init();
	//P2P_Shortcodes::init();

	register_uninstall_hook( __FILE__, array( 'P2P_Storage', 'uninstall' ) );

	if ( is_admin() ) {
		//_gamipress_p2p_load_admin();
	}

}
scb_init( '_gamipress_p2p_load' );

/**
 * P2P init action on wp_loaded.
 *
 * @since 1.0.6
 */
function _gamipress_p2p_init() {
	// Safe hook for calling p2p_register_connection_type()
	do_action( 'p2p_init' );
}
add_action( 'wp_loaded', '_gamipress_p2p_init' );

/**
 * Load P2P admin functionality.
 *
 * @since 1.0.0
 */
function _gamipress_p2p_load_admin() {
	P2P_Autoload::register( 'P2P_', dirname( __FILE__ ) . '/p2p-admin' );

	P2P_Mustache::init();

	new P2P_Box_Factory;
	new P2P_Column_Factory;
	new P2P_Dropdown_Factory;
	//new P2P_Tools_Page;
}

/**
 * Check if we need install db again.
 *
 * @since 1.0.5
 */
function _gamipress_p2p_maybe_install() {
	if ( ! current_user_can( 'manage_options' ) )
		return;

	$current_ver = get_option( 'p2p_storage' );

	if ( $current_ver == P2P_Storage::$version && _gamipress_p2p_db_exists() )
		return;

	P2P_Storage::install();

	update_option( 'p2p_storage', P2P_Storage::$version );
}
add_action( 'admin_notices', '_gamipress_p2p_maybe_install' );

/**
 * Check if p2p db table exists.
 *
 * @since 1.0.6
 */
function _gamipress_p2p_db_exists() {
	global $wpdb;

	return ! empty( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( "{$wpdb->prefix}p2p" ) ) ) );
}
