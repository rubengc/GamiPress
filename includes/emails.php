<?php
/**
 * Emails
 *
 * @package     GamiPress\Emails
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get an array of registered email templates
 *
 * @since 1.3.0
 *
 * @return array The registered email pattern tags
 */
function gamipress_get_email_templates() {

    return apply_filters( 'gamipress_email_templates', array(
        'default'   =>  __(  'Default Template', 'gamipress' ),
        'plain'     =>  __(  'Plain Text', 'gamipress' ),
    ) );

}

/**
 * Send the email
 *
 * @since 1.3.0
 *
 * @param  string       $to             The To address to send to.
 * @param  string       $subject        The subject line of the email to send.
 * @param  string       $message        The body of the email to send.
 * @param  string|array $attachments    Attachments to the email in a format supported by wp_mail()
 *
 * @return bool
 */
function gamipress_send_email( $to, $subject, $message, $attachments = '' ) {

    // Email headers
    $headers = gamipress_get_email_headers( $to, $subject, $message, $attachments );

    // Parse tags on subject
    $subject = gamipress_parse_email_tags( $subject, $to, $subject, $message, $attachments );

    $subject = strip_tags( $subject );

    // Parse tags on message
    $message = gamipress_parse_email_tags( $message, $to, $subject, $message, $attachments );

    // Apply the email template and parses the message to it
    $message = gamipress_get_email_body( $to, $subject, $message, $attachments );

    // Get the email attachments
    $attachments = gamipress_get_email_attachments( $to, $subject, $message, $attachments );

    add_filter( 'wp_mail_from', 'gamipress_get_email_from_address' );
    add_filter( 'wp_mail_from_name', 'gamipress_get_email_from_name' );
    add_filter( 'wp_mail_content_type', 'gamipress_get_email_content_type' );

    // Use WordPress email function
    $sent = wp_mail( $to, $subject, $message, $headers, $attachments );

    remove_filter( 'wp_mail_from', 'gamipress_get_email_from_address' );
    remove_filter( 'wp_mail_from_name', 'gamipress_get_email_from_name' );
    remove_filter( 'wp_mail_content_type', 'gamipress_get_email_content_type' );

    // Check for log errors
    $log_errors = apply_filters( 'gamipress_log_email_errors', true, $to, $subject, $message );

    if( ! $sent && $log_errors === true) {

        if ( is_array( $to ) ) {
            $to = implode( ',', $to );
        }

        $log_message = sprintf(
            __( "[GamiPress] Email failed to send to %s with subject: %s", 'gamipress' ),
            $to,
            $subject
        );

        error_log( $log_message );

    }

    // Return WordPress email function response
    return $sent;

}

/**
 * Function to get the mail from email address
 *
 * @since 1.0.0
 *
 * @param string $from
 *
 * @return string
 */
function gamipress_get_email_from_address( $from = '' ) {

    $from_address = gamipress_get_option( 'email_from_address', get_bloginfo( 'admin_email' ) );

    return sanitize_email( $from_address );
}

/**
 * Function to get the mail from name
 *
 * @since 1.0.0
 *
 * @param string $from_name
 *
 * @return string
 */
function gamipress_get_email_from_name( $from_name = '' ) {

    $from_name = gamipress_get_option( 'email_from_name', get_bloginfo( 'name' ) );

    return wp_specialchars_decode( $from_name );
}

/**
 * Function to get the mail content type
 *
 * @since 1.0.0
 *
 * @param string $content_type
 *
 * @return string
 */
function gamipress_get_email_content_type( $content_type = 'text/html' ) {
    return 'text/html';
}

/**
 * Build the email headers based on the email settings
 *
 * @since 1.3.0
 *
 * @param string|array  $to
 * @param string        $subject
 * @param string        $message
 * @param string|array  $attachments
 *
 * @return string
 */
function gamipress_get_email_headers( $to, $subject, $message, $attachments = '' ) {

    $from_name = gamipress_get_option( 'email_from_name', get_bloginfo( 'name' ) );
    $from_email = gamipress_get_option( 'email_from_address', get_bloginfo( 'admin_email' ) );
    $charset = get_bloginfo( 'charset' );

    // Setup email headers
    $headers  = "From: {$from_name} <{$from_email}>\r\n";
    $headers .= "Reply-To: {$from_email}\r\n";
    $headers .= "Content-Type: text/html; charset={$charset}\r\n";

    return apply_filters( 'gamipress_email_headers', $headers, $to, $subject, $message, $attachments );

}

/**
 * Get the configured email template and applies it ot the given message
 *
 * @since 1.3.0
 *
 * @param string|array  $to
 * @param string        $subject
 * @param string        $message
 * @param string|array  $attachments
 *
 * @return string
 */
