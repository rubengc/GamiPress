<?php
/**
 * Admin Assets Pages
 *
 * @package     GamiPress\Admin\Assets
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.6.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Assets page
 *
 * @since  1.6.0
 *
 * @return void
 */
function gamipress_assets_page() {

    if( ! function_exists( 'plugins_api' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    wp_enqueue_script( 'plugin-install' );
    add_thickbox();
    wp_enqueue_script( 'updates' );

    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>
        <h1 class="wp-heading-inline"><?php _e( 'GamiPress Assets', 'gamipress' ); ?></h1>
        <hr class="wp-header-end">

        <p><?php _e( 'Resources to decorate your gamification elements and take their design to the next level!.', 'gamipress' ); ?></p>

        <form id="plugin-filter" method="post">
            <div class="wp-list-table widefat gamipress-assets">

                <?php

                $plugins = gamipress_plugins_api();

                if ( is_wp_error( $plugins ) ) {
                    echo $plugins->get_error_message();
                    return;
                }

                foreach ( $plugins as $plugin ) {

                    // Skip if is not an asset
                    if( ! gamipress_is_plugin_asset( $plugin ) )
                        continue;

                    gamipress_render_plugin_card( $plugin );

                }

                ?>
            </div>
        </form>
    </div>
    <?php
}

/**
 * Helper function to determine if give plugin has the passes category
 *
 * @since   1.6.9.2
 *
 * @param stdClass $plugin
 *
 * @return bool
 */
function gamipress_is_plugin_pass( $plugin ) {

    // Check if plugin has categories
    if( is_array( $plugin->info->category ) && count( $plugin->info->category ) ) {

        // Loop plugin categories
        foreach( $plugin->info->category as $category ) {

            // Passes category found
            if( $category->slug === 'passes' ) {
                return true;
            }
        }

    }

    return false;
}

/**
 * Helper function to determine if give plugin has the asset tag
 *
 * @since  1.6.0
 *
 * @param stdClass $plugin
 *
 * @return bool
 */
function gamipress_is_plugin_asset( $plugin ) {

    // Check if plugin has tags
    if( is_array( $plugin->info->tags ) && count( $plugin->info->tags ) ) {

        // Loop plugin tags
        foreach( $plugin->info->tags as $tag ) {

            // asset tag found
            if( $tag->slug === 'asset' ) {
                return true;
            }
        }

    }

    return false;
}