/**
 * Helper function to initialize select2 on fields
 *
 * @since 1.6.6
 *
 * @param {Object} $this
 */
function gamipress_selector( $this ) {

    // Prevent load select2 on widgets lists
    if( $this.closest('#available-widgets').length )
        return;

    var select2_args = {
        theme: 'default gamipress-select2',
        placeholder: ( $this.data('placeholder') ? $this.data('placeholder') : gamipress_admin_functions.selector_placeholder ),
        allowClear: true,
        multiple: ( $this[0].hasAttribute('multiple') )
    };

    $this.gamipress_select2( select2_args );

}

/**
 * Helper function to initialize select2 post selector on fields
 *
 * @since 1.6.6
 *
 * @param {Object} $this
 */
function gamipress_post_selector( $this ) {

    // Prevent load select2 on widgets lists
    if( $this.closest('#available-widgets').length )
        return;

    var select2_args = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'gamipress_get_posts',
                    nonce: gamipress_admin_functions.nonce,
                    post_type: $this.data('post-type').split(','),
                };
            },
            processResults: gamipress_select2_posts_process_results
        },
        escapeMarkup: function ( markup ) { return markup; },
        templateResult: gamipress_select2_posts_template_result,
        theme: 'default gamipress-select2',
        placeholder: ( $this.data('placeholder') ? $this.data('placeholder') : gamipress_admin_functions.post_selector_placeholder ),
        allowClear: true,
        multiple: ( $this[0].hasAttribute('multiple') )
    };

    $this.gamipress_select2( select2_args );

}

/**
 * Helper function to initialize select2 user selector on fields
 *
 * @since 1.6.6
 *
 * @param {Object} $this
 */
function gamipress_user_selector( $this ) {

    // Prevent load select2 on widgets lists
    if( $this.closest('#available-widgets').length )
        return;

    var select2_args = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'gamipress_get_users',
                    nonce: gamipress_admin_functions.nonce,
                };
            },
            processResults: gamipress_select2_users_process_results
        },
        escapeMarkup: function ( markup ) { return markup; },
        templateResult: gamipress_select2_users_template_result,
        theme: 'default gamipress-select2',
        placeholder: ( $this.data('placeholder') ? $this.data('placeholder') : gamipress_admin_functions.user_selector_placeholder ),
        allowClear: true,
        multiple: ( $this[0].hasAttribute('multiple') )
    };

    $this.gamipress_select2( select2_args );

}

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
 * Check if given term is on reserved terms
 *
 * @since 1.7.4
 *
 * @param {string} term
 *
 * @return {boolean}
 */
function gamipress_is_reserved_term( term ) {
    return ( gamipress_admin_functions.reserved_terms.indexOf( term ) !== -1 )
}

/**
 * Check if given slug has any error (like invalid chars, exceeds lenght, etc)
 *
 * @since 1.7.4
 *
 * @param {string} slug
 * @param {string} current_slug
 *
 * @return {string}
 */
function gamipress_get_slug_error( slug, current_slug ) {

    // Only allow alphanumeric characters, "-" adn "_"
    if( /[^a-zA-Z0-9\-_]/.test( slug ) )
        return gamipress_admin_functions.slug_error_special_char;

    // Check if slug is greater than 20 characters (maximum allowed for a post type)
    if ( slug.length > 20 )
        return gamipress_admin_functions.slug_error_max_length;

    // Check if slug has been already registered
    if( gamipress_post_type_exists( slug ) && slug !== current_slug )
        return gamipress_admin_functions.slug_error_post_type.replace( '%s', gamipress_get_post_type_label( slug ) );

    // Check if slug matches a reserved term
    if ( gamipress_is_reserved_term( slug ) )
        return gamipress_admin_functions.slug_error_reserved_term;

    // No errors
    return '';
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

    gamipress_download_file( csv, filename, 'csv' );

}

/**
 * Function to force the download of the given content (Used on import/export tools)
 *
 * @since 1.7.0
 *
 * @param {string} content
 * @param {string} filename
 * @param {string} extension
 * @param {string} mime_type
 * @param {string} charset
 */
function gamipress_download_file( content, filename, extension, mime_type = '', charset = '' ) {

    if( mime_type === undefined || mime_type === '' )
        mime_type = 'text/' + extension;

    if( charset === undefined || charset === '' )
        charset = 'utf-8';

    // Setup the file name
    var file = ( filename.length ? filename + '.' + extension : 'file.' + extension );

    var blob = new Blob( [content], { type: mime_type + ';charset=' + charset + ';' } );

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

            link.click(); // NOTE: Is not a jQuery element, so is safe to use click()

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

/**
 * Helper function to get a parameter from an URL
 *
 * @since 1.0.0
 *
 * @param {String} url
 * @param {String} param
 * @param default_value
 *
 * @return {String}
 */
function gamipress_get_url_param( url, param, default_value = false ) {

    var results = new RegExp('[\?&]' + param + '=([^&#]*)').exec( url );

    return results[1] || default_value;

}

/**
 * Helper function to check if given URL is a valid one
 *
 * @since 1.0.0
 *
 * @param {String} url
 *
 * @return {boolean}
 */
function gamipress_is_valid_url( url ) {
    var result = url.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return ( result !== null )
}