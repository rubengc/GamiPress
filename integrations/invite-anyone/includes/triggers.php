<?php
/**
 * Triggers
 *
 * @package GamiPress\Invite_Anyone\Triggers
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @param array $triggers
 * @return  mixed
 * @since   1.0.0
 *
 */
function gamipress_invite_anyone_activity_triggers($triggers) {

    $triggers[__('Invite Anyone', 'gamipress')] = array(
        'gamipress_invite_anyone_send_invite'       => __( 'Send an invitation', 'gamipress' ),
        'gamipress_invite_anyone_accept_invite'     => __( 'Accept an invitation', 'gamipress' ),
        'gamipress_invite_anyone_accepted_invite'   => __( 'Get an invitation accepted', 'gamipress' ),
    );

    return $triggers;

}

add_filter('gamipress_activity_triggers', 'gamipress_invite_anyone_activity_triggers');

/**
 * Get user for a given trigger action.
 *
 * @param integer $user_id user ID to override.
 * @param string $trigger Trigger name.
 * @param array $args Passed trigger args.
 * @return integer          User ID.
 * @since  1.0.0
 *
 */
function gamipress_invite_anyone_trigger_get_user_id($user_id, $trigger, $args) {

    switch ($trigger) {
        case 'gamipress_invite_anyone_send_invite':
        case 'gamipress_invite_anyone_accept_invite':
            $user_id = $args[0];
            break;
        case 'gamipress_invite_anyone_accepted_invite':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}

add_filter('gamipress_trigger_get_user_id', 'gamipress_invite_anyone_trigger_get_user_id', 10, 3);

/**
 * Extended meta data for event trigger logging
 *
 * @param array $log_meta
 * @param integer $user_id
 * @param string $trigger
 * @param integer $site_id
 * @param array $args
 *
 * @return array
 * @since 1.0.0
 *
 */
function gamipress_invite_anyone_log_event_trigger_meta_data($log_meta, $user_id, $trigger, $site_id, $args) {

    switch ($trigger) {
        case 'gamipress_invite_anyone_send_invite':
            $log_meta['email'] = $args[1];
            break;
        case 'gamipress_invite_anyone_accepted_invite':
            $log_meta['invited_user_id'] = $args[1];
            break;
        case 'gamipress_invite_anyone_accept_invite':
            $log_meta['inviter_user_id'] = $args[0];
            break;
    }

    return $log_meta;
}

add_filter('gamipress_log_event_trigger_meta_data', 'gamipress_invite_anyone_log_event_trigger_meta_data', 10, 5);

/**
 * Extra data fields
 *
 * @param array $fields
 * @param int $log_id
 * @param string $type
 *
 * @return array
 * @since 1.0.0
 *
 */
function gamipress_invite_anyone_log_extra_data_fields($fields, $log_id, $type) {

    $prefix = '_gamipress_';

    $log = ct_get_object($log_id);
    $trigger = $log->trigger_type;

    if ($type !== 'event_trigger') {
        return $fields;
    }

    switch ($trigger) {

        case 'gamipress_invite_anyone_send_invite':
            $fields[] = array(
                'name' => __( 'Email', 'gamipress' ),
                'desc' => __( 'Email user sent an invitation.', 'gamipress' ),
                'id' => $prefix . 'email',
                'type' => 'text',
            );
            break;
        case 'gamipress_invite_anyone_accepted_invite':
            $fields[] = array(
                'name' => __( 'Invited User', 'gamipress' ),
                'desc' => __( 'User that has been invited.', 'gamipress' ),
                'id' => $prefix . 'invited_user_id',
                'type' => 'select',
                'options_cb' => 'gamipress_options_cb_users'
            );
            break;
        case 'gamipress_invite_anyone_accept_invite':
            $fields[] = array(
                'name' => __( 'Inviter User', 'gamipress' ),
                'desc' => __( 'User that has sent the invitation.', 'gamipress' ),
                'id' => $prefix . 'inviter_user_id',
                'type' => 'select',
                'options_cb' => 'gamipress_options_cb_users'
            );
            break;
    }

    return $fields;

}

add_filter('gamipress_log_extra_data_fields', 'gamipress_invite_anyone_log_extra_data_fields', 10, 3);

/**
 * Extra filter to check duplicated activity
 *
 * @param bool $return
 * @param integer $user_id
 * @param string $trigger
 * @param integer $site_id
 * @param array $args
 *
 * @return bool                    True if user deserves trigger, else false
 * @since 1.0.0
 *
 */
function gamipress_invite_anyone_trigger_duplicity_check($return, $user_id, $trigger, $site_id, $args) {

    // If user doesn't deserves trigger, then bail to prevent grant access
    if (!$return)
        return $return;

    $log_meta = array(
        'type' => 'event_trigger',
        'trigger_type' => $trigger,
    );

    switch ($trigger) {
        case 'gamipress_invite_anyone_send_invite':
            // Prevent duplicate email invitation
            $log_meta['email'] = $args[1];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        case 'gamipress_invite_anyone_accepted_invite':
            // Prevent duplicate invited user ID
            $log_meta['invited_user_id'] = $args[1];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        case 'gamipress_invite_anyone_accept_invite':
            // Prevent duplicate inviter user ID
            $log_meta['inviter_user_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
    }

    return $return;

}

add_filter('gamipress_user_deserves_trigger', 'gamipress_invite_anyone_trigger_duplicity_check', 10, 5);