(function($) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Setup vars
        var trigger_type = $(this).val();

        var type_input = $(this).siblings('.wp-job-manager-type');

        // Toggle type field visibility
        if( trigger_type === 'gamipress_wp_job_manager_publish_job_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_mark_filled_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_mark_not_filled_specific_type'
            // Applications
            || trigger_type === 'gamipress_wp_job_manager_job_application_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_get_job_application_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_job_application_hired_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_job_application_rejected_specific_type' ) {
            type_input.show();
        } else {
            type_input.hide();
        }

    });

    // Loop requirement list items to show/hide score input on initial load
    $('.requirements-list li').each(function() {

        // Setup vars
        var trigger_type = $(this).find('.select-trigger-type').val();

        var type_input = $(this).find('.wp-job-manager-type');

        // Toggle type field visibility
        if( trigger_type === 'gamipress_wp_job_manager_publish_job_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_mark_filled_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_mark_not_filled_specific_type'
            // Applications
            || trigger_type === 'gamipress_wp_job_manager_job_application_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_get_job_application_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_job_application_hired_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_job_application_rejected_specific_type' ) {
            type_input.show();
        } else {
            type_input.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        var trigger_type = requirement_details.trigger_type

        // Add the type field
        if( trigger_type === 'gamipress_wp_job_manager_publish_job_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_mark_filled_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_mark_not_filled_specific_type'
            // Applications
            || trigger_type === 'gamipress_wp_job_manager_job_application_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_get_job_application_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_job_application_hired_specific_type'
            || trigger_type === 'gamipress_wp_job_manager_job_application_rejected_specific_type' ) {
            requirement_details.wp_job_manager_type_id = requirement.find( '.wp-job-manager-type select' ).val();
        }

    });

})(jQuery);