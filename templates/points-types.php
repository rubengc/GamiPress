<?php
/**
 * Points Types template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/points-types.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/points-types-{points-type}.php
 */
global $gamipress_template_args;

$points_types = gamipress_get_points_types();
?>

<div class="gamipress-points-types">
    <?php foreach( $gamipress_template_args['points-types'] as $points_type => $points_awards ) :
            if( ! isset( $points_types[$points_type] ) ) :
                continue;
            endif; ?>
        <div class="gamipress-points-type-<?php echo $points_type; ?>">

            <h2 class="gamipress-points-type-title"><?php echo $points_types[$points_type]['plural_name']; ?></h2>

            <?php if( $points_awards ) : ?>
                <div class="gamipress-points-type-awards">

                    <div id="show-more-<?php echo $points_type; ?>" class="gamipress-open-close-switch">
                        <a class="show-hide-open" data-action="open" data-open-text="<?php _e( 'Show Details', 'gamipress' ); ?>" data-close-text="<?php _e( 'Hide Details', 'gamipress' ); ?>" href="#"><?php _e( 'Show Details', 'gamipress' ); ?></a>
                    </div>

                    <div id="gamipress-toggle-more-window-<?php echo $points_type; ?>" class="gamipress-extras-window">
                        <?php echo gamipress_get_points_awards_for_points_types_list_markup( $points_awards, $points_type ); ?>
                    </div><!-- .gamipress-extras-window -->

                </div><!-- .gamipress-points-type-awards -->
            <?php endif; ?>

        </div><!-- .gamipress-points-type-{points_type} -->
    <?php endforeach; ?>
</div><!-- .gamipress-points-types -->