function gamipress_get_email_body( $to, $subject, $message, $attachments = '' ) {

    $template = gamipress_get_option( 'email_template', 'default' );

    ob_start();

    // Get the email header template
    gamipress_get_template_part( 'emails/header', $template );

    // Get the email body template
    gamipress_get_template_part( 'emails/body', $template );

    // Get the email footer template
    gamipress_get_template_part( 'emails/footer', $template );

    $body = ob_get_clean();

    // Apply wpautop on message text
    $message = wpautop( $message );

    $body = str_replace( '{email}', $message, $body );

    return apply_filters( 'gamipress_email_body', $body, $to, $subject, $message, $attachments );

}

/**
 * Get the email attachments (actually not used in GamiPress but the filters allows to extend it)
 *
 * @since 2.0.4
 *
 * @param string|array  $to
 * @param string        $subject
 * @param string        $message
 * @param string|array  $attachments
 *
 * @return string
 */
function gamipress_get_email_attachments( $to, $subject, $message, $attachments = '' ) {

    return apply_filters( 'gamipress_email_attachments', $attachments, $to, $subject, $message );

}

/**
 * Parse the email tags to the given content
 *
 * @since 1.3.0
 *
 * @param string        $content
 * @param string|array  $to
 * @param string        $subject
 * @param string        $message
 * @param string|array  $attachments
 *
 * @return string
 */
function gamipress_parse_email_tags( $content, $to, $subject, $message, $attachments = '' ) {

    global $gamipress_email_template_args;

    if( ! is_array( $gamipress_email_template_args ) ) {
        $gamipress_email_template_args = array();
    }

    // Ensure vars
    if( ! isset( $gamipress_email_template_args['user_id'] ) ) {
        $gamipress_email_template_args['user_id'] = get_current_user_id();
    }

    if( ! isset( $gamipress_email_template_args['type'] ) ) {
        $gamipress_email_template_args['type'] = '';
    }

    // Shorthand
    $a = $gamipress_email_template_args;

    // Setup replacements
    $replacements = array();

    if( $a['type'] === 'achievement_earned' && isset( $a['achievement_id'] ) ) {

        $replacements = gamipress_get_achievement_earned_tags_replacements( $a['achievement_id'], $a['user_id'] );

    } else if( $a['type'] === 'step_completed' && isset( $a['step_id'] ) ) {

        $replacements = gamipress_get_step_completed_tags_replacements( $a['step_id'], $a['user_id'] );

    } else if( $a['type'] === 'points_award_completed' && isset( $a['points_award_id'] ) ) {

        $replacements = gamipress_get_points_award_completed_tags_replacements( $a['points_award_id'], $a['user_id'] );

    } else if( $a['type'] === 'points_deduct_completed' && isset( $a['points_deduct_id'] ) ) {

        $replacements = gamipress_get_points_deduct_completed_tags_replacements( $a['points_deduct_id'], $a['user_id'] );

    } else if( $a['type'] === 'rank_earned' && isset( $a['rank_id'] ) ) {

        $replacements = gamipress_get_rank_earned_tags_replacements( $a['rank_id'], $a['user_id'] );

    } else if( $a['type'] === 'rank_requirement_completed' && isset( $a['rank_requirement_id'] ) ) {

        $replacements = gamipress_get_rank_requirement_completed_tags_replacements( $a['rank_requirement_id'], $a['user_id'] );

    }

    // Setup the user
    $user_id = absint( $a['user_id'] );
    $user = ( $user_id !== 0 ? get_userdata( $user_id ) : false );

    /**
     * Parse email tags
     *
     * @since 1.3.4
     *
     * @param array     $replacements
     * @param WP_User   $user
     * @param array     $template_args
     */
    $replacements = apply_filters( 'gamipress_parse_email_tags', $replacements, $user, $a );

    return str_replace( array_keys( $replacements ), $replacements, $content );

}

/**
 * Similar to gamipress_parse_email_tags() but with sample data for preview
 *
 * @since 1.3.0
 *
 * @param string        $content
 * @param string|array  $to
 * @param string        $subject
 * @param string        $message
 * @param string|array  $attachments
 *
 * @return mixed
 */
