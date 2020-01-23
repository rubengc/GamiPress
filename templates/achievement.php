<?php
/**
 * Achievement template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/achievement.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/achievement-{achievement-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

$user_id = isset( $a['user_id'] ) ? absint( $a['user_id'] ) : get_current_user_id();

// Check if user has earned this achievement
$earned = ( $user_id !== 0 && gamipress_get_user_achievements( array( 'user_id' => $user_id, 'achievement_id' => get_the_ID() ) ) );

// Setup achievement classes
$classes = array(
    'gamipress-achievement',
    ( $earned ? 'user-has-earned' : 'user-has-not-earned' ),
    'gamipress-layout-' . $a['layout']
);

/**
 * Achievement classes
 *
 * @since 1.4.0
 *
 * @param array     $classes            Array of achievement classes
 * @param integer   $achievement_id     The Achievement ID
 * @param array     $template_args      Template received arguments
 */
$classes = apply_filters( 'gamipress_achievement_classes', $classes, get_the_ID(), $a ); ?>

<div id="gamipress-achievement-<?php the_ID(); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

    <?php
    /**
     * Before render achievement
     *
     * @since 1.0.0
     *
     * @param integer $achievement_id   The Achievement ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_before_render_achievement', get_the_ID(), $a ); ?>

    <?php // Achievement Image
    if( $a['thumbnail'] === 'yes' ) : ?>
        <div class="gamipress-achievement-image">

            <?php // Link to the achievement page
            if( $a['link'] === 'yes' ) : ?>
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo gamipress_get_achievement_post_thumbnail( get_the_ID() ); ?></a>
            <?php else : ?>
                <?php echo gamipress_get_achievement_post_thumbnail( get_the_ID() ); ?>
            <?php endif; ?>

        </div><!-- .gamipress-achievement-image -->

        <?php
        /**
         * After achievement thumbnail
         *
         * @since 1.0.0
         *
         * @param integer $achievement_id   The Achievement ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_after_achievement_thumbnail', get_the_ID(), $a ); ?>

    <?php endif; ?>

    <?php // Achievement Content ?>
    <div class="gamipress-achievement-description">

        <?php // Achievement Title
        if( $a['title'] === 'yes' ) : ?>
            <h2 class="gamipress-achievement-title">

                <?php // Link to the achievement page
                if( $a['link'] === 'yes' ) : ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                <?php else : ?>
                    <?php the_title(); ?>
                <?php endif; ?>

            </h2>

            <?php
            /**
             * After achievement title
             *
             * @since 1.0.0
             *
             * @param integer $achievement_id   The Achievement ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_achievement_title', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php // Achievement points
        if( $a['points_awarded'] === 'yes' ) : ?>

            <?php echo gamipress_achievement_points_markup( get_the_ID(), $a ); ?>

            <?php
            /**
             * After achievement points
             *
             * @since 1.0.0
             *
             * @param integer $achievement_id   The Achievement ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_achievement_points', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php // Achievement Short Description
        if( $a['excerpt'] === 'yes' ) :  ?>
            <div class="gamipress-achievement-excerpt">
                <?php
                $excerpt = has_excerpt() ? gamipress_get_post_field( 'post_excerpt', get_the_ID() ) : gamipress_get_post_field( 'post_content', get_the_ID() );
                echo wpautop( do_blocks( apply_filters( 'get_the_excerpt', $excerpt ) ) );
                ?>
            </div><!-- .gamipress-achievement-excerpt -->

            <?php
            /**
             * After achievement excerpt
             *
             * @since 1.0.0
             *
             * @param integer $achievement_id   The Achievement ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_achievement_excerpt', get_the_ID(), $a ); ?>
        <?php endif; ?>

        <?php // Times Earned
        if( $a['times_earned'] === 'yes' ) : ?>

            <?php echo gamipress_achievement_times_earned_markup( get_the_ID(), $a ); ?>

            <?php
            /**
             * After achievement times earned
             *
             * @since 1.5.9
             *
             * @param integer $achievement_id   The Achievement ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_achievement_times_earned', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php // Achievement Steps
        if ( $a['steps'] === 'yes' && $steps = gamipress_get_achievement_steps( get_the_ID() ) ) : ?>
            <div class="gamipress-achievement-attached">

                <?php if ( $a['toggle'] === 'yes' ) : ?>

                    <div id="show-more-<?php the_ID(); ?>" class="gamipress-open-close-switch">
                        <a class="show-hide-open" data-achievement-id="<?php the_ID(); ?>" data-action="open" data-open-text="<?php _e( 'Show Details', 'gamipress' ); ?>" data-close-text="<?php _e( 'Hide Details', 'gamipress' ); ?>" href="#"><?php _e( 'Show Details', 'gamipress' ); ?></a>
                    </div>

                    <div id="gamipress-toggle-more-window-<?php the_ID(); ?>" class="gamipress-extras-window">
                        <?php echo gamipress_get_required_achievements_for_achievement_list_markup( $steps, get_the_ID(), $user_id, $a ); ?>
                    </div><!-- .gamipress-extras-window -->

                <?php else : ?>

                    <?php echo gamipress_get_required_achievements_for_achievement_list_markup( $steps, get_the_ID(), $user_id, $a ); ?>

                <?php endif; ?>

            </div><!-- .gamipress-achievement-attached -->

            <?php
            /**
             * After achievement steps
             *
             * @since 1.0.0
             *
             * @param integer $achievement_id   The Achievement ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_achievement_steps', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php // Achievement unlock with points
        if ( $a['unlock_button'] === 'yes' ) :
            echo gamipress_achievement_unlock_with_points_markup( get_the_ID(), $a );
        endif; ?>

        <?php // Achievement Earners
        if ( $a['earners'] === 'yes' ) :
            echo gamipress_get_achievement_earners_list( get_the_ID(), array( 'limit' => $a['earners_limit'] ) );

            /**
             * After achievement earners
             *
             * @since 1.0.0
             *
             * @param integer $achievement_id   The Achievement ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_achievement_earners', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php
        /**
         * Achievement description bottom
         *
         * @since 1.4.0
         *
         * @param integer $achievement_id   The Achievement ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_achievement_description_bottom', get_the_ID(), $a ); ?>

    </div><!-- .gamipress-achievement-description -->

    <?php
    /**
     * After render achievement
     *
     * @since 1.0.0
     *
     * @param integer $achievement_id   The Achievement ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_after_render_achievement', get_the_ID(), $a ); ?>

</div><!-- .gamipress-achievement -->