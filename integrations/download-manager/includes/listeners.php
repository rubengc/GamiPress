<?php
/**
 * Listeners
 *
 * @package GamiPress\Download_Manager\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * File gets downloaded
 *
 * @since 1.0.0
 *
 * @param array $package
 */
function gamipress_download_manager_gets_downloaded( $package ) {

    // Bail if file is not publish
    if ( $package['post_status'] !== 'publish' && $package['post_status'] !== 'private' ) {
        return;
    }
    
    $user_id = get_current_user_id();

    // Bail if user is not logged
    if ($user_id === 0) {
        return;
    }

    // Gets any download
    do_action( 'gamipress_download_manager_any_download', $package['ID'], $user_id );

    // Gets specific download
    do_action( 'gamipress_download_manager_specific_download', $package['ID'], $user_id );

}
add_action( 'wpdm_onstart_download', 'gamipress_download_manager_gets_downloaded', 10, 1 );
