<?php
/**
 * Requirements UI
 *
 * @package     GamiPress\Admin\Requirements_UI
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add Points Awards and Steps meta boxes
 *
 * @since  1.0.5
 *
 * @return void
 */
function gamipress_add_requirements_ui_meta_box() {

    // Points Awards
    add_meta_box( 'gamipress-requirements-ui-points-award', __( 'Automatic Points Awards', 'gamipress' ), 'gamipress_requirements_ui_meta_box', 'points-type', 'advanced', 'default', array( 'requirement_type' => 'points-award' ) );

    // Points Deducts
    add_meta_box( 'gamipress-requirements-ui-points-deduct', __( 'Automatic Points Deducts', 'gamipress' ), 'gamipress_requirements_ui_meta_box', 'points-type', 'advanced', 'default', array( 'requirement_type' => 'points-deduct' ) );

    // Steps
    foreach ( gamipress_get_achievement_types_slugs() as $achievement_type ) {
        add_meta_box( 'gamipress-requirements-ui', __( 'Required Steps', 'gamipress' ), 'gamipress_requirements_ui_meta_box', $achievement_type, 'advanced', 'default', array( 'requirement_type' => 'step' ) );
    }

    // Rank Requirements
    foreach ( gamipress_get_rank_types_slugs() as $rank_type ) {
        add_meta_box( 'gamipress-requirements-ui', __( 'Rank Requirements', 'gamipress' ), 'gamipress_requirements_ui_meta_box', $rank_type, 'advanced', 'default', array( 'requirement_type' => 'rank-requirement' ) );
    }

}
add_action( 'add_meta_boxes', 'gamipress_add_requirements_ui_meta_box' );

/**
 * Renders the HTML for meta box, refreshes whenever a new point award is added
 *
 * @since 1.0.5
 * @updated 1.3.7 Added $metabox
 *
 * @param WP_Post $post     The current post object.
 * @param array   $metabox  With metabox id, title, callback, and args elements.
 *
 * @return void
 */
function gamipress_requirements_ui_meta_box( $post = null, $metabox ) {

    // Define the requirement type to use
    $requirement_type = gamipress_requirements_ui_get_requirement_type( $post, $metabox );

    // If is lowest priority rank then show a notice and prevent to show requirements UI
    if( $requirement_type === 'rank-requirement' ) {

        if( ( $post->post_status === 'auto-draft' && gamipress_get_rank_priority( $post->ID ) === 1 )
            || ( $post->post_status !== 'auto-draft' && gamipress_is_lowest_priority_rank( $post->ID ) ) ) {

            echo '<p>' .
                __( 'The rank with the lowest priority is set as default rank for all users so this rank should be created without requirements.', 'gamipress' )
                . '<br>'
                . __( 'You will be able to set requirements on next ranks. After save this rank, if it is not configured as the lowest priority you will be able to edit the requirements again.', 'gamipress' )
                . '</p>';
            return;

        }
    }

    // Setup the requirement object based on the requirement type
    $requirement_types = gamipress_get_requirement_types();
    $requirement_type_object = $requirement_types[$requirement_type];

    // Get assigned requirements
    $assigned_requirements = gamipress_get_assigned_requirements( $post->ID, $requirement_type, 'any' );

    if( ! $assigned_requirements ) {
        $assigned_requirements = array();
    }

    // Sort the requirements by their order
    uasort( $assigned_requirements, 'gamipress_compare_requirements_order' );

    switch( $requirement_type ) {
        case 'points-award':
            echo '<p>' .
                __( 'Define the automatic ways an user could retrieve an amount of this points type. Use the "Label" field to optionally customize the titles of each one.', 'gamipress' )
            . '</p>';
            break;
        case 'points-deduct':
            echo '<p>' .
                __( 'Define the automatic ways an user could lose an amount of this points type. Use the "Label" field to optionally customize the titles of each one.', 'gamipress' )
                . '</p>';
            break;
        case 'step':
            echo '<p>' .
                __( 'Define the required "steps" for this achievement to be considered complete. Use the "Label" field to optionally customize the titles of each step.', 'gamipress' )
            . '</p>';
            break;
        case 'rank-requirement':
            echo '<p>' .
                __( 'Define the required requirements for this rank to be considered that user is ranked on it. Use the "Label" field to optionally customize the titles of each requirement.', 'gamipress' )
                . '<br>'
                . __( '<strong>Important!</strong> User will not earn this requirements until be ranked on the previous rank.', 'gamipress' )
            . '</p>';
            break;
    }

    // Sequential input
    if( in_array( $requirement_type, array( 'step', 'rank-requirement' ) ) ) : ?>

        <label for="_gamipress_sequential">
            <strong><?php echo ( $requirement_type === 'step'
                ? __( 'Sequential Steps', 'gamipress' )
                : __( 'Sequential Requirements', 'gamipress' )
            ); ?></strong>
        </label>

        <div class="gamipress-switch gamipress-switch-small gamipress-requirements-sequential">
            <input type="checkbox" id="_gamipress_sequential" name="_gamipress_sequential" <?php checked( true, (bool) gamipress_get_post_meta( $post->ID, '_gamipress_sequential' ) ); ?> value="on">
            <label for="_gamipress_sequential"><?php echo ( $requirement_type === 'step'
                    ? __( 'Check this option to force users to complete steps in order.', 'gamipress' )
                    : __( 'Check this option to force users to complete requirements in order.', 'gamipress' )
                ); ?></label>
        </div>

    <?php endif;

    do_action( 'gamipress_before_requirements_list', $requirement_type, $requirement_type_object, $assigned_requirements );

    // Concatenate our requirements output
    echo '<ul id="requirements-list" class="requirements-list">';
    foreach ( $assigned_requirements as $requirement ) {
        gamipress_requirement_ui_html( $requirement->ID, $post->ID );
    }
    echo '</ul>';

    // Render our buttons ?>
    <input style="margin-right: 1em" class="button" type="button" onclick="gamipress_add_requirement( this, <?php echo $post->ID; ?>, '<?php echo $requirement_type; ?>');" value="<?php printf( __( 'Add New %s', 'gamipress' ), $requirement_type_object['singular_name'] ); ?>">
    <input class="button-primary" type="button" onclick="gamipress_update_requirements( this );" value="<?php printf( __( 'Save All %s', 'gamipress' ), $requirement_type_object['plural_name'] ); ?>">
    <span class="spinner requirements-spinner"></span>
    <?php
}

