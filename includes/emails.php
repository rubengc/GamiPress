<?php
/**
 * Emails
 *
 * @package     GamiPress\Emails
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
 * Get an array of email pattern tags
 *
 * @since 1.3.0
 *
 * @return array The registered email pattern tags
 */
function gamipress_get_email_pattern_tags() {

    return apply_filters( 'gamipress_email_pattern_tags', array(
        '{site_title}'          =>  __( 'Site name.', 'gamipress' ),
        '{site_link}'           =>  __( 'Link to the site with site name as text.', 'gamipress' ),
        '{user}'                =>  __( 'User display name.', 'gamipress' ),
        '{user_first}'          =>  __( 'User first name.', 'gamipress' ),
        '{user_last}'           =>  __( 'User last name.', 'gamipress' ),
    ) );

}

/**
 * Get an array of email pattern tags used on achievement earned email
 *
 * @since 1.3.0
 *
 * @return array The registered achievement earned email pattern tags
 */
function gamipress_get_achievement_earned_email_pattern_tags() {

    $email_pattern_tags = gamipress_get_email_pattern_tags();

    return apply_filters( 'gamipress_achievement_earned_email_pattern_tags', array_merge( $email_pattern_tags, array(
        '{achievement_title}'   =>  __(  'Achievement title.', 'gamipress' ),
        '{achievement_excerpt}'   =>  __(  'Achievement excerpt.', 'gamipress' ),
        '{achievement_image}'   =>  __(  'Achievement featured image.', 'gamipress' ),
        '{achievement_steps}'   =>  __(  'Achievement steps.', 'gamipress' ),
        '{achievement_type}'    =>  __(  'Type of the achievement.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on step completed email
 *
 * @since 1.3.0
 *
 * @return array The registered step completed email pattern tags
 */
function gamipress_get_step_completed_email_pattern_tags() {

    $email_pattern_tags = gamipress_get_email_pattern_tags();

    return apply_filters( 'gamipress_step_completed_email_pattern_tags', array_merge( $email_pattern_tags, array(
        '{label}'               =>  __(  'Step label.', 'gamipress' ),
        '{achievement_title}'   =>  __(  'Step Achievement title.', 'gamipress' ),
        '{achievement_excerpt}' =>  __(  'Step Achievement excerpt.', 'gamipress' ),
        '{achievement_image}'   =>  __(  'Step Achievement featured image.', 'gamipress' ),
        '{achievement_steps}'   =>  __(  'Step Achievement steps.', 'gamipress' ),
        '{achievement_type}'    =>  __(  'Type of the step achievement.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on points award completed email
 *
 * @since 1.3.0
 *
 * @return array The registered points award completed email pattern tags
 */
function gamipress_get_points_award_completed_email_pattern_tags() {

    $email_pattern_tags = gamipress_get_email_pattern_tags();

    return apply_filters( 'gamipress_points_award_completed_email_pattern_tags', array_merge( $email_pattern_tags, array(
        '{label}'           =>  __( 'Points award label.', 'gamipress' ),
        '{points}'          =>  __( 'The amount of points earned.', 'gamipress' ),
        '{points_balance}'  =>  __( 'The full amount of points user has earned until this date.', 'gamipress' ),
        '{points_type}'     =>  __( 'The points award points type. Singular or plural is based on the amount of points earned.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on points deduct completed email
 *
 * @since 1.3.7
 *
 * @return array The registered points deduct completed email pattern tags
 */
function gamipress_get_points_deduct_completed_email_pattern_tags() {

    $email_pattern_tags = gamipress_get_email_pattern_tags();

    return apply_filters( 'gamipress_points_deduct_completed_email_pattern_tags', array_merge( $email_pattern_tags, array(
        '{label}'           =>  __( 'Points deduct label.', 'gamipress' ),
        '{points}'          =>  __( 'The amount of points deducted.', 'gamipress' ),
        '{points_balance}'  =>  __( 'The full amount of points user has earned until this date.', 'gamipress' ),
        '{points_type}'     =>  __( 'The points deduct points type. Singular or plural is based on the amount of points deducted.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on rank earned email
 *
 * @since 1.3.1
 *
 * @return array The registered rank earned email pattern tags
 */
function gamipress_get_rank_earned_email_pattern_tags() {

    $email_pattern_tags = gamipress_get_email_pattern_tags();

    return apply_filters( 'gamipress_rank_earned_email_pattern_tags', array_merge( $email_pattern_tags, array(
        '{rank_title}'          =>  __(  'Rank title.', 'gamipress' ),
        '{rank_excerpt}'        =>  __(  'Rank excerpt.', 'gamipress' ),
        '{rank_image}'          =>  __(  'Rank featured image.', 'gamipress' ),
        '{rank_requirements}'   =>  __(  'Rank requirements.', 'gamipress' ),
        '{rank_type}'           =>  __(  'Type of the rank.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on rank requirement completed email
 *
 * @since 1.3.1
 *
 * @return array The registered rank requirement completed email pattern tags
 */
function gamipress_get_rank_requirement_completed_email_pattern_tags() {

    $email_pattern_tags = gamipress_get_email_pattern_tags();

    return apply_filters( 'gamipress_step_completed_email_pattern_tags', array_merge( $email_pattern_tags, array(
        '{label}'               =>  __(  'Requirement label.', 'gamipress' ),
        '{rank_title}'          =>  __(  'Requirement rank title.', 'gamipress' ),
        '{rank_excerpt}'        =>  __(  'Requirement rank excerpt.', 'gamipress' ),
        '{rank_image}'          =>  __(  'Requirement rank featured image.', 'gamipress' ),
        '{rank_requirements}'   =>  __(  'Requirement rank requirements.', 'gamipress' ),
        '{rank_type}'           =>  __(  'Type of the rank.', 'gamipress' ),
    ) ) );

}

/**
 * Get a string with the desired email pattern tags html markup
 *
 * @since 1.3.0
 *
 * @param string $email
 *
 * @return string Log pattern tags html markup
 */
function gamipress_get_email_pattern_tags_html( $email = '' ) {

    if( $email === 'achievement_earned' ) {
        $email_pattern_tags = gamipress_get_achievement_earned_email_pattern_tags();
    } else if( $email === 'step_completed' ) {
        $email_pattern_tags = gamipress_get_step_completed_email_pattern_tags();
    } else if( $email === 'points_award_completed' ) {
        $email_pattern_tags = gamipress_get_points_award_completed_email_pattern_tags();
    } else if( $email === 'points_deduct_completed' ) {
        $email_pattern_tags = gamipress_get_points_deduct_completed_email_pattern_tags();
    } else if( $email === 'rank_earned' ) {
        $email_pattern_tags = gamipress_get_rank_earned_email_pattern_tags();
    } else if( $email === 'rank_requirement_completed' ) {
        $email_pattern_tags = gamipress_get_rank_requirement_completed_email_pattern_tags();
    } else {
        $email_pattern_tags = gamipress_get_email_pattern_tags();
    }

    $output = '<ul class="gamipress-pattern-tags-list">';

    foreach( $email_pattern_tags as $tag => $description ) {

        $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

        $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
    }

    $output .= '</ul>';

    return $output;

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

    // Use WordPress email function
    $sent = wp_mail( $to, $subject, $message, $headers, $attachments );

    // Check for log errors
    $log_errors = apply_filters( 'gamipress_log_email_errors', true, $to, $subject, $message );

    if( ! $sent && $log_errors === true) {

        if ( is_array( $to ) ) {
            $to = implode( ',', $to );
        }

        $log_message = sprintf(
            __( "[GamiPress] Email failed to send.\nSend time: %s\nTo: %s\nSubject: %s\n\n", 'gamipress' ),
            date_i18n( 'F j Y H:i:s', current_time( 'timestamp' ) ),
            $to,
            $subject
        );

        error_log( $log_message );

    }

    // Return WordPress email function response
    return $sent;

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

    // Setup site replacements
    $replacements = array(
        '{site_title}'  => get_bloginfo( 'name' ),
        '{site_link}'   =>  '<a href="' . esc_url( home_url() ) . '">' . get_bloginfo( 'name' ) . '</a>',
    );

    // Setup user replacements
    $user = get_userdata( $gamipress_email_template_args['user_id'] );

    if( $user ) {

        $replacements['{user}']         =  $user->display_name;
        $replacements['{user_first}']   =  $user->first_name;
        $replacements['{user_last}']    =  $user->last_name;

    } else {

        $replacements['{user}']         =  '';
        $replacements['{user_first}']   =  '';
        $replacements['{user_last}']    =  '';

    }

    if( $gamipress_email_template_args['type'] === 'achievement_earned' && isset( $gamipress_email_template_args['achievement_id'] ) ) {

        // Get the achievement post object
        $achievement = get_post( $gamipress_email_template_args['achievement_id'] );

        if( $achievement ) {

            $achievement_types = gamipress_get_achievement_types();
            $achievement_type = $achievement_types[$achievement->post_type];

            $achievement_steps_html = '';

            $steps = gamipress_get_required_achievements_for_achievement( $achievement->ID );

            if( count( $steps ) ) {

                $list_tag = gamipress_is_achievement_sequential( $achievement->ID ) ? 'ol' : 'ul';

                $achievement_steps_html .= "<{$list_tag}>";

                foreach( $steps as $step ) {
                    // check if user has earned this Achievement, and add an 'earned' class
                    $earned = count( gamipress_get_user_achievements( array(
                        'user_id' => absint( $user->ID ),
                        'achievement_id' => absint( $step->ID ),
                        'since' => absint( gamipress_achievement_last_user_activity( $achievement->ID, $user->ID ) )
                    ) ) ) > 0;

                    $title = $step->post_title;

                    $achievement_steps_html .= '<li style="' . ( $earned ? 'text-decoration: line-through;' : '' ) . '">' . $title . '</li>';
                }

                $achievement_steps_html .= "</{$list_tag}>";
            }

            $replacements['{achievement_title}'] = $achievement->post_title;
            $replacements['{achievement_excerpt}'] = $achievement->post_excerpt;
            $replacements['{achievement_image}'] = gamipress_get_achievement_post_thumbnail( $achievement->ID );
            $replacements['{achievement_steps}'] = $achievement_steps_html;
            $replacements['{achievement_type}'] = $achievement_type['singular_name'];

        }

    } else if( $gamipress_email_template_args['type'] === 'step_completed' && isset( $gamipress_email_template_args['step_id'] ) ) {

        // Get the step post object
        $step = get_post( $gamipress_email_template_args['step_id'] );

        if( $step ) {
            $replacements['{label}'] = $step->post_title;

            // Get the step achievement to parse their tags
            $achievement = gamipress_get_parent_of_achievement( $step->ID );

            if( $achievement ) {
                $gamipress_email_template_args['achievement_id'] = $achievement->ID;

                // Set a temporal type to parse achievement tags
                $gamipress_email_template_args['type'] = 'achievement_earned';

                $content = gamipress_parse_email_tags( $content, $to, $subject, $message, $attachments );

                // Restore the original type
                $gamipress_email_template_args['type'] = 'step_completed';
            }
        }

    } else if( $gamipress_email_template_args['type'] === 'points_award_completed' && isset( $gamipress_email_template_args['points_award_id'] ) ) {

        // Get the points award post object
        $points_award = get_post( $gamipress_email_template_args['points_award_id'] );

        if( $points_award ) {
            $replacements['{label}'] = $points_award->post_title;

            // Get the points type to allow specific points type template
            $points_type = gamipress_get_points_award_points_type( $points_award->ID );

            if( $points_type ) {

                // Setup vars
                $points = absint( get_post_meta( $points_award->ID, '_gamipress_points', true ) );
                $singular = $points_type->post_title;
                $plural = get_post_meta( $points_type->ID, '_gamipress_plural_name', true );
                $points_balance = gamipress_get_user_points( $user->ID, $points_type->post_name );

                $replacements['{points}'] = $points;
                $replacements['{points_balance}'] = $points_balance;
                $replacements['{points_type}'] = _n( $singular, $plural, $points );

            }
        }

    } else if( $gamipress_email_template_args['type'] === 'points_deduct_completed' && isset( $gamipress_email_template_args['points_deduct_id'] ) ) {

        // Get the points deduct post object
        $points_deduct = get_post( $gamipress_email_template_args['points_deduct_id'] );

        if( $points_deduct ) {
            $replacements['{label}'] = $points_deduct->post_title;

            // Get the points type to allow specific points type template
            $points_type = gamipress_get_points_deduct_points_type( $points_deduct->ID );

            if( $points_type ) {

                // Setup vars
                $points = absint( get_post_meta( $points_deduct->ID, '_gamipress_points', true ) );
                $singular = $points_type->post_title;
                $plural = get_post_meta( $points_type->ID, '_gamipress_plural_name', true );
                $points_balance = gamipress_get_user_points( $user->ID, $points_type->post_name );

                $replacements['{points}'] = $points;
                $replacements['{points_balance}'] = $points_balance;
                $replacements['{points_type}'] = _n( $singular, $plural, $points );

            }
        }

    } else if( $gamipress_email_template_args['type'] === 'rank_earned' && isset( $gamipress_email_template_args['rank_id'] ) ) {

        // Get the rank post object
        $rank = get_post( $gamipress_email_template_args['rank_id'] );

        if( $rank ) {

            $rank_requirements_html = '';

            $requirements = gamipress_get_rank_requirements( $rank->ID );

            if( count( $requirements ) ) {

                $list_tag = gamipress_is_achievement_sequential( $rank->ID ) ? 'ol' : 'ul';

                $rank_requirements_html .= "<{$list_tag}>";

                foreach( $requirements as $requirement ) {
                    // check if user has earned this requirement, and add an 'earned' class
                    $earned = count( gamipress_get_user_achievements( array(
                            'user_id' => absint( $user->ID ),
                            'achievement_id' => absint( $requirement->ID ),
                            'since' => absint( gamipress_achievement_last_user_activity( $requirement->ID, $user->ID ) )
                        ) ) ) > 0;

                    $title = $requirement->post_title;

                    $rank_requirements_html .= '<li style="' . ( $earned ? 'text-decoration: line-through;' : '' ) . '">' . $title . '</li>';
                }

                $rank_requirements_html .= "</{$list_tag}>";
            }

            $replacements['{rank_title}'] = $rank->post_title;
            $replacements['{rank_excerpt}'] = $rank->post_excerpt;
            $replacements['{rank_image}'] = gamipress_get_rank_post_thumbnail( $rank->ID );
            $replacements['{rank_requirements}'] = $rank_requirements_html;
            $replacements['{rank_type}'] = gamipress_get_rank_type_singular( $rank->post_type );

        }

    } else if( $gamipress_email_template_args['type'] === 'rank_requirement_completed' && isset( $gamipress_email_template_args['rank_requirement_id'] ) ) {

        // Get the rank requirement post object
        $requirement = get_post( $gamipress_email_template_args['rank_requirement_id'] );

        if( $requirement ) {
            $replacements['{label}'] = $requirement->post_title;

            // Get the requirement rank to parse their tags
            $rank = gamipress_get_rank_requirement_rank( $requirement->ID );

            if( $rank ) {
                $gamipress_email_template_args['rank_id'] = $rank->ID;

                // Set a temporal type to parse rank tags
                $gamipress_email_template_args['type'] = 'rank_earned';

                $content = gamipress_parse_email_tags( $content, $to, $subject, $message, $attachments );

                // Restore the original type
                $gamipress_email_template_args['type'] = 'rank_requirement_completed';
            }
        }

    }

    /**
     * Parse email tags
     *
     * @since 1.3.4
     *
     * @param array     $replacements
     * @param WP_User   $user
     * @param array     $template_args
     */
    $replacements = apply_filters( 'gamipress_parse_email_tags', $replacements, $user, $gamipress_email_template_args );

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
        '{user}'                =>  $user->display_name,
        '{user_first}'          =>  $user->first_name,
        '{user_last}'           =>  $user->last_name,
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

        $replacements['{achievement_title}'] = __( 'Sample Achievement', 'gamipress' );
        $replacements['{achievement_excerpt}'] = __( 'Sample Achievement Excerpt', 'gamipress' );
        $replacements['{achievement_image}'] = '<div style="' . $img_placeholder_style . '">100x100</div>';
        $replacements['{achievement_steps}'] = '<ul>'
                . '<li>' . __(  'Not earned achievement step.', 'gamipress' ) . '</li>'
                . '<li style="text-decoration: line-through;">' . __(  'Earned achievement step.', 'gamipress' ) . '</li>'
            . '</ul>';
        $replacements['{achievement_type}'] = __( 'Sample Achievement Type', 'gamipress' );

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

    } else if( $gamipress_email_template_args['type'] === 'points_deduct_completed' ) {

        $replacements['{label}'] = __( 'Sample Points Deduct Label', 'gamipress' );
        $replacements['{points}'] = 100;
        $replacements['{points_balance}'] = 1000;
        $replacements['{points_type}'] = __( 'Sample Points Type', 'gamipress' );

    } else if( $gamipress_email_template_args['type'] === 'rank_earned' ) {

        $replacements['{rank_title}'] = __( 'Sample Rank', 'gamipress' );
        $replacements['{rank_excerpt}'] = __( 'Sample Rank Excerpt', 'gamipress' );
        $replacements['{rank_image}'] = '<div style="' . $img_placeholder_style . '">100x100</div>';
        $replacements['{rank_requirements}'] = '<ul>'
            . '<li>' . __(  'Not completed rank requirement.', 'gamipress' ) . '</li>'
            . '<li style="text-decoration: line-through;">' . __(  'Completed rank requirement.', 'gamipress' ) . '</li>'
            . '</ul>';
        $replacements['{rank_type}'] = __( 'Sample Rank Type', 'gamipress' );

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
 * @param $user_id
 * @param $achievement_id
 * @param $trigger
 * @param $site_id
 * @param $args
 */
function gamipress_maybe_send_email_to_user( $user_id, $achievement_id, $trigger, $site_id, $args ) {

    global $gamipress_email_template_args;

    // Setup out achievement types
    $achievement_types = gamipress_get_achievement_types_slugs();

    // Get the achievement type
    $achievement_type = get_post_type( $achievement_id );

    if( in_array( $achievement_type, $achievement_types ) ) {

        if( (bool) apply_filters( 'gamipress_disable_achievement_earned_email', gamipress_get_option( 'disable_achievement_earned_email', false ), $user_id, $achievement_id ) ) {
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

        $achievement = gamipress_get_parent_of_achievement( $achievement_id );

        // Check if step was assigned to an achievement
        if( ! $achievement ) {
            return;
        }

        if( (bool) apply_filters( 'gamipress_disable_step_completed_email', gamipress_get_option( 'disable_step_completed_email', false ), $user_id, $achievement_id, $achievement ) ) {
            return;
        }

        $all_steps_earned = true;
        $steps = gamipress_get_required_achievements_for_achievement( $achievement->ID );

        // Just loop if achievement has more than 1 step
        if( count( $steps ) > 1 ) {

            foreach( $steps as $step ) {
                // check if user has earned this step
                $earned = count( gamipress_get_user_achievements( array(
                        'user_id' => absint( $user_id ),
                        'achievement_id' => absint( $step->ID ),
                        'since' => absint( gamipress_achievement_last_user_activity( $achievement->ID, $user_id ) )
                    ) ) ) > 0;

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

        $all_requirements_earned = true;
        $requirements = gamipress_get_rank_requirements( $rank->ID );

        // Just loop if rank has more than 1 requirement
        if( count( $requirements ) > 1 ) {

            foreach( $requirements as $requirement ) {
                // Check if user has earned this requirement
                $earned = count( gamipress_get_user_achievements( array(
                        'user_id' => absint( $user_id ),
                        'achievement_id' => absint( $requirement->ID ),
                        'since' => absint( gamipress_achievement_last_user_activity( $requirement->ID, $user_id ) )
                    ) ) ) > 0;

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
add_action( 'gamipress_award_achievement', 'gamipress_maybe_send_email_to_user', 10, 5 );

/**
 * Function that check for each awarded rank if it should be emailed to the user
 *
 * @since   1.3.1
 * @updated 1.3.8 Added filters to allow override anything
 *
 * @param $user_id
 * @param $new_rank
 * @param $old_rank
 * @param $admin_id
 * @param $achievement_id
 */
function gamipress_maybe_send_email_to_user_for_rank_earned( $user_id, $new_rank, $old_rank, $admin_id = 0, $achievement_id = null ) {

    global $gamipress_email_template_args;

    if( (bool) apply_filters( 'gamipress_disable_rank_earned_email', gamipress_get_option( 'disable_rank_earned_email', false ), $user_id, $new_rank ) ) {
        return;
    }

    $gamipress_email_template_args = array(
        'user_id' => $user_id,
        'rank_id' => $new_rank->ID,
        'type' => 'rank_earned',
    );

    $user = get_userdata( $user_id );

    // Available filters to allow override subject
    $subject = apply_filters( 'gamipress_rank_earned_email_subject', gamipress_get_option( 'rank_earned_email_subject' ), $user_id, $new_rank );
    $message = apply_filters( 'gamipress_rank_earned_email_content', gamipress_get_option( 'rank_earned_email_content' ), $user_id, $new_rank );

    gamipress_send_email( $user->user_email, $subject, $message );
}
add_action( 'gamipress_update_user_rank', 'gamipress_maybe_send_email_to_user_for_rank_earned', 10, 5 );