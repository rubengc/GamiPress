<?php
/**
 * User-related Functions
 *
 * @package     GamiPress\User_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get a user's gamipress achievements
 *
 * @since  1.0.0
 *
 * @param  array $args An array of all our relevant arguments
 *
 * @return array       An array of all the achievement objects that matched our parameters, or empty if none
 */
function gamipress_get_user_achievements( $args = array() ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
		return gamipress_get_user_achievements_old( $args );
	}

	// Setup our default args
	$defaults = array(
		'user_id'          => 0,     					// The given user's ID
		'site_id'          => get_current_blog_id(), 	// The given site's ID
		'achievement_id'   => false, 					// A specific achievement's post ID
		'achievement_type' => false, 					// A specific achievement type
		'since'            => 0,     					// A specific timestamp to use in place of $limit_in_days
		'limit'            => -1,    					// Limit of achievements to return
		'groupby'          => false,    				// Group by clause, setting it to 'post_id' or 'achievement_id' will prevent duplicated achievements
	);

	$args = wp_parse_args( $args, $defaults );

	// Use current user's ID if none specified
	if ( ! $args['user_id'] )
		$args['user_id'] = get_current_user_id();

	// Setup CT object
	ct_setup_table( 'gamipress_user_earnings' );

	// Setup query args
	$query_args = array(
		'user_id' 			=> $args['user_id'],
		'nopaging' 			=> true,
		'items_per_page' 	=> $args['limit'],
	);

	if( $args['achievement_id'] !== false ) {
		$query_args['post_id'] = $args['achievement_id'];
	}

	if( $args['achievement_type'] !== false ) {
		$query_args['post_type'] = $args['achievement_type'];
	}

	if( $args['groupby'] !== false ) {
		$query_args['groupby'] = $args['groupby'];

		// achievement_id is allowed
		if( $args['groupby'] === 'achievement_id' ) {
			$query_args['groupby'] = 'post_id';
		}
	}

	if( $args['since'] !== 0 ) {
		$query_args['since'] = $args['since'];
	}

	$ct_query = new CT_Query( $query_args );

	$achievements = $ct_query->get_results();

	foreach ( $achievements as $key => $achievement ) {

		// Update object for backward compatibility for usages previously to 1.2.7
		$achievement->ID = $achievement->post_id;
		$achievement->date_earned = strtotime( $achievement->date );

		$achievements[$key] = $achievement;

		if( isset( $args['display'] ) && $args['display'] ) {

			// Unset hidden achievements on display context
			if( gamipress_is_achievement_hidden( $achievement->post_id ) ) {
				unset( $achievements[$key] );
			}
		}

	}

	return $achievements;

}

/**
 * Updates the user's earned achievements
 *
 * @since  1.0.0
 *
 * @param  array $args 	An array containing all our relevant arguments
 *
 * @return bool 		The updated umeta ID on success, false on failure
 */
function gamipress_update_user_achievements( $args = array() ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
		return gamipress_update_user_achievements_old( $args );
	}

	// Setup our default args
	$defaults = array(
		'user_id'          => 0,     // The given user's ID
		'site_id'          => get_current_blog_id(), // The given site's ID
		'new_achievements' => false, // An array of NEW achievements earned by the user
	);

	$args = wp_parse_args( $args, $defaults );

	// Use current user's ID if none specified
	if ( ! $args['user_id'] )
		$args['user_id'] = get_current_user_id();

	// Lets to append the new achievements array
	if ( is_array( $args['new_achievements'] ) && ! empty( $args['new_achievements'] ) ) {

		foreach( $args['new_achievements'] as $new_achievement ) {

			$user_earning_data = array(
				'title' => gamipress_get_post_field( 'post_title', $new_achievement->ID ),
				'post_id' => $new_achievement->ID,
				'post_type' => $new_achievement->post_type,
				'points' => absint( $new_achievement->points ),
				'points_type' => $new_achievement->points_type,
				'date' => date( 'Y-m-d H:i:s', $new_achievement->date_earned )
			);

			gamipress_insert_user_earning( absint( $args['user_id'] ), $user_earning_data );

		}

	}

	return true;

}

