<?php
/**
 * Points Types template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points-types.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/points-types-{points-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

$points_types = gamipress_get_points_types();

if( isset( $a['user_id'] ) ) {
    $user_id = $a['user_id'];
} else {
    $user_id = get_current_user_id();
}
?>

<div class="gamipress-points-types">

    <?php
    /**
     * Before render points types list
     *
     * @param $points_types     array Array of points types to be rendered
     * @param $template_args    array Template received arguments
     */
    do_action( 'gamipress_before_render_points_types_list', $points_types, $a ); ?>

    <?php foreach( $a['points-types'] as $points_type => $points_awards ) :
            if( ! isset( $points_types[$points_type] ) ) :
                continue;
            endif; ?>

        <div id="gamipress-points-type-<?php echo $points_type; ?>" class="gamipress-points-type-<?php echo $points_type; ?>">

            <?php
            /**
             * Before render points type
             *
             * @param $points_type      string  Points type slug
             * @param $points_awards    array   Array of points awards
             * @param $points_types     array   Array of points types to be rendered
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_before_render_points_type', $points_type, $points_awards, $points_types, $a ); ?>

            <h2 class="gamipress-points-type-title"><?php echo $points_types[$points_type]['plural_name']; ?></h2>

            <?php
            /**
             * After points type title
             *
             * @param $points_type      string  Points type slug
             * @param $points_awards    array   Array of points awards
             * @param $points_types     array   Array of points types to be rendered
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_points_type_title', $points_type, $points_awards, $points_types, $a ); ?>

            <?php if( $points_awards ) : ?>

                <div class="gamipress-points-type-awards">

                    <?php if ( $a['toggle'] === 'yes' ) : ?>

                        <div id="show-more-<?php echo $points_type; ?>" class="gamipress-open-close-switch">
                            <a class="show-hide-open" data-action="open" data-open-text="<?php _e( 'Show Details', 'gamipress' ); ?>" data-close-text="<?php _e( 'Hide Details', 'gamipress' ); ?>" href="#"><?php _e( 'Show Details', 'gamipress' ); ?></a>
                        </div>

                        <div id="gamipress-toggle-more-window-<?php echo $points_type; ?>" class="gamipress-extras-window">
                            <?php echo gamipress_get_points_awards_for_points_types_list_markup( $points_awards, $user_id, $a ); ?>
                        </div><!-- .gamipress-extras-window -->

                    <?php else : ?>

                        <?php echo gamipress_get_points_awards_for_points_types_list_markup( $points_awards, $user_id, $a ); ?>

                    <?php endif; ?>

                </div><!-- .gamipress-points-type-awards -->

                <?php
                /**
                 * After points type points awards
                 *
                 * @param $points_type      string  Points type slug
                 * @param $points_awards    array   Array of points awards
                 * @param $points_types     array   Array of points types to be rendered
                 * @param $template_args    array   Template received arguments
                 */
                do_action( 'gamipress_after_points_type_points_awards', $points_type, $points_awards, $points_types, $a ); ?>

            <?php endif; ?>

            <?php
            /**
             * After render points type
             *
             * @param $points_type      string  Points type slug
             * @param $points_awards    array   Array of points awards
             * @param $points_types     array   Array of points types to be rendered
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_render_points_type', $points_type, $points_awards, $points_types, $a ); ?>

        </div><!-- .gamipress-points-type-{points_type} -->

    <?php endforeach; ?>

    <?php
    /**
     * After render points types list
     *
     * @param $points_types     array Array of points types to be rendered
     * @param $template_args    array Template received arguments
     */
    do_action( 'gamipress_after_render_points_types_list', $points_types, $a ); ?>

</div><!-- .gamipress-points-types -->
