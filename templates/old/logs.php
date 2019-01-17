<?php
/**
 * Old logs template
 *
 * Template file for backward compatibility for GamiPress installs without upgrade 1.2.8
 *
 * This template can be overridden by copying it to yourtheme/gamipress/logs-old.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

?>

<div class="gamipress-logs">

    <?php
    /**
     * Before render logs list
     *
     * @param $template_args array Template received arguments
     */
    do_action( 'gamipress_before_render_logs_list', $a ); ?>

    <?php while( $a['query']->have_posts() ) :
        $a['query']->the_post(); ?>

        <?php
        /**
         * Before render log
         *
         * @param $log_id           integer The Log ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_before_render_log', get_the_ID(), $a ); ?>

        <div id="gamipress-log-<?php the_ID(); ?>" class="gamipress-log"><?php the_title(); ?></div>

        <?php
        /**
         * After render log
         *
         * @param $log_id           integer The Log ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_render_log', get_the_ID(), $a ); ?>

    <?php endwhile;
    wp_reset_postdata();?>

    <?php
    /**
     * After render logs list
     *
     * @param $template_args array Template received arguments
     */
    do_action( 'gamipress_after_render_logs_list', $a ); ?>

</div>
