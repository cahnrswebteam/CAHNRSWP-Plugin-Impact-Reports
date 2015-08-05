(function($) {

	var next_page = parseInt( $( '#load-more-impact-reports a' ).data( 'page' ) ) + 1,
			total_pages = parseInt( $( '#load-more-impact-reports a' ).data( 'max' ) );

	$( '#load-more-impact-reports' ).on( 'click', 'a', function( event ) {

		event.preventDefault();

		var more_button = $(this);

		if ( next_page <= total_pages ) {

			$.ajax({
				url: impacts.ajaxurl,
				type: 'post',
				data: {
					action: 'ajax_loading',
					page: next_page
				},
				beforeSend: function() {
					more_button.text('Loading...');
				},
				success: function( html ) {
					$('#impact-reports').append( html );
					if ( next_page <= total_pages ) {
						more_button.text('More');
					} else {
						$( '#load-more-impact-reports' ).hide();
					}
				}
			})

		} else {

			$( '#load-more-impact-reports' ).hide();

		}	

		next_page++;

	})

})(jQuery);