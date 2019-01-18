<?php
/**
 * Rest API
 *
 * @package     GamiPress\Rest_API
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.6.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR . 'includes/api/class-wp-rest-gamipress-posts-controller.php';

/**
 * Register new Rest API routes
 *
 * @since 1.0.0
 */
function gamipress_rest_api_init() {

    // Register the /wp/v2/gamipress-posts endpoint (used on blocks to get a multiple post types endpoint)
    $controller = new WP_REST_GamiPress_Posts_Controller();
    $controller->register_routes();

}
add_action('rest_api_init', 'gamipress_rest_api_init');