/**
 * Display achievements for a user on their profile screen
 *
 * @since  1.0.0
 * @param  object $user The current user's $user object
 * @return void
 */
function gamipress_user_profile_data( $user = null ) {

	?>

	<hr>

	<?php // Verify user meets minimum role to manage earned achievements
	if ( current_user_can( gamipress_get_manager_capability() ) ) : ?>

		<h2><i class="dashicons dashicons-gamipress"></i> <?php _e( 'GamiPress', 'gamipress' ); ?></h2>

	<?php endif; ?>

	<?php // Output markup to user rank
	gamipress_profile_user_rank( $user );

	// Output markup to list user points
	gamipress_profile_user_points( $user );

	// Output markup to list user achievements
	gamipress_profile_user_achievements( $user );

	// Output markup for awarding achievement for user
	gamipress_profile_award_achievement( $user ); ?>

	<hr>

	<?php

}
add_action( 'show_user_profile', 'gamipress_user_profile_data' );
add_action( 'edit_user_profile', 'gamipress_user_profile_data' );


/**
 * Save extra user meta fields to the Edit Profile screen
 *
 * @since  1.0.0
 * @param  int  $user_id      User ID being saved
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
add_action( 'personal_options_update', 'gamipress_save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'gamipress_save_user_profile_fields' );

/**
 * Generate markup to show user rank
 *
 * @since  1.0.0
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function gamipress_profile_user_rank( $user = null ) {

	$rank_types = gamipress_get_rank_types();

	$can_manage = current_user_can( gamipress_get_manager_capability() );

	// Return if not rank types and user is not a manager
	if( empty( $rank_types ) && ! $can_manage ) {
		return;
	}
	?>

	<h2><?php echo $can_manage ? __( 'Ranks', 'gamipress' ) : __( 'Your Ranks', 'gamipress' ); ?></h2>

	<table class="form-table">

		<?php if( empty( $rank_types ) && $can_manage ) : ?>
			<tr>
				<th><label for="user_rank"><?php _e( 'User Ranks', 'gamipress' ); ?></label></th>
				<td>
					<span class="description">
						<?php echo sprintf( __( 'No rank types configured, visit %s to configure some rank types.', 'gamipress' ), '<a href="' . admin_url( 'edit.php?post_type=rank-type' ) . '">' . __( 'this page', 'gamipress' ) . '</a>' ); ?>
					</span>
				</td>
			</tr>
		<?php else : ?>

			<?php foreach( $rank_types as $rank_type => $data ) :

				if( $can_manage ) :
					// Show an editable form of ranks

					// Get all published ranks of this type
					$ranks = gamipress_get_ranks( array(
						'post_type' => $rank_type,
						'posts_per_page' => -1
					) );

					$user_rank_id = gamipress_get_user_rank_id( $user->ID, $rank_type ); ?>

					<tr>
						<th><label for="user_<?php echo $rank_type; ?>_rank"><?php echo sprintf( __( 'User %s', 'gamipress' ), $data['singular_name'] ); ?></label></th>
						<td>

							<?php if( empty( $ranks ) ) : ?>

								<span class="description">
									<?php echo sprintf( __( 'No %1$s configured, visit %2$s to configure some %1$s.', 'gamipress' ),
										strtolower( $data['plural_name'] ),
										'<a href="' . admin_url( 'edit.php?post_type=' . $rank_type ) . '">' . __( 'this page', 'gamipress' ) . '</a>'
									); ?>
								</span>

							<?php else : ?>

								<select name="user_<?php echo $rank_type; ?>_rank" id="user_<?php echo $rank_type; ?>_rank" style="min-width: 15em;">
									<?php foreach( $ranks as $rank ) : ?>
										<option value="<?php echo $rank->ID; ?>" <?php selected( $user_rank_id, $rank->ID ); ?>><?php echo $rank->post_title; ?></option>
									<?php endforeach; ?>
								</select>
								<span class="description"><?php echo sprintf( __( "The user's %s rank. %s listed are ordered by priority.", 'gamipress' ), strtolower( $data['singular_name'] ), $data['plural_name'] ); ?></span>

							<?php endif; ?>

						</td>
					</tr>

				<?php else :
					// Show the information of each user rank

					$user_rank = gamipress_get_user_rank( $user->ID, $rank_type ); ?>

					<tr>
						<th><label for="user_<?php echo $rank_type; ?>_rank"><?php echo $data['singular_name']; ?></label></th>
						<td>

							<?php if( $user_rank ) : ?>
								<?php echo $user_rank->post_title; ?>
							<?php endif; ?>

						</td>
					</tr>

				<?php endif; ?>

			<?php endforeach; ?>

		<?php endif; ?>

	</table>
	<?php
}

/**
 * Generate markup to list user earned points
 *
 * @since  1.0.0
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function gamipress_profile_user_points( $user = null ) {

    $points_types = gamipress_get_points_types();

	$can_manage = current_user_can( gamipress_get_manager_capability() );

	// Return if not points types and user is not a manager
	if( empty( $points_types ) && ! $can_manage ) {
		return;
	}
	?>

    <h2><?php _e( 'Points Balance', 'gamipress' ); ?></h2>

    <table class="form-table">

		<?php if( empty( $points_types ) && $can_manage ) : ?>

			<tr>
				<th><label for="user_points"><?php _e( 'User Points', 'gamipress' ); ?></label></th>
				<td>
					<span class="description">
						<?php echo sprintf( __( 'No points types configured, visit %s to configure some points types.', 'gamipress' ), '<a href="' . admin_url( 'edit.php?post_type=points-type' ) . '">' . __( 'this page', 'gamipress' ) . '</a>' ); ?>
					</span>
				</td>
			</tr>

		<?php else : ?>

			<?php if( $can_manage ) :
				// Show an editable form of points ?>

				<tr>
					<th><label for="user_points"><?php _e( 'Earned Default Points', 'gamipress' ); ?></label></th>
					<td>
						<input type="text" name="user_points" id="user_points" value="<?php echo gamipress_get_user_points( $user->ID ); ?>" class="regular-text" /><br />
						<span class="description"><?php _e( "The user's points total. Entering a new total will automatically log the change and difference between totals.", 'gamipress' ); ?></span>
					</td>
				</tr>

				<?php foreach( $points_types as $points_type => $data ) : ?>

					<tr>
						<th><label for="user_<?php echo $points_type; ?>_points"><?php echo sprintf( __( 'Earned %s', 'gamipress' ), $data['plural_name'] ); ?></label></th>
						<td>
							<input type="text" name="user_<?php echo $points_type; ?>_points" id="user_<?php echo $points_type; ?>_points" value="<?php echo gamipress_get_user_points( $user->ID, $points_type ); ?>" class="regular-text" /><br />
							<span class="description"><?php echo sprintf( __( "The user's %s total. Entering a new total will automatically log the change and difference between totals.", 'gamipress' ), strtolower( $data['plural_name'] ) ); ?></span>
						</td>
					</tr>

				<?php endforeach; ?>

			<?php else :
				// Show the information of each user points balance ?>

				<?php foreach( $points_types as $points_type => $data ) : ?>

					<tr>
						<th><label for="user_<?php echo $points_type; ?>_points"><?php echo $data['plural_name']; ?></label></th>
						<td>
							<?php echo gamipress_get_user_points( $user->ID, $points_type ); ?>
						</td>
					</tr>

				<?php endforeach; ?>

			<?php endif; ?>
		<?php endif; ?>

    </table>
	<?php
}

/**
 * Generate markup to list user earned achievements
 *
 * @since  1.0.0
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function gamipress_profile_user_achievements( $user = null ) {

	$can_manage = current_user_can( gamipress_get_manager_capability() );
	?>

    <h2><?php echo $can_manage ? __( 'User Earnings', 'gamipress' ) : __( 'Your Achievements', 'gamipress' ); ?></h2>

	<?php ct_render_ajax_list_table( 'gamipress_user_earnings',
		array(
			'user_id' => absint( $user->ID )
		),
		array(
			'views' => false,
			'search_box' => false
		)
	);
}

/**
 * Generate markup for awarding an achievement to a user
 *
 * @since  1.0.0
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function gamipress_profile_award_achievement( $user = null ) {

	$can_manage = current_user_can( gamipress_get_manager_capability() );

	// Return if user is not a manager
	if( ! $can_manage ) {
		return;
	}

	$achievements = gamipress_get_user_achievements( array( 'user_id' => absint( $user->ID ) ) );

    $achievement_ids = array_map( function( $achievement ) {
        return $achievement->ID;
    }, $achievements );

	// Grab our achievement types
	$achievement_types = gamipress_get_achievement_types();
	$rank_types = gamipress_get_rank_types();
	$requirement_types = gamipress_get_requirement_types();

    // Merge achievements and requirements (don't merge ranks)
	$achievement_types = array_merge( $achievement_types, $requirement_types );

	// On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
	if( gamipress_is_network_wide_active() && ! is_main_site() ) {
		$blog_id = get_current_blog_id();
		switch_to_blog( get_main_site_id() );
	}
	?>

	<h2><?php _e( 'Award an Achievement', 'gamipress' ); ?></h2>

	<table class="form-table">

		<tr>
			<th><label for="gamipress-award-type-select"><?php _e( 'Select an Achievement Type to Award:', 'gamipress' ); ?></label></th>
			<td>
				<select id="gamipress-award-type-select">
				<option>Choose an achievement type</option>
				<?php foreach ( $achievement_types as $achievement_slug => $achievement_type ) :
					echo '<option value="'. $achievement_slug .'">' . ucwords( $achievement_type['singular_name'] ) .'</option>';
				endforeach; ?>
				</select>
			</td>
		</tr>

	</table>

	<div id="gamipress-awards-options">
		<?php foreach ( $achievement_types as $achievement_slug => $achievement_type ) : ?>
			<table id="<?php echo esc_attr( $achievement_slug ); ?>" class="wp-list-table widefat fixed striped gamipress-table" style="display: none;">

				<thead>
					<tr>
						<th><?php echo ucwords( $achievement_type['singular_name'] ); ?></th>
						<th><?php _e( 'Actions', 'gamipress' ); ?></th>
					</tr>
				</thead>

				<tbody>
				<?php
				// Load achievement type entries
				$the_query = new WP_Query( array(
					'post_type'      	=> $achievement_slug,
					'posts_per_page' 	=> -1,
					'post_status'    	=> 'publish',
					'suppress_filters' 	=> false
				) );

				if ( $the_query->have_posts() ) : ?>

					<?php while ( $the_query->have_posts() ) : $the_query->the_post();

						// if not parent object, skip
						if( $achievement_slug === 'step' && ! $parent_achievement = gamipress_get_step_achievement( get_the_ID() ) ) {
							continue;
						} else if( $achievement_slug === 'points-award' && ! $points_type = gamipress_get_points_award_points_type( get_the_ID() ) ) {
							continue;
						} else if( $achievement_slug === 'points-deduct' && ! $points_type = gamipress_get_points_deduct_points_type( get_the_ID() ) ) {
							continue;
						} else if( $achievement_slug === 'rank-requirement' && ! $parent_rank = gamipress_get_rank_requirement_rank( get_the_ID() ) ) {
                            continue;
                        }

						// Setup our award URL
						$award_url = add_query_arg( array(
							'action'         => 'award',
							'achievement_id' => absint( get_the_ID() ),
							'user_id'        => absint( $user->ID )
						) );
						?>
						<tr>
							<td>
								<?php if( $achievement_slug === 'step' || $achievement_slug === 'points-award' || $achievement_slug === 'points-deduct' || $achievement_slug === 'rank-requirement' ) : ?>

									<?php // Output parent achievement
									if( $achievement_slug === 'step' && $parent_achievement ) : ?>

										<?php // Achievement thumbnail ?>
										<?php echo gamipress_get_achievement_post_thumbnail( $parent_achievement->ID, array( 32, 32 ) ); ?>

										<?php // Step title ?>
										<strong><?php echo gamipress_get_post_field( 'post_title', get_the_ID() ); ?></strong>

										<?php // Step relationship details ?>
										<?php echo ( isset( $achievement_types[$parent_achievement->post_type] ) ? '<br> ' . $achievement_types[$parent_achievement->post_type]['singular_name'] . ': ' : '' ); ?>
										<?php echo '<a href="' . get_edit_post_link( $parent_achievement->ID ) . '">' . gamipress_get_post_field( 'post_title', $parent_achievement->ID ) . '</a>'; ?>

									<?php elseif( in_array( $achievement_slug, array( 'points-award', 'points-deduct' ) ) && $points_type ) : ?>

										<?php // Points type thumbnail ?>
										<?php echo gamipress_get_points_type_thumbnail( $points_type->ID, array( 32, 32 ) ); ?>

										<?php // Points award/deduct title ?>
										<strong><?php echo gamipress_get_post_field( 'post_title', get_the_ID() ); ?></strong>
										<br>
										<?php echo '<a href="' . get_edit_post_link( $points_type->ID ) . '">' . gamipress_get_post_field( 'post_title', $points_type->ID ) . '</a>'; ?>

                                    <?php elseif( $achievement_slug === 'rank-requirement' && $parent_rank ) : ?>

										<?php // Rank thumbnail ?>
										<?php echo gamipress_get_rank_post_thumbnail( $parent_rank->ID, array( 32, 32 ) ); ?>

										<?php // Rank requirement title ?>
										<strong><?php echo gamipress_get_post_field( 'post_title', get_the_ID() ); ?></strong>

										<?php // Rank requirement relationship details ?>
                                        <?php echo ( isset( $rank_types[$parent_rank->post_type] ) ? '<br> ' . $rank_types[$parent_rank->post_type]['singular_name'] . ': ' : '' ); ?>
                                        <?php echo '<a href="' . get_edit_post_link( $parent_rank->ID ) . '">' . gamipress_get_post_field( 'post_title', $parent_rank->ID ) . '</a>'; ?>

									<?php endif; ?>

								<?php else : ?>

									<?php if( in_array( $achievement_slug, gamipress_get_achievement_types_slugs() ) ) : ?>
										<?php echo gamipress_get_achievement_post_thumbnail( get_the_ID(), array( 32, 32 ) ); ?>
									<?php elseif( in_array( $achievement_slug, gamipress_get_rank_types_slugs() ) ) : ?>
										<?php echo gamipress_get_rank_post_thumbnail( get_the_ID(), array( 32, 32 ) ); ?>
									<?php endif; ?>

									<strong><?php echo '<a href="' . get_edit_post_link( get_the_ID() ) . '">' . gamipress_get_post_field( 'post_title', get_the_ID() ) . '</a>'; ?></strong>
								<?php endif; ?>
							</td>
							<td>
								<a class="gamipress-award-achievement" href="<?php echo esc_url( wp_nonce_url( $award_url, 'gamipress_award_achievement' ) ); ?>"><?php printf( __( 'Award %s', 'gamipress' ), ucwords( $achievement_type['singular_name'] ) ); ?></a>
								<?php if ( in_array( get_the_ID(), (array) $achievement_ids ) ) :
									// Setup our revoke URL
									$revoke_url = add_query_arg( array(
										'action'         => 'revoke',
										'user_id'        => absint( $user->ID ),
										'achievement_id' => absint( get_the_ID() ),
									) );
									?>
									| <span class="delete"><a class="error gamipress-revoke-achievement" href="<?php echo esc_url( wp_nonce_url( $revoke_url, 'gamipress_revoke_achievement' ) ); ?>"><?php _e( 'Revoke Award', 'gamipress' ); ?></a></span>
								<?php endif; ?>

							</td>
						</tr>
					<?php endwhile; ?>

				<?php else : ?>
					<tr>
						<td colspan="3"><?php printf( __( 'No %s found.', 'gamipress' ), $achievement_type['plural_name'] ); ?></td>
					</tr>
				<?php endif; wp_reset_postdata(); ?>

				</tbody>

			</table><!-- #<?php echo esc_attr( $achievement_slug ); ?> -->
		<?php endforeach; ?>
	</div><!-- #gamipress-awards-options -->
	<?php

	// If switched to blog, return back to que current blog
	if( isset( $blog_id ) ) {
		switch_to_blog( $blog_id );
	}
}

/**
 * Process the adding/revoking of achievements on the user profile page
 *
 * @since  1.0.0
 */
