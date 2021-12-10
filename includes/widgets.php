<?php
/**
 * Widgets
 *
 * @package     GamiPress\Widgets
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR .'includes/widgets/widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/achievement-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/achievements-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/last-achievements-earned-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/earnings-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/logs-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/user-points-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/site-points-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/points-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/points-types-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/rank-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/ranks-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/user-rank-widget.php';
require_once GAMIPRESS_DIR .'includes/widgets/email-settings-widget.php';

// Register GamiPress widgets
function gamipress_register_widgets() {

	register_widget( 'gamipress_achievement_widget' );
	register_widget( 'gamipress_achievements_widget' );
	register_widget( 'gamipress_last_achievements_earned_widget' );
	register_widget( 'gamipress_earnings_widget' );
	register_widget( 'gamipress_logs_widget' );
	register_widget( 'gamipress_user_points_widget' );
	register_widget( 'gamipress_site_points_widget' );
	register_widget( 'gamipress_points_widget' );
	register_widget( 'gamipress_points_types_widget' );
	register_widget( 'gamipress_rank_widget' );
	register_widget( 'gamipress_ranks_widget' );
	register_widget( 'gamipress_user_rank_widget' );
	register_widget( 'gamipress_email_settings_widget' );

}
add_action( 'widgets_init', 'gamipress_register_widgets' );
