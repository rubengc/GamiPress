<?php
/**
 * 1.3.1 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.3.1
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.3.1 as last required upgrade
 *
 * @return string
 */
function gamipress_131_is_last_required_upgrade() {

    return '1.3.1';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_131_is_last_required_upgrade', 131 );

/**
 * Process 1.3.1 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_131_upgrades( $stored_version ) {

    if ( version_compare( $stored_version, '1.3.1', '>=' ) ) {
        return $stored_version;
    }

    // Setup new default GamiPress options
    $gamipress_settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();

    // Initialize rank earned emails settings
    if ( ! isset( $gamipress_settings['rank_earned_email_content'] ) ) {

        $gamipress_settings['rank_earned_email_subject'] = __( '[{site_title}] {user_first}, you reached the {rank_type} {rank_title}', 'gamipress' );
        $gamipress_settings['rank_earned_email_content'] = '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
            . '{rank_image}' . "\n"
            . __( 'You reached the {rank_type} {rank_title} by completing the following requirements:', 'gamipress' ) . "\n"
            . '{rank_requirements}' . "\n\n"
            . __( 'Best regards', 'gamipress' );

    }

    // Initialize rank requirement completed emails settings
    if ( ! isset( $gamipress_settings['rank_requirement_completed_email_content'] ) ) {

        $gamipress_settings['rank_requirement_completed_email_subject'] = __( '[{site_title}] {user_first}, you complete a requirement of the {rank_type} {rank_title}', 'gamipress' );
        $gamipress_settings['rank_requirement_completed_email_content'] = '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
            . '{rank_image}' . "\n"
            . __( 'You completed the requirement "{label}" of the {rank_type} {rank_title}!', 'gamipress' ) . "\n\n"
            . __( 'You need to complete the following requirements to completely reach this {rank_type}:', 'gamipress' ) . "\n"
            . '{rank_requirements}' . "\n\n"
            . __( 'Best regards', 'gamipress' );

    }

    update_option( 'gamipress_settings', $gamipress_settings );

    $stored_version = '1.3.1';

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_131_upgrades', 131 );