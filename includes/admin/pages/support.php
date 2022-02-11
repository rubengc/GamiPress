<?php
/**
 * Admin Help and Support Page
 *
 * @package     GamiPress\Admin\Help_Support
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Help and Support page
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_help_support_page() {
    ?>

    <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>
        <h1 class="wp-heading-inline"><?php _e( 'GamiPress Help and Support', 'gamipress' ); ?></h1>

        <h2><?php _e( 'About GamiPress', 'gamipress' ); ?>:</h2>
        <p><?php echo __( 'GamiPress is plugin for WordPress that allows your site\'s users to complete tasks, demonstrate achievements, and earn points. You define the points and achievement types, organize your requirements any way you like, and choose from a range of options to determine whether each task or requirement has been achieved.', 'gamipress' ); ?></p>

        <?php do_action( 'gamipress_help_support_page_about' ); ?>

        <h2><?php _e( 'Documentation', 'gamipress' ); ?>:</h2>
        <p><?php printf(
                __( 'For information about all features and configurations of GamiPress, visit the official %1$s.', 'gamipress' ),
                sprintf(
                    '<a href="https://gamipress.com/docs" target="_blank">%s</a>',
                    __( 'documentation', 'gamipress' )
                )
            ); ?></p>

        <?php do_action( 'gamipress_help_support_page_docs' ); ?>

        <h2><?php _e( 'Help / Support', 'gamipress' ); ?>:</h2>
        <p><?php printf(
                __( 'For support on using GamiPress or to suggest feature enhancements, visit the official %1$s. %2$s with inquiries.', 'gamipress' ),
                sprintf(
                    '<a href="https://wordpress.org/support/plugin/gamipress" target="_blank">%s</a>',
                    __( 'GamiPress support forums', 'gamipress' )
                ),
                sprintf(
                    '<a href="https://gamipress.com/contact-us/" target="_blank">%s</a>',
                    __( 'Contact us', 'gamipress' )
                )
            ); ?></p>

        <?php do_action( 'gamipress_help_support_page_help' ); ?>

        <h2><?php _e( 'Shortcodes', 'gamipress' ); ?>:</h2>
        <p><?php _e( 'With GamiPress activated, the following shortcodes can be placed on any page or post within WordPress to expose a variety of GamiPress functions.', 'gamipress' ); ?></p>

        <?php do_action( 'gamipress_help_support_page_shortcodes' ); ?>
    </div>

    <?php
}