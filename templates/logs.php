<?php
/**
 * Logs template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/logs.php
 * To override a specific log type just copy it as yourtheme/gamipress/logs-{log-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

// Execute the query
$logs = $a['query']->get_results();
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
        echo gamipress_array_as_hidden_inputs( $a, array( 'query' ) ); ?>

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

        <?php if( $a['query']->found_results > 0 ) : ?>

            <?php foreach( $logs as $log ) : ?>

                <?php
                /**
                 * Before render log
                 *
                 * @since 1.0.0
                 *
                 * @param integer $log_id           The Log ID
                 * @param array   $template_args    Template received arguments
                 */
                do_action( 'gamipress_before_render_log', $log->log_id, $a );

                /**
                 * Filters the log title
                 *
                 * @since 1.0.0
                 *
                 * @param string    $log_title      The Log title to render
                 * @param integer   $log_id         The Log ID
                 * @param array     $template_args  Template received arguments
                 *
                 * @return string
                 */
                $log_title = apply_filters( 'gamipress_render_log_title', $log->title, $log->log_id, $a ); ?>

                <div id="gamipress-log-<?php echo esc_attr( $log->log_id ); ?>" class="gamipress-log"><?php echo $log_title; ?></div>

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

        <?php else : ?>

            <p id="gamipress-logs-no-results"><?php echo __( 'You have no logs registered yet.', 'gamipress' ); ?></p>

        <?php endif; ?>

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
