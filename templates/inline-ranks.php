<?php
/**
 * Inline Ranks template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/inline-ranks.php
 * To override a specific rank type just copy it as yourtheme/gamipress/inline-ranks-{rank-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args; ?>

<span class="gamipress-inline-ranks">

    <?php foreach( $a['ranks'] as $index => $rank_id ) :
        $a['id'] = $rank_id; ?>

        <?php // Display the rank inline ?>
        <?php echo gamipress_inline_rank_shortcode( $a ); ?>

        <?php // Separator ?>
        <?php echo ( $index !== ( count( $a['ranks'] ) -1 ) ? '<span class="gamipress-inline-ranks-separator">, </span>' : '' ); ?>

     <?php endforeach; ?>

</span>
