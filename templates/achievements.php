<?php
/**
 * Achievements template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/achievements.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/achievements-{achievement-type}.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

// If we're dealing with multiple achievement types
if ( 'all' === $a['type'] ) {
    $post_type_plural = __( 'achievements', 'gamipress' );
} else {
    $types = explode( ',', $a['type'] );
    $post_type_plural = ( 1 == count( $types ) && ! empty( $types[0] ) ) ? get_post_type_object( $types[0] )->labels->name : __( 'achievements', 'gamipress' );
} ?>

<div id="gamipress-achievements-list" class="gamipress-achievements-list">

    <?php
    /**
     * Before render achievements list
     *
     * @since 1.0.0
     *
     * @param array $template_args Template received arguments
     */
    do_action( 'gamipress_before_render_achievements_list', $a ); ?>

    <div id="gamipress-achievements-filters-wrap">

        <?php
        /**
         * Before render achievements list filters
         *
         * @since 1.0.0
         *
         * @param array $template_args Template received arguments
         */
        do_action( 'gamipress_before_render_achievements_list_filters', $a ); ?>

        <?php // Hidden fields for AJAX request
        foreach( $a as $arg => $arg_value ) :

            // Skip excluded args
            if( in_array( $arg, array( 'filter', 'search', 'query' ) ) ) {
                continue;
            } ?>
            <input type="hidden" name="<?php echo $arg; ?>" value="<?php echo $arg_value; ?>">
        <?php endforeach; ?>

        <?php // Filter
        if ( $a['filter'] === 'no' ) : ?>

            <input type="hidden" name="achievements_list_filter" id="achievements_list_filter" value="<?php echo $a['filter_value']; ?>">

        <?php elseif( is_user_logged_in() ) : ?>

            <div id="gamipress-achievements-filter">

                <label for="achievements_list_filter"><?php _e( 'Filter:', 'gamipress' ); ?></label>
                <select name="achievements_list_filter" id="achievements_list_filter">
                    <option value="all" <?php selected( $a['filter_value'], 'all' ); ?>><?php echo sprintf( __( 'All %s', 'gamipress' ), $post_type_plural ); ?></option>
                    <option value="completed" <?php selected( $a['filter_value'], 'completed' ); ?>><?php echo sprintf( __( 'Completed %s', 'gamipress' ), $post_type_plural ); ?></option>
                    <option value="not-completed" <?php selected( $a['filter_value'], 'not-completed' ); ?>><?php echo sprintf( __( 'Not Completed %s', 'gamipress' ), $post_type_plural ); ?></option>
                </select>

            </div>

        <?php endif;

        // Search
        if ( $a['search'] === 'yes' ) :
            $search = isset( $_POST['achievements_list_search'] ) ? $_POST['achievements_list_search'] : '';

            /**
             * Achievements search button text
             *
             * @since 1.4.5
             *
             * @param string    $search_button_text The search button text
             * @param array     $template_args      Template received arguments
             */
            $search_button_text = apply_filters( 'gamipress_achievements_search_button_text', __( 'Go', 'gamipress' ), $a ); ?>

            <div id="gamipress-achievements-search">

                <form id="gamipress-achievements-search-form" action="" method="post">
                    <label for="achievements_list_search"><?php _e( 'Search:', 'gamipress' ); ?></label>
                    <input type="text" id="gamipress-achievements-search-input" name="achievements_list_search" value="<?php echo $search; ?>">
                    <input type="submit" id="gamipress-achievements-search-submit" name="achievements_list_search_go" value="<?php echo esc_attr( $search_button_text ); ?>">
                </form>

            </div>

        <?php endif; ?>

        <?php
        /**
         * After render achievements list filters
         *
         * @since 1.0.0
         *
         * @param array $template_args Template received arguments
         */
        do_action( 'gamipress_after_render_achievements_list_filters', $a ); ?>

    </div><!-- #gamipress-achievements-filters-wrap -->

    <?php // Content Container ?>
    <div id="gamipress-achievements-container" class="gamipress-achievements-container gamipress-columns-<?php echo $a['columns']; ?>">
        <?php echo $a['query']['achievements']; ?>
    </div>

    <?php // Hidden fields ?>
    <input type="hidden" id="gamipress-achievements-offset" value="<?php echo $a['query']['offset']; ?>">
    <input type="hidden" id="gamipress-achievements-count" value="<?php echo $a['query']['achievement_count']; ?>">

    <?php // Load More button ?>
    <?php if ( $a['load_more'] === 'yes' ) :
        $hide_load_more = $a['query']['query_count'] <= $a['query']['offset'];

        /**
         * Achievements load more button text
         *
         * @since 1.4.5
         *
         * @param string    $load_more_button_text  The load more button text
         * @param array     $template_args          Template received arguments
         */
        $load_more_button_text = apply_filters( 'gamipress_achievements_load_more_button_text', __( 'Load More', 'gamipress' ), $a ); ?>

        <button type="button" id="gamipress-achievements-load-more" class="gamipress-load-more-button" <?php if( $hide_load_more ) : ?>style="display:none;"<?php endif; ?>><?php echo $load_more_button_text; ?></button>

    <?php endif; ?>

    <?php // Loading spinner ?>
    <div id="gamipress-achievements-spinner" class="gamipress-spinner" style="display: none;"></div>

    <?php
    /**
     * After render achievements list
     *
     * @since 1.0.0
     *
     * @param array $template_args Template received arguments
     */
    do_action( 'gamipress_after_render_achievements_list', $a ); ?>

</div>

