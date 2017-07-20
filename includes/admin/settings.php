<?php
/**
 * Admin Settings Pages
 *
 * @package     GamiPress\Admin\Settings
 * @since       1.0.0
 */

/**
 * Register GamiPress Settings with Settings API.
 * @return void
 */
function gamipress_register_settings() {
	register_setting( 'gamipress_settings_group', 'gamipress_settings', 'gamipress_settings_validate' );
}
add_action( 'admin_init', 'gamipress_register_settings' );

/**
 * Grant GamiPress manager role ability to edit GamiPress settings.
 *
 * @since  1.0.0
 *
 * @param  string $capability Required capability.
 * @return string             Required capability.
 */
function gamipress_edit_settings_capability( $capability ) {
	return gamipress_get_manager_capability();
}
add_filter( 'option_page_capability_gamipress_settings_group', 'gamipress_edit_settings_capability' );

/**
 * GamiPress Settings validation
 *
 * @param  string $input The input we want to validate
 * @return string        Our sanitized input
 */
function gamipress_settings_validate( $input = '' ) {

	// Fetch existing settings
	$original_settings = get_option( 'gamipress_settings' );

	// Sanitize the settings data submitted
	$input['minimum_role'] = isset( $input['minimum_role'] ) ? sanitize_text_field( $input['minimum_role'] ) : $original_settings['minimum_role'];
	$input['ms_show_all_achievements'] = isset( $input['ms_show_all_achievements'] ) ? sanitize_text_field( $input['ms_show_all_achievements'] ) : $original_settings['ms_show_all_achievements'];

	// Allow add-on settings to be sanitized
	do_action( 'gamipress_settings_validate', $input, $original_settings );

	// Return sanitized inputs
	return $input;

}

/**
 * GamiPress main settings page output
 * @since  1.0.0
 * @return void
 */
function gamipress_settings_page() {
	?>
	<div class="wrap" >
		<h2><?php _e( 'GamiPress Settings', 'gamipress' ); ?></h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'gamipress_settings_group' ); ?>
			<?php $gamipress_settings = get_option( 'gamipress_settings' ); ?>
			<?php
			//load settings
			$minimum_role = ( isset( $gamipress_settings['minimum_role'] ) ) ? $gamipress_settings['minimum_role'] : 'manage_options';
			$ms_show_all_achievements = ( isset( $gamipress_settings['ms_show_all_achievements'] ) ) ? $gamipress_settings['ms_show_all_achievements'] : 'disabled';

			wp_nonce_field( 'gamipress_settings_nonce', 'gamipress_settings_nonce' );
			?>
			<table class="form-table">
				<?php if ( current_user_can( 'manage_options' ) ) { ?>
					<tr valign="top"><th scope="row"><label for="minimum_role"><?php _e( 'Minimum Role to Administer GamiPress: ', 'gamipress' ); ?></label></th>
						<td>
							<select id="minimum_role" name="gamipress_settings[minimum_role]">
								<option value="manage_options" <?php selected( $minimum_role, 'manage_options' ); ?>><?php _e( 'Administrator', 'gamipress' ); ?></option>
								<option value="delete_others_posts" <?php selected( $minimum_role, 'delete_others_posts' ); ?>><?php _e( 'Editor', 'gamipress' ); ?></option>
								<option value="publish_posts" <?php selected( $minimum_role, 'publish_posts' ); ?>><?php _e( 'Author', 'gamipress' ); ?></option>
							</select>
						</td>
					</tr>
				<?php } /* endif current_user_can( 'manage_options' ); */ ?>
				<?php
				// check if multisite is enabled & if plugin is network activated
				if ( is_super_admin() ){
					if ( is_multisite() ) {
					?>
						<tr valign="top"><th scope="row"><label for="ms_show_all_achievements"><?php _e( 'Show achievements earned across all sites on the network:', 'gamipress' ); ?></label></th>
							<td>
								<select id="ms_show_all_achievements" name="gamipress_settings[ms_show_all_achievements]">
									<option value="disabled" <?php selected( $ms_show_all_achievements, 'disabled' ); ?>><?php _e( 'Disabled', 'gamipress' ) ?></option>
									<option value="enabled" <?php selected( $ms_show_all_achievements, 'enabled' ); ?>><?php _e( 'Enabled', 'gamipress' ) ?></option>
								</select>
							</td>
						</tr>
					<?php
					}
				} ?>
			</table>

			<?php do_action( 'gamipress_settings', $gamipress_settings ); ?>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Settings', 'gamipress' ); ?>" />
			</p>
			<!-- TODO: Add settings to select WP page for archives of each achievement type.
				See BuddyPress' implementation of this idea.  -->
		</form>
	</div>
	<?php
}

/**
 * Get capability required for GamiPress administration.
 *
 * @since  1.0.0
 *
 * @return string User capability.
 */
function gamipress_get_manager_capability() {

	$gamipress_settings = get_option( 'gamipress_settings' );

	return isset( $gamipress_settings[ 'minimum_role' ] ) ? $gamipress_settings[ 'minimum_role' ] : 'manage_options';

}
