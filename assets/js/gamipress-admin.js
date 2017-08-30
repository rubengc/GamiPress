(function ( $ ) {

	// Dynamically show/hide achievement meta inputs based on "Award By" selection
	$("#_gamipress_earned_by").change( function() {

		// Define our potentially unnecessary inputs
		var gamipress_sequential = $('#_gamipress_sequential').parent().parent();
		var gamipress_points_required = $('#_gamipress_points_required').parent().parent();
		var gamipress_points_type_required = $('#_gamipress_points_type_required').parent().parent();

		// Hide our potentially unnecessary inputs
		gamipress_sequential.hide();
		gamipress_points_required.hide();
		gamipress_points_type_required.hide();

		// Determine which inputs we should show
		if ( 'triggers' == $(this).val() ) {
			gamipress_sequential.show();
		} else if ( 'points' == $(this).val() ) {
			gamipress_points_required.show();
			gamipress_points_type_required.show();
		}
	}).change();

	$('.gamipress-form').on( 'keyup', 'input#post_name', function() {
		var label = $('#post_type').val() === 'achievement-type' ? 'Achievement Type' : 'Points type';
		var field = $(this);
		var slug = $(this).val();
		var preview = $(this).next('.cmb2-metabox-description').find('.gamipress-post-name');

		if( preview.length ) {
			preview.text(slug);
		}

		// Delete any existing version of this warning
		$('#slug-warning').remove();

		// Throw a warning on Points/Achievement Type editor if slig is > 20 characters
		if ( slug.length > 20 ) {
			// Set input to look like danger
			field.css({'background':'#faa', 'color':'#a00', 'border-color':'#a55' });

			// Output a custom warning
			field.parent().append('<span id="slug-warning" class="cmb2-metabox-description" style="color: #a00;">' + label + '\'s slug supports a maximum of 20 characters.</span>');
		} else {
			// Restore the input style
			field.css({'background':'', 'color':'', 'border-color':''});
		}
	});

	$('.gamipress-form input#post_name').trigger( 'keyup' );

})( jQuery );
