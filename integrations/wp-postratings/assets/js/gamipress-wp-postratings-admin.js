(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var rate_selector = $(this).siblings('.input-wp-postratings-rate');

        if( // Specific rates
            trigger_type === 'gamipress_wp_postratings_rate_specific'
            || trigger_type === 'gamipress_wp_postratings_specific_rate_specific'
            || trigger_type === 'gamipress_wp_postratings_user_rate_specific'
            || trigger_type === 'gamipress_wp_postratings_user_specific_rate_specific'
            // Minimum rates
            || trigger_type === 'gamipress_wp_postratings_minimum_rate'
            || trigger_type === 'gamipress_wp_postratings_specific_minimum_rate'
            || trigger_type === 'gamipress_wp_postratings_user_minimum_rate'
            || trigger_type === 'gamipress_wp_postratings_user_specific_minimum_rate' ) {
            rate_selector.show();
        } else {
            rate_selector.hide();
        }

    });

    // Loop requirement list items to show/hide form select on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var rate_selector = $(this).find('.input-wp-postratings-rate');

        if( // Specific rates
            trigger_type === 'gamipress_wp_postratings_rate_specific'
            || trigger_type === 'gamipress_wp_postratings_specific_rate_specific'
            || trigger_type === 'gamipress_wp_postratings_user_rate_specific'
            || trigger_type === 'gamipress_wp_postratings_user_specific_rate_specific'
            // Minimum rates
            || trigger_type === 'gamipress_wp_postratings_minimum_rate'
            || trigger_type === 'gamipress_wp_postratings_specific_minimum_rate'
            || trigger_type === 'gamipress_wp_postratings_user_minimum_rate'
            || trigger_type === 'gamipress_wp_postratings_user_specific_minimum_rate' ) {
            rate_selector.show();
        } else {
            rate_selector.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        var trigger_type = requirement_details.trigger_type;

        if( // Specific rates
            trigger_type === 'gamipress_wp_postratings_rate_specific'
            || trigger_type === 'gamipress_wp_postratings_specific_rate_specific'
            || trigger_type === 'gamipress_wp_postratings_user_rate_specific'
            || trigger_type === 'gamipress_wp_postratings_user_specific_rate_specific'
            // Minimum rates
            || trigger_type === 'gamipress_wp_postratings_minimum_rate'
            || trigger_type === 'gamipress_wp_postratings_specific_minimum_rate'
            || trigger_type === 'gamipress_wp_postratings_user_minimum_rate'
            || trigger_type === 'gamipress_wp_postratings_user_specific_minimum_rate' ) {
            requirement_details.wp_postratings_rate = requirement.find( '.input-wp-postratings-rate' ).val();
        }

    });

})( jQuery );