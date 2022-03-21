<?php
/**
 * Inline Achievements template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/inline-achievements.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/inline-achievements-{achievement-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args; ?>

<span class="gamipress-inline-achievements">

    <?php foreach( $a['achievements'] as $index => $achievement_id ) :
        $a['id'] = $achievement_id; ?>

        <?php // Display the achievement inline ?>
        <?php echo gamipress_inline_achievement_shortcode( $a ); ?>

        <?php // Separator ?>
        <?php echo ( $index !== ( count( $a['achievements'] ) -1 ) ? '<span class="gamipress-inline-achievements-separator">, </span>' : '' ); ?>

     <?php endforeach; ?>

</span>
