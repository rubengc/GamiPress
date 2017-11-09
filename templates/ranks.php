<?php
/**
 * Ranks template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/ranks.php
 * To override a specific rank type just copy it as yourtheme/gamipress/ranks-{rank-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

$rank_types = gamipress_get_rank_types();

if( isset( $a['user_id'] ) ) {
    $user_id = $a['user_id'];
} else {
    $user_id = get_current_user_id();
}
?>

<div id="gamipress-ranks-list" class="gamipress-ranks-list gamipress-columns-<?php echo $a['columns']; ?> <?php echo ( $a['is_user_ranks'] ? 'gamipress-user-ranks' : '' ); ?>">

    <?php
    /**
     * Before render rank types list
     *
     * @param $rank_types       array Array of points types to be rendered
     * @param $template_args    array Template received arguments
     */
    do_action( 'gamipress_before_render_rank_types_list', $a['rank-types'], $a ); ?>

    <?php foreach( $a['rank-types'] as $rank_type => $rank_ids ) :
        if( ! isset( $rank_types[$rank_type] ) ) :
            continue;
        endif; ?>


        <div id="gamipress-rank-type-<?php echo $rank_type; ?>" class="gamipress-rank-type gamipress-rank-type-<?php echo $rank_type; ?>">

            <?php
            /**
             * Before render rank type
             *
             * @param $rank_type        string  Rank type slug
             * @param $rank_types       array   Array of rank types to be rendered
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_before_render_rank_type', $rank_type, $a['rank-types'], $a ); ?>

            <h2 class="gamipress-rank-type-title"><?php echo $rank_types[$rank_type]['plural_name']; ?></h2>

            <?php
            /**
             * After rank type title
             *
             * @param $rank_type        string  Rank type slug
             * @param $rank_types       array   Array of rank types to be rendered
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_rank_type_title', $rank_type, $a['rank-types'], $a ); ?>

            <?php foreach( $rank_ids as $rank_id ) : ?>

                <?php echo gamipress_render_rank( $rank_id, $a['template_args'] ) ;?>

            <?php endforeach; ?>

            <?php
            /**
             * After render rank type
             *
             * @param $rank_type        string  Rank type slug
             * @param $rank_types       array   Array of rank types to be rendered
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_render_rank_type', $rank_type, $a['rank-types'], $a ); ?>

            </div>

    <?php endforeach; ?>

    <?php
    /**
     * After render rank types list
     *
     * @param $rank_types     array Array of points types to be rendered
     * @param $template_args    array Template received arguments
     */
    do_action( 'gamipress_after_render_rank_types_list', $a['rank-types'], $a ); ?>

</div>
