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

        // Extend select2 keys (id and text) with given keys (ID, post_title and optionally post_type)
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