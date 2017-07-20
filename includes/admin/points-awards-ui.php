<?php
/**
 * Points Awards UI
 *
 * @package     GamiPress\Admin\Points_Awards_UI
 * @since       1.0.0
 */

/**
 * Add Points Awards metabox to the Points type post editor
 *
 * @since  1.0.0
 * @return void
 */
function gamipress_add_points_awards_ui_meta_box() {
    add_meta_box( 'gamipress_points_awards_ui', __( 'Automatic Points Awards', 'gamipress' ), 'gamipress_points_awards_ui_meta_box', 'points-type', 'advanced', 'default' );
}
add_action( 'add_meta_boxes', 'gamipress_add_points_awards_ui_meta_box' );

/**
 * Renders the HTML for meta box, refreshes whenever a new point award is added
 *
 * @since  1.0.0
 * @param  object $post The current post object
 * @return void
 */
function gamipress_points_awards_ui_meta_box( $post  = null) {

    // Grab our points type's points award
    $assigned_points_awards = gamipress_get_points_type_points_awards( $post->ID );

    if( ! $assigned_points_awards ) {
        $assigned_points_awards = array();
    }

    // Loop through each points award and set the sort order
    foreach ( $assigned_points_awards as $assigned_points_award ) {
        $assigned_points_award->order = get_points_award_menu_order( $assigned_points_award->ID );
    }

    // Sort the points awards by their order
    uasort( $assigned_points_awards, 'gamipress_compare_points_award_order' );

    echo '<p>' .__( 'Define the automatic ways an user could retrieve an amount of  this points type. Use the "Label" field to optionally customize the titles of each one.', 'gamipress' ). '</p>';

    // Concatenate our points awards output
    echo '<ul id="points_awards_list">';
    foreach ( $assigned_points_awards as $points_award ) {
        gamipress_points_awards_ui_html( $points_award->ID, $post->ID );
    }
    echo '</ul>';

    // Render our buttons
    echo '<input style="margin-right: 1em" class="button" type="button" onclick="gamipress_add_new_points_award(' . $post->ID . ');" value="' . apply_filters( 'gamipress_points_awards_ui_add_new', __( 'Add New Points Award', 'gamipress' ) ) . '">';
    echo '<input class="button-primary" type="button" onclick="gamipress_update_points_awards();" value="' . apply_filters( 'gamipress_points_awards_ui_save_all', __( 'Save All Points Awards', 'gamipress' ) ) . '">';
    echo '<img class="save-points-awards-spinner" src="' . admin_url( '/images/wpspin_light.gif' ) . '" style="margin-left: 10px; display: none;" />';

}

/**
 * Helper function for generating the HTML output for configuring a given points award
 *
 * @since  1.0.0
 * @param  integer $points_award_id The given points award's ID
 * @param  integer $post_id The given points award's parent $post ID
 * @return string           The concatenated HTML input for the points award
 */
