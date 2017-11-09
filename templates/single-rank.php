<?php
/**
 * Single Rank template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/single-rank.php
 * To override a specific rank type just copy it as yourtheme/gamipress/single-rank-{rank-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

// Check if user has earned this Achievement, and add an 'earned' class
$class = gamipress_get_user_achievements( array( 'achievement_id' => absint( get_the_ID() ) ) ) ? 'user-has-earned' : ''; ?>

<div class="single-rank rank-wrap <?php echo $class; ?>">

    <?php
    /**
     * Before single rank
     *
     * @param $rank_id          integer The Rank ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_before_single_rank', get_the_ID(), $a ); ?>

    <?php // Check if current user has earned this rank
    echo gamipress_render_earned_rank_text( get_the_ID(), get_current_user_id() ); ?>

    <div class="alignleft gamipress-rank-image">
        <?php echo gamipress_get_rank_post_thumbnail( get_the_ID() ); ?>
    </div>

    <?php
    /**
     * After single rank thumbnail
     *
     * @param $rank_id          integer The Rank ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_after_single_rank_thumbnail', get_the_ID(), $a ); ?>

    <?php // Rank content
    if( isset( $a['original_content'] ) ) :
        echo wpautop( $a['original_content'] );
    endif; ?>

    <?php
    /**
     * After single rank content
     *
     * @param $rank_id          integer The Rank ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_after_single_rank_content', get_the_ID(), $a ); ?>

    <?php // Include output for our requirements
    echo gamipress_get_rank_requirements_list( get_the_ID() ); ?>

    <?php
    /**
     * After single rank requirements
     *
     * @param $rank_id          integer The Rank ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_after_single_rank_requirements', get_the_ID(), $a ); ?>

    <?php // Include rank earners, if this rank supports it
    if ( $show_earners = get_post_meta( get_the_ID(), '_gamipress_show_earners', true ) ) {
        echo gamipress_get_rank_earners_list( get_the_ID() );

        /**
         * After single rank earners
         *
         * @param $rank_id          integer The Rank ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_single_rank_earners', get_the_ID(), $a );

    } ?>

    <?php
    /**
     * After single rank
     *
     * @param $rank_id          integer The Rank ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_after_single_rank', get_the_ID(), $a ); ?>

</div><!-- .rank-wrap -->