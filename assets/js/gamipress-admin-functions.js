/**
 * Custom formatting for posts on select2
 *
 * @since 1.4.1
 *
 * @param {Object} item
 *
 * @return {string}
 */
function gamipress_select2_posts_template_result( item ) {

    if( item.post_type !== undefined ) {

        var post_type_label = item.post_type;

        if( gamipress_post_type_exists( item.post_type ) ) {
            post_type_label = gamipress_get_post_type_label( item.post_type );
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
 * @param {Object} response
 * @param {Object} params
 *
 * @return {string}
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
 * @param {Object} item
 *
 * @return {string}
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
 * @param {Object} response
 * @param {Object} params
 *
 * @return {string}
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
 * @param {Object} params
 * @param {Object} data
 *
 * @return {mixed}
 */
function gamipress_select2_optgroup_matcher( params, data ) {

    // Initialize required vars
    data.parentText = data.parentText   || '';

    // Always return the object if there is nothing to compare
    if ( params.term === undefined ) {
        return data;
    }

    if ( params.term.trim() === '' ) {
        return data;
    }

    // Do a recursive check for options with children
    if ( data.children && data.children.length > 0 ) {

        // Clone the data object if there are children
        // This is required as we modify the object to remove any non-matches
        var match = jQuery.extend( true, {}, data );

        // Check each child of the option
        for ( var c = data.children.length - 1; c >= 0; c-- ) {

            var child = data.children[c];
            child.parentText += data.parentText + " " + data.text;

            var matches = gamipress_select2_optgroup_matcher( params, child );

            // If there wasn't a match, remove the object in the array
            if (matches == null) {
                match.children.splice( c, 1 );
            }

        }

        // If any children matched, return the new object
        if ( match.children.length > 0 ) {
            return match;
        }

        // If there were no matching children, check just the plain object
        return gamipress_select2_optgroup_matcher( params, match );

    }

    // If the typed-in term matches the text of this term, or the text from any
    // parent term, then it's a match.
    var original = ( data.parentText + ' ' + data.text ).toUpperCase();
    var term = params.term.toUpperCase();


    // Check if the text contains the term
    if ( original.indexOf( term ) > -1 ) {
        return data;
    }

    // If it doesn't contain the term, don't return anything
    return null;
}

/**
 * Function to check if select2 is correctly updated to latest release
 *
 * @since 1.6.3
 *
 * @param {boolean} show_in_console
 *
 * @return {boolean}
 */
function gamipress_is_select2_updated( show_in_console ) {

    if( show_in_console === undefined )
        show_in_console = false;

    // Select2 version check
    try {
        // Select2 ver >= 4.x

        // Let's to create a hidden select element to turn it into a select2 element and check that everything works correctly
        jQuery('<select id="gamipress-select2-version-check" style="display: none;"><option value=""></option></select>').insertAfter('body');

        jQuery("#gamipress-select2-version-check").select2({ theme: 'gamipress-select2-hidden' });

        // If this function doesn't triggers any error, then Select2 is correctly up to date
        jQuery("#gamipress-select2-version-check").select2('isOpen');

        if( show_in_console )
            console.log('Select2 is up to date!');

        return true;

    } catch(e) {
        // Select2 ver <= 3.x

        if( show_in_console )
            console.log('Select2 is outdated!');

        return false;
    }
}

/**
 * Check if post type has been registered
 *
 * @since 1.6.5
 *
 * @param {string} post_type
 *
 * @return {boolean}
 */
function gamipress_post_type_exists( post_type ) {
    return ( gamipress_admin_functions.post_type_labels[post_type] !== undefined )
}

/**
 * Get the post type label (singular name)
 *
 * @since 1.6.5
 *
 * @param {string} post_type
 *
 * @return {string}
 */
function gamipress_get_post_type_label( post_type ) {

    var label = '';

    if( gamipress_post_type_exists( post_type ) ) {
        label = gamipress_admin_functions.post_type_labels[post_type];
    }

    return label;
}

/**
 * Function to turn an object or a JSON object to a CSV file and force the download (Used on import/export tools)
 *
 * @since 1.6.4
 *
 * @param {Object} data
 * @param {string} filename
 */
function gamipress_download_csv( data, filename ) {

    // Convert JSON to CSV
    var csv = gamipress_object_to_csv( data );

    var file = ( filename.length ? filename + '.csv' : 'export.csv' );

    var blob = new Blob( [csv], { type: 'text/csv;charset=utf-8;' } );

    if (navigator.msSaveBlob) {

        // IE 10+
        navigator.msSaveBlob( blob, file );

    } else {

        var link = document.createElement("a");

        // Hide the link element
        link.style.visibility = 'hidden';

        // Check if browser supports HTML5 download attribute
        if ( link.download !== undefined ) {

            // Build the URL object
            var url = URL.createObjectURL( blob );

            // Update link attributes
            link.setAttribute( "href", url );
            link.setAttribute( "download", file );

            // Append the link element and trigger the click event
            document.body.appendChild( link );
            link.click();

            // Finally remove the link element
            document.body.removeChild( link );

        }
    }

}

/**
 * Format an object into a CSV line
 *
 * @since 1.6.4
 *
 * @param {Object} obj
 *
 * @return {string}
 */
function gamipress_object_to_csv( obj ) {

    // Convert JSON to Object
    var array = typeof obj !== 'object' ? JSON.parse( obj ) : obj;
    var str = '';

    for ( var i = 0; i < array.length; i++ ) {

        var line = '';

        for ( var index in array[i] ) {

            // Separator
            if ( line !== '' ) {
                line += ',';
            }

            // Build a new line
            line += '"' + array[i][index] + '"';
        }

        // Append the line break
        str += line + '\r\n';

    }

    return str;

}