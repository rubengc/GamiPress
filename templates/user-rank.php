<?php
/**
 * User Rank template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/user-rank.php
 * To override a specific rank type just copy it as yourtheme/gamipress/user-rank-{rank-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

$rank_type = $a['type'];

if( isset( $a['user_id'] ) ) {
    $user_id = $a['user_id'];
} else {
    $user_id = get_current_user_id();
}

$user_rank_id = gamipress_get_user_rank_id( $user_id, $rank_type );
?>

<div id="gamipress-user-ranks" class="gamipress-user-ranks gamipress-columns-<?php echo esc_attr( $a['columns'] ); ?>">

    <?php
    /**
     * Before render user ranks
     *
     * @param $rank_type        string  Rank type
     * @param $user_id          integer User ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_before_render_user_ranks', $rank_type, $user_id, $a ); ?>


    <?php // Previous rank
    if( $a['prev_rank'] === 'yes' ) :
        $prev_rank_id = gamipress_get_prev_rank_id( $user_rank_id ); ?>

        <?php if( $prev_rank_id !== $user_rank_id && $prev_rank_id !== 0 ) : ?>

            <?php
            /**
             * Before render previous user rank
             *
             * @param $rank_type        string  Rank type slug
             * @param $rank_id          integer Rank ID
             * @param $user_id          integer User ID
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_before_render_previous_user_rank', $rank_type, $prev_rank_id, $user_id, $a ); ?>

            <?php echo gamipress_render_rank( $prev_rank_id, $a['template_args'] ) ;?>

            <?php
            /**
             * After render previous user rank
             *
             * @param $rank_type        string  Rank type slug
             * @param $rank_id          integer Rank ID
             * @param $user_id          integer User ID
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_render_previous_user_rank', $rank_type, $prev_rank_id, $user_id, $a ); ?>

        <?php endif; ?>

    <?php endif; ?>

    <?php // Current rank
    if( $a['current_rank'] === 'yes' ) : ?>

        <?php
        /**
         * Before render current user rank
         *
         * @param $rank_type        string  Rank type slug
         * @param $rank_id          integer Rank ID
         * @param $user_id          integer User ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_before_render_current_user_rank', $rank_type, $user_rank_id, $user_id, $a ); ?>

        <?php echo gamipress_render_rank( $user_rank_id, $a['template_args'] ) ;?>

        <?php
        /**
         * After render current user rank
         *
         * @param $rank_type        string  Rank type slug
         * @param $rank_id          integer Rank ID
         * @param $user_id          integer User ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_render_current_user_rank', $rank_type, $user_rank_id, $user_id, $a ); ?>

    <?php endif; ?>

    <?php // Next rank
    if( $a['next_rank'] === 'yes' ) :
        $next_rank_id = gamipress_get_next_rank_id( $user_rank_id ); ?>

        <?php if( $next_rank_id !== $user_rank_id && $next_rank_id !== 0 ) : ?>

            <?php
            /**
             * Before render next user rank
             *
             * @param $rank_type        string  Rank type slug
             * @param $rank_id          integer Rank ID
             * @param $user_id          integer User ID
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_before_render_next_user_rank', $rank_type, $next_rank_id, $user_id, $a ); ?>

            <?php echo gamipress_render_rank( $next_rank_id, $a['template_args'] ) ;?>

            <?php
            /**
             * After render next user rank
             *
             * @param $rank_type        string  Rank type slug
             * @param $rank_id          integer Rank ID
             * @param $user_id          integer User ID
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_render_next_user_rank', $rank_type, $next_rank_id, $user_id, $a ); ?>

        <?php endif; ?>

    <?php endif; ?>

    <?php
    /**
     * After render user ranks
     *
     * @param $rank_type        string  Rank type
     * @param $user_id          integer User ID
     * @param $template_args    array   Template received arguments
     */
    do_action( 'gamipress_after_render_user_ranks', $rank_type, $user_id, $a ); ?>

</div>
