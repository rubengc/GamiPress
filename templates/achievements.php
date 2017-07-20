<?php
/**
 * Achievements template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/achievements.php
 * To override a specific achievement type just copy it as yourtheme/gamipress/achievements-{achivement-type}.php
 */
global $gamipress_template_args;

// If we're dealing with multiple achievement types
if ( 'all' === $gamipress_template_args['type'] ) {
    $post_type_plural = __( 'achievements', 'gamipress' );
} else {
    $types = explode( ',', $gamipress_template_args['type'] );
    $post_type_plural = ( 1 == count( $types ) && ! empty( $types[0] ) ) ? get_post_type_object( $types[0] )->labels->name : __( 'achievements', 'gamipress' );
}
?>

<div id="gamipress-achievements-list" class="gamipress-achievements-list">

    <div id="gamipress-achievements-filters-wrap">

        <?php // Filter
        if ( ! gamipress_shortcode_att_to_bool( $gamipress_template_args['show_filter'] ) ) :
            $filter_value = 'all';

            if( $gamipress_template_args['user_id'] ) :
                $filter_value = 'completed'; ?>
                <input type="hidden" name="user_id" id="user_id" value="<?php echo $gamipress_template_args['user_id']; ?>">
            <?php endif; ?>

            <input type="hidden" name="achievements_list_filter" id="achievements_list_filter" value="<?php echo $filter_value; ?>">

        <?php elseif( is_user_logged_in() ) : ?>

            <div id="gamipress-achievements-filter">

                <label for="achievements_list_filter"><?php _e( 'Filter:', 'gamipress' ); ?></label>
                <select name="achievements_list_filter" id="achievements_list_filter">
                    <option value="all"><?php echo sprintf( __( 'All %s', 'gamipress' ), $post_type_plural ); ?></option>
                    <option value="completed"><?php echo sprintf( __( 'Completed %s', 'gamipress' ), $post_type_plural ); ?></option>
                    <option value="not-completed"><?php echo sprintf( __( 'Not Completed %s', 'gamipress' ), $post_type_plural ); ?></option>
                    <?php
                    // TODO: if show_points is true "Badges by Points" option
                    // TODO: if dev adds a custom taxonomy to this post type then load all of the terms to filter by or add a new filter by this taxonomy
                    ?>
                </select>

            </div>

        <?php endif;

        // Search
        if ( gamipress_shortcode_att_to_bool( $gamipress_template_args['show_search'] ) ) :
            $search = isset( $_POST['achievements_list_search'] ) ? $_POST['achievements_list_search'] : ''; ?>

            <div id="gamipress-achievements-search">

                <form id="achievements_list_search_go_form" action="" method="post">
                    <label for="achievements_list_search"><?php _e( 'Search:', 'gamipress' ); ?></label>
                    <input type="text" id="achievements_list_search" name="achievements_list_search" value="<?php echo $search; ?>">
                    <input type="submit" id="achievements_list_search_go" name="achievements_list_search_go" value="<?php echo esc_attr__( 'Go', 'gamipress' ); ?>">
                </form>

            </div>

        <?php endif; ?>

        <?php // Hidden fields for AJAX request
            foreach( $gamipress_template_args as $template_arg => $template_arg_value ) : ?>
                <input type="hidden" name="<?php echo $template_arg; ?>" value="<?php echo $template_arg_value; ?>">
        <?php endforeach; ?>

    </div><!-- #gamipress-achievements-filters-wrap -->

    <?php // Content Container ?>
    <div id="gamipress-achievements-container" class="gamipress-achievements-list"></div>

    <?php // Hidden fields and Load More button ?>
    <input type="hidden" id="gamipress_achievements_offset" value="0">
    <input type="hidden" id="gamipress_achievements_count" value="0">
    <input type="button" id="achievements_list_load_more" value="<?php echo esc_attr__( 'Load More', 'gamipress' ); ?>" style="display:none;">
    <div class="gamipress-spinner"></div>

</div>

