( function( $ ) {

    $( document ).ready(function() {
        // Setup vars
        var user_id = parseInt( gamipress_events.user_id );
        var post_id = parseInt( gamipress_events.post_id );

        // Trigger track visit function at init
        gamipress_track_visit( user_id, post_id );
    });

    /**
     * Visit tracking
     *
     * @since   1.5.1
     * @updated 1.7.9 Added nonce usage
     *
     * @var int user_id
     * @var int post_id
     */
    function gamipress_track_visit( user_id, post_id ) {
        // Bail if user is not logged in
        if( user_id === 0 ) {
            return;
        }

        // Bail if user already visited this post
        if( gamipress_is_post_visited( post_id ) ) {
            return;
        }

        // Add the post ID to the list of posts visited
        gamipress_add_post_visited( post_id );

        $.ajax({
            url: gamipress_events.ajaxurl,
            type: 'POST',
            data: {
                action: 'gamipress_track_visit',
                nonce: gamipress_events.nonce,
                user_id: user_id,
                post_id: post_id
            },
            success: function(response) {
                // Debug success response
                if( gamipress_events.debug_mode ) {
                    console.log( response );
                }
            }
        }).fail( function (response) {
            // Debug any server error
            if( gamipress_events.debug_mode ) {
                console.log( response );
            }
        });
    }

    /**
     * Check if post has been visited yet
     *
     * @since 1.9.2
     *
     * @var int post_id
     */
    function gamipress_is_post_visited( post_id ) {

        var posts_visited = gamipress_get_posts_visited();

        return posts_visited.includes( post_id );

    }

    /**
     * Get the user posts visited (only if browser supports local storage)
     *
     * @since 1.9.2
     */
    function gamipress_get_posts_visited() {

        var posts_visited = [];

        if( window.localStorage ) {
            var posts_cache_date = window.localStorage.getItem('gamipress_posts_visited_date');

            // Check if cache date and server date matches
            if( posts_cache_date !== gamipress_events.server_date ) {
                // Reset posts visited cache for today
                window.localStorage.removeItem('gamipress_posts_visited');

                // Update cache date
                window.localStorage.setItem( 'gamipress_posts_visited_date', gamipress_events.server_date );
            } else {

                var posts_cached = window.localStorage.getItem('gamipress_posts_visited');

                if( posts_cached ) {
                    var posts_array = posts_cached.split(',');

                    // Turn array items to int
                    for( var i = 0; i < posts_array.length; i++ ) {

                        var post_id = parseInt( posts_array[i] );

                        // Prevent to insert duplicated entries
                        if( ! isNaN( post_id ) && ! posts_visited.includes( post_id ) ) {
                            posts_visited[posts_visited.length] = post_id;
                        }
                    }
                }

            }
        }

        return posts_visited;

    }

    /**
     * Add the post id given to the visited posts list
     *
     * @since 1.9.2
     *
     * @var int post_id
     */
    function gamipress_add_post_visited( post_id ) {

        if( window.localStorage ) {
            var posts_cached = window.localStorage.getItem('gamipress_posts_visited');

            if( ! posts_cached ) {
                posts_cached = '';
            }

            var posts_visited = posts_cached.split(',');

            posts_visited.push( post_id );

            window.localStorage.setItem( 'gamipress_posts_visited', posts_visited.join(',') );
        }

    }

} )( jQuery );