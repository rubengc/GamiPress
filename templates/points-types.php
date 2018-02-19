<?php
/**
 * Points Types template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points-types.php
 * To override a specific points type just copy it as yourtheme/gamipress/points-types-{points-type}.php
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

// Setup points classes
$classes = array(
    'gamipress-points-types',
    'gamipress-columns-' . $a['columns'],
    'gamipress-layout-' . $a['layout']
);

/**
 * Points types classes
 *
 * @since 1.4.0
 *
 * @param array     $classes            Array of points types classes
 * @param integer   $points_types       Array of points types to be rendered
 * @param array     $template_args      Template received arguments
 */
$classes = apply_filters( 'gamipress_points_types_classes', $classes, $a['points-types'], $a ); ?>

<div class="<?php echo implode( ' ', $classes ); ?>">

    <?php
    /**
     * Before render points types list
     *
     * @since 1.0.0
     *
     * @param array $points_types     Array of points types to be rendered
     * @param array $template_args    Template received arguments
     */
    do_action( 'gamipress_before_render_points_types_list', $a['points-types'], $a ); ?>

    <?php foreach( $a['points-types'] as $points_type => $points_type_args ) :
            if( ! isset( $points_types[$points_type] ) ) :
                continue;
            endif;

            if( $a['awards'] === 'yes' ) :
                $points_awards = $points_type_args['awards'];
            else :
                $points_awards = array();
            endif;

            if( $a['deducts'] === 'yes' ) :
                $points_deducts = $points_type_args['deducts'];
            else :
                $points_deducts = array();
            endif;
        ?>

        <div id="gamipress-points-type-<?php echo $points_type; ?>" class="gamipress-points-type gamipress-points-type-<?php echo $points_type; ?>">

            <?php
            /**
             * Before render points type
             *
             * @since 1.0.0
             *
             * @param string  $points_type      Points type slug
             * @param array   $points_awards    Array of points awards
             * @param array   $points_deducts   Array of points deducts
             * @param array   $points_types     Array of points types to be rendered
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_before_render_points_type', $points_type, $points_awards, $points_deducts, $a['points-types'], $a ); ?>

            <?php // Points Type Image
            if( $a['thumbnail'] === 'yes' ) : ?>
                <div class="gamipress-points-type-image gamipress-points-type-<?php echo $points_type; ?>-image">
                    <?php echo gamipress_get_points_type_thumbnail( $points_type ); ?>
                </div><!-- .gamipress-points-image -->

                <?php
                /**
                 * After points type thumbnail
                 *
                 * @since 1.0.0
                 *
                 * @param string  $points_type      Points type slug
                 * @param array   $points_awards    Array of points awards
                 * @param array   $points_deducts   Array of points deducts
                 * @param array   $points_types     Array of points types to be rendered
                 * @param array   $template_args    Template received arguments
                 */
                do_action( 'gamipress_after_points_type_thumbnail', $points_type, $points_awards, $points_deducts, $a['points-types'], $a ); ?>

            <?php endif; ?>

            <div class="gamipress-points-type-description">

                <h2 class="gamipress-points-type-title"><?php echo $points_types[$points_type]['plural_name']; ?></h2>

                <?php
                /**
                 * After points type title
                 *
                 * @since 1.0.0
                 *
                 * @param string  $points_type      Points type slug
                 * @param array   $points_awards    Array of points awards
                 * @param array   $points_deducts   Array of points deducts
                 * @param array   $points_types     Array of points types to be rendered
                 * @param array   $template_args    Template received arguments
                 */
                do_action( 'gamipress_after_points_type_title', $points_type, $points_awards, $points_deducts, $a['points-types'], $a ); ?>

                <?php if( $a['awards'] === 'yes' && $points_awards ) : ?>

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
                     * @since 1.0.0
                     *
                     * @param string  $points_type      Points type slug
                     * @param array   $points_awards    Array of points awards
                     * @param array   $points_deducts   Array of points deducts
                     * @param array   $points_types     Array of points types to be rendered
                     * @param array   $template_args    Template received arguments
                     */
                    do_action( 'gamipress_after_points_type_points_awards', $points_type, $points_awards, $points_deducts, $a['points-types'], $a ); ?>

                <?php endif; ?>

                <?php if( $a['deducts'] === 'yes' && $points_deducts ) : ?>

                    <div class="gamipress-points-type-deducts">

                        <?php if ( $a['toggle'] === 'yes' ) : ?>

                            <div id="show-more-<?php echo $points_type; ?>" class="gamipress-open-close-switch">
                                <a class="show-hide-open" data-action="open" data-open-text="<?php _e( 'Show Details', 'gamipress' ); ?>" data-close-text="<?php _e( 'Hide Details', 'gamipress' ); ?>" href="#"><?php _e( 'Show Details', 'gamipress' ); ?></a>
                            </div>

                            <div id="gamipress-toggle-more-window-<?php echo $points_type; ?>" class="gamipress-extras-window">
                                <?php echo gamipress_get_points_deducts_for_points_types_list_markup( $points_deducts, $user_id, $a ); ?>
                            </div><!-- .gamipress-extras-window -->

                        <?php else : ?>

                            <?php echo gamipress_get_points_deducts_for_points_types_list_markup( $points_deducts, $user_id, $a ); ?>

                        <?php endif; ?>

                    </div><!-- .gamipress-points-type-deducts -->

                    <?php
                    /**
                     * After points type points deducts
                     *
                     * @since 1.0.0
                     *
                     * @param string  $points_type      Points type slug
                     * @param array   $points_awards    Array of points awards
                     * @param array   $points_deducts   Array of points deducts
                     * @param array   $points_types     Array of points types to be rendered
                     * @param array   $template_args    Template received arguments
                     */
                    do_action( 'gamipress_after_points_type_points_deducts', $points_type, $points_awards, $points_deducts, $a['points-types'], $a ); ?>

                <?php endif; ?>

            </div><!-- .gamipress-points-type-description -->

            <?php
            /**
             * After render points type
             *
             * @since 1.0.0
             *
             * @param string  $points_type      Points type slug
             * @param array   $points_awards    Array of points awards
             * @param array   $points_deducts   Array of points deducts
             * @param array   $points_types     Array of points types to be rendered
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_render_points_type', $points_type, $points_awards, $points_deducts, $a['points-types'], $a ); ?>

        </div><!-- .gamipress-points-type-{points_type} -->

    <?php endforeach; ?>

    <?php
    /**
     * After render points types list
     *
     * @param array $points_types     Array of points types to be rendered
     * @param array $template_args    Template received arguments
     */
    do_action( 'gamipress_after_render_points_types_list', $a['points-types'], $a ); ?>

</div><!-- .gamipress-points-types -->
