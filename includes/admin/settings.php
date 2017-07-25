<?php
/**
 * Admin Settings Pages
 *
 * @package     GamiPress\Admin\Settings
 * @since       1.0.0
 */

/**
 * Register GamiPress Settings with Settings API.
 *
 * @return void
 */
function gamipress_register_settings() {
	register_setting( 'gamipress_settings', 'gamipress_settings' );
}
add_action( 'admin_init', 'gamipress_register_settings' );

/**
 * Helper function to get an option value.
 *
 * @since  1.0.1
 *
 * @return mixed Option value or default parameter value if not exists.
 */
function gamipress_get_option( $option_name, $default = false ) {

    $gamipress_settings = get_option( 'gamipress_settings' );

    return isset( $gamipress_settings[ $option_name ] ) ? $gamipress_settings[ $option_name ] : $default;

}

/**
 * GamiPress registered settings
 *
 * @since  1.0.1
 *
 * @return array
 */
function gamipress_get_settings() {

    $gamipress_settings = array(
        'minimum_role' => array(
            'name' => __( 'Minimum role to administer GamiPress', 'gamipress' ),
            'description' => '',
            'type' => 'select',
            'options' => array(
                'manage_options' => __( 'Administrator', 'gamipress' ),
                'delete_others_posts' => __( 'Editor', 'gamipress' ),
                'publish_posts' => __( 'Author', 'gamipress' ),
            ),
        ),
        'achievement_image_size' => array(
            'name' => __( 'Achievment Image Size', 'gamipress' ),
            'description' => '',
            'type' => 'size',
        ),
        'disable_css' => array(
            'name' => __( 'Disable frontend CSS', 'gamipress' ),
            'description' => '',
            'type' => 'checkbox',
            'classes' => 'gamipress-switch',
        ),
    );

    if( is_multisite() ) {
        $gamipress_settings['ms_show_all_achievements'] = array(
            'name' => __( 'Show achievements earned across all sites on the network', 'gamipress' ),
            'description' => '',
            'type' => 'checkbox',
            'classes' => 'gamipress-switch',
        );
    }

    return apply_filters( 'gamipress_settings', $gamipress_settings );

}

/**
 * Register GamiPress Settings CMB2 Form.
 *
 * @since  1.0.1
 *
 * @return void
 */
function gamipress_register_settings_form() {

    $fields = array();

    // Parse GamiPress settings
    foreach( gamipress_get_settings() as $field_id => $field ) {
        $field['id'] = $field_id;

        $fields[] = $field;
    }

	new_cmb2_box( array(
		'id'         => 'gamipress_settings_box',
		'hookup'     => false,
		'cmb_styles' => false,
		'show_on'    => array(
			'key'   => 'options-page',
			'value' => array( 'gamipress_settings' )
		),
		'fields' => $fields
	) );
}
add_action( 'cmb2_admin_init', 'gamipress_register_settings_form' );

/**
 * Settings page notices.
 *
 * @since  1.0.1
 *
 * @return void
 */
function gamipress_settings_notices( $object_id, $updated ) {

	if ( $object_id !== 'gamipress_settings' || empty( $updated ) ) {
		return;
	}

	add_settings_error( 'gamipress_settings_notices', '', __( 'Settings updated successfully.', 'gamipress' ), 'updated' );

	settings_errors( 'gamipress_settings_notices' );
}
add_action( 'cmb2_save_options-page_fields_gamipress_settings_box', 'gamipress_settings_notices', 10, 2 );

/**
 * GamiPress main settings page output
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_settings_page() {
	?>
	<div class="wrap" >

		<h2><?php _e( 'GamiPress Settings', 'gamipress' ); ?></h2>

		<?php cmb2_metabox_form(
            'gamipress_settings_box',
            'gamipress_settings',
            array(
                'save_button' => __( 'Save Settings', 'gamipress' )
            )
        ); ?>

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

    return gamipress_get_option( 'minimum_role', 'manage_options' );

}
