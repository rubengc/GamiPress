<?php
/**
 * Logs template
 */
global $gamipress_template_args;

?>

<div class="gamipress-logs">

    <?php
    /**
     * Before render logs list
     *
     * @param $template_args array Template received arguments
     */
    do_action( 'gamipress_before_render_logs_list', $gamipress_template_args ); ?>

    <?php while( $gamipress_template_args['query']->have_posts() ) :
        $gamipress_template_args['query']->the_post(); ?>

        <?php
        /**
         * Before render log
         *
         * @param $log_id           integer The Log ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_before_render_log', get_the_ID(), $gamipress_template_args ); ?>

        <div class="gamipress-log gamipress-log-<?php the_ID(); ?>"><?php the_title(); ?></div>

        <?php
        /**
         * After render log
         *
         * @param $log_id           integer The Log ID
         * @param $template_args    array   Template received arguments
         */
        do_action( 'gamipress_after_render_log', get_the_ID(), $gamipress_template_args ); ?>

    <?php endwhile;
    wp_reset_postdata();?>

    <?php
    /**
     * After render logs list
     *
     * @param $template_args array Template received arguments
     */
    do_action( 'gamipress_after_render_logs_list', $gamipress_template_args ); ?>

</div>
