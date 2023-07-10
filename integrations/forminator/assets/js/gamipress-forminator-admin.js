(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var field_name_input = $(this).siblings('.forminator-field-name');
        var field_value_input = $(this).siblings('.forminator-field-value');

        if( trigger_type === 'gamipress_forminator_form_field_value_submission'
            || trigger_type === 'gamipress_forminator_specific_form_field_value_submission'
            || trigger_type === 'gamipress_forminator_poll_field_value_submission'
            || trigger_type === 'gamipress_forminator_specific_poll_field_value_submission'
            || trigger_type === 'gamipress_forminator_quiz_field_value_submission'
            || trigger_type === 'gamipress_forminator_specific_quiz_field_value_submission' ) {
            field_name_input.show();
            field_value_input.show();
        } else {
            field_name_input.hide();
            field_value_input.hide();
        }

    });

    // Loop requirement list items to show/hide amount input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var field_name_input = $(this).find('.forminator-field-name');
        var field_value_input = $(this).find('.forminator-field-value');

        if( trigger_type === 'gamipress_forminator_form_field_value_submission'
            || trigger_type === 'gamipress_forminator_specific_form_field_value_submission'
            || trigger_type === 'gamipress_forminator_poll_field_value_submission'
            || trigger_type === 'gamipress_forminator_specific_poll_field_value_submission'
            || trigger_type === 'gamipress_forminator_quiz_field_value_submission'
            || trigger_type === 'gamipress_forminator_specific_quiz_field_value_submission' ) {
            field_name_input.show();
            field_value_input.show();
        } else {
            field_name_input.hide();
            field_value_input.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        var trigger_type = requirement_details.trigger_type;

        if( trigger_type === 'gamipress_forminator_form_field_value_submission'
            || trigger_type === 'gamipress_forminator_specific_form_field_value_submission'
            || trigger_type === 'gamipress_forminator_poll_field_value_submission'
            || trigger_type === 'gamipress_forminator_specific_poll_field_value_submission'
            || trigger_type === 'gamipress_forminator_quiz_field_value_submission'
            || trigger_type === 'gamipress_forminator_specific_quiz_field_value_submission' ) {
            requirement_details.forminator_field_name = requirement.find( '.forminator-field-name input' ).val();
            requirement_details.forminator_field_value = requirement.find( '.forminator-field-value input' ).val();
        }

    });

})( jQuery );