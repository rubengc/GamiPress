<?php
/**
 * Logs template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/logs.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

?>

<div class="gamipress-logs">

    <div class="gamipress-logs-atts">

        <?php
        /**
         * Before render logs atts
         *
         * @since 1.4.9
         *
         * @param array $template_args Template received arguments
         */
        do_action( 'gamipress_before_render_logs_atts', $a ); ?>

        <?php // Hidden fields for ajax request
        foreach( $a as $arg => $arg_value ) :

            // Skip excluded args
            if( in_array( $arg, array( 'query' ) ) ) {
                continue;
            } ?>
            <input type="hidden" name="<?php echo $arg; ?>" value="<?php echo ( is_array( $arg_value ) ? implode(',', $arg_value ) : $arg_value ); ?>">
        <?php endforeach; ?>

        <?php
        /**
         * After render logs atts
         *
         * @since 1.4.9
         *
         * @param array $template_args Template received arguments
         */
        do_action( 'gamipress_after_render_logs_atts', $a ); ?>

    </div>

    <div class="gamipress-logs-list">

        <?php
        /**
         * Before render logs list
         *
         * @since 1.0.0
         *
         * @param array $template_args Template received arguments
         */
        do_action( 'gamipress_before_render_logs_list', $a ); ?>

        <?php foreach( $a['query']->get_results() as $log ) : ?>

            <?php
            /**
             * Before render log
             *
             * @since 1.0.0
             *
             * @param integer $log_id           The Log ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_before_render_log', $log->log_id, $a ); ?>

            <div id="gamipress-log-<?php echo $log->log_id; ?>" class="gamipress-log"><?php echo apply_filters( 'gamipress_render_log_title', $log->title, $log->log_id ); ?></div>

            <?php
            /**
             * After render log
             *
             * @since 1.0.0
             *
             * @param integer $log_id           The Log ID
             * @param array   $template_args    Template received arguments
             */
            do_action( 'gamipress_after_render_log', $log->log_id, $a ); ?>

        <?php endforeach; ?>

        <?php
        /**
         * After render logs list
         *
         * @since 1.0.0
         *
         * @param array $template_args Template received arguments
         */
        do_action( 'gamipress_after_render_logs_list', $a ); ?>

    </div>

    <?php // Pagination
    if( $a['pagination'] === 'yes' ) : ?>

        <?php
        /**
         * Before render logs list pagination
         *
         * @since 1.4.9
         *
         * @param array $template_args Template received arguments
         */
        do_action( 'gamipress_before_render_logs_list_pagination', $a ); ?>

        <div id="gamipress-logs-pagination" class="gamipress-logs-pagination navigation">

            <?php echo paginate_links( array(
                'base'    => str_replace( 999999, '%#%', esc_url( get_pagenum_link( 999999 ) ) ),
                'format'  => '?paged=%#%',
                'current' => max( 1, get_query_var( 'paged' ) ),
                'total'   => ceil( $a['query']->found_results / $a['limit'] )
            ) ); ?>

        </div>

        <?php
        /**
         * After render logs list pagination
         *
         * @since 1.4.9
         *
         * @param array $template_args Template received arguments
         */
        do_action( 'gamipress_after_render_logs_list_pagination', $a ); ?>

        <?php // Loading spinner ?>
        <div id="gamipress-logs-spinner" class="gamipress-spinner" style="display: none;"></div>

    <?php endif; ?>

</div>
