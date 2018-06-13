<?php
/**
 * 1.3.0 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.3.0
 * @since       1.3.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.3.0 as last required upgrade
 *
 * @return string
 */
function gamipress_130_is_last_required_upgrade() {

    return '1.3.0';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_130_is_last_required_upgrade', 130 );

/**
 * Process 1.3.0 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_130_upgrades( $stored_version ) {

    if ( version_compare( $stored_version, '1.3.0', '>=' ) ) {
        return $stored_version;
    }

    // Setup new default GamiPress options
    $gamipress_settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();

    // Initialize achievement earned emails settings
    if ( ! isset( $gamipress_settings['achievement_earned_email_content'] ) ) {

        $gamipress_settings['achievement_earned_email_subject'] = __( '[{site_title}] {user_first}, you unlocked the {achievement_type} {achievement_title}', 'gamipress' );
        $gamipress_settings['achievement_earned_email_content'] = '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
            . '{achievement_image}' . "\n"
            . __( 'You unlocked the {achievement_type} {achievement_title} by completing the following steps:', 'gamipress' ) . "\n"
            . '{achievement_steps}' . "\n\n"
            . __( 'Best regards', 'gamipress' );

    }

    // Initialize step completed emails settings
    if ( ! isset( $gamipress_settings['step_completed_email_content'] ) ) {

        $gamipress_settings['step_completed_email_subject'] = __( '[{site_title}] {user_first}, you complete a step of the {achievement_type} {achievement_title}', 'gamipress' );
        $gamipress_settings['step_completed_email_content'] = '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
            . '{achievement_image}' . "\n"
            . __( 'You completed the step "{label}" of the {achievement_type} {achievement_title}!', 'gamipress' ) . "\n\n"
            . __( 'You need to complete the following steps to completely unlock this {achievement_type}:', 'gamipress' ) . "\n"
            . '{achievement_steps}' . "\n\n"
            . __( 'Best regards', 'gamipress' );

    }

    // Initialize points award completed emails settings
    if ( ! isset( $gamipress_settings['points_award_completed_email_content'] ) ) {

        $gamipress_settings['points_award_completed_email_subject'] = __( '[{site_title}] {user_first}, you got {points} {points_type}', 'gamipress' );
        $gamipress_settings['points_award_completed_email_content'] = '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
            . __( 'You got {points} {points_type} for completing "{label}".', 'gamipress' ) . "\n"
            . __( 'Your new {points_type} balance is:', 'gamipress' ) . "\n"
            . '{points_balance}' . "\n\n"
            . __( 'Best regards', 'gamipress' );

    }

    update_option( 'gamipress_settings', $gamipress_settings );

    $stored_version = '1.3.0';

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_130_upgrades', 130 );