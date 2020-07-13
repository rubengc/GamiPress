<?php
/**
 * Log Extra Data UI
 *
 * @package     GamiPress\Admin\Log_Extra_Data_UI
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add log data and extra data meta box
 *
 * @since  1.0.0
 * @return void
 */
function gamipress_add_log_extra_data_ui_meta_box() {

    add_meta_box( 'gamipress_log_details_ui', __( 'Log Details', 'gamipress' ), 'gamipress_log_details_ui_meta_box', 'gamipress_logs', 'side', 'default' );
    add_meta_box( 'gamipress_log_extra_data_ui', __( 'Extra Data', 'gamipress' ), 'gamipress_log_extra_data_ui_meta_box', 'gamipress_logs', 'normal', 'default' );

}
add_action( 'add_meta_boxes', 'gamipress_add_log_extra_data_ui_meta_box' );

/**
 * Renders the log data meta box
 *
 * @since  1.2.8
 *
 * @param  stdClass $object The current object
 */
function gamipress_log_details_ui_meta_box( $object  = null ) {
    ?>
    <div id="log-data-ui">
        <?php gamipress_log_details_ui_html( $object, $object->type ); ?>
    </div>
    <?php
}

/**
 * Renders the log data meta box HTML
 *
 * @since  1.2.8
 *
 * @param  stdClass $object     The current object
 * @param  string   $type       Type to render form
 */
function gamipress_log_details_ui_html( $object, $type ) {

    global $ct_table;

    // Setup vars
    $log_types = gamipress_get_log_types();
    $primary_key = $ct_table->db->primary_key;
    $object_id = $object->$primary_key;

    ?>
    <div class="submitbox" id="submitpost" style="margin: -6px -12px -12px;">

        <div id="minor-publishing">

            <div id="misc-publishing-actions">

                <div class="misc-pub-section misc-pub-post-status">
                    <?php echo __( 'Type:', 'gamipress' ); ?> <span id="post-status-display"><?php echo isset( $log_types[$object->type] ) ? $log_types[$object->type] : $object->type ; ?></span>
                </div>

                <div class="misc-pub-section misc-pub-visibility" id="visibility">
                    <?php echo __( 'Visibility:', 'gamipress' ); ?> <span id="post-visibility-display"><?php echo $object->access === 'public' ? __( 'Public', 'gamipress' ) : __( 'Private', 'gamipress' ); ?></span>
                </div>

                <div class="misc-pub-section curtime misc-pub-curtime">
                    <span id="timestamp"><?php echo __( 'Date:', 'gamipress' ); ?> <b><abbr title="<?php echo date( 'Y/m/d g:i:s a', strtotime( $object->date ) ); ?>"><?php echo date( 'Y/m/d', strtotime( $object->date ) ); ?></abbr></b></span>
                </div>

            </div>

        </div>

        <div id="major-publishing-actions">

            <div id="delete-action">
                <?php
                    printf(
                        '<a href="%s" class="submitdelete deletion" onclick="%s" aria-label="%s">%s</a>',
                        ct_get_delete_link( $ct_table->name, $object_id ),
                        "return confirm('" .
                        esc_attr( __( "Are you sure you want to delete this item?\\n\\nClick \\'Cancel\\' to go back, \\'OK\\' to confirm the delete." ) ) .
                        "');",
                        esc_attr( __( 'Delete permanently' ) ),
                        __( 'Delete Permanently' )
                    );
                ?>
            </div>

            <div id="publishing-action">
                <span class="spinner"></span>
                <?php submit_button( __( 'Save Changes' ), 'primary large', 'ct-save', false ); ?>
            </div>

            <div class="clear"></div>

        </div>

    </div>
    <?php
}

/**
 * Renders the log extra data
 *
 * @since  1.0.0
 * @param  stdClass $object The current object
 */
function gamipress_log_extra_data_ui_meta_box( $object  = null ) {
    ?>
    <div id="log-extra-data-ui">
        <?php gamipress_log_extra_data_ui_html( $object, $object->log_id, $object->type ); ?>
    </div>
    <?php
}

/**
 * Renders the HTML for meta box based on the log type given
 *
 * @since  1.0.0
 *
 * @param  stdClass $object     The current object
 * @param  integer  $object_id  The current object ID
 * @param  string   $type       Type to render form
 */
