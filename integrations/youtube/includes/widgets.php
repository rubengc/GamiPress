<?php
/**
 * Widgets
 *
 * @package GamiPress\Youtube\Widgets
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Youtube Widgets
require_once GAMIPRESS_YOUTUBE_DIR . 'includes/widgets/youtube-widget.php';

// Register widgets
function gamipress_youtube_register_widgets() {

    register_widget( 'gamipress_youtube_widget' );

}
add_action( 'widgets_init', 'gamipress_youtube_register_widgets' );