function gamipress_parse_preview_email_tags( $content, $to, $subject, $message, $attachments = '' ) {

    global $gamipress_email_template_args;

    $user = wp_get_current_user();

    $replacements = array(
        '{user_id}'             =>  $user->ID,
        '{user}'                =>  $user->display_name,
        '{user_first}'          =>  $user->first_name,
        '{user_last}'           =>  $user->last_name,
        '{user_email}'          =>  $user->user_email,
        '{user_username}'       =>  $user->user_login,
        '{site_title}'          =>  get_bloginfo( 'name' ),
        '{site_link}'           =>  '<a href="' . esc_url( home_url() ) . '">' . get_bloginfo( 'name' ) . '</a>',
    );

    $img_placeholder_style = "
        display: inline-block;
        line-height: 100px;
        width: 100px;
        text-align: center;
        font-weight: bold;
        background-color: #eee;
        color: #888;
    ";

    if( $gamipress_email_template_args['type'] === 'achievement_earned' ) {

        $replacements['{achievement_id}'] = 1;
        $replacements['{achievement_title}'] = __( 'Sample Achievement', 'gamipress' );
        $replacements['{achievement_url}'] = '#';
        $replacements['{achievement_link}'] = '<a href="#" title="' . $replacements['{achievement_title}'] . '">' . $replacements['{achievement_title}'] . '</a>';
        $replacements['{achievement_excerpt}'] = __( 'Sample Achievement Excerpt', 'gamipress' );
        $replacements['{achievement_image}'] = '<div style="' . $img_placeholder_style . '">100x100</div>';
        $replacements['{achievement_steps}'] = '<ul>'
                . '<li>' . __(  'Not earned achievement step.', 'gamipress' ) . '</li>'
                . '<li style="text-decoration: line-through;">' . __(  'Earned achievement step.', 'gamipress' ) . '</li>'
            . '</ul>';
        $replacements['{achievement_type}'] = __( 'Sample Achievement Type', 'gamipress' );
        $replacements['{achievement_congratulations}'] = __( 'Sample Achievement Congratulations Text', 'gamipress' );

    } else if( $gamipress_email_template_args['type'] === 'step_completed' ) {

        $replacements['{label}'] = __( 'Sample Step Label', 'gamipress' );

        // Set a temporal type to parse achievement tags
        $gamipress_email_template_args['type'] = 'achievement_earned';

        $content = gamipress_parse_preview_email_tags( $content, $to, $subject, $message, $attachments );

        // Restore the original type
        $gamipress_email_template_args['type'] = 'step_completed';

    } else if( $gamipress_email_template_args['type'] === 'points_award_completed' ) {

        $replacements['{label}'] = __( 'Sample Points Award Label', 'gamipress' );
        $replacements['{points}'] = 100;
        $replacements['{points_balance}'] = 1000;
        $replacements['{points_type}'] = __( 'Sample Points Type', 'gamipress' );
        $replacements['{points_image}'] = '<div style="' . $img_placeholder_style . '">100x100</div>';;

    } else if( $gamipress_email_template_args['type'] === 'points_deduct_completed' ) {

        $replacements['{label}'] = __( 'Sample Points Deduction Label', 'gamipress' );
        $replacements['{points}'] = 100;
        $replacements['{points_balance}'] = 1000;
        $replacements['{points_type}'] = __( 'Sample Points Type', 'gamipress' );
        $replacements['{points_image}'] = '<div style="' . $img_placeholder_style . '">100x100</div>';;

    } else if( $gamipress_email_template_args['type'] === 'rank_earned' ) {

        $replacements['{rank_id}'] = 1;
        $replacements['{rank_title}'] = __( 'Sample Rank', 'gamipress' );
        $replacements['{rank_url}'] = '#';
        $replacements['{rank_link}'] = '<a href="#" title="' . $replacements['{rank_title}'] . '">' . $replacements['{rank_title}'] . '</a>';
        $replacements['{rank_excerpt}'] = __( 'Sample Rank Excerpt', 'gamipress' );
        $replacements['{rank_image}'] = '<div style="' . $img_placeholder_style . '">100x100</div>';
        $replacements['{rank_requirements}'] = '<ul>'
            . '<li>' . __(  'Not completed rank requirement.', 'gamipress' ) . '</li>'
            . '<li style="text-decoration: line-through;">' . __(  'Completed rank requirement.', 'gamipress' ) . '</li>'
            . '</ul>';
        $replacements['{rank_type}'] = __( 'Sample Rank Type', 'gamipress' );
        $replacements['{rank_congratulations}'] = __( 'Sample Rank Congratulations Text', 'gamipress' );

    } else if( $gamipress_email_template_args['type'] === 'rank_requirement_completed' ) {

        $replacements['{label}'] = __( 'Sample Rank Requirement Label', 'gamipress' );

        // Set a temporal type to parse rank tags
        $gamipress_email_template_args['type'] = 'rank_earned';

        $content = gamipress_parse_preview_email_tags( $content, $to, $subject, $message, $attachments );

        // Restore the original type
        $gamipress_email_template_args['type'] = 'rank_requirement_completed';

    }

    /**
     * Parse email tags for preview email
     *
     * @since 1.3.4
     *
     * @param array     $replacements
     * @param WP_User   $user
     * @param array     $template_args
     */
    $replacements = apply_filters( 'gamipress_parse_preview_email_tags', $replacements, $user, $gamipress_email_template_args );

    return str_replace( array_keys( $replacements ), $replacements, $content );

}

