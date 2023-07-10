<?php
/**
 * Requirements
 *
 * @package GamiPress\WishList_Member\Requirements
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the link fields to the requirement object
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_wishlist_member_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] ) ) {

        if( in_array( $requirement['trigger_type'], array(
            'gamipress_wishlist_member_add_specific_level',
            'gamipress_wishlist_member_remove_specific_level',
            'gamipress_wishlist_member_approve_specific_level',
            'gamipress_wishlist_member_unapprove_specific_level',
            'gamipress_wishlist_member_cancel_specific_level',
            'gamipress_wishlist_member_uncancel_specific_level',
        ) ) ) {

            // The level id
            $requirement['wishlist_member_level_id'] = gamipress_get_post_meta( $requirement_id, '_gamipress_wishlist_member_level_id', true );

        }
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_wishlist_member_requirement_object', 10, 2 );

/**
 * WishList_Member fields on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_wishlist_member_requirement_ui_fields( $requirement_id, $post_id ) {

    global $WishListMemberInstance;

    $levels = $WishListMemberInstance->GetOption( 'wpm_levels' );
    $level_id = gamipress_get_post_meta( $requirement_id, '_gamipress_wishlist_member_level_id', true );

    $options = array(
        '' => __( 'Choose a membership level', 'gamipress' ),
    );

    if( function_exists( 'wlmapi_get_levels' ) ) {

        // Get all registered levels
        $levels = wlmapi_get_levels();

        // Check that levels are correctly setup
        if ( is_array( $levels )
            && isset( $levels['levels'] )
            && isset( $levels['levels']['level'] )
            && ! empty( $levels['levels']['level'] ) ) {

            // Loop levels to add them as options
            foreach ( $levels['levels']['level'] as $level ) {
                $options[$level['id']] = $level['name'];
            }

        }

    } ?>

    <select class="select-wishlist-member-level-id">
        <?php foreach( $options as $value => $label ) { ?>
                <option value="<?php echo $value; ?>" <?php selected( $level_id, $value ); ?>><?php echo $label; ?></option>
        <?php } ?>
    </select>
    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_wishlist_member_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the link fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_wishlist_member_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] ) ) {

        if( in_array( $requirement['trigger_type'], array(
            'gamipress_wishlist_member_add_specific_level',
            'gamipress_wishlist_member_remove_specific_level',
            'gamipress_wishlist_member_approve_specific_level',
            'gamipress_wishlist_member_unapprove_specific_level',
            'gamipress_wishlist_member_cancel_specific_level',
            'gamipress_wishlist_member_uncancel_specific_level',
        ) ) ) {

            // The level id
            update_post_meta( $requirement_id, '_gamipress_wishlist_member_level_id', $requirement['wishlist_member_level_id'] );

        }
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_wishlist_member_ajax_update_requirement', 10, 2 );