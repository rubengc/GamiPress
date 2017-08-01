<?php
/**
 * Post Types
 *
 * @package     GamiPress\Post_Types
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register all GamiPress CPTs
 *
 * @since  1.0.0
 * @return void
 */
function gamipress_register_post_types() {
	// Register Points Types
	register_post_type( 'points-type', array(
		'labels' => array(
			'name'               	=> __( 'Points Types', 'gamipress' ),
			'singular_name'      	=> __( 'Points Type', 'gamipress' ),
			'add_new'            	=> __( 'Add New', 'gamipress' ),
			'add_new_item'       	=> __( 'Add New Points Type', 'gamipress' ),
			'edit_item'          	=> __( 'Edit Points Type', 'gamipress' ),
			'new_item'           	=> __( 'New Points Type', 'gamipress' ),
			'all_items'          	=> __( 'Points Types', 'gamipress' ),
			'view_item'          	=> __( 'View Points Type', 'gamipress' ),
			'search_items'       	=> __( 'Search Points Types', 'gamipress' ),
			'not_found'          	=> __( 'No points types found', 'gamipress' ),
			'not_found_in_trash' 	=> __( 'No points types found in Trash', 'gamipress' ),
			'parent_item_colon'  	=> '',
			'menu_name'          	=> __( 'Points Types', 'gamipress' ),
		),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
		'show_in_menu'       => 'gamipress',
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'page-attributes' ),
	) );

    // Register Points Award
    $public_points_awards = apply_filters( 'gamipress_public_points_awards', false );

    register_post_type( 'points-award', array(
        'labels'             => array(
            'name'               => __( 'Points Awards', 'gamipress' ),
            'singular_name'      => __( 'Points Award', 'gamipress' ),
            'add_new'            => __( 'Add New', 'gamipress' ),
            'add_new_item'       => __( 'Add New Points Award', 'gamipress' ),
            'edit_item'          => __( 'Edit Points Award', 'gamipress' ),
            'new_item'           => __( 'New Points Award', 'gamipress' ),
            'all_items'          => __( 'Points Awards', 'gamipress' ),
            'view_item'          => __( 'View Points Award', 'gamipress' ),
            'search_items'       => __( 'Search Points Awards', 'gamipress' ),
            'not_found'          => __( 'No points awards found', 'gamipress' ),
            'not_found_in_trash' => __( 'No points awards found in Trash', 'gamipress' ),
            'parent_item_colon'  => '',
            'menu_name'          => __( 'Points Awards', 'gamipress' )
        ),
        'public'             => $public_points_awards,
        'publicly_queryable' => $public_points_awards,
        'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
        'show_in_menu'       => $public_points_awards,
        'query_var'          => false,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => $public_points_awards,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title' ),

    ) );

	gamipress_register_achievement_type( 0, 'Points Award', 'Points Awards' );
	gamipress_register_requirement_type( 'Points Award', 'Points Awards' );

	// Register Achivement Types
	register_post_type( 'achievement-type', array(
		'labels' => array(
			'name'               	=> __( 'Achievement Types', 'gamipress' ),
			'singular_name'      	=> __( 'Achievement Type', 'gamipress' ),
			'add_new'            	=> __( 'Add New', 'gamipress' ),
			'add_new_item'       	=> __( 'Add New Achievement Type', 'gamipress' ),
			'edit_item'          	=> __( 'Edit Achievement Type', 'gamipress' ),
			'new_item'           	=> __( 'New Achievement Type', 'gamipress' ),
			'all_items'          	=> __( 'Achievement Types', 'gamipress' ),
			'view_item'          	=> __( 'View Achievement Type', 'gamipress' ),
			'search_items'       	=> __( 'Search Achievement Types', 'gamipress' ),
			'not_found'          	=> __( 'No achievement types found', 'gamipress' ),
			'not_found_in_trash' 	=> __( 'No achievement types found in Trash', 'gamipress' ),
			'parent_item_colon'  	=> '',
			'menu_name'          	=> __( 'Achievement Types', 'gamipress' ),
			'featured_image'     	=> __( 'Default Achievement Image', 'gamipress' ),
			'set_featured_image'    => __( 'Set default achievement image', 'gamipress' ),
			'remove_featured_image' => __( 'Remove default achievement image', 'gamipress' ),
			'use_featured_image'    => __( 'Use default achievement image', 'gamipress' ),
		),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
		'show_in_menu'       => 'gamipress',
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
	) );

	// Register Step
    $public_steps = apply_filters( 'gamipress_public_steps', false );

	register_post_type( 'step', array(
		'labels'             => array(
			'name'               => __( 'Steps', 'gamipress' ),
			'singular_name'      => __( 'Step', 'gamipress' ),
			'add_new'            => __( 'Add New', 'gamipress' ),
			'add_new_item'       => __( 'Add New Step', 'gamipress' ),
			'edit_item'          => __( 'Edit Step', 'gamipress' ),
			'new_item'           => __( 'New Step', 'gamipress' ),
			'all_items'          => __( 'Steps', 'gamipress' ),
			'view_item'          => __( 'View Step', 'gamipress' ),
			'search_items'       => __( 'Search Steps', 'gamipress' ),
			'not_found'          => __( 'No steps found', 'gamipress' ),
			'not_found_in_trash' => __( 'No steps found in Trash', 'gamipress' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Steps', 'gamipress' )
		),
		'public'             => $public_steps,
		'publicly_queryable' => $public_steps,
		'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
		'show_in_menu'       => $public_steps,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => $public_steps,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title' ),

	) );
	gamipress_register_achievement_type( 0, 'Step', 'Steps' );
	gamipress_register_requirement_type( 'Step', 'Steps' );

	// Register Log
	register_post_type( 'gamipress-log', array(
		'labels'             => array(
			'name'               => __( 'Logs', 'gamipress' ),
			'singular_name'      => __( 'Log', 'gamipress' ),
			'add_new'            => __( 'Add New', 'gamipress' ),
			'add_new_item'       => __( 'Add New Log Entry', 'gamipress' ),
			'edit_item'          => __( 'Edit Log Entry', 'gamipress' ),
			'new_item'           => __( 'New Log Entry', 'gamipress' ),
			'all_items'          => __( 'Logs', 'gamipress' ),
			'view_item'          => __( 'View Logs', 'gamipress' ),
			'search_items'       => __( 'Search Logs', 'gamipress' ),
			'not_found'          => __( 'No Log Entries found', 'gamipress' ),
			'not_found_in_trash' => __( 'No Log Entries found in Trash', 'gamipress' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Logs', 'gamipress' )
		),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
		'show_in_menu'       => 'gamipress',
		'show_in_nav_menus'  => false,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'gamipress-log' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( '' )
	) );

}
add_action( 'init', 'gamipress_register_post_types' );

/**
 * Register our various points types for use in the rules engine
 *
 * @since  1.0.0
 *
 * @param  integer $points_type_id Post id of the points type
 * @param  string  $points_name_singular The singular name
 * @param  string  $points_name_plural  The plural name
 *
 * @return void
 */
function gamipress_register_points_type( $points_type_id = 0, $points_name_singular = '', $points_name_plural = '' ) {
	GamiPress()->points_types[sanitize_title( strtolower( $points_name_singular ) )] = array(
        'ID' => $points_type_id,
        'singular_name' => $points_name_singular,
        'plural_name' => $points_name_plural,
    );
}

/**
 * Register each of our Points Types as WordPress post type
 *
 * @since  1.0.0
 * @return void
 */
function gamipress_register_points_types() {

    // Grab all of our points type posts
    $points_types = get_posts( array(
        'post_type'      =>	'points-type',
        'posts_per_page' =>	-1,
    ) );

    // Loop through each points type post and register it as a CPT
    foreach ( $points_types as $points_type ) {

        // Grab our points name
        $points_name = $points_type->post_title;

        if( empty( $points_name ) ) {
            continue;
        }

        // Update our post meta to use the points name, if it's empty
        if ( ! get_post_meta( $points_type->ID, '_gamipress_singular_name', true ) ) update_post_meta( $points_type->ID, '_gamipress_singular_name', $points_name );
        if ( ! get_post_meta( $points_type->ID, '_gamipress_plural_name', true ) ) update_post_meta( $points_type->ID, '_gamipress_plural_name', $points_name );

        // Setup our singular and plural versions to use the corresponding meta
        $points_name_singular = get_post_meta( $points_type->ID, '_gamipress_singular_name', true );
        $points_name_plural   = get_post_meta( $points_type->ID, '_gamipress_plural_name', true );

        // Register the Achievement type
        gamipress_register_points_type( $points_type->ID, $points_name_singular, $points_name_plural );

    }
}
add_action( 'init', 'gamipress_register_points_types', 6 );

/**
 * Register our various achievement types for use in the rules engine
 *
 * @since  1.0.0
 *
 * @param  integer 	$achievement_type_id 		Post id of the achievement type
 * @param  string 	$achievement_name_singular 	The singular name
 * @param  string 	$achievement_name_plural  	The plural name
 *
 * @return void
 */
function gamipress_register_achievement_type( $achievement_type_id = 0, $achievement_name_singular = '', $achievement_name_plural = '' ) {
	GamiPress()->achievement_types[sanitize_title( strtolower( $achievement_name_singular ) )] = array(
		'ID' => $achievement_type_id,
		'singular_name' => $achievement_name_singular,
		'plural_name' => $achievement_name_plural,
	);
}

/**
 * Register each of our Achivement Types as WordPress post type
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_register_achievement_types() {

	// Grab all of our achievement type posts
	$achievement_types = get_posts( array(
		'post_type'      =>	'achievement-type',
		'posts_per_page' =>	-1,
	) );

	// Loop through each achievement type post and register it as a CPT
	foreach ( $achievement_types as $achievement_type ) {

		// Grab our achievement name
		$achievement_name = $achievement_type->post_title;

        if( empty( $achievement_name ) ) {
            continue;
        }

		// Update our post meta to use the achievement name, if it's empty
		if ( ! get_post_meta( $achievement_type->ID, '_gamipress_singular_name', true ) ) update_post_meta( $achievement_type->ID, '_gamipress_singular_name', $achievement_name );
		if ( ! get_post_meta( $achievement_type->ID, '_gamipress_plural_name', true ) ) update_post_meta( $achievement_type->ID, '_gamipress_plural_name', $achievement_name );

		// Setup our singular and plural versions to use the corresponding meta
		$achievement_name_singular = get_post_meta( $achievement_type->ID, '_gamipress_singular_name', true );
		$achievement_name_plural   = get_post_meta( $achievement_type->ID, '_gamipress_plural_name', true );

		// Register the post type
		register_post_type( sanitize_title( substr( strtolower( $achievement_name_singular ), 0, 20 ) ), array(
			'labels'             => array(
				'name'               => $achievement_name_plural,
				'singular_name'      => $achievement_name_singular,
				'add_new'            => __( 'Add New', 'gamipress' ),
				'add_new_item'       => sprintf( __( 'Add New %s', 'gamipress' ), $achievement_name_singular ),
				'edit_item'          => sprintf( __( 'Edit %s', 'gamipress' ), $achievement_name_singular ),
				'new_item'           => sprintf( __( 'New %s', 'gamipress' ), $achievement_name_singular ),
				'all_items'          => $achievement_name_plural,
				'view_item'          => sprintf( __( 'View %s', 'gamipress' ), $achievement_name_singular ),
				'search_items'       => sprintf( __( 'Search %s', 'gamipress' ), $achievement_name_plural ),
				'not_found'          => sprintf( __( 'No %s found', 'gamipress' ), strtolower( $achievement_name_plural ) ),
				'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'gamipress' ), strtolower( $achievement_name_plural ) ),
				'parent_item_colon'  => '',
				'menu_name'          => $achievement_name_plural,
				'featured_image'     	=> sprintf( __( '%s Image', 'gamipress' ), $achievement_name_singular ),
				'set_featured_image'    => sprintf( __( 'Set %s image', 'gamipress' ), strtolower( $achievement_name_singular ) ),
				'remove_featured_image' => sprintf( __( 'Remove %s image', 'gamipress' ), strtolower( $achievement_name_singular ) ),
				'use_featured_image'    => sprintf( __( 'Use %s image', 'gamipress' ), strtolower( $achievement_name_singular ) ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
			'show_in_menu'       => 'gamipress_achievements',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => sanitize_title( strtolower( $achievement_name_singular ) ) ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'page-attributes' )
		) );

		// Register the Achievement type
		gamipress_register_achievement_type( $achievement_type->ID, $achievement_name_singular, $achievement_name_plural );

	}
}
add_action( 'init', 'gamipress_register_achievement_types', 8 );

/**
 * Register a requirement type
 *
 * @since  1.0.5
 *
 * @param  string $requirement_name_singular The singular name
 * @param  string $requirement_name_plural  The plural name
 *
 * @return void
 */
function gamipress_register_requirement_type( $requirement_name_singular = '', $requirement_name_plural = '' ) {
	GamiPress()->requirement_types[sanitize_title( strtolower( $requirement_name_singular ) )] = array(
		'singular_name' => $requirement_name_singular,
		'plural_name' => $requirement_name_plural,
	);
}