/**
 * Preview the desired email
 *
 * @since 1.3.0
 *
 * @param string        $subject
 * @param string        $message
 */
function gamipress_preview_email( $subject, $message ) {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Parse tags on subject
    $subject = gamipress_parse_preview_email_tags( $subject, '', $subject, $message );

    $subject = strip_tags( $subject );

    // Parse tags on message
    $message = gamipress_parse_preview_email_tags( $message, '', $subject, $message );

    echo gamipress_get_email_body( '', $subject, $message );

}

/**
 * Preview achievement earned email action
 *
 * @since 1.3.0
 */
function gamipress_preview_achievement_earned_email() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'achievement_earned'
    );

    $subject = apply_filters( 'gamipress_preview_achievement_earned_email_subject', gamipress_get_option( 'achievement_earned_email_subject' ) );
    $message = apply_filters( 'gamipress_preview_achievement_earned_email_content', gamipress_get_option( 'achievement_earned_email_content' ) );

    gamipress_preview_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_preview_achievement_earned_email', 'gamipress_preview_achievement_earned_email' );

/**
 * Preview step completed email action
 *
 * @since 1.3.0
 */
function gamipress_preview_step_completed_email() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'step_completed'
    );

    $subject = apply_filters( 'gamipress_preview_step_completed_email_subject', gamipress_get_option( 'step_completed_email_subject' ) );
    $message = apply_filters( 'gamipress_preview_step_completed_email_content', gamipress_get_option( 'step_completed_email_content' ) );

    gamipress_preview_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_preview_step_completed_email', 'gamipress_preview_step_completed_email' );

/**
 * Preview points award completed email action
 *
 * @since 1.3.0
 */
function gamipress_preview_points_award_completed_email() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'points_award_completed'
    );

    $subject = apply_filters( 'gamipress_preview_points_award_completed_email_subject', gamipress_get_option( 'points_award_completed_email_subject' ) );
    $message = apply_filters( 'gamipress_preview_points_award_completed_email_content', gamipress_get_option( 'points_award_completed_email_content' ) );

    gamipress_preview_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_preview_points_award_completed_email', 'gamipress_preview_points_award_completed_email' );

/**
 * Preview points deduct completed email action
 *
 * @since 1.3.7
 */
function gamipress_preview_points_deduct_completed_email() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'points_deduct_completed'
    );

    $subject = apply_filters( 'gamipress_preview_points_deduct_completed_email_subject', gamipress_get_option( 'points_deduct_completed_email_subject' ) );
    $message = apply_filters( 'gamipress_preview_points_deduct_completed_email_content', gamipress_get_option( 'points_deduct_completed_email_content' ) );

    gamipress_preview_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_preview_points_deduct_completed_email', 'gamipress_preview_points_deduct_completed_email' );

/**
 * Preview rank earned email action
 *
 * @since 1.3.1
 */
function gamipress_preview_rank_earned_email() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'rank_earned'
    );

    $subject = apply_filters( 'gamipress_preview_rank_earned_email_subject', gamipress_get_option( 'rank_earned_email_subject' ) );
    $message = apply_filters( 'gamipress_preview_rank_earned_email_content', gamipress_get_option( 'rank_earned_email_content' ) );

    gamipress_preview_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_preview_rank_earned_email', 'gamipress_preview_rank_earned_email' );

/**
 * Preview rank requirement completed email action
 *
 * @since 1.3.1
 */
function gamipress_preview_rank_requirement_completed_email() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'rank_requirement_completed'
    );

    $subject = apply_filters( 'gamipress_preview_rank_requirement_completed_email_subject', gamipress_get_option( 'rank_requirement_completed_email_subject' ) );
    $message = apply_filters( 'gamipress_preview_rank_requirement_completed_email_content', gamipress_get_option( 'rank_requirement_completed_email_content' ) );

    gamipress_preview_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_preview_rank_requirement_completed_email', 'gamipress_preview_rank_requirement_completed_email' );

/**
 * Send the desired email to the site admin
 *
 * @since 1.3.0
 *
 * @param string        $subject
 * @param string        $message
 *
 * @return bool
 */
