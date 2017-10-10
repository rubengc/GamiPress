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

if( isset( $a['user_id'] ) ) {
    $user_id = $a['user_id'];
} else {
    $user_id = get_current_user_id();
}

// Check if user has earned this achievement
$earned = gamipress_get_user_achievements( array( 'user_id' => $user_id, 'achievement_id' => get_the_ID() ) ); ?>


<div id="gamipress-achievement-<?php the_ID(); ?>" class="gamipress-achievement <?php echo ( $earned ? 'user-has-earned' : 'user-has-not-earned' ); ?>">

    <?php
    /**
     * Before render achievement
     *
     * @param $achievement_id   integer The Achievement ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_before_render_achievement', get_the_ID(), $a ); ?>

    <?php // Achievement Image
    if( $a['thumbnail'] === 'yes' ) : ?>
        <div class="gamipress-achievement-image">
            <a href="<?php the_permalink(); ?>"><?php echo gamipress_get_achievement_post_thumbnail( get_the_ID() ); ?></a>
        </div><!-- .gamipress-achievement-image -->

        <?php
        /**
         * After achievement thumbnail
         *
         * @param $achievement_id   integer The Achievement ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_achievement_thumbnail', get_the_ID(), $a ); ?>

    <?php endif; ?>

    <?php // Achievement Content ?>
    <div class="gamipress-achievement-description">

        <?php // Achievement Title ?>
        <h2 class="gamipress-achievement-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

        <?php
        /**
         * After achievement title
         *
         * @param $achievement_id   integer The Achievement ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_achievement_title', get_the_ID(), $a ); ?>

        <?php echo gamipress_achievement_points_markup( get_the_ID() ); ?>

        <?php
        /**
         * After achievement points
         *
         * @param $achievement_id   integer The Achievement ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_achievement_points', get_the_ID(), $a ); ?>

        <?php // Achievement Short Description
        if( $a['excerpt'] === 'yes' ) :  ?>
            <div class="gamipress-achievement-excerpt">
                <?php
                $excerpt = has_excerpt() ? get_post_field( 'post_excerpt', get_the_ID() ) : get_post_field( 'post_content', get_the_ID() );
                echo wpautop( apply_filters( 'get_the_excerpt', $excerpt ) );
                ?>
            </div><!-- .gamipress-achievement-excerpt -->

            <?php
            /**
             * After achievement excerpt
             *
             * @param $achievement_id   integer The Achievement ID
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_achievement_excerpt', get_the_ID(), $a ); ?>
        <?php endif; ?>

        <?php // Achievement Steps
        if ( $a['steps'] === 'yes' && $steps = gamipress_get_required_achievements_for_achievement( get_the_ID() ) ) : ?>
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
             * @param $achievement_id   integer The Achievement ID
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_achievement_steps', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php // Achievement Earners
        if ( $a['earners'] === 'yes' ) :
            echo gamipress_get_achievement_earners_list( get_the_ID() );

            /**
             * After achievement steps
             *
             * @param $achievement_id   integer The Achievement ID
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_achievement_earners', get_the_ID(), $a ); ?>

        <?php endif; ?>

    </div><!-- .gamipress-achievement-description -->

    <?php
    /**
     * After render achievement
     *
     * @param $achievement_id   integer The Achievement ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_after_render_achievement', get_the_ID(), $a ); ?>

</div><!-- .gamipress-achievement -->