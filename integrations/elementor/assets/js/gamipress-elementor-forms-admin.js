(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var form_name_input = $(this).siblings('.elementor-forms-form-name');
        var field_name_input = $(this).siblings('.elementor-forms-field-name');
        var field_value_input = $(this).siblings('.elementor-forms-field-value');

        if( trigger_type === 'gamipress_elementor_forms_specific_new_form_submission'
            || trigger_type === 'gamipress_elementor_forms_specific_field_value_submission' ) {
            form_name_input.show();
        } else {
            form_name_input.hide();
        }

        if( trigger_type === 'gamipress_elementor_forms_field_value_submission'
            || trigger_type === 'gamipress_elementor_forms_specific_field_value_submission' ) {
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
        var form_name_input = $(this).find('.elementor-forms-form-name');
        var field_name_input = $(this).find('.elementor-forms-field-name');
        var field_value_input = $(this).find('.elementor-forms-field-value');

        if( trigger_type === 'gamipress_elementor_forms_specific_new_form_submission'
            || trigger_type === 'gamipress_elementor_forms_specific_field_value_submission' ) {
            form_name_input.show();
        } else {
            form_name_input.hide();
        }

        if( trigger_type === 'gamipress_elementor_forms_field_value_submission'
            || trigger_type === 'gamipress_elementor_forms_specific_field_value_submission' ) {
            field_name_input.show();
            field_value_input.show();
        } else {
            field_name_input.hide();
            field_value_input.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        if( requirement_details.trigger_type === 'gamipress_elementor_forms_specific_new_form_submission'
            || requirement_details.trigger_type === 'gamipress_elementor_forms_specific_field_value_submission' ) {
            requirement_details.elementor_forms_form_name = requirement.find( '.elementor-forms-form-name input' ).val();
        }

        if( requirement_details.trigger_type === 'gamipress_elementor_forms_field_value_submission'
            || requirement_details.trigger_type === 'gamipress_elementor_forms_specific_field_value_submission' ) {
            requirement_details.elementor_forms_field_name = requirement.find( '.elementor-forms-field-name input' ).val();
            requirement_details.elementor_forms_field_value = requirement.find( '.elementor-forms-field-value input' ).val();
        }

    });

})( jQuery );