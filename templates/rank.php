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

if( isset( $a['user_id'] ) ) {
    $user_id = $a['user_id'];
} else {
    $user_id = get_current_user_id();
}

// Check if user has earned this rank
$earned = gamipress_get_user_achievements( array( 'user_id' => $user_id, 'achievement_id' => get_the_ID() ) );

// Check if this rank is the current one of the user
$current = gamipress_get_user_rank_id( $user_id ) === get_the_ID();
?>


<div id="gamipress-rank-<?php the_ID(); ?>" class="gamipress-rank <?php echo ( $earned ? 'user-has-earned' : 'user-has-not-earned' ); ?> <?php echo ( $current ? 'current-user-rank' : '' ); ?>">

    <?php
    /**
     * Before render rank
     *
     * @param $rank_id          integer The Rank ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_before_render_rank', get_the_ID(), $a ); ?>

    <?php // Rank Image
    if( $a['thumbnail'] === 'yes' ) : ?>
        <div class="gamipress-rank-image">
            <a href="<?php the_permalink(); ?>"><?php echo gamipress_get_rank_post_thumbnail( get_the_ID() ); ?></a>
        </div><!-- .gamipress-rank-image -->

        <?php
        /**
         * After rank thumbnail
         *
         * @param $rank_id          integer The Rank ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_rank_thumbnail', get_the_ID(), $a ); ?>

    <?php endif; ?>

    <?php // Rank Content ?>
    <div class="gamipress-rank-description">

        <?php // Rank Title ?>
        <h2 class="gamipress-rank-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

        <?php
        /**
         * After rank title
         *
         * @param $rank_id          integer The Rank ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_rank_title', get_the_ID(), $a ); ?>

        <?php // Rank Short Description
        if( $a['excerpt'] === 'yes' ) :  ?>
            <div class="gamipress-rank-excerpt">
                <?php
                $excerpt = has_excerpt() ? get_post_field( 'post_excerpt', get_the_ID() ) : get_post_field( 'post_content', get_the_ID() );
                echo wpautop( apply_filters( 'get_the_excerpt', $excerpt ) );
                ?>
            </div><!-- .gamipress-rank-excerpt -->

            <?php
            /**
             * After rank excerpt
             *
             * @param $rank_id          integer The Rank ID
             * @param $template_args    array   Template received arguments
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
             * @param $rank_id          integer The Rank ID
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_rank_requirements', get_the_ID(), $a ); ?>

        <?php endif; ?>

        <?php // Rank Earners
        if ( $a['earners'] === 'yes' ) :
            echo gamipress_get_rank_earners_list( get_the_ID() );

            /**
             * After rank earners
             *
             * @param $rank_id          integer The Rank ID
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_rank_earners', get_the_ID(), $a ); ?>

        <?php endif; ?>

    </div><!-- .gamipress-rank-description -->

    <?php
    /**
     * After render rank
     *
     * @param $rank_id          integer The Rank ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_after_render_rank', get_the_ID(), $a ); ?>

</div><!-- .gamipress-rank -->