/**
 * Return post assigned requirements
 *
 * @since   1.0.0
 * @updated 1.5.1 Added $post_status parameter
 *
 * @param int       $post_id
 * @param string    $requirement_type
 * @param string    $post_status
 *
 * @return array|bool
 */
function gamipress_get_assigned_requirements( $post_id = null, $requirement_type, $post_status = 'publish' ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        return gamipress_get_assigned_requirements_old( $post_id, $requirement_type, $post_status );
    }

    global $post;

    if( $post_id === null ) {
        $post_id = $post->ID;
    }

    // Grab post's requirements
    return get_posts( array(
        'post_type'         => $requirement_type,
        'post_parent'       => $post_id,
        'post_status'       => $post_status,
        'orderby'			=> 'menu_order',
        'order'				=> 'ASC',
        'posts_per_page'    => -1,
        'suppress_filters'  => false,
    ) );

}

/**
 * Helper function for generating the HTML output for configuring a given requirement
 *
 * @since  1.0.5
 *
 * @param  integer $requirement_id The given requirement's ID
 * @param  integer $post_id The given requirement's parent $post ID
 *
 * @return string           The concatenated HTML input for the requirement
 */
function gamipress_requirement_ui_html( $requirement_id = 0, $post_id = 0 ) {

    // Grab our requirement's requirements and measurement
    $requirements      = gamipress_get_requirement_object( $requirement_id );
    $requirement_type  = gamipress_get_post_type( $requirement_id );
    $status            = gamipress_get_post_status( $requirement_id );
    $count             = ! empty( $requirements['count'] ) ? $requirements['count'] : 1;
    $limit             = ! empty( $requirements['limit'] ) ? $requirements['limit'] : 1;
    $limit_type        = ! empty( $requirements['limit_type'] ) ? $requirements['limit_type'] : 'unlimited';

    // Setup the requirement object based on the requirement type
    $requirement_types = gamipress_get_requirement_types();
    $requirement_type_object = $requirement_types[$requirement_type];
    ?>

    <li class="requirement-row requirement-<?php echo $requirement_id; ?> <?php echo ( $status === 'publish' ? 'requirement-published' : '' ); ?>" data-requirement-id="<?php echo $requirement_id; ?>">

        <input type="hidden" name="requirement_id" value="<?php echo $requirement_id; ?>" />
        <input type="hidden" name="requirement_type" value="<?php echo $requirement_type; ?>" />
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
        <input type="hidden" name="order" value="<?php echo absint( gamipress_get_requirement_menu_order( $requirement_id ) ); ?>" />

        <div class="requirement-header">

            <div class="requirement-header-title">
                <strong><?php echo get_the_title( $requirement_id ); ?></strong>
            </div>

            <div class="requirement-actions">

                <?php $change_status_title = ( $status === 'publish' ? __( 'Disable %s', 'gamipress' ) : __( 'Enable %s', 'gamipress' ) ) ?>

                <div class="requirement-action requirement-action-change-status"
                     title="<?php echo sprintf( $change_status_title, $requirement_type_object['singular_name'] ); ?>"
                     data-enabled-title="<?php echo sprintf( __( 'Disable %s', 'gamipress' ), $requirement_type_object['singular_name'] ); ?>"
                     data-disabled-title="<?php echo sprintf( __( 'Enable %s', 'gamipress' ), $requirement_type_object['singular_name'] ); ?>"
                >
                    <div class="gamipress-switch gamipress-switch-small">
                        <input type="checkbox" id="requirement-action-change-status-input-<?php echo $requirement_id; ?>" <?php checked( 'publish', $status ) ?> />
                        <label for="requirement-action-change-status-input-<?php echo $requirement_id; ?>"></label>
                    </div>
                </div>

                <?php

                // Setup the default requirement actions
                $requirement_actions = array(
                    'duplicate' => array(
                        'label' => __( 'Duplicate', 'gamipress' ),
                        'icon' => 'dashicons-admin-page',
                    ),
                    'delete' => array(
                        'label' => __( 'Delete', 'gamipress' ),
                        'icon' => 'dashicons-trash',
                    )
                );

                /**
                 * Available filter to add custom requirement actions
                 *
                 * @since 1.4.6
                 *
                 * @param array $requirement_actions
                 */
                $requirement_actions = apply_filters( 'gamipress_requirement_ui_requirement_actions', $requirement_actions );

                foreach( $requirement_actions as $requirement_action => $requirement_action_args ) : ?>

                    <div class="requirement-action requirement-action-<?php echo $requirement_action; ?>" title="<?php echo $requirement_action_args['label']; ?>">
                        <span class="dashicons <?php echo $requirement_action_args['icon']; ?>"></span>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>

        <label for="select-trigger-type-<?php echo $requirement_id; ?>"><?php _e( 'When', 'gamipress' ); ?>:</label>

        <?php
        /**
         * Available action to add custom HTML after requirement text
         *
         * @since 1.0.0
         *
         * @param integer $requirement_id
         * @param integer $post_id
         */
        do_action( 'gamipress_requirement_ui_html_after_require_text', $requirement_id, $post_id ); ?>

        <select id="select-trigger-type-<?php echo $requirement_id; ?>" class="select-trigger-type" data-requirement-id="<?php echo $requirement_id; ?>">
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

        <?php
        /**
         * Available action to add custom HTML after trigger type
         *
         * @since 1.0.0
         *
         * @param integer $requirement_id
         * @param integer $post_id
         */
        do_action( 'gamipress_requirement_ui_html_after_trigger_type', $requirement_id, $post_id ); ?>

        <input type="number" name="requirement-points-required" id="requirement-<?php echo $requirement_id; ?>-points-required" class="points-required" value="<?php echo ( $requirements['points_required'] === 0 ? 1 : $requirements['points_required'] ); ?>" />

        <?php do_action( 'gamipress_requirement_ui_html_after_points_required', $requirement_id, $post_id ); ?>

        <select class="select-points-type-required select-points-type-required-<?php echo $requirement_id; ?>">
            <option value=""><?php _e( 'Default points', 'gamipress'); ?></option>
            <?php foreach ( gamipress_get_points_types() as $slug => $data ) :

                echo '<option value="' . $slug . '" ' . selected( $requirements['points_type_required'], $slug, false ) . '>' . $data['plural_name'] . '</option>';

            endforeach; ?>
        </select>

        <?php
        /**
         * Available action to add custom HTML after points type required
         *
         * @since 1.0.0
         *
         * @param integer $requirement_id
         * @param integer $post_id
         */
        do_action( 'gamipress_requirement_ui_html_after_points_type_required', $requirement_id, $post_id ); ?>

        <select class="select-rank-type-required select-rank-type-required-<?php echo $requirement_id; ?>">
            <?php foreach ( gamipress_get_rank_types() as $slug => $data ) :

                echo '<option value="' . $slug . '" ' . selected( $requirements['rank_type_required'], $slug, false ) . '>' . $data['singular_name'] . '</option>';

            endforeach; ?>
        </select>

        <?php
        /**
         * Available action to add custom HTML after rank type required
         *
         * @since 1.2.0
         *
         * @param integer $requirement_id
         * @param integer $post_id
         */
        do_action( 'gamipress_requirement_ui_html_after_rank_type_required', $requirement_id, $post_id ); ?>

        <select class="select-rank-required select-rank-required-<?php echo $requirement_id; ?>">
            <option value=""><?php _e( 'Choose a rank', 'gamipress'); ?></option>
        </select>

        <?php
        /**
         * Available action to add custom HTML after rank required
         *
         * @since 1.2.0
         *
         * @param integer $requirement_id
         * @param integer $post_id
         */
        do_action( 'gamipress_requirement_ui_html_after_rank_required', $requirement_id, $post_id ); ?>

        <select class="select-achievement-type select-achievement-type-<?php echo $requirement_id; ?>">
            <option value=""><?php _e( 'Choose an achievement type', 'gamipress'); ?></option>
            <?php foreach ( gamipress_get_achievement_types() as $slug => $data ) :

                echo '<option value="' . $slug . '" ' . selected( $requirements['achievement_type'], $slug, false ) . '>' . $data['plural_name'] . '</option>';

            endforeach; ?>
        </select>

        <?php
        /**
         * Available action to add custom HTML after achievement type
         *
         * @since 1.0.0
         *
         * @param integer $requirement_id
         * @param integer $post_id
         */
        do_action( 'gamipress_requirement_ui_html_after_achievement_type', $requirement_id, $post_id ); ?>

        <select class="select-achievement-post select-achievement-post-<?php echo $requirement_id; ?>">
            <option value=""><?php _e( 'Choose an achievement', 'gamipress'); ?></option>
        </select>

        <?php // For multisite installs, we need to store site ID of attached post ?>
        <input type="hidden" class="select-post-site-id" value="<?php echo $requirements['achievement_post_site_id']; ?>" />

        <select class="select-post select-post-<?php echo $requirement_id; ?>">
            <?php if( ! empty( $requirements['achievement_post'] ) ) :
                $achievement_post_title = gamipress_get_specific_activity_trigger_post_title( $requirements['achievement_post'], $requirements['trigger_type'], $requirements['achievement_post_site_id'] ); ?>
                <option value="<?php esc_attr_e( $requirements['achievement_post'] ); ?>" selected="selected"><?php echo $achievement_post_title; ?> (#<?php echo $requirements['achievement_post']; ?>)</option>
            <?php endif; ?>
        </select>

        <?php
        /**
         * Available action to add custom HTML after achievement post
         *
         * @since 1.0.0
         *
         * @param integer $requirement_id
         * @param integer $post_id
         */
        do_action( 'gamipress_requirement_ui_html_after_achievement_post', $requirement_id, $post_id ); ?>

        <input class="count" type="number" min="1" value="<?php echo $count; ?>" placeholder="1">
        <span class="count-text"><?php _e( 'time(s)', 'gamipress' ); ?></span>

        <?php do_action( 'gamipress_requirement_ui_html_after_count', $requirement_id, $post_id ); ?>

        <span class="limit-text"><?php _e( 'limited to', 'gamipress' ); ?></span>
        <input class="limit" type="number" min="1" value="<?php echo $limit; ?>" placeholder="1">
        <select class="limit-type">
            <option value="unlimited" <?php selected( $limit_type, 'unlimited' ); ?>><?php _e( 'Unlimited', 'gamipress' ); ?></option>
            <option value="daily" <?php selected( $limit_type, 'daily' ); ?>><?php _e( 'Per Day', 'gamipress' ); ?></option>
            <option value="weekly" <?php selected( $limit_type, 'weekly' ); ?>><?php _e( 'Per Week', 'gamipress' ); ?></option>
            <option value="monthly" <?php selected( $limit_type, 'monthly' ); ?>><?php _e( 'Per Month', 'gamipress' ); ?></option>
            <option value="yearly" <?php selected( $limit_type, 'yearly' ); ?>><?php _e( 'Per Year', 'gamipress' ); ?></option>
        </select>

        <?php
        /**
         * Available action to add custom HTML after requirement limit
         *
         * @since 1.0.0
         *
         * @param integer $requirement_id
         * @param integer $post_id
         */
        do_action( 'gamipress_requirement_ui_html_after_limit', $requirement_id, $post_id ); ?>

        <?php if( in_array( $requirement_type, array( 'points-award', 'points-deduct' ) ) ) :
            $points                 = ! empty( $requirements['points'] ) ? $requirements['points'] : 1;
            $points_singular_name   = get_post_field( 'post_title', $post_id );
            $post_name              = get_post_field( 'post_name', $post_id );
            $points_type            = $requirements['points_type'];
            $maximum_earnings       = absint( $requirements['maximum_earnings'] );

            // Ensure points type is properly set, if not force update it
            if( $points_type !== $post_name ) {
                update_post_meta( $requirement_id, '_gamipress_points_type', $post_name );
                $points_type = $post_name;
            }

            if( ! $points_singular_name ) {
                $points_singular_name = __( 'point(s)', 'gamipress' );
            } else {
                $points_singular_name = strtolower( $points_singular_name . '(s)' );
            }
            ?>
            <div class="requirement-awards">
                <label for="requirement-<?php echo $requirement_id; ?>-points"><?php echo ( $requirement_type === 'points-award' ? __( 'Earn', 'gamipress' ) : __( 'Deduct', 'gamipress' ) ); ?>:</label>
                <input type="number" name="requirement-points" id="requirement-<?php echo $requirement_id; ?>-points" class="points" value="<?php echo $points; ?>" />
                <span class="points-text"><?php echo $points_singular_name; ?></span>
                <input type="hidden" name="points_type" value="<?php echo $points_type; ?>">


                <span class="maximum-earnings-text"><?php echo ( $requirement_type === 'points-award' ? __( 'with a maximum number of times to earn it of', 'gamipress' ) : __( 'with a maximum number of times to deduct it of', 'gamipress' ) ); ?></span>
                <input type="number" min="0" name="requirement-maximum-earnings" id="requirement-<?php echo $requirement_id; ?>-maximum-earnings" class="maximum-earnings" value="<?php echo $maximum_earnings; ?>" />
                <span class="maximum-earnings-notice"><?php _e( '(0 for no maximum)', 'gamipress' ); ?></span>
            </div>

            <?php
            /**
             * Available action to add custom HTML after requirement points ( just available for points awards and deducts )
             *
             * @since 1.0.0
             *
             * @param integer $requirement_id
             * @param integer $post_id
             */
            do_action( 'gamipress_requirement_ui_html_after_points', $requirement_id, $post_id ); ?>
        <?php endif; ?>

        <div class="requirement-title">
            <label for="requirement-<?php echo $requirement_id; ?>-title"><?php _e( 'Label', 'gamipress' ); ?>:</label>
            <input type="text" name="requirement-title" id="requirement-<?php echo $requirement_id; ?>-title" class="title" value="<?php echo get_the_title( $requirement_id ); ?>" />
        </div>

        <?php
        /**
         * Available action to add custom HTML after requirement title
         *
         * @since 1.0.0
         *
         * @param integer $requirement_id
         * @param integer $post_id
         */
        do_action( 'gamipress_requirement_ui_html_after_requirement_title', $requirement_id, $post_id ); ?>

    </li>
    <?php
}

/**
 * Define the requirement type to use
 *
 * @since 1.3.1
 *
 * @param WP_Post   $post
 * @param array     $metabox
 *
 * @return string
 */
function gamipress_requirements_ui_get_requirement_type( $post, $metabox ) {

    $requirement_type = '';

    if( $post->post_type === 'points-type' ) {
        // Requirement type passed through the add_meta_box()
        $requirement_type = $metabox['args']['requirement_type'];
    } else if( gamipress_is_achievement( $post ) ) {
        $requirement_type = 'step';
    } else if( gamipress_is_rank( $post ) ) {
        $requirement_type = 'rank-requirement';
    } else if( isset( $metabox['args'] ) && isset( $metabox['args']['requirement_type'] ) ) {
        $requirement_type = $metabox['args']['requirement_type'];
    }

    return apply_filters( 'gamipress_requirements_ui_get_requirement_type', $requirement_type, $post );
}

/**
 * Get all the requirements of a given points award
 *
 * @since  1.0.0
 *
 * @param  integer $point_award_id The given requirement's post ID
 *
 * @return array|bool       An array of all the requirement requirements if it has any, false if not
 */
function gamipress_get_points_award_requirements( $point_award_id = 0 ) {

    $requirements = gamipress_get_requirement_object( $point_award_id );

    return apply_filters( 'gamipress_get_points_award_requirements', $requirements, $point_award_id );
}

/**
 * Get all the requirements of a given points deduct
 *
 * @since  1.3.7
 *
 * @param  integer $point_deduct_id The given requirement's post ID
 *
 * @return array|bool       An array of all the requirement requirements if it has any, false if not
 */
function gamipress_get_points_deduct_requirements( $point_deduct_id = 0 ) {

    $requirements = gamipress_get_requirement_object( $point_deduct_id );

    return apply_filters( 'gamipress_get_points_deduct_requirements', $requirements, $point_deduct_id );
}

/**
 * Get all the requirements of a given step
 *
 * @since  1.0.0
 *
 * @param  integer $step_id The given requirement's post ID
 *
 * @return array|bool       An array of all the requirement requirements if it has any, false if not
 */
function gamipress_get_step_requirements( $step_id = 0 ) {

    $requirements = gamipress_get_requirement_object( $step_id );

    return apply_filters( 'gamipress_get_step_requirements', $requirements, $step_id );
}

/**
 * Get all the requirements of a given rank requirement
 *
 * @since  1.3.1
 *
 * @param  integer $rank_requirement_id The given requirement's post ID
 *
 * @return array|bool       An array of all the requirement requirements if it has any, false if not
 */
function gamipress_get_rank_requirement_requirements( $rank_requirement_id = 0 ) {

    $requirements = gamipress_get_requirement_object( $rank_requirement_id );

    return apply_filters( 'gamipress_get_rank_requirement_requirements', $requirements, $rank_requirement_id );
}

/**
 * AJAX Handler for adding a new requirement
 *
 * @since 1.0.0
 */
function gamipress_add_requirement_ajax_handler() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    $post = get_post( $_POST['post_id'] );

    $requirement_type = gamipress_requirements_ui_get_requirement_type( $post, array(
        'args' => array(
            'requirement_type' => ( isset($_POST['requirement_type']) ? $_POST['requirement_type'] : '' )
        )
    ) );

    // Create a new requirement post and grab it's ID
    $requirement_id = wp_insert_post( array(
        'post_type'   => $requirement_type,
        'post_status' => 'publish',
        'post_parent' => $post->ID,
        'menu_order'  => 0,
    ) );

    // Output the edit requirement html to insert into the requirements meta box
    gamipress_requirement_ui_html( $requirement_id, $post->ID );

    // Die here, because it's AJAX
    die;
}
add_action( 'wp_ajax_gamipress_add_requirement', 'gamipress_add_requirement_ajax_handler' );

