<?php
/**
 * Email settings template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/email-settings.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;
$user_settings = gamipress_get_user_email_settings( $a['user_id'] ); ?>

<div class="gamipress-email-settings">

    <?php
    /**
     * Before render email settings
     *
     * @since 2.2.1
     *
     * @param array $template_args Template received arguments
     */
    do_action( 'gamipress_before_render_email_settings', $a ); ?>

    <?php // Loop sections ?>
    <?php foreach( $a['email_settings'] as $section => $section_args ) : ?>

        <table class="gamipress-email-settings-table gamipress-email-settings-table-<?php echo esc_attr( $section ); ?>">
            <thead>
            <tr>
                <th class="gamipress-email-settings-header-setting"><?php echo $section_args['label']; ?></th>
                <th class="gamipress-email-settings-header-yes"><?php _e( 'Yes', 'gamipress' ); ?></th>
                <th class="gamipress-email-settings-header-no"><?php _e( 'No', 'gamipress' ); ?></th>
            </tr>
            </thead>
            <tbody>
                <?php // Loop each section settings ?>
                <?php foreach( $section_args['settings'] as $setting => $label ) :
                    $user_setting = ( isset( $user_settings[$setting] ) ? $user_settings[$setting] : 'yes' ); ?>
                    <tr id="gamipress-email-settings-<?php echo esc_attr( $setting ); ?>">
                        <td class="gamipress-email-settings-column-setting">
                            <div class="gamipress-email-settings-loader" style="display: none;">
                                <div class="gamipress-email-settings-saving"><?php _e( 'Saving...', 'gamipress' ); ?></div>
                                <div class="gamipress-email-settings-saved"><?php _e( 'Saved!', 'gamipress' ); ?></div>
                            </div>
                            <?php echo $label; ?>
                        </td>
                        <td class="gamipress-email-settings-column-yes">
                            <div class="gamipress-email-settings-radio">
                                <input type="radio" name="gamipress_email_settings[<?php echo esc_attr( $setting ); ?>]" value="yes" <?php checked( $user_setting, 'yes' ); ?>>
                            </div>
                        </td>
                        <td class="gamipress-email-settings-column-no">
                            <div class="gamipress-email-settings-radio">
                                <input type="radio" name="gamipress_email_settings[<?php echo esc_attr( $setting ); ?>]" value="no" <?php checked( $user_setting, 'no' ); ?>>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endforeach; ?>

    <?php
    /**
     * After render email settings
     *
     * @since 2.2.1
     *
     * @param array $template_args Template received arguments
     */
    do_action( 'gamipress_after_render_email_settings', $a ); ?>

</div>
