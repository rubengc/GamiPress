(function( $ ) {

    // Deactivate license
    $('body').on('click', '.deactivate-license-button', function(e) {
        e.preventDefault();

        var button = $(this);
        var wrapper = button.parent();

        button.prop('disabled', true);

        var data = {};

        // Add the button name and value
        data[button.attr('name')] = button.val();

        // Add hidden inputs name and value
        wrapper.find('input').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        wrapper.append('<span class="spinner is-active" style="float: none;"></span>');
        wrapper.find('.deactivate-license-response').remove();

        $.ajax({
            url: window.location.href,
            data: data,
            success: function( response ) {

                button.prop('disabled', false);

                wrapper.append('<p class="deactivate-license-response deactivate-license-response-' + ( response.success ? 'success' : 'error' ) + '" style="float: none;">' + response.data + '</p>');

                wrapper.find('.spinner').remove();

                // Clear form
                var form = wrapper.closest( '.cmb-td' );
                var input = form.find('input[type="text"]');

                // Clear the false input and update its name
                input.val('').removeAttr( 'readonly' ).attr('name', input.attr('id'));

                // Remove the hidden input with the real value
                form.find('#' + input.attr('id') + '[type="hidden"]').remove();

                // Hide notices
                form.find('.license-error').slideUp('fast');
                form.find('.deactivate-license-button').slideUp('fast');
                form.find('.clear-license-button').slideUp('fast');
                form.find('.license-expiration-notice').slideUp('fast');
                form.find('.renew-license-notice').slideUp('fast');
            },
            error: function( response ) {
                button.prop('disabled', false);

                wrapper.find('.spinner').remove();
            }
        });
    });

    // Clear license
    $('body').on('click', '.clear-license-button', function(e) {
        e.preventDefault();

        var button = $(this);
        var wrapper = button.parent();

        button.prop('disabled', true);

        var data = {};

        // Add the button name and value
        data[button.attr('name')] = button.val();

        // Add hidden inputs name and value
        wrapper.find('input').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        wrapper.append('<span class="spinner is-active" style="float: none;"></span>');
        wrapper.find('.clear-license-response').remove();

        $.ajax({
            url: window.location.href,
            data: data,
            success: function( response ) {

                button.prop('disabled', false);

                wrapper.append('<p class="clear-license-response clear-license-response-' + ( response.success ? 'success' : 'error' ) + '" style="float: none;">' + response.data + '</p>');

                wrapper.find('.spinner').remove();

                // Clear form
                var form = wrapper.closest( '.cmb-td' );
                var input = form.find('input[type="text"]');

                // Clear the false input and update its name
                input.val('').removeAttr( 'readonly' ).attr('name', input.attr('id'));

                // Remove the hidden input with the real value
                form.find('#' + input.attr('id') + '[type="hidden"]').remove();

                // Hide notices
                form.find('.license-error').slideUp('fast');
                form.find('.deactivate-license-button').slideUp('fast');
                form.find('.clear-license-button').slideUp('fast');
                form.find('.license-expiration-notice').slideUp('fast');
                form.find('.renew-license-notice').slideUp('fast');

            },
            error: function( response ) {
                button.prop('disabled', false);

                wrapper.find('.spinner').remove();
            }
        });
    });

})( jQuery );