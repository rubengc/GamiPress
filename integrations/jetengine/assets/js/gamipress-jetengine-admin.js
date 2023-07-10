(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var post_type_selector = $(this).siblings('.select-jetengine-post-type');

        if( trigger_type === 'gamipress_jetengine_publish_post_specific_type'
            || trigger_type === 'gamipress_jetengine_update_post_specific_type'
            || trigger_type === 'gamipress_jetengine_delete_post_specific_type' ) {
            post_type_selector.show();
        } else {
            post_type_selector.hide();
        }

    });

    // Loop requirement list items to show/hide amount input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var post_type_selector = $(this).find('.select-jetengine-post-type');

        if( trigger_type === 'gamipress_jetengine_publish_post_specific_type'
            || trigger_type === 'gamipress_jetengine_update_post_specific_type'
            || trigger_type === 'gamipress_jetengine_delete_post_specific_type' ) {
            post_type_selector.show();
        } else {
            post_type_selector.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        if( requirement_details.trigger_type === 'gamipress_jetengine_publish_post_specific_type'
            || requirement_details.trigger_type === 'gamipress_jetengine_update_post_specific_type'
            || requirement_details.trigger_type === 'gamipress_jetengine_delete_post_specific_type' ) {
            requirement_details.jetengine_post_type = requirement.find( '.select-jetengine-post-type' ).val();
        }

    });

})( jQuery );