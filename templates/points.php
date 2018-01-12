<?php
/**
 * User Points template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/points-{points-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

$points_types = gamipress_get_points_types();

// Default points type
$points_types[''] = array(
    'singular_name' => __( 'Point', 'gamipress' ),
    'plural_name' => __( 'Points', 'gamipress' )
);

// Check to meet if points showed comes from current logged in user
$is_current_user = ( absint( $a['user_id'] ) === get_current_user_id() );
?>

<div class="gamipress-user-points gamipress-columns-<?php echo $a['columns']; ?> <?php echo ( $is_current_user ? 'gamipress-is-current-user' : '' ); ?>">

    <?php
    /**
     * Before render user points list
     *
     * @param $points_types     array Array of points types to be rendered
     * @param $template_args    array Template received arguments
     */
    do_action( 'gamipress_before_render_points_list', $points_types, $a ); ?>

    <?php foreach( $a['points'] as $points_type => $count ) : ?>

        <?php
        /**
         * Before render user points
         *
         * @param $points_type      string  Points type slug
         * @param $count            integer Amount of this points type
         * @param $points_types     array   Array of points types to be rendered
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_before_render_points', $points_type, $count, $points_types, $a ); ?>

        <div class="gamipress-points gamipress-user-points-<?php echo $points_type; ?>">

            <?php // User Points Image
            if( $a['thumbnail'] === 'yes' ) : ?>
                <span class="gamipress-user-points-image gamipress-user-points-<?php echo $points_type; ?>-image">
                    <?php echo gamipress_get_points_type_thumbnail( $points_type ); ?>
                </span><!-- .gamipress-points-image -->

                <?php
                /**
                 * After user points thumbnail
                 *
                 * @param $points_type      string  Points type slug
                 * @param $count            integer Amount of this points type
                 * @param $points_types     array   Array of points types to be rendered
                 * @param $template_args    array   Template received arguments
                 */
                do_action( 'gamipress_after_user_points_thumbnail', $points_type, $count, $points_types, $a ); ?>

            <?php endif; ?>

            <span class="gamipress-user-points-count"><?php echo $count; ?></span>

            <?php
            /**
             * After user points count
             *
             * @param $points_type      string  Points type slug
             * @param $count            integer Amount of this points type
             * @param $points_types     array   Array of points types to be rendered
             * @param $template_args    array   Template received arguments
             */
            do_action( 'gamipress_after_user_points_count', $points_type, $count, $points_types, $a ); ?>

            <?php // User Points Label
            if( $a['label'] === 'yes' ) : ?>
                <span class="gamipress-user-points-label"><?php echo $points_types[$points_type]['plural_name']; ?></span>

                <?php
                /**
                 * After user points label
                 *
                 * @param $points_type      string  Points type slug
                 * @param $count            integer Amount of this points type
                 * @param $points_types     array   Array of points types to be rendered
                 * @param $template_args    array   Template received arguments
                 */
                do_action( 'gamipress_after_user_points_label', $points_type, $count, $points_types, $a ); ?>

            <?php endif; ?>

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
        do_action( 'gamipress_after_render_points', $points_type, $count, $points_types, $a ); ?>

    <?php endforeach; ?>

    <?php
    /**
     * After render user points list
     *
     * @param $points_types     array Array of points types rendered
     * @param $template_args    array Template received arguments
     */
    do_action( 'gamipress_after_render_points_list', $points_types, $a ); ?>

</div>
