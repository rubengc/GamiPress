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
 * @param  array $args An array of all our relevant arguments
 * @return array       An array of all the achievement objects that matched our parameters, or empty if none
 */
function gamipress_get_user_achievements( $args = array() ) {

	// Setup our default args
	$defaults = array(
		'user_id'          => 0,     // The given user's ID
		'site_id'          => get_current_blog_id(), // The given site's ID
		'achievement_id'   => false, // A specific achievement's post ID
		'achievement_type' => false, // A specific achievement type
		'since'            => 0,     // A specific timestamp to use in place of $limit_in_days
	);
	$args = wp_parse_args( $args, $defaults );

	// Use current user's ID if none specified
	if ( ! $args['user_id'] )
		$args['user_id'] = get_current_user_id();

	// Grab the user's current achievements
	$achievements = ( $earned_items = get_user_meta( absint( $args['user_id'] ), '_gamipress_achievements', true ) ) ? (array) $earned_items : array();

	// If we want all sites (or no specific site), return the full array
	if ( empty( $achievements ) || empty( $args['site_id']) || 'all' == $args['site_id'] )
		return $achievements;

	// Otherwise, we only want the specific site's achievements
	$achievements = $achievements[$args['site_id']];

	if ( is_array( $achievements ) && ! empty( $achievements ) ) {
		foreach ( $achievements as $key => $achievement ) {

			// Drop any achievements earned before our since timestamp
			if ( absint($args['since']) > $achievement->date_earned )
				unset($achievements[$key]);

			// Drop any achievements that don't match our achievement ID
			if ( ! empty( $args['achievement_id'] ) && absint( $args['achievement_id'] ) != $achievement->ID )
				unset($achievements[$key]);

			// Drop any achievements that don't match our achievement type
			if ( ! empty( $args['achievement_type'] ) && ( $args['achievement_type'] != $achievement->post_type && ( !is_array( $args['achievement_type'] ) || !in_array( $achievement->post_type, $args['achievement_type'] ) ) ) )
				unset($achievements[$key]);

			if( isset( $args['display'] ) && $args['display'] ) {
				// Unset hidden achievements on display context
				$hidden = gamipress_get_hidden_achievement_by_id( $achievement->ID );

				if( ! empty( $hidden ) ) {
					unset( $achievements[$key] );
				}
			}
		}
	}

	// Return our $achievements array_values (so our array keys start back at 0), or an empty array
	return ( is_array( $achievements ) ? array_values( $achievements ) : array());

}

/**
 * Updates the user's earned achievements
 *
 * We can either replace the achievement's array, or append new achievements to it.
 *
 * @since  1.0.0
 * @param  array        $args An array containing all our relevant arguments
 * @return integer|bool       The updated umeta ID on success, false on failure
 */
function gamipress_update_user_achievements( $args = array() ) {

	// Setup our default args
	$defaults = array(
		'user_id'          => 0,     // The given user's ID
		'site_id'          => get_current_blog_id(), // The given site's ID
		'all_achievements' => false, // An array of ALL achievements earned by the user
		'new_achievements' => false, // An array of NEW achievements earned by the user
	);
	$args = wp_parse_args( $args, $defaults );

	// Use current user's ID if none specified
	if ( ! $args['user_id'] )
		$args['user_id'] = wp_get_current_user()->ID;

	// Grab our user's achievements
	$achievements = gamipress_get_user_achievements( array( 'user_id' => absint( $args['user_id'] ), 'site_id' => 'all' ) );

	// If we don't already have an array stored for this site, create a fresh one
	if ( !isset( $achievements[$args['site_id']] ) )
		$achievements[$args['site_id']] = array();

	// Determine if we should be replacing or appending to our achievements array
	if ( is_array( $args['all_achievements'] ) )
		$achievements[$args['site_id']] = $args['all_achievements'];
	elseif ( is_array( $args['new_achievements'] ) && ! empty( $args['new_achievements'] ) )
		$achievements[$args['site_id']] = array_merge( $achievements[$args['site_id']], $args['new_achievements'] );

	// Finally, update our user meta
	return update_user_meta( absint( $args['user_id'] ), '_gamipress_achievements', $achievements);

}

/**
 * Display achievements for a user on their profile screen
 *
 * @since  1.0.0
 * @param  object $user The current user's $user object
 * @return void
 */