/**
 * AJAX Handler for duplicate a requirement
 *
 * @since 1.0.0
 */
function gamipress_duplicate_requirement_ajax_handler() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    $post = get_post( $_POST['post_id'] );

    $requirement = gamipress_get_requirement_object( absint( $_POST['requirement_id'] ) );

    // Create a new requirement post and grab it's ID
    $clone_requirement_id = wp_insert_post( array(
        'post_title'    => gamipress_get_post_field( 'post_title', $requirement['ID'] ),
        'post_type'     => gamipress_get_post_field( 'post_type', $requirement['ID'] ),
        'post_status'   => 'publish',
        'post_parent'   => $post->ID,
        'menu_order'    => absint( $post->menu_order ) + 1,
    ) );

    // Set the cloned ID to let gamipress_update_requirement() clone the data
    $requirement['ID'] = $clone_requirement_id;

    gamipress_update_requirement( $requirement );

    // Reset the trigger cache
    gamipress_delete_trigger_cache( $requirement['trigger_type'] );

    // Output the edit requirement html to insert into the requirements meta box
    gamipress_requirement_ui_html( $clone_requirement_id, $post->ID );

    // Die here, because it's AJAX
    die;
}
add_action( 'wp_ajax_gamipress_duplicate_requirement', 'gamipress_duplicate_requirement_ajax_handler' );

