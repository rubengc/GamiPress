(function( $ ) {
    $('.cmb2-id-bp-members-points-types .cmb2-list, '
        + '.cmb2-id-bp-members-achievements-types .cmb2-list, '
        + '.cmb2-id-bp-members-ranks-types .cmb2-list, '
        + '.cmb2-id-bp-tab-points-types .cmb2-list, '
        + '.cmb2-id-bp-tab-achievements-types .cmb2-list, '
        + '.cmb2-id-bp-tab-ranks-types .cmb2-list').sortable({
        handle: 'label',
        placeholder: 'ui-state-highlight',
        forcePlaceholderSize: true,
    });
})( jQuery );