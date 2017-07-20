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
    <?php foreach( $gamipress_template_args['points'] as $points_type => $count ) : ?>
        <div class="gamipress-user-points-<?php echo $points_type; ?>">
            <span class="gamipress-user-points-count"><?php echo $count; ?></span>
            <span class="gamipress-user-points-label"><?php echo $points_types[$points_type]['plural_name']; ?></span>
        </div>
    <?php endforeach; ?>
</div>
