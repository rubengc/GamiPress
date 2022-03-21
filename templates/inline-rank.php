<?php
/**
 * Inline Rank template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/inline-rank.php
 * To override a specific rank type just copy it as yourtheme/gamipress/inline-rank-{rank-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

// Setup thumbnail size
$thumbnail_size = absint( $a['thumbnail_size'] );

if( $thumbnail_size === 0 ) {
    $thumbnail_size = 'gamipress-rank';
} else {
    $thumbnail_size = array( $thumbnail_size, $thumbnail_size );
} ?>

<span id="gamipress-inline-rank-<?php the_ID(); ?>" class="gamipress-inline-rank">

     <?php // Link to the rank page
     if( $a['link'] === 'yes' ) : ?>
         <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="gamipress-inline-rank-link">
     <?php endif; ?>

     <?php // Achievement Image
     if( $a['thumbnail'] === 'yes' ) : ?>
        <span class="gamipress-inline-rank-thumbnail"><?php echo gamipress_get_rank_post_thumbnail( get_the_ID(), $thumbnail_size ); ?></span>&nbsp;
     <?php endif; ?>

     <?php // Achievement Ttitle ?>
        <span class="gamipress-inline-rank-title"><?php the_title(); ?></span>

     <?php // Link to the rank page
     if( $a['link'] === 'yes' ) : ?>
         </a>
     <?php endif; ?>

</span>
