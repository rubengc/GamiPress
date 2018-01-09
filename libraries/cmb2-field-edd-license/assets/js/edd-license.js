(function( $ ) {

    $('body').on('click', '.deactivate-license-button', function(e) {
        e.preventDefault();

        $(this).prop('disabled', true);

        var wrapper = $(this).parent();

        var data = {};

        // Add the button name and value
        data[$(this).attr('name')] = $(this).val();

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

                $(this).prop('disabled', false);

                wrapper.append('<p class="deactivate-license-response deactivate-license-response-' + ( response.success ? 'success' : 'error' ) + '" style="float: none;">' + response.data + '</p>');

                wrapper.find('.spinner').remove();
            },
            error: function( response ) {
                $(this).prop('disabled', false);

                wrapper.find('.spinner').remove();
            }
        });
    });

})( jQuery );