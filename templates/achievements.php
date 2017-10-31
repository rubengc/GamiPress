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
}
?>

<div id="gamipress-achievements-list" class="gamipress-achievements-list">

    <?php
    /**
     * Before render achievements list
     *
     * @param $template_args array Template received arguments
     */
    do_action( 'gamipress_before_render_achievements_list', $a ); ?>

    <div id="gamipress-achievements-filters-wrap">

        <?php
        /**
         * Before render achievements list filters
         *
         * @param $template_args array Template received arguments
         */
        do_action( 'gamipress_before_render_achievements_list_filters', $a ); ?>

        <?php // Hidden fields for AJAX request
        foreach( $a as $arg => $arg_value ) : ?>
            <input type="hidden" name="<?php echo $arg; ?>" value="<?php echo $arg_value; ?>">
        <?php endforeach; ?>

        <?php // Filter
        if ( $a['filter'] === 'no' ) :
            $filter_value = 'all';

            if( $a['user_id'] ) :
                $filter_value = 'completed'; ?>
                <input type="hidden" name="user_id" id="user_id" value="<?php echo $a['user_id']; ?>">
            <?php endif; ?>

            <input type="hidden" name="achievements_list_filter" id="achievements_list_filter" value="<?php echo $filter_value; ?>">

        <?php elseif( is_user_logged_in() ) : ?>

            <div id="gamipress-achievements-filter">

                <label for="achievements_list_filter"><?php _e( 'Filter:', 'gamipress' ); ?></label>
                <select name="achievements_list_filter" id="achievements_list_filter">
                    <option value="all"><?php echo sprintf( __( 'All %s', 'gamipress' ), $post_type_plural ); ?></option>
                    <option value="completed"><?php echo sprintf( __( 'Completed %s', 'gamipress' ), $post_type_plural ); ?></option>
                    <option value="not-completed"><?php echo sprintf( __( 'Not Completed %s', 'gamipress' ), $post_type_plural ); ?></option>
                </select>

            </div>

        <?php endif;

        // Search
        if ( $a['search'] === 'yes' ) :
            $search = isset( $_POST['achievements_list_search'] ) ? $_POST['achievements_list_search'] : ''; ?>

            <div id="gamipress-achievements-search">

                <form id="achievements_list_search_go_form" action="" method="post">
                    <label for="achievements_list_search"><?php _e( 'Search:', 'gamipress' ); ?></label>
                    <input type="text" id="achievements_list_search" name="achievements_list_search" value="<?php echo $search; ?>">
                    <input type="submit" id="achievements_list_search_go" name="achievements_list_search_go" value="<?php echo esc_attr__( 'Go', 'gamipress' ); ?>">
                </form>

            </div>

        <?php endif; ?>

        <?php
        /**
         * After render achievements list filters
         *
         * @param $template_args array Template received arguments
         */
        do_action( 'gamipress_after_render_achievements_list_filters', $a ); ?>

    </div><!-- #gamipress-achievements-filters-wrap -->

    <?php // Content Container ?>
    <div id="gamipress-achievements-container" class="gamipress-achievements-container gamipress-columns-<?php echo $a['columns']; ?>"></div>

    <?php // Hidden fields ?>
    <input type="hidden" id="gamipress_achievements_offset" value="0">
    <input type="hidden" id="gamipress_achievements_count" value="0">

    <?php // Load More button ?>
    <button type="button" id="achievements_list_load_more" class="gamipress-load-more-button" style="display:none;"><?php echo __( 'Load More', 'gamipress' ); ?></button>

    <?php // Loading spinner ?>
    <div class="gamipress-spinner"></div>

    <?php
    /**
     * After render achievements list
     *
     * @param $template_args array Template received arguments
     */
    do_action( 'gamipress_after_render_achievements_list', $a ); ?>

</div>

