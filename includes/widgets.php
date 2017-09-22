<?php
/**
 * Widgets
 *
 * @package     GamiPress\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR .'includes/widgets/widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/achievement-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/achievements-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/logs-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/points-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/points-types-widget.php';

// Register GamiPress widgets
function gamipress_register_widgets() {

	register_widget( 'gamipress_achievement_widget' );
	register_widget( 'gamipress_achievements_widget' );
	register_widget( 'gamipress_logs_widget' );
	register_widget( 'gamipress_points_widget' );
	register_widget( 'gamipress_points_types_widget' );

}
add_action( 'widgets_init', 'gamipress_register_widgets' );
