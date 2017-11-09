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

	// Register Points Type
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
        'show_in_menu'       => ( ( $public_points_awards || gamipress_is_debug_mode() ) ? 'gamipress' : false ),
        'query_var'          => false,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => $public_points_awards,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title' ),

    ) );

	gamipress_register_requirement_type( 'Points Award', 'Points Awards' );

	// Register Achievement Type
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
		'supports'           => array( 'thumbnail' ),
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
		'show_in_menu'       => ( ( $public_steps || gamipress_is_debug_mode() ) ? 'gamipress' : false ),
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => $public_steps,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title' ),

	) );
	gamipress_register_requirement_type( 'Step', 'Steps' );

	// Register Rank Type
	register_post_type( 'rank-type', array(
		'labels' => array(
			'name'               	=> __( 'Rank Types', 'gamipress' ),
			'singular_name'      	=> __( 'Rank Type', 'gamipress' ),
			'add_new'            	=> __( 'Add New', 'gamipress' ),
			'add_new_item'       	=> __( 'Add New Rank Type', 'gamipress' ),
			'edit_item'          	=> __( 'Edit Rank Type', 'gamipress' ),
			'new_item'           	=> __( 'New Rank Type', 'gamipress' ),
			'all_items'          	=> __( 'Rank Types', 'gamipress' ),
			'view_item'          	=> __( 'View Rank Type', 'gamipress' ),
			'search_items'       	=> __( 'Search Rank Types', 'gamipress' ),
			'not_found'          	=> __( 'No rank types found', 'gamipress' ),
			'not_found_in_trash' 	=> __( 'No rank types found in Trash', 'gamipress' ),
			'parent_item_colon'  	=> '',
			'menu_name'          	=> __( 'Rank Types', 'gamipress' ),
			'featured_image'     	=> __( 'Default Rank Image', 'gamipress' ),
			'set_featured_image'    => __( 'Set default rank image', 'gamipress' ),
			'remove_featured_image' => __( 'Remove default rank image', 'gamipress' ),
			'use_featured_image'    => __( 'Use default rank image', 'gamipress' ),
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
		'supports'           => array( 'thumbnail' ),
	) );

	// Register Rank Requirement
	$public_rank_requirements = apply_filters( 'gamipress_public_rank_requirements', false );

	register_post_type( 'rank-requirement', array(
		'labels'             => array(
			'name'               => __( 'Rank Requirements', 'gamipress' ),
			'singular_name'      => __( 'Rank Requirement', 'gamipress' ),
			'add_new'            => __( 'Add New', 'gamipress' ),
			'add_new_item'       => __( 'Add New Rank Requirement', 'gamipress' ),
			'edit_item'          => __( 'Edit Rank Requirement', 'gamipress' ),
			'new_item'           => __( 'New Rank Requirement', 'gamipress' ),
			'all_items'          => __( 'Rank Requirements', 'gamipress' ),
			'view_item'          => __( 'View Rank Requirement', 'gamipress' ),
			'search_items'       => __( 'Search Rank Requirements', 'gamipress' ),
			'not_found'          => __( 'No rank requirements found', 'gamipress' ),
			'not_found_in_trash' => __( 'No rank requirements found in Trash', 'gamipress' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Rank Requirements', 'gamipress' )
		),
		'public'             => $public_rank_requirements,
		'publicly_queryable' => $public_rank_requirements,
		'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
		'show_in_menu'       => ( ( $public_rank_requirements || gamipress_is_debug_mode() ) ? 'gamipress' : false ),
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => $public_rank_requirements,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title' ),

	) );
	gamipress_register_requirement_type( 'Rank Requirement', 'Rank Requirements' );

}
add_action( 'init', 'gamipress_register_post_types' );

/**
 * Register our various points types for use in the rules engine
 *
 * @since  1.0.0
 *
 * @param  integer  $points_type_id 		Post id of the points type
 * @param  string   $points_name_singular 	The singular name
 * @param  string   $points_name_plural  	The plural name
 * @param  string 	$slug  						If null or empty, slug will be auto-generated from singular name
 *
 * @return void
 */
function gamipress_register_points_type( $points_type_id = 0, $points_name_singular = '', $points_name_plural = '', $slug = null ) {

    if( $slug === null || empty( $slug ) ) {
        $slug = sanitize_title( strtolower( $points_name_singular ) );
    }

	GamiPress()->points_types[sanitize_key( $slug )] = array(
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
        $points_slug = $points_type->post_name;

        if( empty( $points_slug ) ) {
            continue;
        }

        // Update our post meta to use the points name, if it's empty
        if ( ! get_post_meta( $points_type->ID, '_gamipress_plural_name', true ) ) update_post_meta( $points_type->ID, '_gamipress_plural_name', $points_slug );

        // Setup our singular and plural versions to use the corresponding meta
        $points_name_singular = $points_type->post_title;
        $points_name_plural   = get_post_meta( $points_type->ID, '_gamipress_plural_name', true );

        // Register the Achievement type
        gamipress_register_points_type( $points_type->ID, $points_name_singular, $points_name_plural, $points_slug );
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
 * @param  string 	$slug  						If null or empty, slug will be auto-generated from singular name
 */
function gamipress_register_achievement_type( $achievement_type_id = 0, $achievement_name_singular = '', $achievement_name_plural = '', $slug = null ) {

	if( $slug === null || empty( $slug ) ) {
		$slug = sanitize_title( strtolower( $achievement_name_singular ) );
	}

	GamiPress()->achievement_types[sanitize_key( $slug )] = array(
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
		$achievement_slug = $achievement_type->post_name;

        if( empty( $achievement_slug ) ) {
            continue;
        }

		// Update our post meta to use the achievement name, if it's empty
		if ( ! get_post_meta( $achievement_type->ID, '_gamipress_plural_name', true ) ) update_post_meta( $achievement_type->ID, '_gamipress_plural_name', $achievement_slug );

		// Setup our singular and plural versions to use the corresponding meta
		$achievement_name_singular 	= $achievement_type->post_title;
		$achievement_name_plural   	= get_post_meta( $achievement_type->ID, '_gamipress_plural_name', true );

		// Register the post type
		register_post_type( $achievement_slug, array(
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
			'rewrite'            => array( 'slug' => $achievement_slug ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'page-attributes' )
		) );

		// Register the Achievement type
		gamipress_register_achievement_type( $achievement_type->ID, $achievement_name_singular, $achievement_name_plural, $achievement_slug );

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

	GamiPress()->requirement_types[sanitize_key( sanitize_title( strtolower( $requirement_name_singular ) ) )] = array(
		'singular_name' => $requirement_name_singular,
		'plural_name' => $requirement_name_plural,
	);

}

/**
 * Register each of our Rank Types as WordPress post type
 *
 * @since  1.3.1
 *
 * @return void
 */
function gamipress_register_rank_types() {

	// Grab all of our rank type posts
	$rank_types = get_posts( array(
		'post_type'      =>	'rank-type',
		'posts_per_page' =>	-1,
	) );

	// Loop through each rank type post and register it as a CPT
	foreach ( $rank_types as $rank_type ) {

		// Grab our rank name
		$rank_slug = $rank_type->post_name;

		if( empty( $rank_slug ) ) {
			continue;
		}

		// Update our post meta to use the rank name, if it's empty
		if ( ! get_post_meta( $rank_type->ID, '_gamipress_plural_name', true ) ) update_post_meta( $rank_type->ID, '_gamipress_plural_name', $rank_slug );

		// Setup our singular and plural versions to use the corresponding meta
		$rank_name_singular 	= $rank_type->post_title;
		$rank_name_plural   	= get_post_meta( $rank_type->ID, '_gamipress_plural_name', true );

		// Register the post type
		register_post_type( $rank_slug, array(
			'labels'             => array(
				'name'               => $rank_name_plural,
				'singular_name'      => $rank_name_singular,
				'add_new'            => __( 'Add New', 'gamipress' ),
				'add_new_item'       => sprintf( __( 'Add New %s', 'gamipress' ), $rank_name_singular ),
				'edit_item'          => sprintf( __( 'Edit %s', 'gamipress' ), $rank_name_singular ),
				'new_item'           => sprintf( __( 'New %s', 'gamipress' ), $rank_name_singular ),
				'all_items'          => $rank_name_plural,
				'view_item'          => sprintf( __( 'View %s', 'gamipress' ), $rank_name_singular ),
				'search_items'       => sprintf( __( 'Search %s', 'gamipress' ), $rank_name_plural ),
				'not_found'          => sprintf( __( 'No %s found', 'gamipress' ), strtolower( $rank_name_plural ) ),
				'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'gamipress' ), strtolower( $rank_name_plural ) ),
				'parent_item_colon'  => '',
				'menu_name'          => $rank_name_plural,
				'featured_image'     	=> sprintf( __( '%s Image', 'gamipress' ), $rank_name_singular ),
				'set_featured_image'    => sprintf( __( 'Set %s image', 'gamipress' ), strtolower( $rank_name_singular ) ),
				'remove_featured_image' => sprintf( __( 'Remove %s image', 'gamipress' ), strtolower( $rank_name_singular ) ),
				'use_featured_image'    => sprintf( __( 'Use %s image', 'gamipress' ), strtolower( $rank_name_singular ) ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
			'show_in_menu'       => 'gamipress_ranks',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $rank_slug ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail' )
		) );

		// Register the Achievement type
		gamipress_register_rank_type( $rank_type->ID, $rank_name_singular, $rank_name_plural, $rank_slug );

	}
}
add_action( 'init', 'gamipress_register_rank_types', 8 );

/**
 * Register our various rank types for use in the rules engine
 *
 * @since  1.3.1
 *
 * @param  integer 	$rank_type_id 		Post id of the rank type
 * @param  string 	$rank_name_singular 	The singular name
 * @param  string 	$rank_name_plural  	The plural name
 * @param  string 	$slug  						If null or empty, slug will be auto-generated from singular name
 */
function gamipress_register_rank_type( $rank_type_id = 0, $rank_name_singular = '', $rank_name_plural = '', $slug = null ) {

	if( $slug === null || empty( $slug ) ) {
		$slug = sanitize_title( strtolower( $rank_name_singular ) );
	}

	GamiPress()->rank_types[sanitize_key( $slug )] = array(
		'ID' => $rank_type_id,
		'singular_name' => $rank_name_singular,
		'plural_name' => $rank_name_plural,
	);

}
