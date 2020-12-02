CMB2 Field Type: EDD License
==================

Custom field for [CMB2](https://github.com/WebDevStudios/CMB2) to automatically handle [EDD Software Licensing](https://easydigitaldownloads.com/downloads/software-licensing/) license activation and item updates.

## Parameters

Field accepts same parameters as field type text, with the addition of the next one:

License parameters:
- **server** (string, Required) : Server URL to the EDD SL API, for example "http://your-site.com/edd-sl-api" (by default, "")
- **file** (string, Required) : Path to the item main file (for example if you call it from your main plugin file, then you can use __FILE__)
- **item_id** (string, Optional) : Item ID from the server
- **item_name** (string, Optional) : Item name (same as returned by the function plugin_basename(), example my-plugin/my-plugin.php)
- **version** (string, Optional) : Item version of the installed one to check for updates (by default will get the one provided in the main file header)
- **author** (string, Optional) : Item author (by default will get the one provided in the main file header)
- **wp_override** (bool, Optional) : Set it to true to override WordPress plugin information to get the one provided by your server (by default, false)

Output parameters:
- **deactivate_button** (string|false, Optional) : Button to deactivate the license (if is valid), set it to false to disable it (by default, "Deactivate License")
- **clear_button** (string|false, Optional) : Button to deactivate the license (if is valid), set it to false to disable it (by default, "Deactivate License")
- **license_expiration** (bool, Optional) : Add a expiration notice with the remaining time (if is valid), set it to false to disable it (by default, true)
- **renew_license** (string|false, Optional) : Add a link  (if is valid), set it to false to disable it (by default, "Renew your license key.")
- **renew_license_link** (string, Optional) : If you set `renew_license` also you can add a link to your website to allow to your users renew this license (by default, false)
- **renew_license_timestamp** (integer|false, Optional) : If you set `renew_license` also you can set when notice to the user the license renew, set it to false to show the renew notice always (by default, DAY_IN_SECONDS * 30 (means 30 days before expiration))
- **hide_license** (bool, Optional) : Hide the license key entered (by default, true)
- **hide_license_character** (string, Optional) : Character used to hide the license key (by default, "*")
- **hide_license_visible_characters** (integer, Optional) : Number of characters to keep visible, set it to 0 to completely hide the license key (by default, 4)


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
		
		// Field specific settings
		'server'                    => 'http://your-site.com/edd-sl-api',
		'file'                      => __DIR__,
		'item_id'                   => 123,
		'item_name'                 => 'my-plugin/my-plugin.php',
		'version'                   => '1.0.0',
		'author'                    => 'rubengc',
		'wp_override'               => true,
		
		// Extra settings
		'deactivate_button'                 => __( 'Deactivate License', 'cmb2-edd-license' ),      // string|false String to set the button text, false to remove it
		'clear_button'                      => __( 'Clear License', 'cmb2-edd-license' ),      // string|false String to set the button text, false to remove it
		'license_expiration'                => true,                                                // bool         True to enable license expiration notice, false to deactivate it
		'renew_license'                     => __( 'Renew your license key.', 'cmb2-edd-license' ), // string|false String to set the renew license text, false to remove it
		'renew_license_timestamp'           => ( DAY_IN_SECONDS * 30 ),                             // int          Minimum time to show the license renewal text, by default 30 days
		
		// Links, used for license errors as a shortcut to business website
		'renew_license_link' 		        => false,                                               // string|false Link where users can renew their licenses, false to remove it
		'license_management_link' 	        => false,                                               // string|false Link where users can manage their licenses, false to remove it
		'contact_link' 				        => false,                                               // string|false Link where users can contact with your team, false to remove it
		
		// Hide license settings
		'hide_license'                      => true,                                                // bool         True to hide the license (just if license is valid), with default settings license will be displayed as: **********1234
		'hide_license_character'            => '*',                                                 // string       Character to hide the license
		'hide_license_visible_characters'   => 4,                                                   // int          Number of visible license characters
	) );

}
```

## Retrieve the license status

You can use the function `cmb2_edd_license_data( $license_key )` to see the data returned by the server of this license (is an stdClass object)
In Addition, you can use the function `cmb2_edd_license_status( $license_key )` to see the status of this license (valid, invalid or false if not license key or license not checked)

## Changelog

### 1.1.1

* Fixed a bug with the license clear and deactivate functions causing that previous value gets back after save the licenses again.

### 1.1.0

* Prevent to override WordPress sslverify on requests.
* Updated EDD SL Plugin Updater class to version 1.8.0.

### 1.0.9

* Clear field when license gets deactivated included when server responds that license has been already deactivated.

### 1.0.8

* Added the ability to clear license.

### 1.0.7

* Make hide_license to work always, indepently of the license status.

### 1.0.6

* Force update transient data on plugins page and update core.

### 1.0.5

* Updated EDD SL Plugin Updater class to version 1.6.18.

### 1.0.4

* Prevent to store hidden license value when license is valid.

### 1.0.3

* Added the ability to hide license.
* Update example with new field attributes.

### 1.0.2

* Added invalid license error checks.

### 1.0.1

* Deactivation functionality.
* Expiration notice.
* Renew notice.

### 1.0.0

* Initial release.
