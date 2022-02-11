<?php
/**
 * Admin Dashboard Page
 *
 * @package     GamiPress\Admin\Dashboard
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       2.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Dashboard page
 *
 * @since  2.0.0
 */
function gamipress_dashboard_page() {
    ?>
    <div class="wrap gamipress-dashboard">

        <div id="icon-options-general" class="icon32"></div>
        <h1 class="wp-heading-inline"><?php _e( 'Dashboard', 'gamipress' ); ?></h1>
        <hr class="wp-header-end">

        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="metabox-holder">

                <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                    <?php // Logo ?>
                    <div class="gamipress-dashboard-logo">
                        <img src="<?php echo GAMIPRESS_URL . 'assets/img/gamipress-brand-logo.svg' ?>" alt="GamiPress">
                        <strong class="gamipress-dashboard-version">v<?php echo GAMIPRESS_VER; ?></strong>
                    </div>

                    <?php // Welcome ?>
                    <?php gamipress_dashboard_box( array(
                        'id' => 'welcome',
                        'title' => __( 'Welcome to GamiPress', 'gamipress' ),
                        'content_cb' => 'gamipress_dashboard_welcome_box',
                    ) ); ?>

                </div>

                <div id="postbox-container-1" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <?php // Documentation ?>
                        <?php gamipress_dashboard_box( array(
                            'id' => 'docs',
                            'title' => __( 'Documentation', 'gamipress' ),
                            'content_cb' => 'gamipress_dashboard_docs_box',
                        ) ); ?>

                    </div>
                </div>

                <div id="postbox-container-2" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <?php // Blocks ?>
                        <?php gamipress_dashboard_box( array(
                            'id' => 'blocks',
                            'title' => __( 'Blocks, Shortcodes and Widgets', 'gamipress' ),
                            'content_cb' => 'gamipress_dashboard_blocks_box',
                        ) ); ?>

                    </div>
                </div>

                <div id="postbox-container-3" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <?php // Team ?>
                        <?php gamipress_dashboard_box( array(
                            'id' => 'team',
                            'title' => __( 'Meet the team', 'gamipress' ),
                            'content_cb' => 'gamipress_dashboard_team_box',
                        ) ); ?>

                    </div>
                </div>

                <div id="postbox-container-4" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                    <?php // Get involved ?>
                    <?php gamipress_dashboard_box( array(
                            'id' => 'social',
                            'title' => __( 'Follow us', 'gamipress' ),
                            'content_cb' => 'gamipress_dashboard_social_box',
                    ) ); ?>

                    </div>
                </div>

            </div>
        </div>

    </div>
    <?php
}

/**
 * Dashboard page
 *
 * @since  2.0.0
 */
function gamipress_dashboard_box( $args ) {

    $args = wp_parse_args( $args, array(
        'id' => '',
        'title' => '',
        'content' => '',
        'content_cb' => '',
    ) );

    ?>
        <div id="gamipress-dashboard-<?php echo $args['id']; ?>" class="gamipress-dashboard-box postbox">

            <div class="postbox-header">
                <h2 class="hndle"><?php echo $args['title']; ?></h2>
            </div>

            <div class="inside">

                <?php if( is_callable( $args['content_cb'] ) ) {
                    call_user_func( $args['content_cb'] );
                } else {
                    echo $args['content'];
                } ?>

            </div>

        </div>
    <?php

}

/**
 * Dashboard welcome box
 *
 * @since  2.0.0
 */
