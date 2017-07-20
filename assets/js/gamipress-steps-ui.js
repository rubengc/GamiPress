(function($) {
	// Hide our Triggers metabox if unnecessary
	$("#_gamipress_earned_by").change( function() {
		if ( 'triggers' == $(this).val() )
			$('#gamipress_steps_ui').show();
		else
			$('#gamipress_steps_ui').hide();
	}).change();

	// Make our Triggers list sortable
	$("#steps_list").sortable({

		// When the list order is updated
		update : function () {

			// Loop through each element
			$('#steps_list li').each(function( index, value ) {

				// Write it's current position to our hidden input value
				$(this).children('input[name="order"]').val( index );

			});

		}
	});

	// Listen for our change to our trigger type selectors
	$('#steps_list').on( 'change', '.select-trigger-type', function() {

		// Grab our selected trigger type and achievement selector
		var trigger_type = $(this).val();
		var achievement_selector = $(this).siblings('.select-achievement-type');

		// If we're working with achievements, show the achievement selecter (otherwise, hide it)
		if ( 'any-achievement' == trigger_type || 'all-achievements' == trigger_type || 'specific-achievement' == trigger_type ) {
			achievement_selector.show();
		} else {
			achievement_selector.hide();
		}

		// Trigger a change for our achievement type post selector to determine if it should show
		achievement_selector.change();

	});
	// Trigger a change for our trigger type post selector to determine if it should show
	$( '.select-trigger-type' ).change();

	// Listen for a change to our achivement type selectors
	$('#steps_list').on( 'change', '.select-achievement-type', function() {

		// Setup our necessary variables
		var achievement_selector = $(this);
		var achievement_type     = achievement_selector.val();
		var step_id              = achievement_selector.parent('li').attr('data-step-id');
		var excluded_posts       = [achievement_selector.siblings('input[name="post_id"]').val()];
		var trigger_type         = achievement_selector.siblings('.select-trigger-type').val();

		// If we've selected a *specific* achievement type, show our post selector
		// and populate it w/ the corresponding achievement posts
		if ( '' !== achievement_type && 'specific-achievement' == trigger_type ) {
            achievement_selector.siblings('.select-post').hide();
            achievement_selector.siblings('.select-post.select2-hidden-accessible').next().hide();

			$.post(
				ajaxurl,
				{
					action: 'post_select_ajax',
					achievement_type: achievement_type,
					step_id: step_id,
					excluded_posts: excluded_posts
				},
				function( response ) {
					achievement_selector.siblings('select.select-achievement-post').html( response );
					achievement_selector.siblings('select.select-achievement-post').show();
				}
			);

		// Otherwise, keep our post selector hidden
		} else {
			achievement_selector.siblings('.select-achievement-post').hide();
			achievement_selector.siblings('.select-post').hide();
            achievement_selector.siblings('.select-post.select2-hidden-accessible').next().hide();

			if ( gamipress_steps_ui.specific_activity_triggers[trigger_type] !== undefined ) {
				achievement_selector.siblings('.select-post').show().data( 'post-type', gamipress_steps_ui.specific_activity_triggers[trigger_type].join(',') );
                achievement_selector.siblings('.select-post.select2-hidden-accessible').next().show();

                achievement_selector.siblings( '.select-post:not(.select2-hidden-accessible)' ).select2({
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        type: 'POST',
                        data: function( params ) {
                            return {
                                q: params.term,
                                action: 'gamipress_get_posts',
                                post_type: $(this).data('post-type').split(',')
                            };
                        },
                        processResults: function( results, page ) {
                            if( results === null ) {
                                return { results: [] };
                            }

                            var formatted_results = [];

                            results.data.forEach(function(item) {
                                formatted_results.push({
                                    id: item.ID,
                                    text: item.post_title,
                                });
                            });

                            return { results: formatted_results };
                        }
                    },
                    theme: 'default gamipress-select2',
                    placeholder: gamipress_steps_ui.post_placeholder,
                    allowClear: true,
                    multiple: false
                });
			}
		}
	});

	// Limit inputs
	$('#steps_list').on( 'change', '.limit-type', function() {
		var limit_type_selector = $(this);

		if( limit_type_selector.val() === 'unlimited' ) {
			limit_type_selector.siblings('.limit').hide();
		} else {
			limit_type_selector.siblings('.limit').show();
		}
	});

	// Trigger a change for our achievement type post selector to determine if it should show
	$( '.select-achievement-type' ).change();
	$( '.limit-type' ).change();
})(jQuery);

// Add a step
function gamipress_add_new_step( achievement_id ) {
	jQuery.post(
		ajaxurl,
		{
			action: 'add_step',
			achievement_id: achievement_id
		 },
		function( response ) {
			jQuery( response ).appendTo( '#steps_list' );

			// Dynamically add the menu order for the new step to be one higher than the last in line
			var new_step_menu_order = Number( jQuery( '#steps_list li.step-row' ).eq( -2 ).children( 'input[name="order"]' ).val() ) + 1;
			jQuery( '#steps_list li.step-row:last' ).children( 'input[name="order"]' ).val( new_step_menu_order );

			// Trigger a change for the new trigger type <select> element
			jQuery( '#steps_list li.step-row:last' ).children( '.select-trigger-type' ).change();
			jQuery( '#steps_list li.step-row:last' ).children( '.limit-type' ).change();
		}
	);
}

// Delete a step
function gamipress_delete_step( step_id ) {
	jQuery.post(
		ajaxurl,
		{
			action: 'delete_step',
			step_id: step_id
		},
		function( response ) {
			jQuery( '.step-' + step_id ).remove();
		}
	);
}

// Update all steps
function gamipress_update_steps() {

	jQuery( '.save-steps-spinner' ).show();
	step_data = {
		action: 'update_steps',
		steps: []
	};

	// Loop through each step and collect its data
	jQuery( '.step-row' ).each( function() {

		// Cache our step object
		var step = jQuery(this);
		var trigger_type = step.find( '.select-trigger-type' ).val();

		// Setup our step object
		var step_details = {
			step_id          : step.attr( 'data-step-id' ),
			order            : step.find( 'input[name="order"]' ).val(),
			required_count   : step.find( '.required-count' ).val(),
			limit            : step.find( '.limit' ).val(),
			limit_type       : step.find( '.limit-type' ).val(),
			trigger_type     : trigger_type,
			achievement_type : step.find( '.select-achievement-type' ).val(),
			achievement_post : ( gamipress_steps_ui.specific_activity_triggers[trigger_type] !== undefined ? step.find( '.select-post' ).val() : step.find( 'select.select-achievement-post' ).val() ),
			title            : step.find( '.step-title .title' ).val()
		};

		// Allow external functions to add their own data to the array
		step.trigger( 'update_step_data', [ step_details, step ] );

		// Add our relevant data to the array
		step_data.steps.push( step_details );

	});

	jQuery.post(
		ajaxurl,
		step_data,
		function( response ) {
			// Parse response
			var titles = jQuery.parseJSON( response );

			// Update each step titles
			jQuery.each( titles, function( id, value ) {
				jQuery('#step-' + id + '-title').val(value);
			});

			// Hide our save spinner
			jQuery( '.save-steps-spinner' ).hide();
		}
	);
}
