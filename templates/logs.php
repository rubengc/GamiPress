<?php
/**
 * Logs template
 */
global $gamipress_template_args;

?>

<div class="gamipress-logs">
    <?php while( $gamipress_template_args['query']->have_posts() ) :
        $gamipress_template_args['query']->the_post(); ?>
        <div class="gamipress-log gamipress-log-<?php the_ID(); ?>"><?php the_title(); ?></div>
    <?php endwhile;
    wp_reset_postdata();?>
</div>
