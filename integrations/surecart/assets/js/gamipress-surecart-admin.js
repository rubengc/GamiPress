(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var product_selector = $(this).siblings('.select-surecart-product');

        if( trigger_type === 'gamipress_surecart_specific_product_purchase' ) {
            product_selector.show();
        } else {
            product_selector.hide();
        }

    });

    // Loop requirement list items to show/hide form select on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var product_selector = $(this).find('.select-surecart-product');

        if( trigger_type === 'gamipress_surecart_specific_product_purchase' ) {
            product_selector.show();
        } else {
            product_selector.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        if( requirement_details.trigger_type === 'gamipress_surecart_specific_product_purchase' ) {
            requirement_details.surecart_product = requirement.find( '.select-surecart-product' ).val();
        }

    });

})( jQuery );