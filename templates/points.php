<?php
/**
 * User Points template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/points-{points-type}.php
 */
global $gamipress_template_args;

$points_types = gamipress_get_points_types();

// Default points type
$points_types[''] = array(
    'singular_name' => __( 'Point', 'gamipress' ),
    'plural_name' => __( 'Points', 'gamipress' )
);
?>

<div class="gamipress-user-points">

    <?php
    /**
     * Before render user points list
     *
     * @param $points_types     array Array of points types to be rendered
     * @param $template_args    array Template received arguments
     */
    do_action( 'gamipress_before_render_points_list', $points_types, $gamipress_template_args ); ?>

    <?php foreach( $gamipress_template_args['points'] as $points_type => $count ) : ?>

        <?php
        /**
         * Before render user points
         *
         * @param $points_type      string  Points type slug
         * @param $count            integer Amount of this points type
         * @param $points_types     array   Array of points types to be rendered
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_before_render_points', $points_type, $count, $points_types, $gamipress_template_args ); ?>

        <div class="gamipress-user-points-<?php echo $points_type; ?>">

            <span class="gamipress-user-points-count"><?php echo $count; ?></span>
            <span class="gamipress-user-points-label"><?php echo $points_types[$points_type]['plural_name']; ?></span>

        </div>

        <?php
        /**
         * After render user points
         *
         * @param $points_type      string  Points type slug
         * @param $count            integer Amount of this points type
         * @param $points_types     array   Array of points types to be rendered
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_render_points', $points_type, $count, $points_types, $gamipress_template_args ); ?>

    <?php endforeach; ?>

    <?php
    /**
     * After render user points list
     *
     * @param $points_types     array Array of points types rendered
     * @param $template_args    array Template received arguments
     */
    do_action( 'gamipress_after_render_points_list', $points_types, $gamipress_template_args ); ?>

</div>