function gamipress_process_user_data() {

	// verify user meets minimum role to view earned achievements
	if ( current_user_can( gamipress_get_manager_capability() ) ) {

		// Process awarding achievement to user
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'award' &&  isset( $_GET['user_id'] ) && isset( $_GET['achievement_id'] ) ) {

			// Verify our nonce
			check_admin_referer( 'gamipress_award_achievement' );

			// Award the achievement
			gamipress_award_achievement_to_user( absint( $_GET['achievement_id'] ), absint( $_GET['user_id'] ), get_current_user_id() );

			// Redirect back to the user editor
			wp_redirect( add_query_arg( 'user_id', absint( $_GET['user_id'] ), admin_url( 'user-edit.php' ) ) );
			exit();
		}

		// Process revoking achievement from a user
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'revoke' && isset( $_GET['user_id'] ) && isset( $_GET['achievement_id'] ) ) {

			// Verify our nonce
			check_admin_referer( 'gamipress_revoke_achievement' );

			$earning_id = isset( $_GET['user_earning_id'] ) ? absint( $_GET['user_earning_id'] ) : 0 ;

			// Revoke the achievement
			gamipress_revoke_achievement_to_user( absint( $_GET['achievement_id'] ), absint( $_GET['user_id'] ), $earning_id );

			// Redirect back to the user editor
			wp_redirect( add_query_arg( 'user_id', absint( $_GET['user_id'] ), admin_url( 'user-edit.php' ) ) );
			exit();

		}

	}

}
add_action( 'init', 'gamipress_process_user_data' );

/**
 * Returns array of achievement types a user has earned across a multisite network
 *
 * @since  1.0.0
 * @param  integer $user_id  The user's ID
 * @return array             An array of post types
 */
function gamipress_get_network_achievement_types_for_user( $user_id ) {
	global $blog_id;

	// Store a copy of the original ID for later
	$cached_id = $blog_id;

	// Assume we have no achievement types
	$all_achievement_types = array();

	// Loop through all active sites
	$sites = gamipress_get_network_site_ids();
	foreach( $sites as $site_blog_id ) {

		// If we're polling a different blog, switch to it
		if ( $blog_id != $site_blog_id ) {
			switch_to_blog( $site_blog_id );
		}

		// Merge earned achievements to our achievement type array
		$achievement_types = gamipress_get_user_earned_achievement_types( $user_id );

		if ( is_array( $achievement_types ) ) {
			$all_achievement_types = array_merge( $achievement_types, $all_achievement_types );
		}
	}

	if ( is_multisite() ) {
		// Restore the original blog so the sky doesn't fall
		switch_to_blog( $cached_id );
	}

	// Pare down achievement type list so we return no duplicates
	$achievement_types = array_unique( $all_achievement_types );

	// Return all found achievements
	return $achievement_types;
}
