(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var id_input = $(this).siblings('.vimeo-video-id');

        // Toggle custom field visibility
        if( trigger_type === 'gamipress_vimeo_watch_specific_video' ) {
            id_input.show();
        } else {
            id_input.hide();
        }

    });

    // Loop requirement list items to show/hide score input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var id_input = $(this).find('.vimeo-video-id');

        // Toggle custom field visibility
        if( trigger_type === 'gamipress_vimeo_watch_specific_video' ) {
            id_input.show();
        } else {
            id_input.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        // Add custom field
        if( requirement_details.trigger_type === 'gamipress_vimeo_watch_specific_video' ) {
            requirement_details.vimeo_video_id = requirement.find( '.vimeo-video-id input' ).val();
        }
    });

})( jQuery );