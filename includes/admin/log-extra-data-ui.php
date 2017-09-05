<?php
/**
 * Log Extra Data UI
 *
 * @package     GamiPress\Admin\Log_Extra_Data_UI
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add Points Awards metabox to the Badge post editor
 *
 * @since  1.0.0
 * @return void
 */
function gamipress_add_log_extra_data_ui_meta_box() {
    add_meta_box( 'gamipress_log_extra_data_ui', __( 'Extra Data', 'gamipress' ), 'gamipress_log_extra_data_ui_meta_box', 'gamipress-log', 'advanced', 'default' );
}
add_action( 'add_meta_boxes', 'gamipress_add_log_extra_data_ui_meta_box' );

/**
 * Renders the HTML for meta box, refreshes whenever a new step is added
 *
 * @since  1.0.0
 * @param  WP_Post $post The current post object
 * @return void
 */
function gamipress_log_extra_data_ui_meta_box( $post  = null ) {
    ?>
    <div id="log-extra-data-ui">
        <?php gamipress_log_extra_data_ui_html( $post->ID ); ?>
    </div>
    <?php
}

/**
 * Renders the HTML for meta box based on the log type given
 *
 * @since  1.0.0
 * @param  WP_Post $post The current post object
 * @param  string $type Type to render form
 * @return void
 */
function gamipress_log_extra_data_ui_html( $post_id, $type = null ) {
    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';
    $fields = array();

    if( $type === null ) {
        $type = get_post_meta( $post_id, $prefix .'type', true );
    }

    if( $type === 'event_trigger' ) {

        $fields = array(
            array(
                'name' 	=> __( 'Trigger', 'gamipress' ),
                'desc' 	=> __( 'The event user has triggered.', 'gamipress' ),
                'id'   	=> $prefix . 'trigger_type',
                'type' 	=> 'advanced_select',
                'options' 	=> gamipress_get_activity_triggers(),
            ),
            array(
                'name' 	=> __( 'Count', 'gamipress' ),
                'desc' 	=> __( 'Number of times user triggered this event until this log.', 'gamipress' ),
                'id'   	=> $prefix . 'count',
                'type' 	=> 'text',
            ),
        );

        $trigger = get_post_meta( $post_id, $prefix . 'trigger_type', true );

        // If is a specific activity trigger, then add the achievement_post field
        if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

            $achievement_post_id = get_post_meta( $post_id, $prefix . 'achievement_post', true );

            $fields[] = array(
                'name' 	=> __( 'Assigned Post', 'gamipress' ),
                'desc' 	=> __( 'Attached post to this log.', 'gamipress' ),
                'id'   	=> $prefix . 'achievement_post',
                'type' 	=> 'select',
                'options' 	=> array(
                    $achievement_post_id => get_post_field( 'post_title', $achievement_post_id ),
                ),
            );
        }
    } else if( $type === 'achievement_earn' || $type === 'achievement_award' ) {
        $achievement_id = get_post_meta( $post_id, $prefix . 'achievement_id', true );

        $fields = array(
            array(
                'name' 	=> __( 'Achievement', 'gamipress' ),
                'desc' 	=> __( 'Achievement user has earned.', 'gamipress' ),
                'id'   	=> $prefix . 'achievement_id',
                'type' 	=> 'select',
                'options' 	=> array(
                    $achievement_id => get_post_field( 'post_title', $achievement_id ),
                ),
            ),
        );
    } else if( $type === 'points_award' || $type === 'points_earn' ) {
        // Grab our points types as an array
        $points_types_options = array(
            '' => 'Default'
        );

        foreach( gamipress_get_points_types() as $slug => $data ) {
            $points_types_options[$slug] = $data['plural_name'];
        }

        $fields = array(
            array(
                'name' 	=> __( 'Points', 'gamipress' ),
                'desc' 	=> __( 'Points user has earned.', 'gamipress' ),
                'id'   	=> $prefix . 'points',
                'type' 	=> 'text_small',
            ),
            array(
                'name' 	=> __( 'Points Type', 'gamipress' ),
                'desc' 	=> __( 'Points type user has earned.', 'gamipress' ),
                'id'   	=> $prefix . 'points_type',
                'type' 	=> 'select',
                'options' => $points_types_options
            ),
            array(
                'name' 	=> __( 'Total Points', 'gamipress' ),
                'desc' 	=> __( 'Total points user has earned until this log.', 'gamipress' ),
                'id'   	=> $prefix . 'total_points',
                'type' 	=> 'text_small',
            ),
        );

        if( $type === 'points_award' ) {
            $admin_id = get_post_meta( $post_id, $prefix . 'admin_id', true );
            $admin = get_userdata( $admin_id );

            $fields[] = array(
                'name' 	=> __( 'Administrator', 'gamipress' ),
                'desc' 	=> __( 'User has made the award.', 'gamipress' ),
                'id'   	=> $prefix . 'admin_id',
                'type' 	=> 'select',
                'options' 	=> array(
                    $admin_id => $admin->user_login,
                ),
            );
        }
    }

    $fields = apply_filters( 'gamipress_log_extra_data_fields', $fields, $post_id, $type );

    if( ! empty( $fields ) ) {
        // Create a new box to render the form
        $cmb2 = new CMB2( array(
            'id'      => 'log_extra_data_ui_box',
            'classes' => 'gamipress-form gamipress-box-form',
            'hookup'  => false,
            'show_on' => array(
                'key'   => 'gamipress-log',
                'value' => $post_id
            ),
            'fields' => $fields
        ) );

        $cmb2->object_id( $post_id );

        $cmb2->show_form();
    } else {
        _e( 'No extra data registered', 'gamipress' );
    }
}

/**
 * AJAX Handler for retrieve the HTML with
 *
 * @since 1.0.0
 * @return void
 */
function gamipress_get_log_extra_data_ui_ajax_handler() {
    gamipress_log_extra_data_ui_html( $_REQUEST['post_id'], $_REQUEST['type'] );
    die;
}
add_action( 'wp_ajax_get_log_extra_data_ui', 'gamipress_get_log_extra_data_ui_ajax_handler' );