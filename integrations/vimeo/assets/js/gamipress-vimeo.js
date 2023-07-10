// Setup vars
var gamipress_vimeo_videos = {};
var gamipress_vimeo_players = {};

// Get all video players on page
var players = document.querySelectorAll( '.gamipress-vimeo-video' );

for (var i = 0; i < players.length; i++ ) {

    var player = players[i];

    // Remove the image preview element
    if( player.firstChild ) {
        player.removeChild(player.firstChild);
    }

    var id = player.getAttribute( 'data-id' );
    var url = player.getAttribute( 'data-url' );
    var from_url = player.getAttribute( 'data-from-url' );

    gamipress_vimeo_players[id] = new Vimeo.Player( player, {
        id: ( from_url === 'yes' ? url : id ),
        height: player.getAttribute( 'data-height' ),
        width: player.getAttribute( 'data-width' )
    } );

    // Play
    gamipress_vimeo_players[id].on('play', function(e) {

        var id = this._originalElement.getAttribute( 'data-id' );

        console.log( 'play', e );

        gamipress_vimeo_video_change( id, e.duration, 'play' );

    });

    // Pause
    gamipress_vimeo_players[id].on('pause', function(e) {

        var id = this._originalElement.getAttribute( 'data-id' );

        console.log( 'pause', e );

        gamipress_vimeo_video_change( id, e.duration, 'pause' );

    });

    // Ended
    gamipress_vimeo_players[id].on('ended', function(e) {

        var id = this._originalElement.getAttribute( 'data-id' );

        console.log( 'ended', e );

        gamipress_vimeo_video_change( id, e.duration, 'ended' );

    });

}

function gamipress_vimeo_video_change( id, duration, state ) {

    // Initialize video object
    if( gamipress_vimeo_videos[id] === undefined ) {

        gamipress_vimeo_videos[id] = {
            state: state,
            last_state: state,
            seconds: 0,
            duration: Math.floor( duration ),
            interval: undefined
        };

        // Start interval
        gamipress_vimeo_videos[id].interval = setInterval( function() {

            // Only increase seconds if the video gets watched
            if( gamipress_vimeo_videos[id].last_state === 'play' )
                gamipress_vimeo_videos[id].seconds++;

            // Update last state
            gamipress_vimeo_videos[id].last_state = gamipress_vimeo_videos[id].state;
        }, 1000 );

    }

    // Update the video state
    gamipress_vimeo_videos[id].state = state;

    console.log( id, state, gamipress_vimeo_videos[id] );

    if( state === 'play' ) {
        // Play

    } else if( state === 'pause' ) {
        // Pause

        // Clear the interval
        //clearInterval( gamipress_vimeo_videos[id].interval );

    } else if( state === 'ended' ) {
        // End

        // Add an extra second to watched seconds to avoid any delay issue
        if( gamipress_vimeo_videos[id].seconds < gamipress_vimeo_videos[id].duration
            && ( gamipress_vimeo_videos[id].seconds + gamipress_vimeo.allowed_delay ) >= gamipress_vimeo_videos[id].duration )
            gamipress_vimeo_videos[id].seconds += gamipress_vimeo.allowed_delay;

        // Update last state
        gamipress_vimeo_videos[id].last_state = gamipress_vimeo_videos[id].state;

        // Check if user has seen the video
        if( gamipress_vimeo_videos[id].seconds >= gamipress_vimeo_videos[id].duration ) {

            jQuery.ajax({
                url: gamipress_vimeo.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gamipress_vimeo_track_watch_video',
                    video_id: id,
                    seconds: gamipress_vimeo_videos[id].seconds,
                    duration: gamipress_vimeo_videos[id].duration,
                    user_id: gamipress_vimeo.user_id,
                    post_id: gamipress_vimeo.post_id
                },
                success: function(response) {

                    // Debug success response
                    if(  gamipress_vimeo.debug_mode )
                        console.log( response );

                }
            }).fail( function (response) {

                // Debug any server error
                if(  gamipress_vimeo.debug_mode )
                    console.log( response );

            });

        }

        // Clear the interval and reset seconds played
        clearInterval( gamipress_vimeo_videos[id].interval );

        // Remove the video from the array to let the script start again
        delete gamipress_vimeo_videos[id];

    }

}