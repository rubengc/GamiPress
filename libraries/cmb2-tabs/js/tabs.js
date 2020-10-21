(function($) {
    // Initial check
    if( $('.cmb-tabs').length ) {
        $('.cmb-tabs').each(function() {
            // Activate first tab
            if( ! $(this).find('.cmb-tab.active').length ) {
                $(this).find('.cmb-tab').first().addClass('active');

                $($(this).find('.cmb-tab').first().data('fields')).addClass('cmb-tab-active-item');
                
                // Support for groups and repeatable fields
                $($(this).find('.cmb-tab').first().data('fields')).find('.cmb-repeat .cmb-row, .cmb-repeatable-group .cmb-row').addClass('cmb-tab-active-item');
            }
        });
    }

    $('body').on('click.cmbTabs', '.cmb-tabs .cmb-tab', function(e) {
        var tab = $(this);

        if( ! tab.hasClass('active') ) {
            var tabs = tab.closest('.cmb-tabs');
            var form = tabs.next('.cmb2-wrap');

            // Hide current active tab fields
            form.find(tabs.find('.cmb-tab.active').data('fields')).fadeOut('fast', function() {
                $(this).removeClass('cmb-tab-active-item');

                form.find(tab.data('fields')).fadeIn('fast', function() {
                    $(this).addClass('cmb-tab-active-item');

                    // Support for groups and repeatable fields
                    $(this).find('.cmb-repeat-table .cmb-row, .cmb-repeatable-group .cmb-row').addClass('cmb-tab-active-item');
                });
            });

            // Update tab active class
            tabs.find('.cmb-tab.active').removeClass('active');
            tab.addClass('active');
        }
    });
   
    // Adding a new group element needs to get the active class also
    $('body').on('click', '.cmb-add-group-row', function() {
        $(this).closest('.cmb-repeatable-group').find('.cmb-row').addClass('cmb-tab-active-item');
    });

    // Adding a new repeatable element needs to get the active class also
    $('body').on('click', '.cmb-add-row-button', function() {
        $(this).closest('.cmb-repeat').find('.cmb-row').addClass('cmb-tab-active-item');
    });

    // Initialize on widgets area
    $(document).on('widget-updated widget-added', function(e, widget) {

        if( widget.find('.cmb-tabs').length ) {

            widget.find('.cmb-tabs').each(function() {
                // Activate first tab
                if( ! $(this).find('.cmb-tab.active').length ) {
                    $(this).find('.cmb-tab').first().addClass('active');

                    $($(this).find('.cmb-tab').first().data('fields')).addClass('cmb-tab-active-item');

                    // Support for groups and repeatable fields
                    $($(this).find('.cmb-tab').first().data('fields')).find('.cmb-repeat .cmb-row, .cmb-repeatable-group .cmb-row').addClass('cmb-tab-active-item');
                }
            });

        }

    });
    
})(jQuery);
