(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type
        var trigger_type = $(this).val();
        var percentage_row = $(this).siblings('.buddyboss-percentage');

        percentage_row.hide();

        if( trigger_type === 'gamipress_buddyboss_profile_progress' ) {
            percentage_row.show();
        }

    });

    // Loop requirement list items to show/hide inputs on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type
        var trigger_type = $(this).find('.select-trigger-type').val();
        var percentage_row = $(this).find('.buddyboss-percentage');

        percentage_row.hide();

        if( trigger_type === 'gamipress_buddyboss_profile_progress' ) {
            percentage_row.show();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        if( requirement_details.trigger_type === 'gamipress_buddyboss_profile_progress' ) {
            requirement_details.buddyboss_percentage = requirement.find( '.input-buddyboss-percentage' ).val();
        }

    });

})( jQuery );