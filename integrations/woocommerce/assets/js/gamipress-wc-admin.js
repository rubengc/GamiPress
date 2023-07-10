(function($) {

    // Award points listener
    $('#_gamipress_wc_award_points').on('change', function() {
        if( $(this).prop('checked') ) {
            $(this).closest('.cmb-row').siblings('.cmb-row').show();
        } else {
            $(this).closest('.cmb-row').siblings('.cmb-row').hide();
        }
    });

    $('#_gamipress_wc_award_points').trigger('change');

    // Listen for changes on post selector field
    $('.requirements-list').on( 'change', '.select-post', function() {

        // Setup vars
        var $this = $(this);
        var trigger_type = $this.siblings('.select-trigger-type').val();
        var post_id = $this.val();
        var site_id = $this.siblings('.select-post-site-id').val();
        var variation_input = $this.siblings('.wc-variation');

        if( post_id && post_id.length
            && ( trigger_type === 'gamipress_wc_product_variation_purchase'
                || trigger_type === 'gamipress_wc_product_variation_refund' ) ) {

            $('<span class="spinner is-active" style="float: none; margin: 0 2px 0 4px;"></span>').insertAfter( variation_input );

            variation_input.hide();

            // Add a timeout function to get the post site ID correctly updated
            setTimeout( function() {

                site_id = $this.siblings('.select-post-site-id').val();

                $.post(
                    ajaxurl,
                    {
                        action: 'gamipress_wc_get_product_variations',
                        post_id: post_id,
                        site_id: site_id,
                        selected: variation_input.find('select').val()
                    },
                    function( response ) {

                        // Remove the loader
                        variation_input.next('.spinner').remove();

                        // Add the new options and show the input again
                        variation_input.html( response );
                        variation_input.show();
                    }
                );

            }, 10 );

        } else {
            variation_input.hide();
        }

    });

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Setup vars
        var trigger_type = $(this).val();

        // Purchase total
        var purchase_total_inputs = $(this).siblings('.wc-purchase-total');

        if( trigger_type === 'gamipress_wc_new_purchase_total') {
            purchase_total_inputs.show();
        } else {
            purchase_total_inputs.hide();
        }

        // Variation field
        var post_id = $(this).siblings('.select-post').val();
        var variation_input = $(this).siblings('.wc-variation');

        if( post_id && post_id.length
            && ( trigger_type === 'gamipress_wc_product_variation_purchase'
                || trigger_type === 'gamipress_wc_product_variation_refund' ) ) {
            variation_input.show();
        } else {
            variation_input.hide();
        }

        var category_input = $(this).siblings('.wc-category');

        // Toggle category field visibility
        if( trigger_type === 'gamipress_wc_product_category_purchase' || trigger_type === 'gamipress_wc_product_category_refund' ) {
            category_input.show();
        } else {
            category_input.hide();
        }

        var tag_input = $(this).siblings('.wc-tag');

        // Toggle tag field visibility
        if( trigger_type === 'gamipress_wc_product_tag_purchase' || trigger_type === 'gamipress_wc_product_tag_refund' ) {
            tag_input.show();
        } else {
            tag_input.hide();
        }

        // Lifetime
        var lifetime_inputs = $(this).siblings('.wc-lifetime');

        if( trigger_type === 'gamipress_wc_lifetime_value') {
            lifetime_inputs.show();
        } else {
            lifetime_inputs.hide();
        }

    });

    // Loop requirement list items to show/hide score input on initial load
    $('.requirements-list li').each(function() {

        // Setup vars
        var trigger_type = $(this).find('.select-trigger-type').val();

        // Purchase total
        var purchase_total_inputs = $(this).find('.wc-purchase-total');

        if( trigger_type === 'gamipress_wc_new_purchase_total') {
            purchase_total_inputs.show();
        } else {
            purchase_total_inputs.hide();
        }

        // Variation
        var post_id = $(this).find('.select-post').val();
        var variation_input = $(this).find('.wc-variation');

        if( post_id && post_id.length
            && ( trigger_type === 'gamipress_wc_product_variation_purchase'
                || trigger_type === 'gamipress_wc_product_variation_refund' ) ) {
            variation_input.show();
        } else {
            variation_input.hide();
        }

        var category_input = $(this).find('.wc-category');

        // Toggle category field visibility
        if( trigger_type === 'gamipress_wc_product_category_purchase' || trigger_type === 'gamipress_wc_product_category_refund' ) {
            category_input.show();
        } else {
            category_input.hide();
        }

        var tag_input = $(this).find('.wc-tag');

        // Toggle tag field visibility
        if( trigger_type === 'gamipress_wc_product_tag_purchase' || trigger_type === 'gamipress_wc_product_tag_refund' ) {
            tag_input.show();
        } else {
            tag_input.hide();
        }

        // Lifetime
        var lifetime_inputs = $(this).find('.wc-lifetime');

        if( trigger_type === 'gamipress_wc_lifetime_value') {
            lifetime_inputs.show();
        } else {
            lifetime_inputs.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        // Purchase total fields
        if( requirement_details.trigger_type === 'gamipress_wc_new_purchase_total' ) {
            requirement_details.wc_purchase_total_condition = requirement.find( '.wc-purchase-total select' ).val();
            requirement_details.wc_purchase_total = requirement.find( '.wc-purchase-total input' ).val();
        }

        // Add the variation field
        if( requirement_details.trigger_type === 'gamipress_wc_product_variation_purchase' 
            || requirement_details.trigger_type === 'gamipress_wc_product_variation_refund' ) {
            requirement_details.wc_variation_id = requirement.find( '.wc-variation select' ).val();
        }

        // Add the category field
        if( requirement_details.trigger_type === 'gamipress_wc_product_category_purchase'
            || requirement_details.trigger_type === 'gamipress_wc_product_category_refund' ) {
            requirement_details.wc_category_id = requirement.find( '.wc-category select' ).val();
        }

        // Add the tag field
        if( requirement_details.trigger_type === 'gamipress_wc_product_tag_purchase'
            || requirement_details.trigger_type === 'gamipress_wc_product_tag_refund' ) {
            requirement_details.wc_tag_id = requirement.find( '.wc-tag select' ).val();
        }

        // Lifetime fields
        if( requirement_details.trigger_type === 'gamipress_wc_lifetime_value' ) {
            requirement_details.wc_lifetime_condition = requirement.find( '.wc-lifetime select' ).val();
            requirement_details.wc_lifetime = requirement.find( '.wc-lifetime input' ).val();
        }
    });

})(jQuery);