/**
 * AJAX Handler for deleting a requirement
 *
 * @since 1.0.0
 */
function gamipress_delete_requirement_ajax_handler() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Reset the trigger cache
    $trigger = gamipress_get_post_meta( $_POST['requirement_id'], '_gamipress_trigger_type' );

    gamipress_delete_trigger_cache( $trigger );

    // Delete the requirement post
    wp_delete_post( $_POST['requirement_id'] );

    die;
}
add_action( 'wp_ajax_gamipress_delete_requirement', 'gamipress_delete_requirement_ajax_handler' );

/**
 * AJAX Handler for saving all requirements
 *
 * @since 1.0.5
 */
function gamipress_update_requirements_ajax_handler() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    $post_id = $_POST['post_id'];
    $requirements_limit = 20;
    $order = absint( $_POST['loop'] ) * $requirements_limit;

    // Save sequential steps (now placed on requirements UI)
    if( isset( $_POST['_gamipress_sequential'] ) && ! empty( $_POST['_gamipress_sequential'] ) ) {
        gamipress_update_post_meta( $post_id, '_gamipress_sequential', 'on' );
    } else {
        gamipress_delete_post_meta( $post_id, '_gamipress_sequential', 'on' );
    }

    // Only continue if we have any requirements
    if ( isset( $_POST['requirements'] ) ) {

        // Setup an array for storing all our requirement titles
        // This let's us dynamically update the Label field when requirements are saved
        $new_titles = array();
        $triggers = array();

        // Loop through each of the created requirements
        foreach ( $_POST['requirements'] as $requirement ) {

            $requirement_updated = gamipress_update_requirement( $requirement, $order );

            // Grab the requirement ID
            $requirement_id = $requirement['requirement_id'];

            // Add the title to our AJAX return
            $new_titles[$requirement_id] = stripslashes( $requirement_updated['title'] );

            // Add the trigger to the triggers array if not added yet
            if( ! in_array( $requirement['trigger_type'], $triggers ) ) {
                $triggers[] = $requirement['trigger_type'];
            }

            // Update order
            $order++;

        }

        // Loop all saved triggers to reset their caches
        foreach( $triggers as $trigger ) {
            // Reset the trigger cache
            gamipress_delete_trigger_cache( $trigger );
        }

        // Send back all our requirement titles
        echo json_encode( $new_titles );

    }

    // We're done here.
    die;

}
add_action( 'wp_ajax_gamipress_update_requirements', 'gamipress_update_requirements_ajax_handler' );

