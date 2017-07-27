<?php
/**
 * Achievement template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/achievement.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/achievement-{achivement-type}.php
 */
global $gamipress_template_args;

// Check if user has earned this achievement
$earned = gamipress_get_user_achievements( array( 'user_id' => get_current_user_id(), 'achievement_id' => get_the_ID() ) ); ?>


<div id="gamipress-achievement-<?php the_ID(); ?>" class="gamipress-achievement <?php echo ( $earned ? 'user-has-earned' : 'user-has-not-earned' ); ?>">

    <?php do_action( 'gamipress_before_render_achievement', get_the_ID(), $gamipress_template_args ); ?>

    <?php // Achievement Image
    if( $gamipress_template_args['thumbnail'] ) : ?>
        <div class="gamipress-achievement-image">
            <a href="<?php the_permalink(); ?>"><?php echo gamipress_get_achievement_post_thumbnail( get_the_ID() ); ?></a>
        </div><!-- .gamipress-achievement-image -->

        <?php
        /**
         * After achievement thumbnail
         */
        do_action( 'after_achievement_thumbnail', get_the_ID() );
        ?>
    <?php endif; ?>

    <?php // Achievement Content ?>
    <div class="gamipress-achievement-description">

        <?php // Achievement Title ?>
        <h2 class="gamipress-achievement-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

        <?php
        /**
         * After achievement title
         */
        do_action( 'after_achievement_title', get_the_ID() );
        ?>

        <?php // Achievement Short Description
        if( $gamipress_template_args['excerpt'] ) :  ?>
            <div class="gamipress-achievement-excerpt">
                <?php echo gamipress_achievement_points_markup( get_the_ID() ); ?>

                <?php
                /**
                 * After achievement points
                 */
                do_action( 'after_achievement_points', get_the_ID() );
                ?>

                <?php
                $excerpt = has_excerpt() ? get_post_field( 'post_excerpt', get_the_ID() ) : get_post_field( 'post_content', get_the_ID() );
                echo wpautop( apply_filters( 'get_the_excerpt', $excerpt ) );
                ?>
            </div><!-- .gamipress-achievement-excerpt -->

            <?php
            /**
             * After achievement excerpt
             */
            do_action( 'after_achievement_excerpt', get_the_ID() );
            ?>
        <?php endif; ?>

        <?php // Achievement Steps
        if ( $gamipress_template_args['steps'] && $steps = gamipress_get_required_achievements_for_achievement( get_the_ID() ) ) : ?>
            <div class="gamipress-achievement-attached">

                <div id="show-more-<?php the_ID(); ?>" class="gamipress-open-close-switch">
                    <a class="show-hide-open" data-achievement-id="<?php the_ID(); ?>" data-action="open" data-open-text="<?php _e( 'Show Details', 'gamipress' ); ?>" data-close-text="<?php _e( 'Hide Details', 'gamipress' ); ?>" href="#"><?php _e( 'Show Details', 'gamipress' ); ?></a>
                </div>

                <div id="gamipress-toggle-more-window-<?php the_ID(); ?>" class="gamipress-extras-window">
                    <?php echo gamipress_get_required_achievements_for_achievement_list_markup( $steps, get_the_ID() ); ?>
                </div><!-- .gamipress-extras-window -->

            </div><!-- .gamipress-achievement-attached -->

            <?php
            /**
             * After achievement steps
             */
            do_action( 'after_achievement_steps', get_the_ID() );
            ?>
        <?php endif; ?>

    </div><!-- .gamipress-achievement-description -->

    <?php do_action( 'gamipress_after_render_achievement', get_the_ID(), $gamipress_template_args ); ?>

</div><!-- .gamipress-achievements-list-item -->