/**
 * Custom formatting for posts on select2
 *
 * @since 1.4.1
 *
 * @param item
 *
 * @return string
 */
function gamipress_select2_posts_template_result( item ) {

    if( item.post_type !== undefined ) {

        var post_type_label = item.post_type;

        if( gamipress_admin_functions.post_type_labels[item.post_type] !== undefined ) {
            post_type_label = gamipress_admin_functions.post_type_labels[item.post_type];
        }

        return '<strong>' + item.post_title + '</strong>'
            + '<span class="result-description">'
            + 'ID: ' + item.ID + '<span class="align-right">' + post_type_label + '</span>'
            + ( item.site_name !== undefined ? ' (' + item.site_name + ')' : '' )
            + '</span>';
    }

    return item.text;

}

/**
 * Custom results processing for posts on select2
 *
 * @since 1.4.1
 *
 * @param response
 * @param params
 *
 * @return string
 */
function gamipress_select2_posts_process_results( response, params ) {

    if( response === null ) {
        return { results: [] };
    }

    var formatted_results = [];

    // Paginated responses will come with results and more_results keys
    var results = ( response.data.results !== undefined ? response.data.results : response.data );

    results.forEach( function( item ) {

        // Extend select2 keys (id and text) with given keys (ID, post_title and optionally post_type, site_id and site_name)
        formatted_results.push( jQuery.extend({
            id: item.ID,
            text: item.post_title + ' (#' + item.ID + ')',
        }, item ) );

    } );

    return {
        results: formatted_results,
        pagination: {
            more: ( response.data.more_results !== undefined ? response.data.more_results : false )
        }
    };

}

/**
 * Custom formatting for users on select2
 *
 * @since 1.4.1
 *
 * @param item
 *
 * @return string
 */
function gamipress_select2_users_template_result( item ) {

    if( item.display_name !== undefined ) {

        return '<strong>' + item.display_name + '</strong>'
            + '<span class="result-description">'
                + 'ID: ' + item.ID + ' - ' + item.user_email + ' (' + item.user_login + ')'
            + '</span>';
    }

    return item.text;

}

/**
 * Custom results processing for users on select2
 *
 * @since 1.4.1
 *
 * @param response
 * @param params
 *
 * @return string
 */
function gamipress_select2_users_process_results( response, params ) {

    if( response === null ) {
        return { results: [] };
    }

    var formatted_results = [];

    // Paginated responses will come with results and more_results keys
    var results = ( response.data.results !== undefined ? response.data.results : response.data );

    results.forEach( function( item ) {

        // Extend select2 keys (id and text) with given keys (ID, post_title and optionally post_type)
        formatted_results.push( jQuery.extend({
            id: item.ID,
            text: item.user_login + ' (#' + item.ID + ')',
        }, item ) );

    } );

    return {
        results: formatted_results,
        pagination: {
            more: ( response.data.more_results !== undefined ? response.data.more_results : false )
        }
    };

}

/**
 * Custom search matcher for selects with groups on select2
 *
 * @since 1.6.3
 *
 * @param params
 * @param data
 *
 * @return mixed
 */
function gamipress_select2_optgroup_matcher(params, data) {

    data.parentText = data.parentText || "";

    // Always return the object if there is nothing to compare
    if ($.trim(params.term) === '') {
        return data;
    }

    // Do a recursive check for options with children
    if (data.children && data.children.length > 0) {
        // Clone the data object if there are children
        // This is required as we modify the object to remove any non-matches
        var match = $.extend(true, {}, data);

        // Check each child of the option
        for ( var c = data.children.length - 1; c >= 0; c-- ) {
            var child = data.children[c];
            child.parentText += data.parentText + " " + data.text;

            var matches = modelMatcher(params, child);

            // If there wasn't a match, remove the object in the array
            if (matches == null) {
                match.children.splice(c, 1);
            }
        }

        // If any children matched, return the new object
        if (match.children.length > 0) {
            return match;
        }

        // If there were no matching children, check just the plain object
        return modelMatcher(params, match);
    }

    // If the typed-in term matches the text of this term, or the text from any
    // parent term, then it's a match.
    var original = (data.parentText + ' ' + data.text).toUpperCase();
    var term = params.term.toUpperCase();


    // Check if the text contains the term
    if ( original.indexOf(term) > -1 ) {
        return data;
    }

    // If it doesn't contain the term, don't return anything
    return null;
}