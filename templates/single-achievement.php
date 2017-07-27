<?php
/**
 * Single Achievement template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/single-achievement.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/single-achievement-{achivement-type}.php
 */
global $gamipress_template_args;

// Check if user has earned this Achievement, and add an 'earned' class
$class = gamipress_get_user_achievements( array( 'achievement_id' => absint( get_the_ID() ) ) ) ? ' earned' : ''; ?>

<?php // wrap our content, add the thumbnail and title and add wpautop back ?>
<div class="achievement-wrap<?php echo $class; ?>">

    <?php
    /**
     * Before single achievement
     */
    do_action( 'before_single_achievement', get_the_ID() );
    ?>

    <?php // Check if current user has earned this achievement
    echo gamipress_render_earned_achievement_text( get_the_ID(), get_current_user_id() ); ?>

    <div class="alignleft gamipress-achievement-image">
        <?php echo gamipress_get_achievement_post_thumbnail( get_the_ID() ); ?>
    </div>

    <?php
    /**
     * After single achievement thumbnail
     */
    do_action( 'after_single_achievement_thumbnail', get_the_ID() );
    ?>

    <?php // Points of the achievement
    echo gamipress_achievement_points_markup(); ?>

    <?php
    /**
     * After single achievement points markup
     */
    do_action( 'after_single_achievement_points', get_the_ID() );
    ?>

    <?php // Achievement content
    if( isset( $gamipress_template_args['original_content'] ) ) :
        echo wpautop( $gamipress_template_args['original_content'] );
    endif; ?>

    <?php
    /**
     * After single achievement content
     */
    do_action( 'after_single_achievement_content', get_the_ID() );
    ?>

    <?php // Include output for our steps
    echo gamipress_get_required_achievements_for_achievement_list( get_the_ID() ); ?>

    <?php
    /**
     * After single achievement steps
     */
    do_action( 'after_single_achievement_steps', get_the_ID() );
    ?>

    <?php // Include achievement earners, if this achievement supports it
    if ( $show_earners = get_post_meta( get_the_ID(), '_gamipress_show_earners', true ) ) {
        echo gamipress_get_achievement_earners_list( get_the_ID() );

        /**
         * After single achievement steps
         */
        do_action( 'after_single_achievement_earners', get_the_ID() );

    } ?>

    <?php
    /**
     * After single achievement
     */
    do_action( 'after_single_achievement', get_the_ID() );
    ?>

</div><!-- .achievement-wrap -->