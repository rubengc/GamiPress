<?php
/**
 * Single Achievement template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/single-achievement.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/single-achievement-{achievement-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

// Check if user has earned this Achievement, and add an 'earned' class
$earned = gamipress_has_user_earned_achievement( get_the_ID(), get_current_user_id() );

// Setup achievement classes
$classes = array(
    'single-achievement',
    'achievement-wrap',
    ( $earned ? 'user-has-earned' : '' ),
    'gamipress-layout-' . $a['layout'],
    'gamipress-align-' . $a['align']
);

/**
 * Single achievement classes
 *
 * @since 1.4.0
 *
 * @param array     $classes            Array of achievement classes
 * @param integer   $achievement_id     The Achievement ID
 * @param array     $template_args      Template received arguments
 */
$classes = apply_filters( 'gamipress_single_achievement_classes', $classes, get_the_ID(), $a ); ?>

<?php // Check if current user has earned this achievement
echo gamipress_render_earned_achievement_text( get_the_ID(), get_current_user_id() ); ?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

    <?php
    /**
     * Before single achievement
     *
     * @since 1.0.0
     *
     * @param integer $achievement_id   The Achievement ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_before_single_achievement', get_the_ID(), $a ); ?>

    <div class="gamipress-achievement-image">
        <?php // Thumbnail
        echo gamipress_get_achievement_post_thumbnail( get_the_ID() ); ?>

        <?php // Share
        echo gamipress_achievement_share_markup( get_the_ID(), $a ); ?>
    </div>

    <?php
    /**
     * After single achievement thumbnail
     *
     * @since 1.0.0
     *
     * @param integer $achievement_id   The Achievement ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_after_single_achievement_thumbnail', get_the_ID(), $a ); ?>

    <div class="gamipress-achievement-description">

        <?php // Points of the achievement
        echo gamipress_achievement_points_markup( get_the_ID(), $a ); ?>

        <?php
        /**
         * After single achievement points markup
         *
         * @since 1.0.0
         *
         * @param integer $achievement_id   The Achievement ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_after_single_achievement_points', get_the_ID(), $a ); ?>

        <?php // Achievement content
        if( isset( $a['original_content'] ) ) :
            echo wpautop( $a['original_content'] );
        endif; ?>

        <?php // Times earned
        if ( (bool) gamipress_get_post_meta( get_the_ID(), '_gamipress_show_times_earned' ) ) :

            echo gamipress_achievement_times_earned_markup( get_the_ID(), $a );

            /**
             * After achievement times earned
             *
             * @since 1.5.9
             *
             * @param integer $achievement_id   The Achievement ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_single_achievement_times_earned', get_the_ID(), $a );

        endif; ?>

        <?php // Global Times earned
        if ( (bool) gamipress_get_post_meta( get_the_ID(), '_gamipress_show_global_times_earned' ) ) :

            echo gamipress_achievement_global_times_earned_markup( get_the_ID(), $a );

            /**
             * After achievement times earned by all users
             *
             * @since 2.0.0
             *
             * @param integer $achievement_id   The Achievement ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_single_achievement_global_times_earned', get_the_ID(), $a );

        endif; ?>

        <?php
        /**
         * After single achievement content
         *
         * @since 1.0.0
         *
         * @param integer $achievement_id   The Achievement ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_after_single_achievement_content', get_the_ID(), $a ); ?>

        <?php // Include output for our steps
        echo gamipress_get_required_achievements_for_achievement_list( get_the_ID() ); ?>

        <?php
        /**
         * After single achievement steps
         *
         * @since 1.0.0
         *
         * @param integer $achievement_id   The Achievement ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_after_single_achievement_steps', get_the_ID(), $a ); ?>

        <?php // Achievement unlock with points
        echo gamipress_achievement_unlock_with_points_markup( get_the_ID(), $a ); ?>

        <?php // Include achievement earners, if this achievement supports it
        if ( (bool) gamipress_get_post_meta( get_the_ID(), '_gamipress_show_earners' ) ) :

            $maximum_earners = absint( gamipress_get_post_meta( get_the_ID(), '_gamipress_maximum_earners' ) );

            echo gamipress_get_achievement_earners_list( get_the_ID(), array( 'limit' => $maximum_earners ) );

            /**
             * After single achievement earners
             *
             * @since 1.0.0
             *
             * @param integer $achievement_id   The Achievement ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_single_achievement_earners', get_the_ID(), $a );

        endif; ?>

        <?php
        /**
         * Single achievement description bottom
         *
         * @since 1.4.0
         *
         * @param integer $achievement_id   The Achievement ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_single_achievement_description_bottom', get_the_ID(), $a ); ?>

    </div><!-- .gamipress-achievement-description -->

    <?php
    /**
     * After single achievement
     *
     * @since 1.0.0
     *
     * @param integer $achievement_id   The Achievement ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_after_single_achievement', get_the_ID(), $a ); ?>

</div><!-- .achievement-wrap -->