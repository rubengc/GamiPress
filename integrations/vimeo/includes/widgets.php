<?php
/**
 * Widgets
 *
 * @package GamiPress\Vimeo\Widgets
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Vimeo Widgets
require_once GAMIPRESS_VIMEO_DIR . 'includes/widgets/vimeo-widget.php';

// Register widgets
function gamipress_vimeo_register_widgets() {

    register_widget( 'gamipress_vimeo_widget' );

}
add_action( 'widgets_init', 'gamipress_vimeo_register_widgets' );