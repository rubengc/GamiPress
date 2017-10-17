<?php
/**
 * Upgrades
 *
 * @package     GamiPress\Admin\Upgrades
 * @since       1.2.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_process_upgrades() {

    // Get stored version
    $stored_version = get_option( 'gamipress_version', '1.0.0' );

    if( $stored_version === GAMIPRESS_VER ) {
        return;
    }

    do_action( 'gamipress_before_process_upgrades', $stored_version );

    if ( version_compare( $stored_version, '1.1.0', '<' ) ) {
        gamipress_110_upgrades();
    }

    if ( version_compare( $stored_version, '1.2.7', '<' ) ) {
        gamipress_127_upgrades();
    }

    do_action( 'gamipress_after_process_upgrades', $stored_version );

    // Updated stored version
    update_option( 'gamipress_version', GAMIPRESS_VER );

}
add_action( 'admin_init', 'gamipress_process_upgrades' );

function gamipress_110_upgrades() {

    global $wpdb;

    // Update wp_login trigger to gamipress_login
    $wpdb->update(
        $wpdb->postmeta,
        array(
            'meta_value' => 'gamipress_login'
        ),
        array(
            'meta_key' => '_gamipress_trigger_type',
            'meta_value' => 'wp_login',
        )
    );

}

function gamipress_127_upgrades() {

    global $wpdb;

    // A bug of wrong points awards on newly created points types was discover and fixed, so old points awards need to be updated
    $points_awards = $wpdb->get_results( $wpdb->prepare(
        "SELECT p.post_id
		FROM   $wpdb->postmeta AS p
		WHERE  p.meta_key = %s
		       AND p.meta_value = %s",
        "_gamipress_points_type",
        ""
    ) );

    foreach( $points_awards as $points_award ) {

        $points_type_id = gamipress_get_requirement_connected_id( $points_award->post_id );

        if( $points_type_id ) {
            update_post_meta( $points_award->post_id, '_gamipress_points_type', get_post_field( 'post_name', $points_type_id ) );
        }
    }

}
