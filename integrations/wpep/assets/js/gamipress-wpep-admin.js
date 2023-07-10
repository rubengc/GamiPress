(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var score_input = $(this).siblings('.wpep-assessment-score');
        var min_score_input = $(this).siblings('.wpep-assessment-min-score');
        var max_score_input = $(this).siblings('.wpep-assessment-max-score');

        // Toggle score field visibility
        if(
            trigger_type === 'gamipress_wpep_complete_assessment_min_grade'
            || trigger_type === 'gamipress_wpep_complete_specific_assessment_min_grade'
            || trigger_type === 'gamipress_wpep_complete_assessment_max_grade'
            || trigger_type === 'gamipress_wpep_complete_specific_assessment_max_grade'
        ) {
            score_input.show();
        } else {
            score_input.hide();
        }

        // Toggle min and max score fields visibility
        if(
            trigger_type === 'gamipress_wpep_complete_assessment_between_grade'
            || trigger_type === 'gamipress_wpep_complete_specific_assessment_between_grade'
        ) {
            min_score_input.show();
            max_score_input.show();
        } else {
            min_score_input.hide();
            max_score_input.hide();
        }

    });

    // Loop requirement list items to show/hide score input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var score_input = $(this).find('.wpep-assessment-score');
        var min_score_input = $(this).find('.wpep-assessment-min-score');
        var max_score_input = $(this).find('.wpep-assessment-max-score');

        // Toggle score field visibility
        if(
            trigger_type === 'gamipress_wpep_complete_assessment_min_grade'
            || trigger_type === 'gamipress_wpep_complete_specific_assessment_min_grade'
            || trigger_type === 'gamipress_wpep_complete_assessment_max_grade'
            || trigger_type === 'gamipress_wpep_complete_specific_assessment_max_grade'
        ) {
            score_input.show();
        } else {
            score_input.hide();
        }

        // Toggle min and max score fields visibility
        if(
            trigger_type === 'gamipress_wpep_complete_assessment_between_grade'
            || trigger_type === 'gamipress_wpep_complete_specific_assessment_between_grade'
        ) {
            min_score_input.show();
            max_score_input.show();
        } else {
            min_score_input.hide();
            max_score_input.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        // Add score field
        if(
            requirement_details.trigger_type === 'gamipress_wpep_complete_assessment_min_grade'
            || requirement_details.trigger_type === 'gamipress_wpep_complete_specific_assessment_min_grade'
            || requirement_details.trigger_type === 'gamipress_wpep_complete_assessment_max_grade'
            || requirement_details.trigger_type === 'gamipress_wpep_complete_specific_assessment_max_grade'
        ) {
            requirement_details.wpep_score = requirement.find( '.wpep-assessment-score input' ).val();
        }

        // Add min and max score fields
        if(
            requirement_details.trigger_type === 'gamipress_wpep_complete_assessment_between_grade'
            || requirement_details.trigger_type === 'gamipress_wpep_complete_specific_assessment_between_grade'
        ) {
            requirement_details.wpep_min_score = requirement.find( '.wpep-assessment-min-score input' ).val();
            requirement_details.wpep_max_score = requirement.find( '.wpep-assessment-max-score input' ).val();
        }
    });

})( jQuery );