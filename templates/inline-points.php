<?php
/**
 * Inline Points template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/inline-points.php
 * To override a specific points type just copy it as yourtheme/gamipress/inline-points-{points-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

// Setup thumbnail size
$thumbnail_size = absint( $a['thumbnail_size'] );

if( $thumbnail_size === 0 ) {
    $thumbnail_size = 'gamipress-points';
} else {
    $thumbnail_size = array( $thumbnail_size, $thumbnail_size );
}

// Get the last points type to show to meet if should append the separator or not
$last_points_type = key( array_slice( $a['points'], -1, 1, true ) ); ?>

<span class="gamipress-inline-points-wrapper">

    <?php foreach( $a['points'] as $points_type => $amount ) :
        $label_position = gamipress_get_points_type_label_position( $points_type ); ?>

        <span class="gamipress-inline-points gamipress-inline-points-<?php echo $points_type; ?>">

            <?php // Thumbnail
            if ( $a['thumbnail'] === 'yes' ) : ?>
                <span class="gamipress-inline-points-thumbnail gamipress-inline-points-<?php echo $points_type; ?>-thumbnail"><?php echo gamipress_get_points_type_thumbnail( $points_type, $thumbnail_size ); ?></span>&nbsp;
            <?php endif; ?>

            <?php // Points label (before)
            if ( $a['label'] === 'yes' && $label_position === 'before' ) : ?>
                <span class="gamipress-inline-points-label gamipress-inline-points-<?php echo $points_type; ?>-label"><?php echo gamipress_get_points_amount_label( $amount, $points_type ); ?></span>&nbsp;
            <?php endif; ?>

            <?php // Points amount ?>
            <span class="gamipress-inline-points-amount gamipress-inline-points-<?php echo $points_type; ?>-amount"><?php echo gamipress_format_amount( $amount, $points_type ); ?></span>

            <?php // Points label (after)
            if ( $a['label'] === 'yes' && $label_position !== 'before' ) : ?>
                &nbsp;<span class="gamipress-inline-points-label gamipress-inline-points-<?php echo $points_type; ?>-label"><?php echo gamipress_get_points_amount_label( $amount, $points_type ); ?></span>
            <?php endif; ?>

            <?php // Separator
            echo ( $points_type !== $last_points_type ? '<span class="gamipress-inline-points-separator gamipress-inline-points-' . $points_type . '-separator">, </span>' : '' ); ?>

        </span>

    <?php endforeach; ?>

</span>
