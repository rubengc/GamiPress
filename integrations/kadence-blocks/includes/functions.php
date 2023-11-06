<?php
/**
 * Functions
 *
 * @package GamiPress\Kadence_Blocks\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_kadence_blocks_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'kadence_form', $_REQUEST['post_type'] ) ) {

        // Pull back the search string
        $results = array();

        $forms = gamipress_kadence_blocks_get_forms();

        foreach ( $forms as $form ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $form['id'],
                'post_title' => $form['name'],
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_kadence_blocks_ajax_get_posts', 5 );

/**
 * Get all forms
 *
 * @since 1.0.0
 *
 * @return string|null
 */
function gamipress_kadence_blocks_get_forms( ) {

    global $wpdb;

    $all_forms = array();

    $forms = $wpdb->get_results("select id,post_title,post_content from {$wpdb->posts} where post_content like '%<!-- wp:kadence/form%' and post_status = 'publish'");
    
    foreach ($forms as $post) {
        $form_id = $post->id;
        $form_title = $post->post_title;
        $form_content = $post->post_content;
        	
        $contentArray = explode('<!--', $form_content);
        $content = [];
        
        foreach ($contentArray as $key => $value) {
        	if (str_contains($value, ' wp:kadence/form')) {
        	    $temp = str_replace(' wp:kadence/form', '', $value);
        	    $temp1 = explode('-->', $temp, 2);
        	    $content[] = json_decode($temp1[0]);
        	}
        }
        	
        if (is_array($content)) {
        	foreach ($content as $form) {
        	    $parent_id = $form->postID;
        	    $unique_id = $form->uniqueID;
        	    $all_forms[] = array(
                    'id' => $unique_id,
                    'name' => $form_title . '_' . $unique_id,     
                );
        	    }
        }
    }

    return $all_forms;

}

/**
 * Get the form title
 *
 * @since 1.0.0
 *
 * @param int $form_id
 *
 * @return string|null
 */
function gamipress_kadence_blocks_get_form_title( $form_id ) {

    // Empty title if no ID provided
    if( empty( $form_id ) ) {
        return '';
    }

    $complete_form_id = explode( '_', $form_id );
    $post_title = get_the_title( $complete_form_id[0] );

    return $post_title . '_' . $form_id;

}