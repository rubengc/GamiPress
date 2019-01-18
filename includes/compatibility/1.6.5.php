<?php
/**
 * GamiPress 1.6.5 compatibility functions
 *
 * @package     GamiPress\1.6.5
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.6.5
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Save extra user meta fields to the Edit Profile screen
 *
 * @deprecated User fields are saved through ajax
 *
 * @since  1.0.0
 *
 * @param  int  $user_id      User ID being saved
 *
 * @return mixed			  false if current user can not edit users, void if can
 */
function gamipress_save_user_profile_fields( $user_id = 0 ) {

    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    // Update user's rank, but only if edited
    if ( isset( $_POST['user_rank'] ) && absint( $_POST['user_rank'] ) !== gamipress_get_user_rank_id( $user_id ) ) {
        gamipress_update_user_rank( $user_id, absint( $_POST['user_rank'] ), get_current_user_id() );
    }

    $rank_types = gamipress_get_rank_types();

    foreach( $rank_types as $rank_type => $data ) {
        // Update each user's rank type, but only if edited
        if ( isset( $_POST['user_' . $rank_type . '_rank'] ) && absint( $_POST['user_' . $rank_type . '_rank'] ) !== gamipress_get_user_rank_id( $user_id, $rank_type ) ) {
            gamipress_update_user_rank( $user_id, absint( $_POST['user_' . $rank_type . '_rank'] ), get_current_user_id() );
        }
    }

    // Update our user's points total, but only if edited
    if ( isset( $_POST['user_points'] ) &&  absint( $_POST['user_points'] ) !== gamipress_get_user_points( $user_id ) ) {
        gamipress_update_user_points( $user_id, absint( $_POST['user_points'] ), get_current_user_id() );
    }

    $points_types = gamipress_get_points_types();

    foreach( $points_types as $points_type => $data ) {
        // Update each user's points type total, but only if edited
        if ( isset( $_POST['user_' . $points_type . '_points'] ) && absint( $_POST['user_' . $points_type . '_points'] ) !== gamipress_get_user_points( $user_id, $points_type ) ) {
            gamipress_update_user_points( $user_id, absint( $_POST['user_' . $points_type . '_points'] ), get_current_user_id(), null, $points_type );
        }
    }

}
//add_action( 'personal_options_update', 'gamipress_save_user_profile_fields' );
//add_action( 'edit_user_profile_update', 'gamipress_save_user_profile_fields' );