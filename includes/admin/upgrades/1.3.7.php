<?php
/**
 * 1.3.7 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.3.7
 * @since       1.3.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.3.7 as last required upgrade
 *
 * @return string
 */
function gamipress_137_is_last_required_upgrade() {

    return '1.3.7';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_137_is_last_required_upgrade', 137 );

/**
 * Process 1.3.7 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_137_upgrades( $stored_version ) {

    if ( version_compare( $stored_version, '1.3.7', '>=' ) ) {
        return $stored_version;
    }

    // Setup new default GamiPress options
    $gamipress_settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();

    // Initialize points deduct completed emails settings
    if ( ! isset( $gamipress_settings['points_deduct_completed_email_content'] ) ) {

        $gamipress_settings['points_deduct_completed_email_subject'] = __( '[{site_title}] {user_first}, you lost {points} {points_type}', 'gamipress' );
        $gamipress_settings['points_deduct_completed_email_content'] = '<h2>' . __( 'Oops {user_first}!', 'gamipress' ) . '</h2>' . "\n"
            . __( 'You lost {points} {points_type} for "{label}".', 'gamipress' ) . "\n"
            . __( 'Your new {points_type} balance is:', 'gamipress' ) . "\n"
            . '{points_balance}' . "\n\n"
            . __( 'Best regards', 'gamipress' );

    }

    update_option( 'gamipress_settings', $gamipress_settings );

    $stored_version = '1.3.7';

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_137_upgrades', 137 );