function gamipress_user_profile_data( $user = null ) {
	// Verify user meets minimum role to view earned badges
	if ( current_user_can( gamipress_get_manager_capability() ) ) {

        // Output markup to list user points
		gamipress_profile_user_points( $user );

        // Output markup to list user achivements
		gamipress_profile_user_achievements( $user );

		// Output markup for awarding achievement for user
		gamipress_profile_award_achievement( $user );

	}

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

	// Update our user's points total, but only if edited
	if ( isset( $_POST['user_points'] ) && $_POST['user_points'] != gamipress_get_users_points( $user_id ) ) {
		gamipress_update_users_points( $user_id, absint( $_POST['user_points'] ), get_current_user_id() );
	}

    $points_types = gamipress_get_points_types();

    foreach( $points_types as $points_type => $data ) {
        // Update each user's points type total, but only if edited
        if ( isset( $_POST['user_' . $points_type . '_points'] ) && $_POST['user_' . $points_type . '_points'] != gamipress_get_users_points( $user_id, $points_type ) ) {
            gamipress_update_users_points( $user_id, absint( $_POST['user_' . $points_type . '_points'] ), get_current_user_id(), null, $points_type );
        }
    }
}
add_action( 'personal_options_update', 'gamipress_save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'gamipress_save_user_profile_fields' );

/**
 * Generate markup to list user earned points
 *
 * @since  1.0.0
 * @param  object $user         The current user's $user object
 * @return string               concatenated markup
 */
function gamipress_profile_user_points( $user = null ) {

    $points_types = gamipress_get_points_types(); ?>

    <h2><?php _e( 'Points Balance', 'gamipress' ); ?></h2>

    <table class="form-table">

    <tr>
        <th><label for="user_points"><?php _e( 'Earned Default Points', 'gamipress' ); ?></label></th>
        <td>
            <input type="text" name="user_points" id="user_points" value="<?php echo gamipress_get_users_points( $user->ID ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e( "The user's points total. Entering a new total will automatically log the change and difference between totals.", 'gamipress' ); ?></span>
        </td>
    </tr>

    <?php foreach( $points_types as $points_type => $data ) : ?>
        <tr>
            <th><label for="user_<?php echo $points_type; ?>_points"><?php echo sprintf( __( 'Earned %s', 'gamipress' ), $data['plural_name'] ); ?></label></th>
            <td>
                <input type="text" name="user_<?php echo $points_type; ?>_points" id="user_<?php echo $points_type; ?>_points" value="<?php echo gamipress_get_users_points( $user->ID, $points_type ); ?>" class="regular-text" /><br />
                <span class="description"><?php echo sprintf( __( "The user's %s total. Entering a new total will automatically log the change and difference between totals.", 'gamipress' ), strtolower( $data['plural_name'] ) ); ?></span>
            </td>
        </tr>
	<?php endforeach; ?>

    </table>
	<?php
}

/**
 * Generate markup to list user earned achievements
 *
 * @since  1.0.0
 * @param  object $user         The current user's $user object
 * @return string               concatenated markup
 */
function gamipress_profile_user_achievements( $user = null ) {
	$achievement_types = gamipress_get_achievement_types();
    $achievements = gamipress_get_user_achievements( array( 'user_id' => absint( $user->ID ) ) ); ?>

    <h2><?php _e( 'Earned Achievements', 'gamipress' ); ?></h2>

	<?php // List all of a user's earned achievements
    if ( $achievements ) : ?>
        <table class="wp-list-table widefat fixed striped gamipress-table">
			<thead>
				<tr>
					<th width="60px"><?php _e( 'Image', 'gamipress' ); ?></th>
					<th><?php _e( 'Name', 'gamipress' ); ?></th>
					<th><?php _e( 'Date', 'gamipress' ); ?></th>
					<th><?php _e( 'Action', 'gamipress' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $achievements as $achievement ) :

					// Setup our revoke URL
					$revoke_url = add_query_arg( array(
						'action'         => 'revoke',
						'user_id'        => absint( $user->ID ),
						'achievement_id' => absint( $achievement->ID ),
					) ); ?>

					<tr>
						<td>
							<?php echo gamipress_get_achievement_post_thumbnail( $achievement->ID, array( 50, 50 ) ); ?>
						</td>
						<td>
							<?php if( $achievement->post_type === 'step' || $achievement->post_type === 'points-award' ) : ?>
								<strong><?php echo get_the_title( $achievement->ID ); ?></strong>
								<?php echo ( isset( $achievement_types[$achievement->post_type] ) ? '<br>' . $achievement_types[$achievement->post_type]['singular_name'] : '' ); ?>

								<?php // Output parent achievement
								if( $parent_achievement = gamipress_get_parent_of_achievement( $achievement->ID ) ) : ?>
									<?php echo ( isset( $achievement_types[$parent_achievement->post_type] ) ? ', ' . $achievement_types[$parent_achievement->post_type]['singular_name'] . ': ' : '' ); ?>
									<?php echo '<a href="' . get_edit_post_link( $parent_achievement->ID ) . '">' . get_the_title( $parent_achievement->ID ) . '</a>'; ?>
								<?php elseif( $points_type = gamipress_get_points_award_points_type( $achievement->ID ) ) : ?>
									<?php echo ', <a href="' . get_edit_post_link( $points_type->ID ) . '">' . get_the_title( $points_type->ID ) . '</a>'; ?>
								<?php endif; ?>
							<?php else : ?>
								<strong><?php echo '<a href="' . get_edit_post_link( $achievement->ID ) . '">' . get_the_title( $achievement->ID ) . '</a>'; ?></strong>
								<?php echo ( isset( $achievement_types[$achievement->post_type] ) ? '<br>' . $achievement_types[$achievement->post_type]['singular_name'] : '' ); ?>
							<?php endif; ?>
						</td>
						<td>
							<abbr title="<?php echo date( 'Y/m/d g:i:s a', $achievement->date_earned ); ?>"><?php echo date( 'Y/m/d', $achievement->date_earned ); ?></abbr>
						</td>
						<td>
							<span class="delete"><a class="error" href="<?php echo esc_url( wp_nonce_url( $revoke_url, 'gamipress_revoke_achievement' ) ); ?>"><?php _e( 'Revoke Award', 'gamipress' ); ?></a></span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
        </table>
	<?php else : ?>
        <p><?php _e( 'This user has no earned any achievement', 'gamipress' ); ?></p>
	<?php endif;
}

/**
 * Generate markup for awarding an achievement to a user
 *
 * @since  1.0.0
 * @param  object $user         The current user's $user object
 * @param  array  $achievements array of user-earned achievement IDs
 * @return string               concatenated markup
 */
function gamipress_profile_award_achievement( $user = null ) {
	$achievement_types = gamipress_get_achievement_types();
	$achievements = gamipress_get_user_achievements( array( 'user_id' => absint( $user->ID ) ) );
    $achievement_ids = array_map( function( $achievement ) {
        return $achievement->ID;
    }, $achievements );

	// Grab our achivement types
	$achievement_types = gamipress_get_achievement_types();
	?>

	<h2><?php _e( 'Award an Achievement', 'gamipress' ); ?></h2>

	<table class="form-table">
		<tr>
			<th><label for="thechoices"><?php _e( 'Select an Achievement Type to Award:', 'gamipress' ); ?></label></th>
			<td>
				<select id="thechoices">
				<option>Choose an achievement type</option>
				<?php foreach ( $achievement_types as $achievement_slug => $achievement_type ) :
					echo '<option value="'. $achievement_slug .'">' . ucwords( $achievement_type['singular_name'] ) .'</option>';
				endforeach; ?>
				</select>
			</td>
		</tr>
	</table>

	<div id="boxes">
		<?php foreach ( $achievement_types as $achievement_slug => $achievement_type ) : ?>
			<table id="<?php echo esc_attr( $achievement_slug ); ?>" class="wp-list-table widefat fixed striped gamipress-table">

				<thead>
					<tr>
						<th width="60px"><?php _e( 'Image', 'gamipress' ); ?></th>
						<th><?php echo ucwords( $achievement_type['singular_name'] ); ?></th>
						<th><?php _e( 'Actions', 'gamipress' ); ?></th>
					</tr>
				</thead>

				<tbody>
				<?php
				// Load achievement type entries
				$the_query = new WP_Query( array(
					'post_type'      => $achievement_slug,
					'posts_per_page' => '999',
					'post_status'    => 'publish'
				) );

				if ( $the_query->have_posts() ) : ?>

					<?php while ( $the_query->have_posts() ) : $the_query->the_post();

						// if not parent object, skip
						if( $achievement_slug === 'step' && ! $parent_achievement = gamipress_get_parent_of_achievement( get_the_ID() ) ) {
							continue;
						} else if( $achievement_slug === 'points-award' && ! $points_type = gamipress_get_points_award_points_type( get_the_ID() ) ) {
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
							<td><?php the_post_thumbnail( array( 50, 50 ) ); ?></td>
							<td>
								<?php if( $achievement_slug === 'step' || $achievement_slug === 'points-award' ) : ?>
									<strong><?php echo get_the_title( get_the_ID() ); ?></strong>
									<?php // Output parent achievement
									if( $achievement_slug === 'step' && $parent_achievement ) : ?>
										<?php echo ( isset( $achievement_types[$parent_achievement->post_type] ) ? '<br> ' . $achievement_types[$parent_achievement->post_type]['singular_name'] . ': ' : '' ); ?>
										<?php echo '<a href="' . get_edit_post_link( $parent_achievement->ID ) . '">' . get_the_title( $parent_achievement->ID ) . '</a>'; ?>
									<?php elseif( $points_type ) : ?>
										<br>
										<?php echo '<a href="' . get_edit_post_link( $points_type->ID ) . '">' . get_the_title( $points_type->ID ) . '</a>'; ?>
									<?php endif; ?>
								<?php else : ?>
									<strong><?php echo '<a href="' . get_edit_post_link( get_the_ID() ) . '">' . get_the_title( get_the_ID() ) . '</a>'; ?></strong>
								<?php endif; ?>
							</td>
							<td>
								<a href="<?php echo esc_url( wp_nonce_url( $award_url, 'gamipress_award_achievement' ) ); ?>"><?php printf( __( 'Award %s', 'gamipress' ), ucwords( $achievement_type['singular_name'] ) ); ?></a>
								<?php if ( in_array( get_the_ID(), (array) $achievement_ids ) ) :
									// Setup our revoke URL
									$revoke_url = add_query_arg( array(
										'action'         => 'revoke',
										'user_id'        => absint( $user->ID ),
										'achievement_id' => absint( get_the_ID() ),
									) );
									?>
									| <span class="delete"><a class="error" href="<?php echo esc_url( wp_nonce_url( $revoke_url, 'gamipress_revoke_achievement' ) ); ?>"><?php _e( 'Revoke Award', 'gamipress' ); ?></a></span>
								<?php endif; ?>

							</td>
						</tr>
					<?php endwhile; ?>

				<?php else : ?>
					<tr>
						<td><?php printf( __( 'No %s found.', 'gamipress' ), $achievement_type['plural_name'] ); ?></td>
					</tr>
				<?php endif; wp_reset_postdata(); ?>

				</tbody>

			</table><!-- #<?php echo esc_attr( $achievement_slug ); ?> -->
		<?php endforeach; ?>
	</div><!-- #boxes -->

	<script type="text/javascript">
		(function($){
			<?php foreach ( $achievement_types as $achievement_slug => $achievement_type ) { ?>
				$('#<?php echo $achievement_slug; ?>').hide();
			<?php } ?>
			$("#thechoices").change(function(){
				if ( 'all' == this.value )
					$("#boxes").children().show();
				else
					$("#" + this.value).show().siblings().hide();
			}).change();
		})(jQuery);
	</script>
	<?php
}

/**
 * Process the adding/revoking of achievements on the user profile page
 *
 * @since  1.0.0
 * @return void
 */
function gamipress_process_user_data() {

	//verify uesr meets minimum role to view earned badges
	if ( current_user_can( gamipress_get_manager_capability() ) ) {

		// Process awarding achievement to user
		if ( isset( $_GET['action'] ) && 'award' == $_GET['action'] &&  isset( $_GET['user_id'] ) && isset( $_GET['achievement_id'] ) ) {

			// Verify our nonce
			check_admin_referer( 'gamipress_award_achievement' );

			// Award the achievement
			gamipress_award_achievement_to_user( absint( $_GET['achievement_id'] ), absint( $_GET['user_id'] ), get_current_user_id() );

			// Redirect back to the user editor
			wp_redirect( add_query_arg( 'user_id', absint( $_GET['user_id'] ), admin_url( 'user-edit.php' ) ) );
			exit();
		}

		// Process revoking achievement from a user
		if ( isset( $_GET['action'] ) && 'revoke' == $_GET['action'] && isset( $_GET['user_id'] ) && isset( $_GET['achievement_id'] ) ) {

			// Verify our nonce
			check_admin_referer( 'gamipress_revoke_achievement' );

			// Revoke the achievement
			gamipress_revoke_achievement_from_user( absint( $_GET['achievement_id'] ), absint( $_GET['user_id'] ) );

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