function gamipress_points_awards_ui_html( $points_award_id = 0, $post_id = 0 ) {

    // Grab our points award's requirements and measurement
    $requirements      = gamipress_get_points_award_requirements( $points_award_id );
    $count             = ! empty( $requirements['count'] ) ? $requirements['count'] : 1;
    $points            = ! empty( $requirements['points'] ) ? $requirements['points'] : 1;
    $limit             = ! empty( $requirements['limit'] ) ? $requirements['limit'] : 1;
    $limit_type        = ! empty( $requirements['limit_type'] ) ? $requirements['limit_type'] : 'unlimited';
    $points_singular_name = get_post_meta( $post_id, '_gamipress_singular_name', true );
    $points_type       = sanitize_title( strtolower( $points_singular_name ) );

    if( ! $points_singular_name ) {
        $points_singular_name = __( 'point(s)', 'gamipress' );
    } else {
        $points_singular_name = strtolower( $points_singular_name . '(s)' );
    }
    ?>

    <li class="points-award-row points-award-<?php echo $points_award_id; ?>" data-points-award-id="<?php echo $points_award_id; ?>">
        <div class="points-award-handle"></div>
        <a class="delete-points-award" href="javascript: gamipress_delete_points_award( <?php echo $points_award_id; ?> );"><?php _e( 'Delete', 'gamipress' ); ?></a>
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
        <input type="hidden" name="order" value="<?php echo get_points_award_menu_order( $points_award_id ); ?>" />

        <label for="select-trigger-type-<?php echo $points_award_id; ?>"><?php _e( 'When', 'gamipress' ); ?>:</label>

        <?php do_action( 'gamipress_points_awards_ui_html_after_require_text', $points_award_id, $post_id ); ?>

        <select id="select-trigger-type-<?php echo $points_award_id; ?>" class="select-trigger-type" data-points-award-id="<?php echo $points_award_id; ?>">
            <?php
            $activity_triggers = gamipress_get_activity_triggers();

            // Grouped activity triggers
            foreach ( $activity_triggers as $group => $group_triggers ) : ?>
                <optgroup label="<?php echo esc_attr( $group ); ?>">
                    <?php foreach( $group_triggers as $trigger => $label ) : ?>
                            <option value="<?php echo esc_attr( $trigger ); ?>" <?php selected( $requirements['trigger_type'], $trigger, true ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>

        <?php do_action( 'gamipress_points_awards_ui_html_after_trigger_type', $points_award_id, $post_id ); ?>

        <select class="select-achievement-type select-achievement-type-<?php echo $points_award_id; ?>">
            <option value=""><?php _e( 'Choose an achievement type', 'gamipress'); ?></option>
            <?php
            foreach ( gamipress_get_achievement_types() as $slug => $data ) {
                if ( 'points-award' === $slug || 'step' === $slug ) {
                    continue;
                }
                echo '<option value="' . $slug . '" ' . selected( $requirements['achievement_type'], $slug, false ) . '>' . $data['plural_name'] . '</option>';
            }
            ?>
        </select>

        <?php do_action( 'gamipress_points_awards_ui_html_after_achievement_type', $points_award_id, $post_id ); ?>

        <select class="select-achievement-post select-achievement-post-<?php echo $points_award_id; ?>">
            <option value=""><?php _e( 'Choose an achievement', 'gamipress'); ?></option>
        </select>

        <select class="select-post select-post-<?php echo $points_award_id; ?>">
            <?php if( ! empty( $requirements['achievement_post'] ) ) : ?>
                <option value="<?php esc_attr_e( $requirements['achievement_post'] ); ?>" selected="selected"><?php echo get_post_field( 'post_title', $requirements['achievement_post'] ); ?></option>
            <?php endif; ?>
        </select>

        <?php do_action( 'gamipress_points_awards_ui_html_after_achievement_post', $points_award_id, $post_id ); ?>

        <input class="required-count" type="text" size="3" maxlength="3" value="<?php echo $count; ?>" placeholder="1">
        <span class="required-count-text"><?php _e( 'time(s)', 'gamipress' ); ?></span>

        <?php do_action( 'gamipress_points_awards_ui_html_after_count', $points_award_id, $post_id ); ?>

        <span class="limit-text"><?php _e( 'limited to', 'gamipress' ); ?></span>
        <input class="limit" type="text" size="3" maxlength="3" value="<?php echo $limit; ?>" placeholder="1">
        <select class="limit-type">
            <option value="unlimited" <?php selected( $limit_type, 'unlimited' ); ?>><?php _e( 'Unlimited', 'gamipress' ); ?></option>
            <option value="daily" <?php selected( $limit_type, 'daily' ); ?>><?php _e( 'Per Day', 'gamipress' ); ?></option>
            <option value="weekly" <?php selected( $limit_type, 'weekly' ); ?>><?php _e( 'Per Week', 'gamipress' ); ?></option>
            <option value="monthly" <?php selected( $limit_type, 'monthly' ); ?>><?php _e( 'Per Month', 'gamipress' ); ?></option>
            <option value="yearly" <?php selected( $limit_type, 'yearly' ); ?>><?php _e( 'Per Year', 'gamipress' ); ?></option>
        </select>

        <?php do_action( 'gamipress_points_awards_ui_html_after_limit', $points_award_id, $post_id ); ?>

        <div class="points-award-points">
            <label for="points-award-<?php echo $points_award_id; ?>-points"><?php _e( 'Earn', 'gamipress' ); ?>:</label> <input type="text" name="points-award-points" id="points-award-<?php echo $points_award_id; ?>-points" class="points" value="<?php echo $points; ?>" />
            <?php echo $points_singular_name; ?>
            <input type="hidden" name="points_type" value="<?php echo $points_type; ?>">
        </div>

        <?php do_action( 'gamipress_points_awards_ui_html_after_points', $points_award_id, $post_id ); ?>

        <div class="points-award-title">
            <label for="points-award-<?php echo $points_award_id; ?>-title"><?php _e( 'Label', 'gamipress' ); ?>:</label>
            <input type="text" name="points-award-title" id="points-award-<?php echo $points_award_id; ?>-title" class="title" value="<?php echo get_the_title( $points_award_id ); ?>" />
        </div>

        <span class="spinner spinner-points-award-<?php echo $points_award_id;?>"></span>
    </li>
    <?php
}

/**
 * Get all the requirements of a given points award
 *
 * @since  1.0.0
 * @param  integer $points_award_id The given points award's post ID
 * @return array|bool       An array of all the points award requirements if it has any, false if not
 */
function gamipress_get_points_award_requirements( $points_award_id = 0 ) {

    // Setup our default requirements array, assume we require nothing
    $requirements = array(
        'count'            => absint( get_post_meta( $points_award_id, '_gamipress_count', true ) ),
        'points'           => absint( get_post_meta( $points_award_id, '_gamipress_points', true ) ),
        'points_type'      => absint( get_post_meta( $points_award_id, '_gamipress_points_type', true ) ),
        'limit'            => absint( get_post_meta( $points_award_id, '_gamipress_limit', true ) ),
        'limit_type'       => get_post_meta( $points_award_id, '_gamipress_limit_type', true ),
        'trigger_type'     => get_post_meta( $points_award_id, '_gamipress_trigger_type', true ),
        'achievement_type' => get_post_meta( $points_award_id, '_gamipress_achievement_type', true ),
        'achievement_post' => ''
    );

    // If the points award requires a specific achievement
    if ( ! empty( $requirements['achievement_type'] ) ) {
        $connected_activities = @get_posts( array(
            'post_type'        => $requirements['achievement_type'],
            'posts_per_page'   => 1,
            'suppress_filters' => false,
            'connected_type'   => $requirements['achievement_type'] . '-to-points-award',
            'connected_to'     => $points_award_id
        ));

        if ( ! empty( $connected_activities ) ) {
            $requirements['achievement_post'] = $connected_activities[0]->ID;
        }
    } elseif ( in_array( $requirements['trigger_type'], array_keys( gamipress_get_specific_activity_triggers() ) ) ) {
        $achievement_post = absint( get_post_meta( $points_award_id, '_gamipress_achievement_post', true ) );

        if ( 0 < $achievement_post ) {
            $requirements[ 'achievement_post' ] = $achievement_post;
        }
    }

    // Available filter for overriding elsewhere
    return apply_filters( 'gamipress_get_points_award_requirements', $requirements, $points_award_id );
}

/**
 * AJAX Handler for adding a new points award
 *
 * @since 1.0.0
 * @return void
 */
function gamipress_add_points_award_ajax_handler() {

    // Create a new Points Award post and grab it's ID
    $points_award_id = wp_insert_post( array(
        'post_type'   => 'points-award',
        'post_status' => 'publish'
    ) );

    // Output the edit points award html to insert into the Points Awards metabox
    gamipress_points_awards_ui_html( $points_award_id, $_POST['post_id'] );

    // Grab the post object for our Badge
    $post = get_post( $_POST['post_id'] );

    // Create the P2P connection from the points award to the badge
    $p2p_id = p2p_create_connection(
        'points-award-to-' . $post->post_type,
        array(
            'from' => $points_award_id,
            'to'   => $_POST['post_id'],
            'meta' => array(
                'date' => current_time( 'mysql' )
            )
        )
    );

    // Add relevant meta to our P2P connection
    p2p_add_meta( $p2p_id, 'order', '0' );

    // Die here, because it's AJAX
    die;
}
add_action( 'wp_ajax_add_points_award', 'gamipress_add_points_award_ajax_handler' );

/**
 * AJAX Handler for deleting a points award
 *
 * @since 1.0.0
 * @return void
 */
function gamipress_delete_points_award_ajax_handler() {
    wp_delete_post( $_POST['points_award_id'] );
    die;
}
add_action( 'wp_ajax_delete_points_award', 'gamipress_delete_points_award_ajax_handler' );

/**
 * AJAX Handler for saving all points awards
 *
 * @since 1.0.0
 * @return void
 */
function gamipress_update_points_awards_ajax_handler() {

    // Only continue if we have any points awards
    if ( isset( $_POST['points_awards'] ) ) {

        // Grab our $wpdb global
        global $wpdb;

        // Setup an array for storing all our points award titles
        // This lets us dynamically update the Label field when points awards are saved
        $new_titles = array();

        // Loop through each of the created points awards
        foreach ( $_POST['points_awards'] as $key => $points_award ) {

            // Grab all of the relevant values of that points award
            $points_award_id   = $points_award['points_award_id'];
            $required_count   = ( ! empty( $points_award['required_count'] ) ) ? $points_award['required_count'] : 1;
            $points           = ( ! empty( $points_award['points'] ) ) ? $points_award['points'] : 1;
            $points_type      = ( ! empty( $points_award['points_type'] ) ) ? $points_award['points_type'] : '';
            $limit            = ( ! empty( $points_award['limit'] ) ) ? $points_award['limit'] : 1;
            $limit_type       = ( ! empty( $points_award['limit_type'] ) ) ? $points_award['limit_type'] : 'unlimited';
            $trigger_type     = $points_award['trigger_type'];
            $achievement_type = $points_award['achievement_type'];

            // Clear all relation data
            $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->p2p WHERE p2p_to=%d", $points_award_id ) );
            delete_post_meta( $points_award_id, '_gamipress_achievement_post' );

            // Flip between our requirement types and make an appropriate connection
            switch ( $trigger_type ) {

                // Connect the points award to ANY of the given achievement type
                case 'any-achievement' :
                    $title = sprintf( __( 'any %s', 'gamipress' ), $achievement_type );
                    break;
                case 'all-achievements' :
                    $title = sprintf( __( 'all %s', 'gamipress' ), $achievement_type );
                    break;
                case 'specific-achievement' :
                    p2p_create_connection(
                        $points_award['achievement_type'] . '-to-points-award',
                        array(
                            'from' => absint( $points_award['achievement_post'] ),
                            'to'   => $points_award_id,
                            'meta' => array(
                                'date' => current_time('mysql')
                            )
                        )
                    );
                    $title = '"' . get_the_title( $points_award['achievement_post'] ) . '"';
                    break;
                default :
                    $title = gamipress_get_activity_trigger_label( $trigger_type );
                    break;

            }

            // Specific activity trigger type
            if( in_array( $trigger_type, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {
                $achievement_post_id = absint( $points_award['achievement_post'] );

                // Update achievement post to check it on rules engine
                update_post_meta( $points_award_id, '_gamipress_achievement_post', $achievement_post_id );

                if( $achievement_post_id ) {
                    // Filtered title
                    $title = sprintf( gamipress_get_specific_activity_trigger_label( $trigger_type ),  get_the_title( $achievement_post_id ) );
                }
            }

            // Update the points award order
            p2p_update_meta( gamipress_get_p2p_id_from_child_id( $points_award_id ), 'order', $key );

            // Update our relevant meta
            update_post_meta( $points_award_id, '_gamipress_count', $required_count );
            update_post_meta( $points_award_id, '_gamipress_points', $points );
            update_post_meta( $points_award_id, '_gamipress_points_type', $points_type );
            update_post_meta( $points_award_id, '_gamipress_limit', $limit );
            update_post_meta( $points_award_id, '_gamipress_limit_type', $limit_type );
            update_post_meta( $points_award_id, '_gamipress_trigger_type', $trigger_type );
            update_post_meta( $points_award_id, '_gamipress_achievement_type', $achievement_type );

            // Available hook for custom Activity Triggers
            $custom_title = sprintf( __( '%1$s %2$s.', 'gamipress' ), $title, sprintf( _n( '%d time', '%d times', $required_count ), $required_count ) );
            $custom_title = apply_filters( 'gamipress_save_points_award', $custom_title, $points_award_id, $points_award );

            // Update our original post with the new title
            $post_title = ! empty( $points_award['title'] ) ? $points_award['title'] : $custom_title;
            wp_update_post( array( 'ID' => $points_award_id, 'post_title' => $post_title ) );

            // Add the title to our AJAX return
            $new_titles[$points_award_id] = stripslashes( $post_title );

        }

        // Send back all our points award titles
        echo json_encode($new_titles);

    }

    // Cave Johnson. We're done here.
    die;

}
add_action( 'wp_ajax_update_points_awards', 'gamipress_update_points_awards_ajax_handler' );

/**
 * Get the sort order for a given points award
 *
 * @since  1.0.0
 * @param  integer $points_award_id The given points award's post ID
 * @return integer          The points award's sort order
 */
function get_points_award_menu_order( $points_award_id = 0 ) {
    global $wpdb;
    $p2p_id = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_id FROM $wpdb->p2p WHERE p2p_from = %d", $points_award_id ) );
    $menu_order = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->p2pmeta WHERE p2p_id=%d AND meta_key='order'", $p2p_id ) );
    if ( ! $menu_order || $menu_order == 'NaN' ) $menu_order = '0';
    return $menu_order;
}

/**
 * Helper function for comparing our points award sort order (used in uasort() in gamipress_create_points_awards_meta_box())
 *
 * @since  1.0.0
 * @param  integer $points_award1 The order number of our given points award
 * @param  integer $points_award2 The order number of the points award we're comparing against
 * @return integer        0 if the order matches, -1 if it's lower, 1 if it's higher
 */
function gamipress_compare_points_award_order( $points_award1 = 0, $points_award2 = 0 ) {
    if ( $points_award1->order == $points_award2->order ) return 0;
    return ( $points_award1->order < $points_award2->order ) ? -1 : 1;
}
