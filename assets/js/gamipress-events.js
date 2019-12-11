( function( $ ) {

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
        if( user_id === 0 ) return;

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
                if(  gamipress_events.debug_mode )
                    console.log( response );
            }
        }).fail( function (response) {
            // Debug any server error
            if(  gamipress_events.debug_mode )
                console.log( response );
        });
    }

    $( document ).ready(function() {
        // Setup vars
        var user_id = parseInt( gamipress_events.user_id );
        var post_id = parseInt( gamipress_events.post_id );

        // Trigger track visit function at init
        gamipress_track_visit( user_id, post_id );
    });

} )( jQuery );