function gamipress_send_test_email( $subject, $message ) {

    global $gamipress_email_template_args;

    $is_ajax = ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' );

    $user = wp_get_current_user();

    $gamipress_email_template_args['user_id'] = $user->ID;

    // Parse tags on subject
    $subject = gamipress_parse_preview_email_tags( $subject, '', $subject, $message );

    $subject = strip_tags( $subject );

    // Parse tags on message
    $message = gamipress_parse_preview_email_tags( $message, '', $subject, $message );

    if( gamipress_send_email( $user->user_email, $subject, $message ) ) {
        if( $is_ajax ) {
            wp_send_json_success( __( 'Email sent successfully!', 'gamipress' ) );
        }
    } else {
        if( $is_ajax ) {
            wp_send_json_error( __( 'There was a problem sending the email. Check your WordPress email configuration.', 'gamipress' ) );
        }
    }
}

/**
 * Send a test achievement earned email
 *
 * @since 1.3.0
 */
function gamipress_send_test_achievement_earned_email() {

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'achievement_earned',
    );

    $subject = apply_filters( 'gamipress_send_test_achievement_earned_email_subject', gamipress_get_option( 'achievement_earned_email_subject' ) );
    $message = apply_filters( 'gamipress_send_test_achievement_earned_email_content', gamipress_get_option( 'achievement_earned_email_content' ) );

    gamipress_send_test_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_send_test_achievement_earned_email', 'gamipress_send_test_achievement_earned_email' );

/**
 * Send a test step completed email
 *
 * @since 1.3.0
 */
function gamipress_send_test_step_completed_email() {

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'step_completed',
    );

    $subject = apply_filters( 'gamipress_send_test_step_completed_email_subject', gamipress_get_option( 'step_completed_email_subject' ) );
    $message = apply_filters( 'gamipress_send_test_step_completed_email_content', gamipress_get_option( 'step_completed_email_content' ) );

    gamipress_send_test_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_send_test_step_completed_email', 'gamipress_send_test_step_completed_email' );

/**
 * Send a test points award completed email
 *
 * @since 1.3.0
 */
function gamipress_send_test_points_award_completed_email() {

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'points_award_completed',
    );

    $subject = apply_filters( 'gamipress_send_test_points_award_completed_email_subject', gamipress_get_option( 'points_award_completed_email_subject' ) );
    $message = apply_filters( 'gamipress_send_test_points_award_completed_email_content', gamipress_get_option( 'points_award_completed_email_content' ) );

    gamipress_send_test_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_send_test_points_award_completed_email', 'gamipress_send_test_points_award_completed_email' );

/**
 * Send a test points deduct completed email
 *
 * @since 1.3.0
 */
function gamipress_send_test_points_deduct_completed_email() {

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'points_deduct_completed',
    );

    $subject = apply_filters( 'gamipress_send_test_points_deduct_completed_email_subject', gamipress_get_option( 'points_deduct_completed_email_subject' ) );
    $message = apply_filters( 'gamipress_send_test_points_deduct_completed_email_content', gamipress_get_option( 'points_deduct_completed_email_content' ) );

    gamipress_send_test_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_send_test_points_deduct_completed_email', 'gamipress_send_test_points_deduct_completed_email' );

/**
 * Send a test rank earned email
 *
 * @since 1.3.1
 */
function gamipress_send_test_rank_earned_email() {

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'rank_earned',
    );

    $subject = apply_filters( 'gamipress_send_test_rank_earned_email_subject', gamipress_get_option( 'rank_earned_email_subject' ) );
    $message = apply_filters( 'gamipress_send_test_rank_earned_email_content', gamipress_get_option( 'rank_earned_email_content' ) );

    gamipress_send_test_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_send_test_rank_earned_email', 'gamipress_send_test_rank_earned_email' );

/**
 * Send a test rank requirement completed email
 *
 * @since 1.3.1
 */
function gamipress_send_test_rank_requirement_completed_email() {

    global $gamipress_email_template_args;

    $gamipress_email_template_args = array(
        'type' => 'rank_requirement_completed',
    );

    $subject = apply_filters( 'gamipress_send_test_rank_requirement_completed_email_subject', gamipress_get_option( 'rank_requirement_completed_email_subject' ) );
    $message = apply_filters( 'gamipress_send_test_rank_requirement_completed_email_content', gamipress_get_option( 'rank_requirement_completed_email_content' ) );

    gamipress_send_test_email( $subject, $message );

    exit;

}
add_action( 'gamipress_action_get_send_test_rank_requirement_completed_email', 'gamipress_send_test_rank_requirement_completed_email' );

/**
 * Function that check for each awarded achievement if it should be emailed to the user
 *
 * @since 1.3.0
 *
 * @param int $user_id
 * @param int $achievement_id
 * @param string $trigger
 * @param int $site_id
 * @param array $args
 */
