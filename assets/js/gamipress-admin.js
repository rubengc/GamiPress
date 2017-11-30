(function ( $ ) {

	// Dynamically show/hide achievement meta inputs based on "Award By" selection
	$("#_gamipress_earned_by").change( function() {

		// Define our potentially unnecessary inputs
		var gamipress_sequential = $('#_gamipress_sequential').parent().parent();
		var gamipress_points_required = $('#_gamipress_points_required').parent().parent();
		var gamipress_points_type_required = $('#_gamipress_points_type_required').parent().parent();
		var gamipress_rank_type_required = $('#_gamipress_rank_type_required').parent().parent();
		var gamipress_rank_required = $('#_gamipress_rank_required').parent().parent();

		// Hide our potentially unnecessary inputs
		gamipress_sequential.hide();
		gamipress_points_required.hide();
		gamipress_points_type_required.hide();
		gamipress_rank_type_required.hide();
		gamipress_rank_required.hide();

		// Determine which inputs we should show
		if ( 'triggers' == $(this).val() ) {
			gamipress_sequential.show();
		} else if ( 'points' == $(this).val() ) {
			gamipress_points_required.show();
			gamipress_points_type_required.show();
		} else if ( 'rank' == $(this).val() ) {
			gamipress_rank_type_required.show();
			$('#_gamipress_rank_type_required').change();
			//gamipress_rank_required.show();
		}
	}).change();

	$('#_gamipress_rank_type_required').change(function() {

		var $this = $(this);

		var rank_type = $(this).val();

		if( rank_type === '' ) {
			return;
		}

		$('<span class="spinner is-active" style="float: none;"></span>').insertAfter( $this );

		var gamipress_rank_required = $('#_gamipress_rank_required').parent().parent();
		var gamipress_rank_required_select = $('#_gamipress_rank_required');

		gamipress_rank_required.hide();

		if( rank_type !== '' ) {

			$.post(
				ajaxurl,
				{
					action: 'gamipress_get_ranks_options_html',
					post_type: rank_type,
					selected: gamipress_rank_required_select.val(),
				},
				function( response ) {

					$this.next('.spinner').remove();

					gamipress_rank_required_select.html( response );
					gamipress_rank_required.show();
				}
			);

		} else {
			gamipress_rank_required.hide();
		}
	});

	$('.gamipress-form').on( 'keyup', 'input#post_name', function() {
		var field = $(this);
		var slug = $(this).val();
		var preview = $(this).next('.cmb2-metabox-description').find('.gamipress-post-name');

		if( preview.length ) {
			preview.text(slug);
		}

		var label = '';

		if(  $('#post_type').val() === 'achievement-type' ) {
			label = 'Achievement Type';
		} else if(  $('#post_type').val() === 'rank-type' ) {
			label = 'Rank Type';
		} else {
			label = 'Points Type';
		}

		// Delete any existing version of this warning
		$('#slug-warning').remove();

		// Throw a warning on Points/Achievement Type editor if slig is > 20 characters
		if ( slug.length > 20 ) {
			// Set input to look like danger
			field.css({'background':'#faa', 'color':'#a00', 'border-color':'#a55' });

			// Output a custom warning
			// TODO: Localization here
			field.parent().append('<span id="slug-warning" class="cmb2-metabox-description" style="color: #a00;">' + label + '\'s slug supports a maximum of 20 characters.</span>');
		} else {
			// Restore the input style
			field.css({'background':'', 'color':'', 'border-color':''});
		}
	});

	$('.gamipress-form input#post_name').trigger( 'keyup' );

	// Auto initialize upgrade if user reloads the page during an upgrade
	if( $('#gamipress-upgrade-notice').find('.gamipress-upgrade-progress[data-running-upgrade]').length ) {
		gamipress_start_upgrade( $('#gamipress-upgrade-notice').find('.gamipress-upgrade-progress[data-running-upgrade]').data('running-upgrade') );
	}

})( jQuery );

var gamipress_current_upgrade_info;
var gamipress_current_upgrade_progress;

// Start upgrade
function gamipress_start_upgrade( version ) {

	var $ = $ || jQuery;
	version = version.replace('.', '').replace('.', '').replace('.', '');

	$('#gamipress-upgrade-notice').html('<p>Upgrading GamiPress database...</p><div class="gamipress-upgrade-progress"><div class="gamipress-upgrade-progress-bar" style="width: 0%;"></div></div>');

	$.ajax({
		url: ajaxurl,
		data: {
			action: 'gamipress_' + version + '_upgrade_info'
		},
		success: function( response ) {

			// Upgrade done!
			if( response.data.upgraded !== undefined && response.data.upgraded ) {
				$('#gamipress-upgrade-notice').html('<p>Upgrade has been already completed.</p><div class="gamipress-upgrade-progress"><div class="gamipress-upgrade-progress-bar" style="width: 100%;"></div></div>');
				return;
			}

			// Update progress vars
			gamipress_current_upgrade_info = response.data.total;
			gamipress_current_upgrade_progress = 0;

			gamipress_run_upgrade( version );

		},
		error: function( response ) {
			gamipress_stop_upgrade( version );
			$('#gamipress-upgrade-notice').html('<p class="error">Upgrading process failed.</p>');
		}
	});

}

// Run upgrade
function gamipress_run_upgrade( version ) {

	var $ = $ || jQuery;
	version = version.replace('.', '').replace('.', '').replace('.', '');

	$.ajax({
		url: ajaxurl,
		data: {
			action: 'gamipress_process_' + version + '_upgrade',
			current: gamipress_current_upgrade_progress,
		},
		success: function( response ) {

			// Upgrade done!
			if( response.data.upgraded !== undefined && response.data.upgraded ) {
				$('#gamipress-upgrade-notice').html('<p>Upgrading process finished successfully.</p><div class="gamipress-upgrade-progress"><div class="gamipress-upgrade-progress-bar" style="width: 100%;"></div></div>');
				return;
			}

			gamipress_current_upgrade_progress = response.data.current;

			// Upgraded successfully
			if( gamipress_current_upgrade_progress >= gamipress_current_upgrade_info ) {
				$('#gamipress-upgrade-notice').html('<p>Upgrading process finished successfully.</p><div class="gamipress-upgrade-progress"><div class="gamipress-upgrade-progress-bar" style="width: 100%;"></div></div>');
				return;
			}

			// Update progress bar width
			$('#gamipress-upgrade-notice .gamipress-upgrade-progress .gamipress-upgrade-progress-bar').attr('style', 'width: ' + ( ( gamipress_current_upgrade_progress / gamipress_current_upgrade_info ) * 100 ) + '%')

			gamipress_run_upgrade( version );

		},
		error: function( response ) {
			gamipress_stop_upgrade( version );
			$('#gamipress-upgrade-notice').html('<p class="error">Upgrading process failed.</p>');
		}
	});

}

// Stop upgrade
function gamipress_stop_upgrade( version ) {

	var $ = $ || jQuery;
	version = version.replace('.', '').replace('.', '').replace('.', '');

	$.ajax({
		url: ajaxurl,
		data: {
			action: 'gamipress_stop_process_' + version + '_upgrade'
		}
	});

}
