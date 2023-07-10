(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var amount_input = $(this).siblings('.give-amount');

        if( trigger_type === 'gamipress_give_new_donation_min_amount' ) {
            amount_input.show();
        } else {
            amount_input.hide();
        }

    });

    // Loop requirement list items to show/hide amount input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var amount_input = $(this).find('.give-amount');

        if( trigger_type === 'gamipress_give_new_donation_min_amount' ) {
            amount_input.show();
        } else {
            amount_input.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        if( requirement_details.trigger_type === 'gamipress_give_new_donation_min_amount' ) {
            requirement_details.give_amount = requirement.find( '.give-amount input' ).val();
        }

    });

})( jQuery );