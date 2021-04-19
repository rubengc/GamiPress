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

// Check if user has earned this rank, rank is earned by default if is the lowest priority of this type
if( gamipress_is_lowest_priority_rank( get_the_ID() ) ) {
    $earned = true;
} else {
    $earned = gamipress_has_user_earned_achievement( get_the_ID(), get_current_user_id() );
}

// Setup rank classes
$classes = array(
    'single-rank',
    'rank-wrap',
    ( $earned ? 'user-has-earned' : '' ),
    'gamipress-layout-' . $a['layout'],
    'gamipress-align-' . $a['align']
);

/**
 * Single rank classes
 *
 * @since 1.4.0
 *
 * @param array     $classes            Array of rank classes
 * @param integer   $rank_id            The Rank ID
 * @param array     $template_args      Template received arguments
 */
$classes = apply_filters( 'gamipress_single_rank_classes', $classes, get_the_ID(), $a ); ?>

<?php // Check if current user has earned this rank
echo gamipress_render_earned_rank_text( get_the_ID(), get_current_user_id() ); ?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

    <?php
    /**
     * Before single rank
     *
     * @since 1.0.0
     *
     * @param integer $rank_id          The Rank ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_before_single_rank', get_the_ID(), $a ); ?>

    <div class="gamipress-rank-image">
        <?php // Thumbnail
        echo gamipress_get_rank_post_thumbnail( get_the_ID() ); ?>

        <?php // Share
        echo gamipress_rank_share_markup( get_the_ID(), $a ); ?>
    </div>

    <?php
    /**
     * After single rank thumbnail
     *
     * @since 1.0.0
     *
     * @param integer $rank_id          The Rank ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_after_single_rank_thumbnail', get_the_ID(), $a ); ?>

    <div class="gamipress-rank-description">

        <?php // Rank content
        if( isset( $a['original_content'] ) ) :
            echo wpautop( $a['original_content'] );
        endif; ?>

        <?php
        /**
         * After single rank content
         *
         * @since 1.0.0
         *
         * @param integer $rank_id          The Rank ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_after_single_rank_content', get_the_ID(), $a ); ?>

        <?php // Include output for our requirements
        echo gamipress_get_rank_requirements_list( get_the_ID() ); ?>

        <?php
        /**
         * After single rank requirements
         *
         * @since 1.0.0
         *
         * @param integer $rank_id          The Rank ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_after_single_rank_requirements', get_the_ID(), $a ); ?>

        <?php // Rank unlock with points
        echo gamipress_rank_unlock_with_points_markup( get_the_ID(), $a ); ?>

        <?php // Include rank earners, if this rank supports it
        if ( $show_earners = gamipress_get_post_meta( get_the_ID(), '_gamipress_show_earners' ) ) {

            $maximum_earners = absint( gamipress_get_post_meta( get_the_ID(), '_gamipress_maximum_earners' ) );

            echo gamipress_get_rank_earners_list( get_the_ID(), array( 'limit' => $maximum_earners ) );

            /**
             * After single rank earners
             *
             * @since 1.0.0
             *
             * @param integer $rank_id          The Rank ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_single_rank_earners', get_the_ID(), $a );

        } ?>

        <?php
        /**
         * Single rank description bottom
         *
         * @since 1.4.0
         *
         * @param integer $rank_id          The Rank ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_single_rank_description_bottom', get_the_ID(), $a ); ?>

    </div><!-- .gamipress-rank-description -->

    <?php
    /**
     * After single rank
     *
     * @since 1.0.0
     *
     * @param integer $rank_id          The Rank ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_after_single_rank', get_the_ID(), $a ); ?>

</div><!-- .rank-wrap -->