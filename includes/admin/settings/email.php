<?php
/**
 * Admin Email Settings
 *
 * @package     GamiPress\Admin\Settings\Email
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Email Settings meta boxes
 *
 * @since  1.3.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_email_meta_boxes( $meta_boxes ) {

    $meta_boxes['email-settings'] = array(
        'title' => gamipress_dashicon( 'email-alt' ) . __( 'Emails Settings', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_email_settings_fields', array(
            'email_template' => array(
                'name' => __( 'Template', 'gamipress' ),
                'desc' => __( 'The email template to be used.', 'gamipress' ),
                'type' => 'select',
                'options' => gamipress_get_email_templates(),
            ),
            'email_logo' => array(
                'name' => __( 'Email Logo', 'gamipress' ),
                'desc' => __( 'Upload, choose or paste the URL of the logo to be displayed at the top of the emails (not displayed in plain text template).', 'gamipress' ),
                'type' => 'file',
            ),
            'email_from_name' => array(
                'name' => __( 'From Name', 'gamipress' ),
                'desc' => __( 'Name to display as sender.', 'gamipress' ),
                'type' => 'text',
                'default_cb' => 'gamipress_site_name_default_cb',
            ),
            'email_from_address' => array(
                'name' => __( 'From Address', 'gamipress' ),
                'desc' => __( 'This email address will be used as the "from" and "reply-to" address.', 'gamipress' ),
                'type' => 'text',
                'default' => get_bloginfo( 'admin_email' ),
            ),
            'email_footer_text' => array(
                'name' => __( 'Footer Text', 'gamipress' ),
                'desc' => __( 'Text to be shown at email footer.', 'gamipress' ),
                'type' => 'textarea',
                'default' => sprintf( __( '%s - Powered by GamiPress', 'gamipress' ), '<a href="' . esc_url( home_url() ) . '">' . get_bloginfo( 'name' ) . '</a>' ),
            ),
        ) )
    );

    $meta_boxes['email-templates'] = array(
        'title' => gamipress_dashicon( 'editor-table' ) . __( 'Emails Templates', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_email_templates_fields', array(

            // Achievement Earned

            'achievement_earned_email_actions' => array(
                'type' => 'multi_buttons',
                'buttons' => array(
                    'achievement-earned-email-preview' => array(
                        'label' => __( 'Preview Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=preview_achievement_earned_email' ),
                        'target' => '_blank',
                    ),
                    'achievement-earned-email-send' => array(
                        'label' => __( 'Send Test Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=send_test_achievement_earned_email' ),
                        'target' => '_blank',
                    )
                ),
            ),
            'disable_achievement_earned_email' => array(
                'name' => __( 'Disable sending of achievements earned emails', 'gamipress' ),
                'desc' => __( 'Check this option to stop sending emails to users about new achievements earned.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'achievement_earned_email_subject' => array(
                'name' => __( 'Subject', 'gamipress' ),
                'desc' => __( 'Enter the subject line for the achievement earned email.', 'gamipress' ),
                'type' => 'text',
                'default' => __( '[{site_title}] {user_first}, you unlocked the {achievement_type} {achievement_title}', 'gamipress' ),
            ),
            'achievement_earned_email_content' => array(
                'name' => __( 'Content', 'gamipress' ),
                'desc' => __( 'Available tags:', 'gamipress' ) . gamipress_get_pattern_tags_html( 'achievement_earned' ),
                'type' => 'wysiwyg',
                'default' =>
                    '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
                    . '{achievement_image}' . "\n"
                    . __( 'You unlocked the {achievement_type} {achievement_title} by completing the following steps:', 'gamipress' ) . "\n"
                    . '{achievement_steps}' . "\n\n"
                    . __( 'Best regards', 'gamipress' ),
            ),

            // Step Completed

            'step_completed_email_actions' => array(
                'type' => 'multi_buttons',
                'buttons' => array(
                    'step-completed-email-preview' => array(
                        'label' => __( 'Preview Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=preview_step_completed_email' ),
                        'target' => '_blank',
                    ),
                    'step-completed-email-send' => array(
                        'label' => __( 'Send Test Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=send_test_step_completed_email' ),
                        'target' => '_blank',
                    )
                ),
            ),
            'disable_step_completed_email' => array(
                'name' => __( 'Disable sending of completed steps emails', 'gamipress' ),
                'desc' => __( 'Check this option to stop sending emails to users about new steps completed.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'step_completed_email_subject' => array(
                'name' => __( 'Subject', 'gamipress' ),
                'desc' => __( 'Enter the subject line for the step completed email.', 'gamipress' ),
                'type' => 'text',
                'default' => __( '[{site_title}] {user_first}, you complete a step of the {achievement_type} {achievement_title}', 'gamipress' ),
            ),
            'step_completed_email_content' => array(
                'name' => __( 'Content', 'gamipress' ),
                'desc' => __( 'Available tags:', 'gamipress' ) . gamipress_get_pattern_tags_html( 'step_completed' ),
                'type' => 'wysiwyg',
                'default' =>
                    '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
                    . '{achievement_image}' . "\n"
                    . __( 'You completed the step "{label}" of the {achievement_type} {achievement_title}!', 'gamipress' ) . "\n\n"
                    . __( 'You need to complete the following steps to completely unlock this {achievement_type}:', 'gamipress' ) . "\n"
                    . '{achievement_steps}' . "\n\n"
                    . __( 'Best regards', 'gamipress' ),
            ),

            // Points Award Completed

            'points_award_completed_email_actions' => array(
                'type' => 'multi_buttons',
                'buttons' => array(
                    'points-award-completed-email-preview' => array(
                        'label' => __( 'Preview Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=preview_points_award_completed_email' ),
                        'target' => '_blank',
                    ),
                    'points-award-completed-email-send' => array(
                        'label' => __( 'Send Test Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=send_test_points_award_completed_email' ),
                        'target' => '_blank',
                    )
                ),
            ),
            'disable_points_award_completed_email' => array(
                'name' => __( 'Disable sending of points awards emails', 'gamipress' ),
                'desc' => __( 'Check this option to stop sending emails to users about new points awards.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'points_award_completed_email_subject' => array(
                'name' => __( 'Subject', 'gamipress' ),
                'desc' => __( 'Enter the subject line for the points award email.', 'gamipress' ),
                'type' => 'text',
                'default' => __( '[{site_title}] {user_first}, you got {points} {points_type}', 'gamipress' ),
            ),
            'points_award_completed_email_content' => array(
                'name' => __( 'Content', 'gamipress' ),
                'desc' => __( 'Available tags:', 'gamipress' ) . gamipress_get_pattern_tags_html( 'points_award_completed' ),
                'type' => 'wysiwyg',
                'default' =>
                    '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
                    . __( 'You got {points} {points_type} for completing "{label}".', 'gamipress' ) . "\n"
                    . __( 'Your new {points_type} balance is:', 'gamipress' ) . "\n"
                    . '{points_balance}' . "\n\n"
                    . __( 'Best regards', 'gamipress' ),
            ),

            // Points Deduction Completed

            'points_deduct_completed_email_actions' => array(
                'type' => 'multi_buttons',
                'buttons' => array(
                    'points-deduct-completed-email-preview' => array(
                        'label' => __( 'Preview Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=preview_points_deduct_completed_email' ),
                        'target' => '_blank',
                    ),
                    'points-deduct-completed-email-send' => array(
                        'label' => __( 'Send Test Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=send_test_points_deduct_completed_email' ),
                        'target' => '_blank',
                    )
                ),
            ),
            'disable_points_deduct_completed_email' => array(
                'name' => __( 'Disable sending of points deductions emails', 'gamipress' ),
                'desc' => __( 'Check this option to stop sending emails to users about new points deductions.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'points_deduct_completed_email_subject' => array(
                'name' => __( 'Subject', 'gamipress' ),
                'desc' => __( 'Enter the subject line for the points deduct email.', 'gamipress' ),
                'type' => 'text',
                'default' => __( '[{site_title}] {user_first}, you lost {points} {points_type}', 'gamipress' ),
            ),
            'points_deduct_completed_email_content' => array(
                'name' => __( 'Content', 'gamipress' ),
                'desc' => __( 'Available tags:', 'gamipress' ) . gamipress_get_pattern_tags_html( 'points_deduct_completed' ),
                'type' => 'wysiwyg',
                'default' =>
                    '<h2>' . __( 'Oops {user_first}!', 'gamipress' ) . '</h2>' . "\n"
                    . __( 'You lost {points} {points_type} for "{label}".', 'gamipress' ) . "\n"
                    . __( 'Your new {points_type} balance is:', 'gamipress' ) . "\n"
                    . '{points_balance}' . "\n\n"
                    . __( 'Best regards', 'gamipress' ),
            ),

            // Rank Reached

            'rank_earned_email_actions' => array(
                'type' => 'multi_buttons',
                'buttons' => array(
                    'rank-earned-email-preview' => array(
                        'label' => __( 'Preview Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=preview_rank_earned_email' ),
                        'target' => '_blank',
                    ),
                    'rank-earned-email-send' => array(
                        'label' => __( 'Send Test Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=send_test_rank_earned_email' ),
                        'target' => '_blank',
                    )
                ),
            ),
            'disable_rank_earned_email' => array(
                'name' => __( 'Disable sending of ranks earned emails', 'gamipress' ),
                'desc' => __( 'Check this option to stop sending emails to users about new ranks reached.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'rank_earned_email_subject' => array(
                'name' => __( 'Subject', 'gamipress' ),
                'desc' => __( 'Enter the subject line for the rank earned email.', 'gamipress' ),
                'type' => 'text',
                'default' => __( '[{site_title}] {user_first}, you reached the {rank_type} {rank_title}', 'gamipress' ),
            ),
            'rank_earned_email_content' => array(
                'name' => __( 'Content', 'gamipress' ),
                'desc' => __( 'Available tags:', 'gamipress' ) . gamipress_get_pattern_tags_html( 'rank_earned' ),
                'type' => 'wysiwyg',
                'default' =>
                    '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
                    . '{rank_image}' . "\n"
                    . __( 'You reached the {rank_type} {rank_title} by completing the following requirements:', 'gamipress' ) . "\n"
                    . '{rank_requirements}' . "\n\n"
                    . __( 'Best regards', 'gamipress' ),
            ),

            // Rank Requirement Completed

            'rank_requirement_completed_email_actions' => array(
                'type' => 'multi_buttons',
                'buttons' => array(
                    'rank-requirement-completed-email-preview' => array(
                        'label' => __( 'Preview Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=preview_rank_requirement_completed_email' ),
                        'target' => '_blank',
                    ),
                    'rank-requirement-completed-email-send' => array(
                        'label' => __( 'Send Test Email', 'gamipress' ),
                        'type' => 'link',
                        'link' => admin_url( 'admin.php?gamipress-action=send_test_rank_requirement_completed_email' ),
                        'target' => '_blank',
                    )
                ),
            ),
            'disable_rank_requirement_completed_email' => array(
                'name' => __( 'Disable sending of rank requirements completed emails', 'gamipress' ),
                'desc' => __( 'Check this option to stop sending emails to users about new rank requirements completed.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'rank_requirement_completed_email_subject' => array(
                'name' => __( 'Subject', 'gamipress' ),
                'desc' => __( 'Enter the subject line for the rank requirement completed email.', 'gamipress' ),
                'type' => 'text',
                'default' => __( '[{site_title}] {user_first}, you complete a requirement of the {rank_type} {rank_title}', 'gamipress' ),
            ),
            'rank_requirement_completed_email_content' => array(
                'name' => __( 'Content', 'gamipress' ),
                'desc' => __( 'Available tags:', 'gamipress' ) . gamipress_get_pattern_tags_html( 'rank_requirement_completed' ),
                'type' => 'wysiwyg',
                'default' =>
                    '<h2>' . __( 'Congratulations {user_first}!', 'gamipress' ) . '</h2>' . "\n"
                    . '{rank_image}' . "\n"
                    . __( 'You completed the requirement "{label}" of the {rank_type} {rank_title}!', 'gamipress' ) . "\n\n"
                    . __( 'You need to complete the following requirements to completely reach this {rank_type}:', 'gamipress' ) . "\n"
                    . '{rank_requirements}' . "\n\n"
                    . __( 'Best regards', 'gamipress' ),
            ),

        ) ),
        'vertical_tabs' => true,
        'tabs' => apply_filters( 'gamipress_email_templates_tabs', array(
            'achievement_earned' => array(
                'title' => __( 'Achievements', 'gamipress' ),
                'icon' => 'dashicons-awards',
                'fields' => array(
                    'achievement_earned_email_actions',
                    'disable_achievement_earned_email',
                    'achievement_earned_email_subject',
                    'achievement_earned_email_content'
                )
            ),
            'step_completed' => array(
                'title' => __( 'Steps', 'gamipress' ),
                'icon' => 'dashicons-editor-ol',
                'fields' => array(
                    'step_completed_email_actions',
                    'disable_step_completed_email',
                    'step_completed_email_subject',
                    'step_completed_email_content'
                )
            ),
            'points_award_completed' => array(
                'title' => __( 'Points Awards', 'gamipress' ),
                'icon' => 'dashicons-star-filled',
                'fields' => array(
                    'points_award_completed_email_actions',
                    'disable_points_award_completed_email',
                    'points_award_completed_email_subject',
                    'points_award_completed_email_content'
                )
            ),
            'points_deduct_completed' => array(
                'title' => __( 'Points Deductions', 'gamipress' ),
                'icon' => 'dashicons-star-empty',
                'fields' => array(
                    'points_deduct_completed_email_actions',
                    'disable_points_deduct_completed_email',
                    'points_deduct_completed_email_subject',
                    'points_deduct_completed_email_content'
                )
            ),
            'rank_earned' => array(
                'title' => __( 'Ranks', 'gamipress' ),
                'icon' => 'dashicons-rank',
                'fields' => array(
                    'rank_earned_email_actions',
                    'disable_rank_earned_email',
                    'rank_earned_email_subject',
                    'rank_earned_email_content'
                )
            ),
            'rank_requirement_completed' => array(
                'title' => __( 'Rank Requirements', 'gamipress' ),
                'icon' => 'dashicons-editor-ol',
                'fields' => array(
                    'rank_requirement_completed_email_actions',
                    'disable_rank_requirement_completed_email',
                    'rank_requirement_completed_email_subject',
                    'rank_requirement_completed_email_content'
                )
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_email_meta_boxes', 'gamipress_settings_email_meta_boxes' );