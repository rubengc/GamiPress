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

	if ( is_array( $achievements) && ! empty( $achievements ) ) {
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

			//unset hidden achievements
			$hidden = gamipress_get_hidden_achievement_by_id( $achievement->ID );
			if( !empty( $hidden ) && isset($args['display']))
				unset($achievements[$key]);

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
            gamipress_update_users_points( $user_id, absint( $_POST['user_' . $points_type . '_points'] ), get_current_user_id(), $points_type );
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
    $points_types = gamipress_get_points_types();

    echo '<h2>' . __( 'Points Balance', 'gamipress' ) . '</h2>';

    echo '<table class="form-table">';

    echo '<tr>';
        echo '<th><label for="user_points">' . __( 'Earned Default Points', 'gamipress' ) . '</label></th>';
        echo '<td>';
            echo '<input type="text" name="user_points" id="user_points" value="' . gamipress_get_users_points( $user->ID ) . '" class="regular-text" /><br />';
            echo '<span class="description">' . __( "The user's points total. Entering a new total will automatically log the change and difference between totals.", 'gamipress' ) . '</span>';
        echo '</td>';
    echo '</tr>';

    foreach( $points_types as $points_type => $data ) {
        echo '<tr>';
            echo '<th><label for="user_' . $points_type . '_points">' . sprintf( __( 'Earned %s', 'gamipress' ), $data['plural_name'] ) . '</label></th>';
            echo '<td>';
                echo '<input type="text" name="user_' . $points_type . '_points" id="user_' . $points_type . '_points" value="' . gamipress_get_users_points( $user->ID, $points_type ) . '" class="regular-text" /><br />';
                echo '<span class="description">' . sprintf( __( "The user's %s total. Entering a new total will automatically log the change and difference between totals.", 'gamipress' ), strtolower( $data['plural_name'] ) ) . '</span>';
            echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
}

/**
 * Generate markup to list user earned achievements
 *
 * @since  1.0.0
 * @param  object $user         The current user's $user object
 * @return string               concatenated markup
 */
function gamipress_profile_user_achievements( $user = null ) {
    $achievements = gamipress_get_user_achievements( array( 'user_id' => absint( $user->ID ) ) );

    echo '<h2>' . __( 'Earned Achievements', 'gamipress' ) . '</h2>';

    // List all of a user's earned achievements
    if ( $achievements ) {
        echo '<table class="widefat gamipress-table">';
        echo '<thead><tr>';
        echo '<th>'. __( 'Image', 'gamipress' ) .'</th>';
        echo '<th>'. __( 'Name', 'gamipress' ) .'</th>';
        echo '<th>'. __( 'Action', 'gamipress' ) .'</th>';
        echo '</tr></thead>';

        foreach ( $achievements as $achievement ) {

            // Setup our revoke URL
            $revoke_url = add_query_arg( array(
                'action'         => 'revoke',
                'user_id'        => absint( $user->ID ),
                'achievement_id' => absint( $achievement->ID ),
            ) );

            echo '<tr>';
            echo '<td>'. gamipress_get_achievement_post_thumbnail( $achievement->ID, array( 50, 50 ) ) .'</td>';
            echo '<td>', edit_post_link( get_the_title( $achievement->ID ), '', '', $achievement->ID ), ' </td>';
            echo '<td> <span class="delete"><a class="error" href="'.esc_url( wp_nonce_url( $revoke_url, 'gamipress_revoke_achievement' ) ).'">' . __( 'Revoke Award', 'gamipress' ) . '</a></span></td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>' . __( 'This user has no earned any achievement', 'gamipress' ) . '</p>';
    }
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
				<?php
				foreach ( $achievement_types as $achievement_slug => $achievement_type ) {
					echo '<option value="'. $achievement_slug .'">' . ucwords( $achievement_type['single_name'] ) .'</option>';
				}
				?>
				</select>
			</td>
		</tr>
	</table>

	<div id="boxes">
		<?php foreach ( $achievement_types as $achievement_slug => $achievement_type ) : ?>
			<table id="<?php echo esc_attr( $achievement_slug ); ?>" class="widefat gamipress-table">
				<thead><tr>
					<th><?php _e( 'Image', 'gamipress' ); ?></th>
					<th><?php echo ucwords( $achievement_type['single_name'] ); ?></th>
					<th><?php _e( 'Action', 'gamipress' ); ?></th>
					<th><?php _e( 'Awarded', 'gamipress' ); ?></th>
				</tr></thead>
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
								<?php echo edit_post_link( get_the_title() ); ?>
							</td>
							<td>
								<a href="<?php echo esc_url( wp_nonce_url( $award_url, 'gamipress_award_achievement' ) ); ?>"><?php printf( __( 'Award %s', 'gamipress' ), ucwords( $achievement_type['single_name'] ) ); ?></a>
								<?php if ( in_array( get_the_ID(), (array) $achievement_ids ) ) :
									// Setup our revoke URL
									$revoke_url = add_query_arg( array(
										'action'         => 'revoke',
										'user_id'        => absint( $user->ID ),
										'achievement_id' => absint( get_the_ID() ),
									) );
									?>
									<span class="delete"><a class="error" href="<?php echo esc_url( wp_nonce_url( $revoke_url, 'gamipress_revoke_achievement' ) ); ?>"><?php _e( 'Revoke Award', 'gamipress' ); ?></a></span>
								<?php endif; ?>

							</td>
						</tr>
					<?php endwhile; ?>

				<?php else : ?>
					<tr>
						<th><?php printf( __( 'No %s found.', 'gamipress' ), $achievement_type['plural_name'] ); ?></th>
					</tr>
				<?php endif; wp_reset_postdata(); ?>

				</tbody>
			</table><!-- #<?php echo esc_attr( $achievement_slug ); ?> -->
		<?php endforeach; ?>
	</div><!-- #boxes -->

	<script type="text/javascript">
		jQuery(document).ready(function($){
			<?php foreach ( $achievement_types as $achievement_slug => $achievement_type ) { ?>
				$('#<?php echo $achievement_slug; ?>').hide();
			<?php } ?>
			$("#thechoices").change(function(){
				if ( 'all' == this.value )
					$("#boxes").children().show();
				else
					$("#" + this.value).show().siblings().hide();
			}).change();
		});
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
		if ( is_array($achievement_types) ) {
			$all_achievement_types = array_merge($achievement_types,$all_achievement_types);
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
