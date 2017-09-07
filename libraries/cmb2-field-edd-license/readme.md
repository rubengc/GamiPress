CMB2 Field Type: EDD License
==================

Custom field for [CMB2](https://github.com/WebDevStudios/CMB2) to automatically handle [EDD Software Licensing](https://easydigitaldownloads.com/downloads/software-licensing/) license activation and item updates.

## Parameters

Field accepts same parameters as field type text, with the addition of the next one:

License fields:
- server (string, Required) : Server URL to the EDD SL API, for example "http://your-site.com/edd-sl-api" (by default, "")
- file (string, Required) : Path to the item main file (for example if you call it from your main plugin file, then you can use __FILE__)
- item_id (string, Optional) : Item ID from the server
- item_name (string, Optional) : Item name (same as returned by the function plugin_basename(), example my-plugin/my-plugin.php)
- version (string, Optional) : Item version of the installed one to check for updates (by default will get the one provided in the main file header)
- author (string, Optional) : Item author (by default will get the one provided in the main file header)
- wp_override (bool, Optional) : Set it to true to override WordPress plugin information to get the one provided by your server (by default, false)

Output fields:
- deactivate_button (string|false, Optional) : Button to deactivate the license (if is valid), set it to false to disable it (by default, "Deactivate License")
- license_expiration (bool, Optional) : Add a expiration notice with the remaining time (if is valid), set it to false to disable it (by default, true)
- renew_license (string|false, Optional) : Add a link  (if is valid), set it to false to disable it (by default, "Renew your license key.")
- renew_license_link (string, Optional) : If you set `renew_license` also you can add a link to your website to allow to your users renew this license (by default, false)
- renew_license_timestamp (integer|false, Optional) : If you set `renew_license` also you can set when notice to the user the license renew, set it to false to show the renew notice always (by default, DAY_IN_SECONDS * 30 (means 30 days before expiration))


## Examples

```php
add_action( 'cmb2_admin_init', 'cmb2_edd_license_metabox' );
function cmb2_edd_license_metabox() {

	$prefix = 'your_prefix_demo_';

	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'EDD License Sample', 'cmb2' ),
		'object_types'  => array( 'page', 'post' ), // Post type
	) );

	$cmb_demo->add_field( array(
		'name'                      => __( 'License', 'cmb2' ),
		'desc'                      => __( 'Field description (optional)', 'cmb2' ),
		'id'                        => $prefix . 'license',
		'type'                      => 'edd_license',
		'server'                    => 'http://your-site.com/edd-sl-api',
        'file'                      => __DIR__,
        'item_id'                   => 123,
        'item_name'                 => 'my-plugin/my-plugin.php',
        'version'                   => '1.0.0',
        'author'                    => 'rubengc',
        'wp_override'               => true,
        //'deactivate_button'         => false,
        //'license_expiration'        => false,
        //'renew_license'             => false,
        'renew_license_link'        => 'http://your-site.com/renew-your-license',
        'renew_license_timestamp'   => false, // Setting it to false will show the notice always (if license is valid)
	) );

}
```

## Retrieve the license status

You can use the function `cmb2_edd_license_data( $license_key )` to see the data returned by the server of this license (is an stdClass object)
In Addition, you can use the function `cmb2_edd_license_status( $license_key )` to see the status of this license (valid, invalid or false if not license key or license not checked)

## Changelog

### 1.0.1
* Deactivation functionality
* Expiration notice
* Renew notice

### 1.0.0
* Initial release
