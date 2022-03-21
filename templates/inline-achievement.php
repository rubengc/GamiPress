<?php
/**
 * Inline Achievement template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/inline-achievement.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/inline-achievement-{achievement-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

// Setup thumbnail size
$thumbnail_size = absint( $a['thumbnail_size'] );

if( $thumbnail_size === 0 ) {
    $thumbnail_size = 'gamipress-achievement';
} else {
    $thumbnail_size = array( $thumbnail_size, $thumbnail_size );
} ?>

<span id="gamipress-inline-achievement-<?php the_ID(); ?>" class="gamipress-inline-achievement">

     <?php // Link to the achievement page
     if( $a['link'] === 'yes' ) : ?>
         <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="gamipress-inline-achievement-link">
     <?php endif; ?>

     <?php // Achievement Image
     if( $a['thumbnail'] === 'yes' ) : ?>
        <span class="gamipress-inline-achievement-thumbnail"><?php echo gamipress_get_achievement_post_thumbnail( get_the_ID(), $thumbnail_size ); ?></span>&nbsp;
     <?php endif; ?>

     <?php // Achievement Ttitle ?>
        <span class="gamipress-inline-achievement-title"><?php the_title(); ?></span>

     <?php // Link to the achievement page
     if( $a['link'] === 'yes' ) : ?>
         </a>
     <?php endif; ?>

</span>