function gamipress_log_extra_data_ui_html( $object, $object_id, $type ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';
    $fields = array();

    if( $type === 'event_trigger' ) {

        $fields = array(
            array(
                'name' 	=> __( 'Trigger', 'gamipress' ),
                'desc' 	=> __( 'The event user has triggered.', 'gamipress' ),
                'id'   	=> ( ( ! is_gamipress_upgraded_to( '1.4.7' ) ) ? $prefix : '' ) . 'trigger_type',
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

        if( ! is_gamipress_upgraded_to( '1.4.7' ) ) {
            $trigger = ct_get_object_meta( $object_id, $prefix . 'trigger_type', true );
        } else {
            $trigger = $object->trigger_type;
        }

        // If is a specific activity trigger, then add the achievement_post field
        if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

            $achievement_post_id = ct_get_object_meta( $object_id, $prefix . 'achievement_post', true );
            $achievement_post_site_id = ct_get_object_meta( $object_id, $prefix . 'achievement_post_site_id', true );

            $fields[] = array(
                'name' 	=> __( 'Assigned Post', 'gamipress' ),
                'desc' 	=> __( 'Attached post to this log.', 'gamipress' ),
                'id'   	=> $prefix . 'achievement_post',
                'type' 	=> 'select',
                'options' 	=> array(
                    $achievement_post_id => gamipress_get_specific_activity_trigger_post_title( $achievement_post_id, $trigger, $achievement_post_site_id ),
                ),
            );
        }
    } else if( $type === 'achievement_earn' || $type === 'achievement_award' ) {
        $achievement_id = ct_get_object_meta( $object_id, $prefix . 'achievement_id', true );

        $fields = array(
            array(
                'name' 	=> __( 'Achievement', 'gamipress' ),
                'desc' 	=> __( 'Achievement user has earned.', 'gamipress' ),
                'id'   	=> $prefix . 'achievement_id',
                'type' 	=> 'select',
                'options' 	=> array(
                    $achievement_id => gamipress_get_post_field( 'post_title', $achievement_id ),
                ),
            ),
        );
    } else if( in_array( $type, array( 'points_award', 'points_earn', 'points_revoke', 'points_deduct', 'points_expend' ) ) ) {
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
                'desc' 	=> ( $type === 'points_award' || $type === 'points_earn' ? __( 'Points user has earned.', 'gamipress' ) : __( 'Points deducted to user.', 'gamipress' ) ),
                'id'   	=> $prefix . 'points',
                'type' 	=> 'text_small',
            ),
            array(
                'name' 	=> __( 'Points Type', 'gamipress' ),
                'desc' 	=> ( $type === 'points_award' || $type === 'points_earn' ? __( 'Points type user has earned.', 'gamipress' ) : __( 'Points type deducted to user.', 'gamipress' ) ),
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

    } else if( $type === 'rank_earn' || $type === 'rank_award' ) {
        $rank_id = ct_get_object_meta( $object_id, $prefix . 'rank_id', true );

        $fields = array(
            array(
                'name' 	=> __( 'Rank', 'gamipress' ),
                'desc' 	=> __( 'Rank user has earned.', 'gamipress' ),
                'id'   	=> $prefix . 'rank_id',
                'type' 	=> 'select',
                'options' 	=> array(
                    $rank_id => gamipress_get_post_field( 'post_title', $rank_id ),
                ),
            ),
        );
    }

    if( in_array( $type, array( 'achievement_award', 'points_award', 'points_revoke', 'rank_award' ) ) ) {
        $admin_id = ct_get_object_meta( $object_id, $prefix . 'admin_id', true );
        $admin = get_userdata( $admin_id );

        $fields[] = array(
            'name' 	=> __( 'Administrator', 'gamipress' ),
            'desc' 	=> ( $type === 'points_revoke' ? __( 'User has made the revoke.', 'gamipress' ) : __( 'User has made the award.', 'gamipress' ) ),
            'id'   	=> $prefix . 'admin_id',
            'type' 	=> 'select',
            'options' 	=> array(
                $admin_id => $admin->user_login,
            ),
        );
    }

    /**
     * Log extra data fields
     *
     * @since   1.0.0
     * @updated 1.6.3 Added $object parameter
     *
     * @param array     $fields     Log extra fields.
     * @param int       $log_id     The log ID.
     * @param string    $type       The log type. See gamipress_get_log_types().
     * @param stdClass  $object     The log object.
     *
     * @return array
     */
    $fields = apply_filters( 'gamipress_log_extra_data_fields', $fields, $object_id, $type, $object );

    if( ! empty( $fields ) ) {

        // Create a new box to render the form
        $cmb2 = new CMB2( array(
            'id'      => 'log_extra_data_ui_box',
            'classes' => 'gamipress-form gamipress-box-form',
            'hookup'  => false,
            'show_on' => array(
                'key'   => 'gamipress_logs',
                'value' => $object_id
            ),
            'fields' => $fields
        ) );

        $cmb2->object_id( $object_id );

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
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    ct_setup_table( 'gamipress_logs' );

    $ct_object = ct_get_object( absint( $_REQUEST['object_id'] ) );

    gamipress_log_extra_data_ui_html( $ct_object, $ct_object->log_id, sanitize_text_field( $_REQUEST['type'] ) );
    die;
}
add_action( 'wp_ajax_gamipress_get_log_extra_data_ui', 'gamipress_get_log_extra_data_ui_ajax_handler' );

/**
 * Helper function to recursive search an array key
 *
 * @since 1.0.0
 *
 * @param $needle_key
 * @param $array
 *
 * @return bool|mixed
 */
function gamipress_array_search_key( $needle_key, $array ) {

    foreach( $array as $key => $value ) {

        if( $key == $needle_key )
            return $value;

        if( is_array( $value ) ) {
            if( ( $result = gamipress_array_search_key( $needle_key, $value ) ) !== false )
                return $result;
        }
    }

    return false;

}