<?php
/**
 * Rank template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/rank.php
 * To override a specific rank just copy it as yourtheme/gamipress/rank-{rank-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

$user_id = isset( $a['user_id'] ) ? absint( $a['user_id'] ) : get_current_user_id();

// Check if user has earned this rank, rank is earned by default if is the lowest priority of this type
if( gamipress_is_lowest_priority_rank( get_the_ID() ) ) {
    $earned = true;
} else {
    $earned = gamipress_has_user_earned_achievement( get_the_ID(), $user_id );
}

// Check if this rank is the current one of the user
$current = gamipress_get_user_rank_id( $user_id, get_post_type( get_the_ID() ) ) === get_the_ID();

// Setup thumbnail size
$thumbnail_size = absint( $a['thumbnail_size'] );

if( $thumbnail_size === 0 ) {
    $thumbnail_size = 'gamipress-rank';
} else {
    $thumbnail_size = array( $thumbnail_size, $thumbnail_size );
}

// Setup rank classes
$classes = array(
    'gamipress-rank',
    ( $earned ? 'user-has-earned' : 'user-has-not-earned' ),
    ( $current ? 'current-user-rank' : '' ),
    'gamipress-layout-' . $a['layout'],
    'gamipress-align-' . $a['align']
);

/**
 * Rank classes
 *
 * @since 1.4.0
 *
 * @param array     $classes        Array of rank classes
 * @param integer   $rank_id        The Rank ID
 * @param array     $template_args  Template received arguments
 */
$classes = apply_filters( 'gamipress_rank_classes', $classes, get_the_ID(), $a );
?>


<div id="gamipress-rank-<?php the_ID(); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

    <?php
    /**
     * Before render rank
     *
     * @since 1.0.0
     *
     * @param integer $rank_id          The Rank ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_before_render_rank', get_the_ID(), $a ); ?>

    <?php // Rank Image
    if( $a['thumbnail'] === 'yes' ) : ?>
        <div class="gamipress-rank-image">

            <?php // Link to the rank page
            if( $a['link'] === 'yes' ) : ?>
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo gamipress_get_rank_post_thumbnail( get_the_ID(), $thumbnail_size ); ?></a>
            <?php else : ?>
                <?php echo gamipress_get_rank_post_thumbnail( get_the_ID(), $thumbnail_size ); ?>
            <?php endif; ?>

            <?php // Share
            echo gamipress_rank_share_markup( get_the_ID(), $a ); ?>

        </div><!-- .gamipress-rank-image -->

        <?php
        /**
         * After rank thumbnail
         *
         * @since 1.0.0
         *
         * @param integer $rank_id          The Rank ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_after_rank_thumbnail', get_the_ID(), $a ); ?>

    <?php endif; ?>

    <?php // Rank Content ?>
    <div class="gamipress-rank-description">

        <?php // Rank Title
        if( $a['title'] === 'yes' ) :  ?>
            <<?php echo $a['title_size']; ?> class="gamipress-rank-title">

                <?php // Link to the rank page
                if( $a['link'] === 'yes' ) : ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                <?php else : ?>
                    <?php the_title(); ?>
                <?php endif; ?>

            </<?php echo $a['title_size']; ?>>

            <?php
            /**
             * After rank title
             *
             * @since 1.0.0
             *
             * @param integer $rank_id          The Rank ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_rank_title', get_the_ID(), $a ); ?>
        <?php endif; ?>

        <?php // Rank Short Description
        if( $a['excerpt'] === 'yes' ) :  ?>
            <div class="gamipress-rank-excerpt">
                <?php
                $excerpt = has_excerpt() ? gamipress_get_post_field( 'post_excerpt', get_the_ID() ) : gamipress_get_post_field( 'post_content', get_the_ID() );
                echo wpautop( do_blocks( apply_filters( 'get_the_excerpt', $excerpt, get_post() ) ) );
                ?>
            </div><!-- .gamipress-rank-excerpt -->

            <?php
            /**
             * After rank excerpt
             *
             * @since 1.0.0
             *
             * @param integer $rank_id          The Rank ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_rank_excerpt', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php // Rank Requirements
        if ( $a['requirements'] === 'yes' && $requirements = gamipress_get_rank_requirements( get_the_ID() ) ) : ?>
            <div class="gamipress-rank-requirements">

                <?php if ( $a['toggle'] === 'yes' ) : ?>

                    <div id="show-more-<?php the_ID(); ?>" class="gamipress-open-close-switch">
                        <a class="show-hide-open" data-rank-id="<?php the_ID(); ?>" data-action="open" data-open-text="<?php _e( 'Show Details', 'gamipress' ); ?>" data-close-text="<?php _e( 'Hide Details', 'gamipress' ); ?>" href="#"><?php _e( 'Show Details', 'gamipress' ); ?></a>
                    </div>

                    <div id="gamipress-toggle-more-window-<?php the_ID(); ?>" class="gamipress-extras-window">
                        <?php echo gamipress_get_rank_requirements_list_markup( $requirements, get_the_ID(), $user_id, $a ); ?>
                    </div><!-- .gamipress-extras-window -->

                <?php else : ?>

                    <?php echo gamipress_get_rank_requirements_list_markup( $requirements, get_the_ID(), $user_id, $a ); ?>

                <?php endif; ?>

            </div><!-- .gamipress-rank-attached -->

            <?php
            /**
             * After rank requirements
             *
             * @since 1.0.0
             *
             * @param integer $rank_id          The Rank ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_rank_requirements', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php // Rank unlock with points
        if ( $a['unlock_button'] === 'yes' ) :
            echo gamipress_rank_unlock_with_points_markup( get_the_ID(), $a );
        endif; ?>

        <?php // If thumbnail is not displayed, place the share buttons at bottom
        if ( $a['thumbnail'] !== 'yes' ) :
            echo gamipress_rank_share_markup( get_the_ID(), $a );
        endif; ?>

        <?php // Rank Earners
        if ( $a['earners'] === 'yes' ) :
            echo gamipress_get_rank_earners_list( get_the_ID(), array( 'limit' => $a['earners_limit'] ) );

            /**
             * After rank earners
             *
             * @since 1.0.0
             *
             * @param integer $rank_id          The Rank ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_rank_earners', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php
        /**
         * Rank description bottom
         *
         * @since 1.4.0
         *
         * @param integer $rank_id          The Rank ID
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_rank_description_bottom', get_the_ID(), $a ); ?>

    </div><!-- .gamipress-rank-description -->

    <?php
    /**
     * After render rank
     *
     * @since 1.0.0
     *
     * @param integer $rank_id          The Rank ID
     * @param array   $template_args    Template received arguments
     */
    do_action( 'gamipress_after_render_rank', get_the_ID(), $a ); ?>

</div><!-- .gamipress-rank -->