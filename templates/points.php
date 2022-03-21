<?php
/**
 * Points template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points.php
 * To override a specific points type just copy it as yourtheme/gamipress/points-{points-type}.php
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

// Setup thumbnail size
$thumbnail_size = absint( $a['thumbnail_size'] );

if( $thumbnail_size === 0 ) {
    $thumbnail_size = 'gamipress-points';
} else {
    $thumbnail_size = array( $thumbnail_size, $thumbnail_size );
}

// Setup points classes
$classes = array(
    'gamipress-user-points',
    ( $is_current_user ? 'gamipress-is-current-user' : '' ),
    'gamipress-columns-' . $a['columns'],
    'gamipress-layout-' . $a['layout'],
    'gamipress-align-' . $a['align']
);

/**
 * Points classes
 *
 * @since 1.4.0
 *
 * @param array     $classes            Array of points classes
 * @param integer   $points_types       Array of points types to be rendered
 * @param array     $template_args      Template received arguments
 */
$classes = apply_filters( 'gamipress_points_classes', $classes, $points_types, $a ); ?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

    <?php
    /**
     * Before render user points list
     *
     * @since 1.0.0
     *
     * @param array $points_types     Array of points types to be rendered
     * @param array $template_args    Template received arguments
     */
    do_action( 'gamipress_before_render_points_list', $points_types, $a ); ?>

    <?php foreach( $a['points'] as $points_type => $amount ) :

        $label_position = gamipress_get_points_type_label_position( $points_type ); ?>

        <?php
        /**
         * Before render user points
         *
         * @since 1.0.0
         *
         * @param string  $points_type      Points type slug
         * @param integer $amount           Amount of this points type
         * @param array   $points_types     Array of points types to be rendered
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_before_render_points', $points_type, $amount, $points_types, $a ); ?>

        <div class="gamipress-points gamipress-user-points-<?php echo esc_attr( $points_type ); ?>">

            <?php // User Points Image
            if( $a['thumbnail'] === 'yes' ) : ?>
                <div class="gamipress-user-points-image gamipress-user-points-<?php echo esc_attr( $points_type ); ?>-image">
                    <?php echo gamipress_get_points_type_thumbnail( $points_type, $thumbnail_size ); ?>
                </div><!-- .gamipress-user-points-image -->

                <?php
                /**
                 * After user points thumbnail
                 *
                 * @since 1.0.0
                 *
                 * @param string  $points_type      Points type slug
                 * @param integer $amount           Amount of this points type
                 * @param array   $points_types     Array of points types to be rendered
                 * @param array   $template_args    Template received arguments
                 */
                do_action( 'gamipress_after_user_points_thumbnail', $points_type, $amount, $points_types, $a ); ?>

            <?php endif; ?>

            <div class="gamipress-user-points-description">

                <?php // User Points Label (before)
                if( $a['label'] === 'yes' && $label_position === 'before' ) : ?>
                    <span class="gamipress-user-points-label"><?php echo gamipress_get_points_amount_label( $amount, $points_type ); ?></span>

                    <?php
                    /**
                     * After user points label
                     *
                     * @since 1.0.0
                     *
                     * @param string  $points_type      Points type slug
                     * @param integer $amount           Amount of this points type
                     * @param array   $points_types     Array of points types to be rendered
                     * @param array   $template_args    Template received arguments
                     */
                    do_action( 'gamipress_after_user_points_label', $points_type, $amount, $points_types, $a ); ?>

                <?php endif; ?>

                <span class="gamipress-user-points-amount"><?php echo gamipress_format_amount( $amount, $points_type ); ?></span>

                <?php
                /**
                 * After user points amount
                 *
                 * @since   1.0.0
                 * @updated 1.5.1 Fixed filter name, changed from 'gamipress_after_user_points_count' to 'gamipress_after_user_points_amount'
                 *
                 * @param string  $points_type      Points type slug
                 * @param integer $amount           Amount of this points type
                 * @param array   $points_types     Array of points types to be rendered
                 * @param array   $template_args    Template received arguments
                 */
                do_action( 'gamipress_after_user_points_amount', $points_type, $amount, $points_types, $a ); ?>

                <?php // User Points Label (after)
                if( $a['label'] === 'yes' && $label_position !== 'before' ) : ?>
                    <span class="gamipress-user-points-label"><?php echo gamipress_get_points_amount_label( $amount, $points_type ); ?></span>

                    <?php
                    /**
                     * After user points label
                     *
                     * @since 1.0.0
                     *
                     * @param string  $points_type      Points type slug
                     * @param integer $amount           Amount of this points type
                     * @param array   $points_types     Array of points types to be rendered
                     * @param array   $template_args    Template received arguments
                     */
                    do_action( 'gamipress_after_user_points_label', $points_type, $amount, $points_types, $a ); ?>

                <?php endif; ?>

            </div><!-- .gamipress-user-points-description -->

        </div><!-- .gamipress-points -->

        <?php
        /**
         * After render user points
         *
         * @since 1.0.0
         *
         * @param string  $points_type      Points type slug
         * @param integer $amount           Amount of this points type
         * @param array   $points_types     Array of points types to be rendered
         * @param array   $template_args    Template received arguments
         */
        do_action( 'gamipress_after_render_points', $points_type, $amount, $points_types, $a ); ?>

    <?php endforeach; ?>

    <?php
    /**
     * After render user points list
     *
     * @since 1.0.0
     *
     * @param array $points_types     Array of points types rendered
     * @param array $template_args    Template received arguments
     */
    do_action( 'gamipress_after_render_points_list', $points_types, $a ); ?>

</div><!-- .gamipress-user-points -->
