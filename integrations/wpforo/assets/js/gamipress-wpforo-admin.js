(function( $ ) {

    $(  '.cmb2-id-wpforo-points-types .cmb2-list,' +
        ' .cmb2-id-wpforo-achievement-types .cmb2-list,' +
        ' .cmb2-id-wpforo-rank-types .cmb2-list'
    ).sortable({
        handle: 'label',
        placeholder: 'ui-state-highlight',
        forcePlaceholderSize: true,
    });
    
    // Helper function to show/hide/init Forums Select2
    function gamipress_wpforo_init_forums_select( trigger_type, wpf_forum_select ) {
        
        // Lets to check if there is a specific activity trigger
        if ( trigger_type === 'gamipress_wpforo_specific_forum_like_post'
            || trigger_type === 'gamipress_wpforo_specific_forum_new_post'
            || trigger_type === 'gamipress_wpforo_specific_forum_new_topic'
            || trigger_type === 'gamipress_wpforo_specific_forum_dislike_post'
            || trigger_type === 'gamipress_wpforo_specific_forum_vote_up_post'
            || trigger_type === 'gamipress_wpforo_specific_forum_vote_down_post'
            || trigger_type === 'gamipress_wpforo_specific_forum_answer_question' ) {
            // Show select post
            wpf_forum_select
                .show()
                .data( 'trigger-type', trigger_type )
                .data( 'post-type', 'wpforo_forum' )
            ;

            // Check if post selector Select2 has been initialized
            if( wpf_forum_select.hasClass('select2-hidden-accessible') ) {
                wpf_forum_select
                    .val('').trigger('change')   // Reset value
                    .next().show();     // Show Select2 container
            } else {
                wpf_forum_select.gamipress_select2({
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        type: 'POST',
                        data: function( params ) {
                            return {
                                q: params.term,
                                page: params.page || 1,
                                action: 'gamipress_wpforo_get_posts',
                                nonce: gamipress_requirements_ui.nonce,
                                post_type: $(this).data('post-type').split(','),
                                trigger_type: $(this).data('trigger-type'),
                            };
                        },
                        processResults: gamipress_select2_posts_process_results
                    },
                    escapeMarkup: function ( markup ) { return markup; }, // Let our custom formatter work
                    templateResult: gamipress_select2_posts_template_result,
                    theme: 'default gamipress-select2',
                    placeholder: gamipress_requirements_ui.post_placeholder,
                    allowClear: true,
                    multiple: false,
                });

                wpf_forum_select.on('select2:select', function (e) {
                    var item = e.params.data;

                    // If site ID is defined, then update the hidden field
                    if( item.site_id !== undefined ) {
                        $(this).siblings('.select-post-site-id').val( item.site_id );
                    }
                });
            }
        } else {
            // Hide select post
            wpf_forum_select.hide();

            if( wpf_forum_select.hasClass('select2-hidden-accessible') ) {
                wpf_forum_select.next().hide(); // Hide select2 container
            }
        }

    }

    // Helper function to show/hide/init Topics Select2
    function gamipress_wpforo_init_topics_select( trigger_type, wpf_topic_select ) {
        
         // Lets to check if there is a specific activity trigger
        if ( trigger_type === 'gamipress_wpforo_specific_topic_new_post'
        || trigger_type === 'gamipress_wpforo_specific_topic_like_post'
        || trigger_type === 'gamipress_wpforo_specific_topic_dislike_post'
        || trigger_type === 'gamipress_wpforo_specific_topic_vote_up_post'
        || trigger_type === 'gamipress_wpforo_specific_topic_vote_down_post' ) {
            // Show select post
            wpf_topic_select
                .show()
                .data( 'trigger-type', trigger_type )
                .data( 'post-type', 'wpforo_topic' )
            ;

            // Check if post selector Select2 has been initialized
            if( wpf_topic_select.hasClass('select2-hidden-accessible') ) {
                wpf_topic_select
                    .val('').trigger('change')   // Reset value
                    .next().show();     // Show Select2 container
            } else {
                wpf_topic_select.gamipress_select2({
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        type: 'POST',
                        data: function( params ) {
                            return {
                                q: params.term,
                                page: params.page || 1,
                                action: 'gamipress_wpforo_get_posts',
                                nonce: gamipress_requirements_ui.nonce,
                                post_type: $(this).data('post-type').split(','),
                                trigger_type: $(this).data('trigger-type'),
                            };
                        },
                        processResults: gamipress_select2_posts_process_results
                    },
                    escapeMarkup: function ( markup ) { return markup; }, // Let our custom formatter work
                    templateResult: gamipress_select2_posts_template_result,
                    theme: 'default gamipress-select2',
                    placeholder: gamipress_requirements_ui.post_placeholder,
                    allowClear: true,
                    multiple: false,
                });

                wpf_topic_select.on('select2:select', function (e) {
                    var item = e.params.data;

                    // If site ID is defined, then update the hidden field
                    if( item.site_id !== undefined ) {
                        $(this).siblings('.select-post-site-id').val( item.site_id );
                    }
                });
            }
        } else {
            // Hide select post
            wpf_topic_select.hide();

            if( wpf_topic_select.hasClass('select2-hidden-accessible') ) {
                wpf_topic_select.next().hide(); // Hide select2 container
            }
        }

    }

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var wpf_forum_select = $(this).siblings('.wpf-forum');
        var wpf_topic_select = $(this).siblings('.wpf-topic');

        gamipress_wpforo_init_forums_select( trigger_type, wpf_forum_select );
        gamipress_wpforo_init_topics_select( trigger_type, wpf_topic_select );

    });

    // Loop requirement list items to show/hide amount input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var wpf_forum_select = $(this).find('.wpf-forum');
        var wpf_topic_select = $(this).find('.wpf-topic');

        gamipress_wpforo_init_forums_select( trigger_type, wpf_forum_select );
        gamipress_wpforo_init_topics_select( trigger_type, wpf_topic_select );

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {
        
        if( requirement_details.trigger_type === 'gamipress_wpforo_specific_forum_like_post'
            || requirement_details.trigger_type === 'gamipress_wpforo_specific_forum_new_post'
            || requirement_details.trigger_type === 'gamipress_wpforo_specific_forum_new_topic'
            || requirement_details.trigger_type === 'gamipress_wpforo_specific_forum_dislike_post'
            || requirement_details.trigger_type === 'gamipress_wpforo_specific_forum_vote_up_post'
            || requirement_details.trigger_type === 'gamipress_wpforo_specific_forum_vote_down_post'
            || requirement_details.trigger_type === 'gamipress_wpforo_specific_forum_answer_question' ) {
            requirement_details.wpf_forum = requirement.find( '.wpf-forum' ).val();
        }

        if( requirement_details.trigger_type === 'gamipress_wpforo_specific_topic_new_post'
        || requirement_details.trigger_type === 'gamipress_wpforo_specific_topic_like_post'
        || requirement_details.trigger_type === 'gamipress_wpforo_specific_topic_dislike_post'
        || requirement_details.trigger_type === 'gamipress_wpforo_specific_topic_vote_up_post'
        || requirement_details.trigger_type === 'gamipress_wpforo_specific_topic_vote_down_post' ) {
            requirement_details.wpf_topic = requirement.find( '.wpf-topic' ).val();
        }

        
    });
})( jQuery );