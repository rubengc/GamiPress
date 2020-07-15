<?php
/*
Plugin Name: CMB2 Metatabs Options
Plugin URI:  https://github.com/rogerlos/cmb2-metatabs-options
Description: Add admin option pages with multiple metaboxes--and place those metaboxes into optional tabs. Requires CMB2.
Version:     1.3
Author:      Roger Los
Author URI:  https://github.com/rogerlos
Text Domain: cmb2
License:     GPLv2 or later
 */
if ( ! defined( 'WPINC' ) ) die;

require plugin_dir_path( __FILE__ ) . 'autoloader.php';

spl_autoload_register( 'rnl_autoloader' );

/**
 * Uncomment the following line if you would like to see a demo. 'example.php' will create
 * an options page, its menu link will be under "Settings".
 */
// include 'example.php';