(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var form_id_input = $(this).siblings('.divi-form-id');
        var field_name_input = $(this).siblings('.divi-field-name');
        var field_value_input = $(this).siblings('.divi-field-value');

        if( trigger_type === 'gamipress_divi_specific_new_form_submission'
            || trigger_type === 'gamipress_divi_specific_field_value_submission' ) {
            form_id_input.show();
        } else {
            form_id_input.hide();
        }

        if( trigger_type === 'gamipress_divi_field_value_submission'
            || trigger_type === 'gamipress_divi_specific_field_value_submission' ) {
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
        var form_id_input = $(this).find('.divi-form-id');
        var field_name_input = $(this).find('.divi-field-name');
        var field_value_input = $(this).find('.divi-field-value');

        if( trigger_type === 'gamipress_divi_specific_new_form_submission'
            || trigger_type === 'gamipress_divi_specific_field_value_submission' ) {
            form_id_input.show();
        } else {
            form_id_input.hide();
        }

        if( trigger_type === 'gamipress_divi_field_value_submission'
            || trigger_type === 'gamipress_divi_specific_field_value_submission' ) {
            field_name_input.show();
            field_value_input.show();
        } else {
            field_name_input.hide();
            field_value_input.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        if( requirement_details.trigger_type === 'gamipress_divi_specific_new_form_submission'
            || requirement_details.trigger_type === 'gamipress_divi_specific_field_value_submission' ) {
            requirement_details.divi_form_id = requirement.find( '.divi-form-id input' ).val();
        }

        if( requirement_details.trigger_type === 'gamipress_divi_field_value_submission'
            || requirement_details.trigger_type === 'gamipress_divi_specific_field_value_submission' ) {
            requirement_details.divi_field_name = requirement.find( '.divi-field-name input' ).val();
            requirement_details.divi_field_value = requirement.find( '.divi-field-value input' ).val();
        }

    });

})( jQuery );