function gamipress_maybe_send_email_to_user( $user_id, $achievement_id, $trigger, $site_id, $args ) {

    global $gamipress_email_template_args;

    // Setup out achievement types
    $achievement_types = gamipress_get_achievement_types_slugs();

    // Get the achievement type
    $achievement_type = gamipress_get_post_type( $achievement_id );

    if( in_array( $achievement_type, $achievement_types ) ) {

        // Check if email disabled from settings
        if( (bool) apply_filters( 'gamipress_disable_achievement_earned_email', gamipress_get_option( 'disable_achievement_earned_email', false ), $user_id, $achievement_id ) ) {
            return;
        }

        // Check if email disabled from user preferences
        if( gamipress_is_email_disabled_from_user_settings( $user_id, $achievement_id ) ) {
            return;
        }

        $gamipress_email_template_args = array(
            'user_id' => $user_id,
            'achievement_id' => $achievement_id,
            'type' => 'achievement_earned',
        );

        $user = get_userdata( $user_id );
        $subject = apply_filters( 'gamipress_achievement_earned_email_subject', gamipress_get_option( 'achievement_earned_email_subject' ), $user_id, $achievement_id );
        $message = apply_filters( 'gamipress_achievement_earned_email_content', gamipress_get_option( 'achievement_earned_email_content' ), $user_id, $achievement_id );

        gamipress_send_email( $user->user_email, $subject, $message );

    } else if( $achievement_type === 'step' ) {

        $achievement = gamipress_get_step_achievement( $achievement_id );

        // Check if step was assigned to an achievement
        if( ! $achievement ) {
            return;
        }

        if( (bool) apply_filters( 'gamipress_disable_step_completed_email', gamipress_get_option( 'disable_step_completed_email', false ), $user_id, $achievement_id, $achievement ) ) {
            return;
        }

        // Check if email disabled from user preferences
        if( gamipress_is_email_disabled_from_user_settings( $user_id, $achievement_id ) ) {
            return;
        }

        $all_steps_earned = true;
        $steps = gamipress_get_achievement_steps( $achievement->ID );

        // Just loop if achievement has more than 1 step
        if( is_array( $steps ) && count( $steps ) > 1 ) {

            foreach( $steps as $step ) {
                // check if user has earned this step
                $earned = gamipress_get_earnings_count( array(
                        'user_id' => absint( $user_id ),
                        'post_id' => absint( $step->ID ),
                        'since' => absint( gamipress_achievement_last_user_activity( $achievement->ID, $user_id ) )
                    ) ) > 0;

                if( ! $earned ) {
                    // Not all steps has been earned, so continue
                    $all_steps_earned = false;
                    break;
                }
            }
        }

        // Just send the email if user has not earned all steps, because user will receive another email that he has earned the achievement
        if( ! $all_steps_earned ) {

            $gamipress_email_template_args = array(
                'user_id' => $user_id,
                'step_id' => $achievement_id,
                'type' => 'step_completed',
            );

            $user = get_userdata( $user_id );
            $subject = apply_filters( 'gamipress_step_completed_email_subject', gamipress_get_option( 'step_completed_email_subject' ), $user_id, $achievement_id, $achievement );
            $message = apply_filters( 'gamipress_step_completed_email_content', gamipress_get_option( 'step_completed_email_content' ), $user_id, $achievement_id, $achievement );

            gamipress_send_email( $user->user_email, $subject, $message );

        }

    } else if( $achievement_type === 'points-award' ) {

        if( (bool) apply_filters( 'gamipress_disable_points_award_completed_email', gamipress_get_option( 'disable_points_award_completed_email', false ), $user_id, $achievement_id ) ) {
            return;
        }

        // Check if email disabled from user preferences
        if( gamipress_is_email_disabled_from_user_settings( $user_id, $achievement_id ) ) {
            return;
        }

        $gamipress_email_template_args = array(
            'user_id' => $user_id,
            'points_award_id' => $achievement_id,
            'type' => 'points_award_completed',
        );

        $user = get_userdata( $user_id );
        $subject = apply_filters( 'gamipress_points_award_completed_email_subject', gamipress_get_option( 'points_award_completed_email_subject' ), $user_id, $achievement_id );
        $message = apply_filters( 'gamipress_points_award_completed_email_content', gamipress_get_option( 'points_award_completed_email_content' ), $user_id, $achievement_id );

        gamipress_send_email( $user->user_email, $subject, $message );

    } else if( $achievement_type === 'points-deduct' ) {

        if( (bool) apply_filters( 'gamipress_disable_points_deduct_completed_email', gamipress_get_option( 'disable_points_deduct_completed_email', false ), $user_id, $achievement_id ) ) {
            return;
        }

        // Check if email disabled from user preferences
        if( gamipress_is_email_disabled_from_user_settings( $user_id, $achievement_id ) ) {
            return;
        }

        $gamipress_email_template_args = array(
            'user_id' => $user_id,
            'points_deduct_id' => $achievement_id,
            'type' => 'points_deduct_completed',
        );

        $user = get_userdata( $user_id );
        $subject = apply_filters( 'gamipress_points_deduct_completed_email_subject', gamipress_get_option( 'points_deduct_completed_email_subject' ), $user_id, $achievement_id );
        $message = apply_filters( 'gamipress_points_deduct_completed_email_content', gamipress_get_option( 'points_deduct_completed_email_content' ), $user_id, $achievement_id );

        gamipress_send_email( $user->user_email, $subject, $message );

    } else if( $achievement_type === 'rank-requirement' ) {

        $rank = gamipress_get_rank_requirement_rank( $achievement_id );

        // Check if requirement was assigned to a rank
        if( ! $rank ) {
            return;
        }

        if( (bool) apply_filters( 'gamipress_disable_rank_requirement_completed_email', gamipress_get_option( 'disable_rank_requirement_completed_email', false ), $user_id, $achievement_id, $rank ) ) {
            return;
        }

        // Check if email disabled from user preferences
        if( gamipress_is_email_disabled_from_user_settings( $user_id, $achievement_id ) ) {
            return;
        }

        $all_requirements_earned = true;
        $requirements = gamipress_get_rank_requirements( $rank->ID );

        // Just loop if rank has more than 1 requirement
        if( is_array( $requirements ) && count( $requirements ) > 1 ) {

            foreach( $requirements as $requirement ) {
                // Check if user has earned this requirement
                $earned = gamipress_get_earnings_count( array(
                        'user_id' => absint( $user_id ),
                        'post_id' => absint( $requirement->ID ),
                        'since' => absint( gamipress_achievement_last_user_activity( $requirement->ID, $user_id ) )
                    ) ) > 0;

                if( ! $earned ) {
                    // Not all requirements has been earned, so continue
                    $all_requirements_earned = false;
                    break;
                }
            }
        }

        // Just send the email if user has not earned all rank requirements, because user will receive another email that he has earned the rank
        if( ! $all_requirements_earned ) {

            $gamipress_email_template_args = array(
                'user_id' => $user_id,
                'rank_requirement_id' => $achievement_id,
                'type' => 'rank_requirement_completed',
            );

            $user = get_userdata( $user_id );
            $subject = apply_filters( 'gamipress_rank_requirement_completed_email_subject', gamipress_get_option( 'rank_requirement_completed_email_subject' ), $user_id, $achievement_id, $rank );
            $message = apply_filters( 'gamipress_rank_requirement_completed_email_content', gamipress_get_option( 'rank_requirement_completed_email_content' ), $user_id, $achievement_id, $rank );

            gamipress_send_email( $user->user_email, $subject, $message );

        }

    }


}
add_action( 'gamipress_award_achievement', 'gamipress_maybe_send_email_to_user', 20, 5 );

