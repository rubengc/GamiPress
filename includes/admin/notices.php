<?php
/**
 * Admin Notices
 *
 * @package     GamiPress\Admin\Notices
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.5.9
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * GamiPress admin notices
 *
 * @since 1.5.9
 */
function gamipress_admin_notices() {

    // Bail if current user is not a site administrator
    if( ! current_user_can( 'update_plugins' ) ){
        return;
    }

    // Check if user checked already hide the review notice
    if( gamipress_is_network_wide_active() ) {
        $hide_review_notice = ( $exists = get_site_option( 'gamipress_hide_review_notice' ) ) ? $exists : '';
    } else {
        $hide_review_notice = ( $exists = get_option( 'gamipress_hide_review_notice' ) ) ? $exists : '';
    }

    if( $hide_review_notice !== 'yes' ) {

        // Get the GamiPress installation date
        if( gamipress_is_network_wide_active() ) {
            $gamipress_install_date = ( $exists = get_site_option( 'gamipress_install_date' ) ) ? $exists : date( 'Y-m-d H:i:s' );
        } else {
            $gamipress_install_date = ( $exists = get_option( 'gamipress_install_date' ) ) ? $exists : date( 'Y-m-d H:i:s' );
        }

        $now = date( 'Y-m-d h:i:s' );
        $datetime1 = new DateTime( $gamipress_install_date );
        $datetime2 = new DateTime( $now );

        // Difference in days between installation date and now
        $diff_interval = round( ( $datetime2->format( 'U' ) - $datetime1->format( 'U' ) ) / ( 60 * 60 * 24 ) );

        if( $diff_interval >= 7 ) {
            ?>

            <div class="notice gamipress-review-notice">
                <p>
                    <?php _e( 'Awesome! You\'ve been using <strong>GamiPress</strong> for a while.', 'gamipress' ); ?><br>
                    <?php _e( 'May I ask you to give it a <strong>5-star rating</strong> on WordPress?', 'gamipress' ); ?><br>
                    <?php _e( 'This will help to spread its popularity and to make this plugin a better one.', 'gamipress' ); ?><br>
                    <br>
                    <?php _e( 'Your help is much appreciated. Thank you very much,', 'gamipress' ); ?><br>
                    <span>~Ruben Garcia</span>
                </p>
                <ul>
                    <li><a href="https://wordpress.org/support/plugin/gamipress/reviews/?rate=5#new-post" class="button button-primary" target="_blank" title="<?php _e( 'Yes, I want to rate it!', 'gamipress' ); ?>"><?php _e( 'Yes, I want to rate it!', 'gamipress' ); ?></a></li>
                    <li><a href="javascript:void(0);" class="gamipress-hide-review-notice button" title="<?php _e( 'I already did', 'gamipress' ); ?>"><?php _e( 'I already did', 'gamipress' ); ?></a></li>
                    <li><a href="javascript:void(0);" class="gamipress-hide-review-notice" title="<?php _e( 'No, I don\'t want to rate it', 'gamipress' ); ?>"><small><?php _e( 'No, I don\'t want to rate it', 'gamipress' ); ?></small></a></li>
                </ul>
                <span class="dashicons-before dashicons-gamipress"></span>
            </div>

            <?php
        }

    }

}
add_action( 'admin_notices', 'gamipress_admin_notices' );

/**
 * Ajax handler for hide review notice action
 *
 * @since 1.5.9
 */
function gamipress_ajax_hide_review_notice() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    if( gamipress_is_network_wide_active() ) {
        update_site_option( 'gamipress_hide_review_notice', 'yes' );
    } else {
        update_option( 'gamipress_hide_review_notice', 'yes' );
    }

    wp_send_json_success( array( 'success' ) );
    exit;
}

add_action( 'wp_ajax_gamipress_hide_review_notice', 'gamipress_ajax_hide_review_notice' );