function gamipress_dashboard_welcome_box() {
    ?>
    <div class="gamipress-dashboard-columns">

        <div class="gamipress-dashboard-column gamipress-dashboard-main-video">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/sinW2JjxsdA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>

        <div class="gamipress-dashboard-column gamipress-dashboard-videos-list">
            <h3><?php _e( 'More videos', 'gamipress' ) ?></h3>
            <div class="gamipress-dashboard-videos">
                <?php
                $videos = array(
                    array(
                        'id' => '-UdktkgGfaU',
                        'title' => 'Creating a points type',
                        'duration' => '2:14',
                    ),
                    array(
                        'id' => 'W52HxozyN5g',
                        'title' => 'Creating an achievement type',
                        'duration' => '3:32',
                    ),
                    array(
                        'id' => 'oh3MFdAy_xc',
                        'title' => ' Creating a rank type',
                        'duration' => '3:58',
                    ),
                    array(
                        'id' => 'JInrhMLQ7aw',
                        'title' => 'Unlock achievements and ranks by expending points',
                        'duration' => '1:51',
                    ),
                );

                foreach( $videos as $video ) { ?>
                    <div class="gamipress-dashboard-video">
                        <a href="https://www.youtube.com/watch?v=<?php echo $video['id']; ?>" target="_blank">
                            <div class="gamipress-dashboard-video-image">
                                <img src="https://img.youtube.com/vi/<?php echo $video['id']; ?>/default.jpg" alt="">
                            </div>
                            <div class="gamipress-dashboard-video-details">
                                <strong class="gamipress-dashboard-video-title"><?php echo $video['title']; ?></strong>
                                <div class="gamipress-dashboard-video-duration"><?php echo $video['duration']; ?></div>
                            </div>
                        </a>
                    </div>
                <?php }

                ?>
            </div>
            <div class="gamipress-dashboard-more-videos">
                <a href="https://www.youtube.com/channel/UC292zdjiKv6C2u3sBOdSNhg/videos" target="_blank"><?php _e( 'View all videos', 'gamipress' ); ?></a>
            </div>
        </div>

        <div class="gamipress-dashboard-column gamipress-dashboard-get-involved">
            <p><?php _e( 'GamiPress is a free and open-source plugin accessible to everyone just like WordPress. There are many ways you can help support GamiPress', 'gamipress' ); ?></p>
            <ul>
                <li><a href="https://github.com/rubengc/GamiPress" target="_blank"><i class="dashicons dashicons-admin-tools"></i> <?php _e( 'Get involved with GamiPress development.', 'gamipress' ); ?></a></li>
                <li><a href="https://translate.wordpress.org/projects/wp-plugins/gamipress/" target="_blank"><i class="dashicons dashicons-translation"></i> <?php _e( 'Translate GamiPress into your language.', 'gamipress' ); ?></a></li>
                <li><a href="https://wordpress.org/plugins/gamipress/#reviews" target="_blank"><i class="dashicons dashicons-wordpress"></i> <?php _e( 'Review GamiPress on WordPress.org.', 'gamipress' ); ?></a></li>
            </ul>
            <p><?php _e( 'Pro add-ons help to maintain the project and offer the most advanced features.', 'gamipress' ); ?></p>
            <div class="gamipress-dashboard-pricing-button">
                <a href="https://gamipress.com/pricing/" target="_blank" class="button button-primary"><?php _e( 'View plans and pricing', 'gamipress' ); ?></a>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Dashboard docs box
 *
 * @since  2.0.0
 */
function gamipress_dashboard_docs_box() {
    ?>
    <ul>
        <li><a href="https://gamipress.com/docs/getting-started/what-is-gamipress/" target="_blank"><?php _e( 'What is GamiPress?', 'gamipress' ); ?></a></li>
        <li><a href="https://gamipress.com/docs/getting-started/" target="_blank"><?php _e( 'Getting started', 'gamipress' ); ?></a></li>
        <li><a href="https://gamipress.com/docs/tutorials/" target="_blank"><?php _e( 'Tutorials', 'gamipress' ); ?></a></li>
        <li><a href="https://gamipress.com/docs/advanced/" target="_blank"><?php _e( 'Advanced', 'gamiress' ); ?></a></li>
        <li><a href="https://gamipress.com/docs/settings/" target="_blank"><?php _e( 'Settings', 'gamipress' ); ?></a></li>
        <li><a href="https://gamipress.com/docs/tools/" target="_blank"><?php _e( 'Tools', 'gamipress' ); ?></a></li>
    </ul>
    <?php
}

/**
 * Dashboard blocks box
 *
 * @since  2.0.0
 */
function gamipress_dashboard_blocks_box() {
    $shortcodes = array(
        'achievement' => __( 'Single Achievement', 'gamipress' ),
        'achievements' => __( 'Achievements', 'gamipress' ),
        'last_achievements_earned' => __( 'Last Achievements Earned', 'gamipress' ),
        'user_points' => __( 'User Points', 'gamipress' ),
        'site_points' => __( 'Site Points', 'gamipress' ),
        'points_types' => __( 'Points Types', 'gamipress' ),
        'rank' => __( 'Single Rank', 'gamipress' ),
        'ranks' => __( 'Ranks', 'gamipress' ),
        'user_rank' => __( 'User Rank', 'gamipress' ),
        'logs' => __( 'Logs', 'gamipress' ),
        'user_earnings' => __( 'User Earnings', 'gamipress' ),
        'email_settings' => __( 'Email Settings', 'gamipress' ),
    );
    ?>
    <ul class="gamipress-dashboard-blocks-list">
        <?php foreach( $shortcodes as $slug => $label ) : ?>
            <li><?php echo $label; ?>: <span><a href="https://gamipress.com/docs/blocks/<?php echo str_replace( '_', '-', $slug ); ?>/" target="_blank"><?php _e( 'Block', 'gamipress' ); ?></a> | <a href="https://gamipress.com/docs/shortcodes/gamipress_<?php echo $slug; ?>/" target="_blank"><?php _e( 'Shortcode', 'gamipress' ); ?></a> | <a href="https://gamipress.com/docs/widgets/<?php echo str_replace( '_', '-', $slug ); ?>/" target="_blank"><?php _e( 'Widget', 'gamipress' ); ?></a></span></li>
        <?php endforeach; ?>
    </ul>
    <?php
}

/**
 * Dashboard team box
 *
 * @since  2.0.0
 */
function gamipress_dashboard_team_box() {
    ?>
    <ul id="contributors-list" class="contributors-list">
        <li>
            <a href="https://profiles.wordpress.org/rubengc/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/103d0ec19ade3804009f105974fd4d05?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Ruben Garcia</span>
            </a>
        </li>
        <li>
            <a href="https://profiles.wordpress.org/eneribs/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/7103ea44d40111ab67a22efe7ebd6f71?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Irene Berna</span>
            </a>
        </li>
        <li>
            <a href="https://profiles.wordpress.org/pacogon/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/348f374779e7433ad6bf3930cb2a492e?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Paco González</span>
            </a>
        </li>
        <li>
            <a href="https://profiles.wordpress.org/dioni00/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/6de68ad3863fdf3c92a194ba16546571?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Dionisio Sanchez</span>
            </a>
        </li>
        <li>
            <a href="https://profiles.wordpress.org/flabernardez/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/fd626d9a8463260894f0f6f07a5cc71a?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Flavia Bernardez</span>
            </a>
        </li>
    </ul>
    <?php
}

/**
 * Dashboard involved box
 *
 * @since  2.0.0
 */
function gamipress_dashboard_social_box() {
    ?>
    <p><?php _e( 'Follow us in your favorite social network!', 'gamipress' ); ?></p>
    <ul class="gamipress-dashboard-social-list">
        <li><a href="https://www.youtube.com/channel/UC292zdjiKv6C2u3sBOdSNhg" target="_blank"><i class="dashicons dashicons-youtube"></i> <?php _e( 'Subscribe to our YouTube channel.', 'gamipress' ); ?></a></li>
        <li><a href="https://www.facebook.com/GamiPress/" target="_blank"><i class="dashicons dashicons-facebook"></i> <?php _e( 'Follow us on Facebook.', 'gamipress' ); ?></a></li>
        <li><a href="https://www.facebook.com/groups/gamipress" target="_blank"><i class="dashicons dashicons-facebook"></i> <?php _e( 'Join our Facebook community.', 'gamipress' ); ?></a></li>
        <li><a href="https://twitter.com/GamiPress" target="_blank"><i class="dashicons dashicons-twitter"></i> <?php _e( 'Follow @GamiPress on Twitter.', 'gamipress' ); ?></a></li>
        <li><a href="https://www.linkedin.com/company/28389774/" target="_blank"><i class="dashicons dashicons-linkedin"></i> <?php _e( 'Follow us on LinkedIn.', 'gamipress' ); ?></a></li>
    </ul>
    <?php
}