/**
 * Save a requirement
 *
 * @since 1.4.6
 *
 * @param array     $requirement
 * @param integer   $order
 *
 * @return array                    The updated requirement object
 */
function gamipress_update_requirement( $requirement, $order = 0 ) {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {

        if( defined( 'DOING_AJAX' ) ) {
            wp_send_json_error( __('You are not allowed to perform this action.', 'gamipress') );
        } else {
            return $requirement;
        }

    }

    global $wpdb;

    // Grab all of the relevant values of that requirement
    $requirement_id         = isset( $requirement['ID'] ) ? $requirement['ID'] : $requirement['requirement_id'];
    $requirement_type       = gamipress_get_post_type( $requirement_id );
    $count                  = ( ! empty( $requirement['count'] ) ) ? absint( $requirement['count'] ) : 1;
    $points_required        = ( ! empty( $requirement['points_required'] ) ) ? absint( $requirement['points_required'] ) : 1;
    $points_type_required   = ( ! empty( $requirement['points_type_required'] ) ) ? $requirement['points_type_required'] : '';
    $rank_type_required     = ( ! empty( $requirement['rank_type_required'] ) ) ? $requirement['rank_type_required'] : '';
    $rank_required          = ( ! empty( $requirement['rank_required'] ) ) ? absint( $requirement['rank_required'] ) : 0;
    $limit                  = ( ! empty( $requirement['limit'] ) ) ? absint( $requirement['limit'] ) : 1;
    $limit_type             = ( ! empty( $requirement['limit_type'] ) ) ? $requirement['limit_type'] : 'unlimited';
    $trigger_type           = $requirement['trigger_type'];
    $achievement_type       = $requirement['achievement_type'];

    // Connect the achievement with the requirement
    if( $trigger_type === 'specific-achievement' ) {

        $achievement_post_id = absint( $requirement['achievement_post'] );

        // Update achievement post to check it on rules engine
        update_post_meta( $requirement_id, '_gamipress_achievement_post', $achievement_post_id );

    }

    // Specific activity trigger type
    if( in_array( $trigger_type, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

        $achievement_post_id = absint( $requirement['achievement_post'] );
        $achievement_post_site_id = ( empty( $requirement['achievement_post_site_id'] ) ? get_current_blog_id() : absint( $requirement['achievement_post_site_id'] ) );

        // Update achievement post to check it on rules engine
        update_post_meta( $requirement_id, '_gamipress_achievement_post', $achievement_post_id );
        update_post_meta( $requirement_id, '_gamipress_achievement_post_site_id', $achievement_post_site_id );

    }

    // Update our relevant meta
    update_post_meta( $requirement_id, '_gamipress_points_required', $points_required );
    update_post_meta( $requirement_id, '_gamipress_points_type_required', $points_type_required );
    update_post_meta( $requirement_id, '_gamipress_rank_type_required', $rank_type_required );
    update_post_meta( $requirement_id, '_gamipress_rank_required', $rank_required );
    update_post_meta( $requirement_id, '_gamipress_count', $count );
    update_post_meta( $requirement_id, '_gamipress_limit', $limit );
    update_post_meta( $requirement_id, '_gamipress_limit_type', $limit_type );
    update_post_meta( $requirement_id, '_gamipress_trigger_type', $trigger_type );
    update_post_meta( $requirement_id, '_gamipress_achievement_type', $achievement_type );

    // Specific points award data
    if( $requirement_type === 'points-award' || $requirement_type === 'points-deduct' ) {
        $points           = ( ! empty( $requirement['points'] ) ) ? absint( $requirement['points'] ) : 1;
        $points_type      = ( ! empty( $requirement['points_type'] ) ) ? $requirement['points_type'] : '';
        $maximum_earnings = ( ! $requirement['maximum_earnings'] !== "" ) ? absint( $requirement['maximum_earnings'] ) : 1;

        update_post_meta( $requirement_id, '_gamipress_points', $points );
        update_post_meta( $requirement_id, '_gamipress_points_type', $points_type );
        update_post_meta( $requirement_id, '_gamipress_maximum_earnings', $maximum_earnings );
    }

    // Action to store custom requirement data when saved
    do_action( 'gamipress_update_requirement', $requirement_id, $requirement );

    if( defined( 'DOING_AJAX' ) ) {
        // Action to store custom requirement data when saved through ajax
        do_action( 'gamipress_ajax_update_requirement', $requirement_id, $requirement );
    }

    // Setup a new title if no set
    $post_title = ! empty( $requirement['title'] ) ? $requirement['title'] : gamipress_build_requirement_title( $requirement_id, $requirement );

    // Update our original post with the new title
    wp_update_post( array(
        'ID' => $requirement_id,
        'post_title' => $post_title,
        'post_status' => isset( $requirement['status'] ) ? $requirement['status'] : gamipress_get_post_status( $requirement_id ),
        'menu_order' => $order
    ) );

    return gamipress_get_requirement_object( $requirement_id );

}

/**
 * Save custom fields
 *
 * @since 1.4.6
 *
 * @param integer   $post_id
 */
function gamipress_on_save_requirements_post_parent( $post_id ) {

    $post_type = gamipress_get_post_type( $post_id );
    $allowed_post_types = array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    if( ! in_array( $post_type, $allowed_post_types ) ) {
        return;
    }

    // Save sequential steps (now placed on requirements UI)
    if( isset( $_REQUEST['_gamipress_sequential'] ) ) {
        gamipress_update_post_meta( $post_id, '_gamipress_sequential', 'on' );
    } else {
        gamipress_delete_post_meta( $post_id, '_gamipress_sequential', 'on' );
    }

}
add_action( 'save_post', 'gamipress_on_save_requirements_post_parent' );

/**
 * Generate a requirement title based on his configuration
 *
 * @since 1.0.5
 *
 * @param $requirement_id   integer The requirement ID
 * @param $requirement      array The requirement array object (Optional)
 *
 * @return string
 */
function gamipress_build_requirement_title( $requirement_id, $requirement = array() ) {

    if( empty( $requirement ) ) {
        $requirement = gamipress_get_requirement_object( $requirement_id );
    }

    $requirement_type       = gamipress_get_post_type( $requirement_id );
    $points_required        = ( ! empty( $requirement['points_required'] ) ) ? absint( $requirement['points_required'] ) : 1;
    $points_type_required   = ( ! empty( $requirement['points_type_required'] ) ) ? $requirement['points_type_required'] : '';
    $rank_type_required     = ( ! empty( $requirement['rank_type_required'] ) ) ? $requirement['rank_type_required'] : '';
    $rank_required          = ( ! empty( $requirement['rank_required'] ) ) ? absint( $requirement['rank_required'] ) : 0;
    $count                  = ( ! empty( $requirement['count'] ) ) ? absint( $requirement['count'] ) : 1;
    $limit                  = ( ! empty( $requirement['limit'] ) ) ? absint( $requirement['limit'] ) : 1;
    $limit_type             = ( ! empty( $requirement['limit_type'] ) ) ? $requirement['limit_type'] : 'unlimited';
    $trigger_type           = $requirement['trigger_type'];
    $achievement_type       = $requirement['achievement_type'];

    // Flip between our requirement types and make an appropriate connection
    switch ( $trigger_type ) {
        case 'earn-points':
            $title = sprintf( __( 'Earn %s', 'gamipress' ), gamipress_format_points( $points_required, $points_type_required ) );
            break;
        case 'gamipress_expend_points':
            $title = sprintf( __( 'Expend %s', 'gamipress' ), gamipress_format_points( $points_required, $points_type_required ) );
            break;
        case 'earn-rank':
            $rank = gamipress_get_post( $rank_required );

            $title = sprintf( __( 'Reach %s %s', 'gamipress' ), ( $rank ? gamipress_get_rank_type_singular( $rank->post_type, true ) : '' ), ( $rank ? $rank->post_title : '' ) );
            break;
        // Connect the requirement to ANY of the given achievement type
        case 'any-achievement':
            $title = sprintf( __( 'Unlock any %s', 'gamipress' ), $achievement_type );
            break;
        case 'all-achievements':
            $title = sprintf( __( 'Unlock all %s', 'gamipress' ), $achievement_type );
            break;
        case 'specific-achievement':
            $title = 'Unlock "' . gamipress_get_specific_activity_trigger_post_title( $requirement['achievement_post'], $trigger_type, $requirement['achievement_post_site_id'] ) . '"';
            break;
        default:
            $title = gamipress_get_activity_trigger_label( $trigger_type );
            break;

    }

    // Specific activity trigger type
    if( in_array( $trigger_type, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {
        $achievement_post_id = absint( $requirement['achievement_post'] );
        $achievement_post_site_id = absint( $requirement['achievement_post_site_id'] );

        if( $achievement_post_id ) {
            // Filtered title
            $title = sprintf(
                gamipress_get_specific_activity_trigger_label( $trigger_type ),
                gamipress_get_specific_activity_trigger_post_title( $achievement_post_id, $trigger_type, $achievement_post_site_id )
            );
        }
    }

    // Filter to completely override activity trigger label
    $title = apply_filters( 'gamipress_activity_trigger_label', $title, $requirement_id, $requirement );

    // Prepend "%d %s for" with the points to title
    if( $requirement_type === 'points-award' || $requirement_type === 'points-deduct' ) {

        $points           = ( ! empty( $requirement['points'] ) ) ? absint( $requirement['points'] ) : 1;
        $points_type      = ( ! empty( $requirement['points_type'] ) ) ? $requirement['points_type'] : '';
        // This var is correctly set but not used to generate the title
        //$maximum_earnings = ( ! empty( $requirement['maximum_earnings'] ) ) ? absint( $requirement['maximum_earnings'] ) : 1;

        if ( $points > 0 ) {

            $points_title = '';

            if( $requirement_type === 'points-award' ) {
                $points_title = sprintf( __( '%s for', 'gamipress' ), gamipress_format_points( $points, $points_type ) ); // 1 Points for
            } else if( $requirement_type === 'points-deduct' ) {
                $points_title = sprintf( __( '-%s for', 'gamipress' ), gamipress_format_points( $points, $points_type ) ); // -1 Points for
            }

            // Prepend the points title to the title
            $title = sprintf( __( '%1$s %2$s', 'gamipress' ), $points_title, lcfirst( $title ) );

        }

    }

    // Check if trigger is excluded from activity limits
    if( ! in_array( $trigger_type, gamipress_get_activity_triggers_excluded_from_activity_limit() ) ) {
        // Add "%d time(s)" to title
        $title = sprintf( __( '%1$s %2$s', 'gamipress' ), $title, sprintf( _n( '%d time', '%d times', $count ), $count ) );
    }

    // Add "(limited to %d per %s)" to title if is limited
    if( $limit_type !== 'unlimited' ) {

        $limit_type_label = '';

        switch( $limit_type ) {
            case 'daily':
                $limit_type_label = __( 'day', 'gamipress' );
                break;
            case 'weekly':
                $limit_type_label = __( 'week', 'gamipress' );
                break;
            case 'monthly':
                $limit_type_label = __( 'month', 'gamipress' );
                break;
            case 'yearly':
                $limit_type_label = __( 'year', 'gamipress' );
                break;
        }

        $title = sprintf( __( '%1$s %2$s', 'gamipress' ), $title, sprintf( __( '(limited to %d per %s)', 'gamipress' ), $limit, $limit_type_label ) );
    }

    // Available hook for custom Activity Triggers
    return apply_filters( 'gamipress_requirement_title', $title, $requirement_id, $requirement );

}

/**
 * Get the sort order for a given requirement
 *
 * @since   1.0.5
 * @updated 1.5.1
 *
 * @param int $requirement_id   The given requirement's post ID
 *
 * @return int                  The requirement's sort order
 */
function gamipress_get_requirement_menu_order( $requirement_id = 0 ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        return gamipress_get_requirement_menu_order_old( $requirement_id );
    }

    return gamipress_get_post_field( 'menu_order', $requirement_id );

}

/**
 * Helper function for comparing our requirement sort order (used in uasort() in gamipress_create_points_awards_meta_box())
 *
 * @since   1.0.5
 * @updated 1.5.1
 *
 * @param WP_Post $requirement_x    The given requirement
 * @param WP_Post $requirement_y    The requirement we're comparing against
 *
 * @return int                      0 if the order matches, -1 if it's lower, 1 if it's higher
 */
function gamipress_compare_requirements_order( $requirement_x, $requirement_y ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) && property_exists( $requirement_x, 'order' ) ) {
        return gamipress_compare_requirements_order_old( $requirement_x, $requirement_y );
    }

    if ( $requirement_x->menu_order == $requirement_y->menu_order ) {
        return 0;
    }

    return ( $requirement_x->menu_order < $requirement_y->menu_order ) ? -1 : 1;

}
