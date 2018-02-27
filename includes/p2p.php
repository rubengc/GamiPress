<?php
/**
 * Post2Post relationships
 *
 * @package     GamiPress\Post2Post
 * @since       1.3.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register points Post2Post relationships
 */
function gamipress_register_points_relationships() {

    // Connect points awards to points type
    // Used to get a points type's active awards (e.g. This points type has these 3 points awards active)
    p2p_register_connection_type( array(
        'name'      => 'points-award-to-points-type',
        'from'      => 'points-award',
        'to'        => 'points-type',
        'admin_box' => false,
        'fields'    => array(
            'order'   => array(
                'title'   => __( 'Order', 'gamipress' ),
                'type'    => 'text',
                'default' => 0,
            ),
        ),
    ) );

    // Connect points deducts to points type
    // Used to get a points type's active deducts (e.g. This points type has these 3 points deducts active)
    p2p_register_connection_type( array(
        'name'      => 'points-deduct-to-points-type',
        'from'      => 'points-deduct',
        'to'        => 'points-type',
        'admin_box' => false,
        'fields'    => array(
            'order'   => array(
                'title'   => __( 'Order', 'gamipress' ),
                'type'    => 'text',
                'default' => 0,
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_points_relationships' );

/**
 * Register achievements Post2Post relationships
 */
function gamipress_register_achievement_relationships() {

    // Grab all our registered achievement types and loop through them
    $achievement_types = gamipress_get_achievement_types_slugs();

    if ( is_array( $achievement_types ) && ! empty( $achievement_types ) ) {

        foreach ( $achievement_types as $achievement_type ) {

            // Connect steps to each achievement type
            // Used to get an achievement's required steps (e.g. This achievement requires these 3 steps)
            p2p_register_connection_type( array(
                'name'      => 'step-to-' . $achievement_type,
                'from'      => 'step',
                'to'        => $achievement_type,
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

            // Connect each achievement type to a step
            // Used to get a step's required achievement (e.g. this step requires earning Level 1)
            p2p_register_connection_type( array(
                'name'      => $achievement_type . '-to-step',
                'from'      => $achievement_type,
                'to'        => 'step',
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

            // Connect each achievement type to a rank requirement
            // Used to get a rank requirement's required achievement (e.g. this step requires earning Level 1)
            p2p_register_connection_type( array(
                'name'      => $achievement_type . '-to-rank-requirement',
                'from'      => $achievement_type,
                'to'        => 'rank-requirement',
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

            // Connect each achievement type to a points award
            // Used to get a points award's required achievement (e.g. this points award requires earning Level 1)
            p2p_register_connection_type( array(
                'name'      => $achievement_type . '-to-points-award',
                'from'      => $achievement_type,
                'to'        => 'points-award',
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

            // Connect each achievement type to a points deduct
            // Used to get a points deduct's required achievement (e.g. this points deduct requires earning Level 1)
            p2p_register_connection_type( array(
                'name'      => $achievement_type . '-to-points-deduct',
                'from'      => $achievement_type,
                'to'        => 'points-deduct',
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

        }
    }

}
add_action( 'init', 'gamipress_register_achievement_relationships' );

/**
 * Register ranks Post2Post relationships
 */
function gamipress_register_rank_relationships() {

    // Grab all our registered rank types and loop through them
    $rank_types = gamipress_get_rank_types_slugs();

    if ( is_array( $rank_types ) && ! empty( $rank_types ) ) {

        foreach ( $rank_types as $rank_type ) {

            // Connect steps to each rank type
            // Used to get an rank's required steps (e.g. This rank requires these 3 steps)
            p2p_register_connection_type( array(
                'name'      => 'rank-requirement-to-' . $rank_type,
                'from'      => 'rank-requirement',
                'to'        => $rank_type,
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

            // Connect each rank type to a step
            // Used to get a step's required rank (e.g. this requirement requires earning Level 1)
            p2p_register_connection_type( array(
                'name'      => $rank_type . '-to-step',
                'from'      => $rank_type,
                'to'        => 'step',
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

            // Connect each rank type to a rank requirement
            // Used to get a rank requirement's required rank (e.g. this requirement requires earning Level 1)
            p2p_register_connection_type( array(
                'name'      => $rank_type . '-to-rank-requirement',
                'from'      => $rank_type,
                'to'        => 'rank-requirement',
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

            // Connect each rank type to a points award
            // Used to get a points award's required rank (e.g. this points award requires earning Level 1)
            p2p_register_connection_type( array(
                'name'      => $rank_type . '-to-points-award',
                'from'      => $rank_type,
                'to'        => 'points-award',
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

            // Connect each rank type to a points deduct
            // Used to get a points deduct's required rank (e.g. this points deduct requires earning Level 1)
            p2p_register_connection_type( array(
                'name'      => $rank_type . '-to-points-deduct',
                'from'      => $rank_type,
                'to'        => 'points-deduct',
                'admin_box' => false,
                'fields'    => array(
                    'order'   => array(
                        'title'   => __( 'Order', 'gamipress' ),
                        'type'    => 'text',
                        'default' => 0,
                    ),
                ),
            ) );

        }
    }

}
add_action( 'init', 'gamipress_register_rank_relationships' );