/**
 * Function that check for each awarded rank if it should be emailed to the user
 *
 * @since   1.3.1
 * @updated 1.3.8 Added filters to allow override anything
 *
 * @param int $user_id
 * @param WP_Post $new_rank
 * @param WP_Post $old_rank
 * @param int $admin_id
 * @param int $achievement_id
 */
function gamipress_maybe_send_email_to_user_for_rank_earned( $user_id, $new_rank, $old_rank, $admin_id = 0, $achievement_id = null ) {

    global $gamipress_email_template_args;

    if( (bool) apply_filters( 'gamipress_disable_rank_earned_email', gamipress_get_option( 'disable_rank_earned_email', false ), $user_id, $new_rank ) ) {
        return;
    }

    // Check if email disabled from user preferences
    if( gamipress_is_email_disabled_from_user_settings( $user_id, $new_rank->ID ) ) {
        return;
    }

    $gamipress_email_template_args = array(
        'user_id' => $user_id,
        'rank_id' => $new_rank->ID,
        'type' => 'rank_earned',
    );

    $user = get_userdata( $user_id );

    // Available filters to allow override subject
    $subject = apply_filters( 'gamipress_rank_earned_email_subject', gamipress_get_option( 'rank_earned_email_subject' ), $user_id, $new_rank->ID );
    $message = apply_filters( 'gamipress_rank_earned_email_content', gamipress_get_option( 'rank_earned_email_content' ), $user_id, $new_rank->ID );

    gamipress_send_email( $user->user_email, $subject, $message );
}
add_action( 'gamipress_update_user_rank', 'gamipress_maybe_send_email_to_user_for_rank_earned', 20, 5 );

