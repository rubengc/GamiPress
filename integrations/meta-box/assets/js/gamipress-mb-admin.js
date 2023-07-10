(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();

        // Specific field Any value for Post fields
        var field_name_input = $(this).siblings('.mb-field-name');

        if( trigger_type === 'gamipress_meta_box_update_specific_post_field_any_value'
        || trigger_type === 'gamipress_meta_box_update_specific_post_field_specific_value' ) {
            field_name_input.show();
        } else {
            field_name_input.hide();
        }

        // Specific field Any value for User fields
        var field_name_user_input = $(this).siblings('.mb-field-name-user');

        if( trigger_type === 'gamipress_meta_box_update_specific_user_field_any_value'
        || trigger_type === 'gamipress_meta_box_update_specific_user_field_specific_value' ) {
            field_name_user_input.show();
        } else {
            field_name_user_input.hide();
        }

        // Specific field Any value
        var field_value_input = $(this).siblings('.mb-field-value');

        if( trigger_type === 'gamipress_meta_box_update_any_post_field_specific_value'
            || trigger_type === 'gamipress_meta_box_update_any_user_field_specific_value'
            || trigger_type === 'gamipress_meta_box_update_specific_post_field_specific_value'
            || trigger_type === 'gamipress_meta_box_update_specific_user_field_specific_value' ) {
            field_value_input.show();
        } else {
            field_value_input.hide();
        }


    });

    // Loop requirement list items to show/hide amount input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var field_name_input = $(this).find('.mb-field-name');

        if( trigger_type === 'gamipress_meta_box_update_specific_post_field_any_value'
        || trigger_type === 'gamipress_meta_box_update_specific_post_field_specific_value' ) {
            field_name_input.show();
        } else {
            field_name_input.hide();
        }

        var field_name_user_input = $(this).find('.mb-field-name-user');

        if( trigger_type === 'gamipress_meta_box_update_specific_user_field_any_value'
        ||  trigger_type === 'gamipress_meta_box_update_specific_user_field_specific_value' ) {
            field_name_user_input.show();
        } else {
            field_name_user_input.hide();
        }

        var field_value_input = $(this).find('.mb-field-value');

        if( trigger_type === 'gamipress_meta_box_update_any_post_field_specific_value'
        || trigger_type === 'gamipress_meta_box_update_any_user_field_specific_value'
        || trigger_type === 'gamipress_meta_box_update_specific_post_field_specific_value'
        || trigger_type === 'gamipress_meta_box_update_specific_user_field_specific_value' ) {
            field_value_input.show();
        } else {
            field_value_input.hide();
        }

        
    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        if( requirement_details.trigger_type === 'gamipress_meta_box_update_specific_post_field_any_value' ) {
            requirement_details.mb_field_name = requirement.find( '.mb-field-name select' ).val();
        }

        if( requirement_details.trigger_type === 'gamipress_meta_box_update_specific_user_field_any_value' ) {
            requirement_details.mb_field_name_user = requirement.find( '.mb-field-name-user select' ).val();
        }

        if( requirement_details.trigger_type === 'gamipress_meta_box_update_any_post_field_specific_value'
        || requirement_details.trigger_type === 'gamipress_meta_box_update_any_user_field_specific_value' ) {
            requirement_details.mb_field_value_condition = requirement.find( '.mb-field-value select' ).val();
            requirement_details.mb_field_value = requirement.find( '.mb-field-value input' ).val();
        }

        if( requirement_details.trigger_type === 'gamipress_meta_box_update_specific_post_field_specific_value' ) {
            requirement_details.mb_field_name = requirement.find( '.mb-field-name select' ).val();
            requirement_details.mb_field_value_condition = requirement.find( '.mb-field-value select' ).val();
            requirement_details.mb_field_value = requirement.find( '.mb-field-value input' ).val();
        }

        if( requirement_details.trigger_type === 'gamipress_meta_box_update_specific_user_field_specific_value' ) {
            requirement_details.mb_field_name_user = requirement.find( '.mb-field-name-user select' ).val();
            requirement_details.mb_field_value_condition = requirement.find( '.mb-field-value select' ).val();
            requirement_details.mb_field_value = requirement.find( '.mb-field-value input' ).val();
        }
    });

})( jQuery );