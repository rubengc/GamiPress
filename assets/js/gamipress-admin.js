(function ($) {

	// Dynamically show/hide achievement meta inputs based on "Award By" selection
	$("#_gamipress_earned_by").change( function() {

		// Define our potentially unnecessary inputs
		var gamipress_sequential = $('#_gamipress_sequential').parent().parent();
		var gamipress_points_required = $('#_gamipress_points_required').parent().parent();
		var gamipress_points_type_required = $('#_gamipress_points_type_required').parent().parent();

		// // Hide our potentially unnecessary inputs
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

	// Throw a warning on Achievement Type editor if title is > 20 characters
	$('#titlewrap').on( 'keyup', 'input[name=post_title]', function() {

		// Make sure we're editing an achievement type
		if ( 'achievement-type' == $('#post_type').val() ) {
			// Cache the title input selector
			var $title = $(this);
			if ( $title.val().length > 20 ) {
				// Set input to look like danger
				$title.css({'background':'#faa', 'color':'#a00', 'border-color':'#a55' });

				// Output a custom warning (and delete any existing version of that warning)
				$('#title-warning').remove();
				$title.parent().append('<p id="title-warning">Achievement Type supports a maximum of 20 characters. Please choose a shorter title.</p>');
			} else {
				// Set the input to standard style, hide our custom warning
				$title.css({'background':'#fff', 'color':'#333', 'border-color':'#DFDFDF'});
				$('#title-warning').remove();
			}
		}
	} );

})(jQuery);