/**
 * Get the user email settings
 *
 * @since 2.2.1
 *
 * @param int $user_id  The user ID
 *
 * @return array        The user email settings
 */
function gamipress_get_user_email_settings( $user_id ) {

    $user_id = absint( $user_id );

    $cache = gamipress_get_cache( 'user_email_settings', array(), false );

    // If result already cached, return it
    if( isset( $cache[$user_id] ) ) {
        return $cache[$user_id];
    }

    $email_settings = gamipress_get_user_meta( $user_id, 'gamipress_email_settings', true );

    // Ensure that is an array
    if( ! is_array( $email_settings ) ) {
        $email_settings = array();
    }

    // Setup the default values
    $default_settings =array(
        'all' => 'yes',
        'points_types' => 'yes',
        'achievement_types' => 'yes',
        'rank_types' => 'yes',
    );

    // Setup the default values per points type
    foreach ( gamipress_get_points_types() as $type => $data ) {
        $default_settings['points_types_' . $type] = 'yes';
    }

    // Setup the default values per achievement type
    foreach ( gamipress_get_achievement_types() as $type => $data ) {
        $default_settings['achievement_types_' . $type] = 'yes';
    }

    // Setup the default values per rank type
    foreach ( gamipress_get_rank_types() as $type => $data ) {
        $default_settings['rank_types_' . $type] = 'yes';
    }

    // Apply the default values
    $email_settings = wp_parse_args( $email_settings, $default_settings );

    /**
     * Filter to override the user email settings
     *
     * @since 2.2.1
     *
     * @param array $email_settings The user email settings
     * @param int   $user_id        The user ID
     *
     * @return array
     */
    $email_settings = apply_filters( 'gamipress_get_user_email_settings', $email_settings, $user_id );

    // Cache the user email settings
    $cache[$user_id] = $email_settings;
    gamipress_set_cache( 'user_email_settings', $cache );

    return $email_settings;


}

/**
 * Check if email for a specific type is disabled via the user email settings
 *
 * @since 2.2.1
 *
 * @param int $user_id The user ID
 * @param int $post_id The post ID to check if emails are disabled or not
 *
 * @return bool
 */
function gamipress_is_email_disabled_from_user_settings( $user_id, $post_id ) {

    $disabled = false;

    // Get the user email settings
    $user_email_settings = gamipress_get_user_email_settings( $user_id );

    // Skip the whole function checks if user has disabled all emails
    if( $user_email_settings['all'] === 'no' ) {
        $disabled = true;
    }

    if( ! $disabled ) {

        // Get the post type of the element to check
        $post_type = gamipress_get_post_type( $post_id );
        $group = '';
        $type = '';

        if( in_array( $post_type, gamipress_get_achievement_types() ) ) {
            // Achievement
            $group = 'achievement_types';
            $type = $post_type;
        } else if( in_array( $post_type, gamipress_get_rank_types() ) ) {
            // Rank
            $group = 'rank_types';
            $type = $post_type;
        } else if( $post_type === 'step' ) {
            // Step
            $group = 'achievement_types';

            $achievement = gamipress_get_step_achievement( $post_id );

            if( $achievement ) {
                $type = $achievement->post_type;
            }

        } else if( $post_type === 'rank-requirement' ) {
            // Rank requirement
            $group = 'rank_types';

            $rank = gamipress_get_rank_requirement_rank( $post_id );

            if( $rank ) {
                $type = $rank->post_type;
            }

        } else if( $post_type === 'points-award' || $post_type === 'points-deduct' ) {
            // Points Award/Deduct
            $group = 'points_types';

            $points_type = gamipress_get_points_award_points_type( $post_id );

            if( $points_type ) {
                $type = $points_type->post_name;
            }
        }

        // Check if emails has been disabled for the group (points, achievements or ranks) or for the type
        if( ( isset( $user_email_settings[$group] ) && $user_email_settings[$group] === 'no' )
            || ( isset( $user_email_settings[$group . '_' . $type] ) && $user_email_settings[$group . '_' . $type] === 'no' ) ) {
            $disabled = true;
        }
    }

    /**
     * Check if email for a specific type is disabled via the user email settings
     *
     * @since 2.2.1
     *
     * @param int $user_id The user ID
     * @param int $post_id The post ID to check if emails are disabled or not
     *
     * @return bool
     */
    return apply_filters( 'gamipress_is_email_disabled_from_user_settings', $disabled, $user_id, $post_id );

}