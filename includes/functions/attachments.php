<?php
/**
 * Attachments Functions
 *
 * @package     GamiPress\Attachments_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.7.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Attempt to find the attachment ID from the file URL or, if comes from external URL, attempt to download and insert it as attachment
 *
 * @since 1.7.0
 *
 * @param string $url       Attachment URL
 *
 * @return int|WP_Error     Attachment ID on success, WP_Error otherwise
 */
function gamipress_import_attachment( $url ) {

    $site_url = site_url();

    // If the URL is absolute, but does not contain address, then upload it assuming base_site_url
    if ( preg_match( '|^/[\w\W]+$|', $url ) )
        $url = rtrim( $site_url, '/' ) . $url;

    $thumbnail_id = gamipress_get_attachment_id_from_url( $url );

    if( $thumbnail_id ) {
        return $thumbnail_id;
    } else {
        // Remove protocol for following checks
        $site_url = str_replace( array( 'https://', 'http://' ), array( '', '' ), $site_url );

        if( strpos( $url, $site_url ) === false )
            return gamipress_insert_external_attachment( $url );
    }

    return new WP_Error( 'attachment_import_error', __('Attachment not found', 'gamipress') );

}

/**
 * Retrieves the attachment ID from the file URL
 *
 * @since 1.7.0
 *
 * @param string $url   Attachment URL
 *
 * @return int|false    Attachment ID on success, false otherwise
 */
function gamipress_get_attachment_id_from_url( $url ) {

    global $wpdb;

    $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url ) );

    return isset( $attachment[0] ) ? $attachment[0] : false;

}

/**
 * Attempt to create a new attachment from an external URL
 *
 * Taken from WordPress Importer tool
 *
 * @since 1.7.0
 *
 * @param string $url URL to fetch attachment from
 * @param array $post Attachment post details
 *
 * @return int|WP_Error Post ID on success, WP_Error otherwise
 */
function gamipress_insert_external_attachment( $url, $post = array() ) {

    $upload = gamipress_fetch_remote_file( $url );

    if ( is_wp_error( $upload ) )
        return $upload;

    if ( $info = wp_check_filetype( $upload['file'] ) )
        $post['post_mime_type'] = $info['type'];
    else
        return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'gamipress') );

    $post['guid'] = $upload['url'];

    // as per wp-admin/includes/upload.php
    $post_id = wp_insert_attachment( $post, $upload['file'] );
    wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

    return $post_id;

}

/**
 * Attempt to download a remote file attachment
 *
 * Taken from WordPress Importer tool
 *
 * @since 1.7.0
 *
 * @param string $url URL of item to fetch
 *
 * @return array|WP_Error Local file location details on success, WP_Error otherwise
 */
function gamipress_fetch_remote_file( $url ) {

    if( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
        return new WP_Error( 'import_url_error', __('Invalid URL', 'gamipress') );
    }

    // extract the file name and extension from the url
    $file_name = basename( $url );

    // get placeholder file in the upload dir with a unique, sanitized filename
    $upload = wp_upload_bits( $file_name, 0, '', null );
    if ( $upload['error'] )
        return new WP_Error( 'upload_dir_error', $upload['error'] );

    // fetch the remote url and write it to the placeholder file
    $remote_response = wp_safe_remote_get( $url, array(
        'timeout' => 300,
        'stream' => true,
        'filename' => $upload['file'],
    ) );

    $headers = wp_remote_retrieve_headers( $remote_response );

    // request failed
    if ( ! $headers ) {
        @unlink( $upload['file'] );
        return new WP_Error( 'import_file_error', __('Remote server did not respond', 'gamipress') );
    }

    $remote_response_code = wp_remote_retrieve_response_code( $remote_response );

    // make sure the fetch was successful
    if ( $remote_response_code != '200' ) {
        @unlink( $upload['file'] );
        return new WP_Error( 'import_file_error', sprintf( __('Remote server returned error response %1$d %2$s', 'gamipress'), esc_html( $remote_response_code ), get_status_header_desc($remote_response_code) ) );
    }

    $filesize = filesize( $upload['file'] );

    if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
        @unlink( $upload['file'] );
        return new WP_Error( 'import_file_error', __('Remote file is incorrect size', 'gamipress') );
    }

    if ( 0 == $filesize ) {
        @unlink( $upload['file'] );
        return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'gamipress') );
    }

    return $upload;

}