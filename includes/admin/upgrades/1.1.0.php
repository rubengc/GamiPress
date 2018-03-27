<?php
/**
 * 1.1.0 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.1.0
 * @since       1.1.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Process 1.1.0 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_110_upgrades( $stored_version ) {

    if ( version_compare( $stored_version, '1.1.0', '>=' ) ) {
        return $stored_version;
    }

    global $wpdb;

    $postmeta = GamiPress()->db->postmeta;

    // Update wp_login trigger to gamipress_login
    $wpdb->update(
        $postmeta,
        array(
            'meta_value' => 'gamipress_login'
        ),
        array(
            'meta_key' => '_gamipress_trigger_type',
            'meta_value' => 'wp_login',
        )
    );

    $stored_version = '1.1.0';

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_110_upgrades', 110 );