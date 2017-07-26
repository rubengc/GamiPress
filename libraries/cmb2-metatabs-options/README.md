# CMB2 Metatabs Options

Contributors: @rogerlos, @rubengc

Tags: cmb2, metaboxes, forms, fields, options, settings, tabs, cmo

Requires at least: 3.8.0

Tested up to: 4.8

Stable tag: 1.3

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html


Extends CMB2--create WordPress options pages with multiple metaboxes,
support for tabs, and flexible menu locations.

# Description

CMB2 Metatabs Options (CMO) is a plugin (or class) for developers using CMB2 to manage metaboxes and fields.
CMO makes it easy to create options pages with multiple metaboxes--and optional WordPress admin tabs.
You can attach your option page(s) to any existing Wordpress menu or add them as a new
top-level menu. You can also add multiple options pages!

This plugin requires the [CMB2 Plugin](http://wordpress.org/plugins/cmb2/), or your project
must already utilize the [CMB2](https://github.com/WebDevStudios/CMB2) library. CMB2 is *not* included.

Please see the wiki at CMO's github repository for a
[detailed user's guide](https://github.com/rogerlos/cmb2-metatabs-options/wiki).

Thanks to the folks maintaining CMB2 for their continued development, and providing the
starting point for this plugin.

# Installation (as WP plugin)

Download the plugin zip file and add via Plugins->Add New->Upload. Or FTP the unzipped plugin folder to
your wp_content/plugins directory. Activate the plugin within WP admin.

Note this plugin does nothing by default other than give you access to the Cmb2_Metatabs_Options() class.

You can see an example of what this plugin does by using the WP plugin editor and uncommenting the line
in the main plugin file which reads "include 'example.php';".

# Installation (as stand-alone class)

Copy the files within the code directory to your project and include the class file in your code. If you
change the location of the JS file, you must inject 'jsuri' with its new URL into the class when creating 
an options page.

# Changelog

= 1.3 =
* Added "Reset Options" button. Thanks @rubengc

= 1.2 =
* Added test to see if autoloader has already been loaded. Thanks: @rubengc
* Added menu argument 'view_capability' to set page viewing capability. Idea: @Julianoe
* Added argument 'plugincss': disables plugin css, still allows custom CSS via 'admincss'. Idea: @jquimera
* Added wp box nonces to options page form. Bug report:  @Jekyll4k, @Kaleidosko

= 1.1.2 =
* Changed way empty string was passed in before- and after-form filters, now supports cumulative filtering

= 1.1.1 =
* Added 'admincss' parameter to turn off (false) or inject your own css (string) into admin page
* Added 'class' to allow class(es) to be added to the WordPress admin page wrapper
* Code cleanup to WordPress standards

= 1.1.0 =
* Oops: Class now works when adding multiple options pages
* New: add options page(s) to multisite network menus
* New: argument parameters to turn off calls to CMB2_Box::get_all() and make registering the option optional
* New: page load actions can now be passed into the contructor
* Improved: 'boxes' array may now contain either/mixed CMB2 box objects / CMB2 box ids
* Improved: Less reliance on options key to trigger internal events
* Improved: Tighter checks for when to load plugin JS and CSS
* Improved: before and after filters now pass page id as second argument (useful for multiple pages)
* Bug fix: Submenu page added with same slug as parent works as WP gods intended
* Bug fix: Wrong parameter passed to localized JS fixed
* Note: Extensive use of closures requires PHP >= 5.3

= 1.0.3 =
* Added composer.json -- thanks misfist 
* Changed should_save() method to static -- thanks chrisgherbert

= 1.0.2 =
* Revised the menu building method and injected properties to be clearer and more dependable -- thanks ajuliano
* Added multidimensional argument parsing method

= 1.0.1 =
* code refactoring and comment revisions

= 1.0.0 =
* Initial release.
