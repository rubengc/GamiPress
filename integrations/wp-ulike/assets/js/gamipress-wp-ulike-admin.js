(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();

        // Post Type
        var post_type_select = $(this).siblings('.wp-ulike-post-type');

        if(
            trigger_type === 'gamipress_wp_ulike_post_type_like'
            || trigger_type === 'gamipress_wp_ulike_get_post_type_like'
        ) {
            post_type_select.show();
        } else {
            post_type_select.hide();
        }

    });

    // Loop requirement list items to show/hide score input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();

        // Post Type
        var topic_category_select = $(this).find('.wp-ulike-post-type');

        if(
            trigger_type === 'gamipress_wp_ulike_post_type_like'
            || trigger_type === 'gamipress_wp_ulike_get_post_type_like'
        ) {
            topic_category_select.show();
        } else {
            topic_category_select.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        // Post Type
        if(
            requirement_details.trigger_type === 'gamipress_wp_ulike_post_type_like'
            || requirement_details.trigger_type === 'gamipress_wp_ulike_get_post_type_like'
        ) {
            requirement_details.wp_ulike_post_type = requirement.find( '.wp-ulike-post-type select' ).val();
        }
    });

})( jQuery );