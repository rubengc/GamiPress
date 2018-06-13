<?php
/**
 * 1.2.7 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.2.7
 * @since       1.2.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.2.7 as last required upgrade
 *
 * @return string
 */
function gamipress_127_is_last_required_upgrade() {

    return '1.2.7';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_127_is_last_required_upgrade', 127 );

/**
 * Process 1.2.7 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_127_upgrades( $stored_version ) {

    if ( version_compare( $stored_version, '1.2.7', '>=' ) ) {
        return $stored_version;
    }

    global $wpdb;

    $postmeta = GamiPress()->db->postmeta;

    // A bug of wrong points awards on newly created points types was discover and fixed, so old points awards need to be updated
    $points_awards = $wpdb->get_results( $wpdb->prepare(
        "SELECT p.post_id
		FROM   $postmeta AS p
		WHERE  p.meta_key = %s
		       AND p.meta_value = %s",
        "_gamipress_points_type",
        ""
    ) );

    foreach( $points_awards as $points_award ) {

        // Get the points type ID
        $points_type_id = gamipress_get_requirement_connected_id( $points_award->post_id );

        if( $points_type_id ) {
            // Update points award points type
            update_post_meta( $points_award->post_id, '_gamipress_points_type', get_post_field( 'post_name', $points_type_id ) );
        }
    }

    $stored_version = '1.2.7';

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_127_upgrades', 127 );