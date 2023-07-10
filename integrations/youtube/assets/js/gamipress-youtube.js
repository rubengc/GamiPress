// Setup vars
var gamipress_youtube_videos = {};

function onYouTubeIframeAPIReady() {

    // Keep to confirm that this file was loaded
    if( gamipress_youtube.debug_mode )
        console.log( 'GamiPress - YouTube Iframe API' );

    // Get all video players on page
    var players = document.querySelectorAll( '.gamipress-youtube-video' );

    for (var i = 0; i < players.length; i++ ) {

        var player = players[i];

        // Remove the image preview element
        if( player.firstChild ) {
            player.removeChild(player.firstChild);
        }

        var iframe = new YT.Player( player, {
            videoId: player.getAttribute( 'data-id' ),
            height: player.getAttribute( 'data-height' ),
            width: player.getAttribute( 'data-width' ),
            playerVars: {
                'autoplay': ( player.getAttribute( 'data-autoplay' ) === 'yes' ? 1 : 0 ),
                'controls': ( player.getAttribute( 'data-controls' ) === 'yes' ? 1 : 0 ),
            },
            events : {
                'onStateChange': 'gamipress_youtube_video_change'
            }
        } );

    }

}

function gamipress_youtube_video_change( event ) {

    var data = event.target.getVideoData();
    var id = data.video_id;
    var duration = event.target.getDuration();
    var state = event.data;

    // Initialize video object
    if( gamipress_youtube_videos[id] === undefined ) {

        gamipress_youtube_videos[id] = {
            state: state,
            last_state: state,
            seconds: 0,
            duration: Math.floor( duration ),
            interval: undefined
        };

        // Start interval
        gamipress_youtube_videos[id].interval = setInterval( function() {

            if( gamipress_youtube_videos[id].last_state === YT.PlayerState.PLAYING )
                gamipress_youtube_videos[id].seconds++;

            // Update last state
            gamipress_youtube_videos[id].last_state = gamipress_youtube_videos[id].state;
        }, 1000 );

    }

    // Update the video state
    gamipress_youtube_videos[id].state = state;

    if( gamipress_youtube.debug_mode )
        console.log( id, state, gamipress_youtube_videos[id] );

    if( state === YT.PlayerState.PLAYING ) {
        // Play

    } else if( state === YT.PlayerState.PAUSED ) {
        // Pause

        // Clear the interval
        //clearInterval( gamipress_youtube_videos[id].interval );

    } else if( state === YT.PlayerState.ENDED ) {
        // End

        // Add an extra second to watched seconds to avoid any delay issue
        if( gamipress_youtube_videos[id].seconds < gamipress_youtube_videos[id].duration
            && ( gamipress_youtube_videos[id].seconds + gamipress_youtube.allowed_delay ) >= gamipress_youtube_videos[id].duration )
            gamipress_youtube_videos[id].seconds += gamipress_youtube.allowed_delay;

        // Update last state
        gamipress_youtube_videos[id].last_state = gamipress_youtube_videos[id].state;

        // Check if user has seen the video
        if( gamipress_youtube_videos[id].seconds >= gamipress_youtube_videos[id].duration ) {

            jQuery.ajax({
                url: gamipress_youtube.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gamipress_youtube_track_watch_video',
                    video_id: id,
                    seconds: gamipress_youtube_videos[id].seconds,
                    duration: gamipress_youtube_videos[id].duration,
                    user_id: gamipress_youtube.user_id,
                    post_id: gamipress_youtube.post_id
                },
                success: function(response) {

                    // Debug success response
                    if(  gamipress_youtube.debug_mode )
                        console.log( response );

                }
            }).fail( function (response) {

                // Debug any server error
                if(  gamipress_youtube.debug_mode )
                    console.log( response );

            });

        }

        // Clear the interval and reset seconds played
        clearInterval( gamipress_youtube_videos[id].interval );

        // Remove the video from the array to let the script start again
        delete gamipress_youtube_videos[id];

    }

}