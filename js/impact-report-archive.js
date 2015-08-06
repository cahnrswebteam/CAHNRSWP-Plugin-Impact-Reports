(function($) {

	var next_page = parseInt( $( '#load-more-impact-reports a' ).data( 'page' ) ) + 1,
			total_pages = parseInt( $( '#load-more-impact-reports a' ).data( 'max' ) );

	// Pagination
	$( '#load-more-impact-reports' ).on( 'click', 'a', function( event ) {

		event.preventDefault();

		var more_button = $(this);

		more_button.data( 'loaded', next_page );

		if ( next_page <= total_pages ) {

			$.ajax({
				url: impacts.ajaxurl,
				type: 'post',
				data: {
					action: 'ajax_post_request',
					page: next_page
				},
				beforeSend: function() {
					more_button.text( 'Loading...' );
				},
				success: function( html ) {
					$( '#impact-reports' ).append( html );
					if ( next_page <= total_pages ) {
						more_button.text( 'More' );
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

	// Term filter
	$( '.browse-terms' ).on( 'click', 'a', function( event ) {

		event.preventDefault();

		var term = $(this),
				type = term.data( 'type' ),
				id   = term.data( 'id' );

		if ( ! term.hasClass( 'active' ) ) {

			$( '.browse-terms > li > a' ).removeClass( 'active' );
			
			if ( 'top' == term.data( 'level' ) ) {
				$( '.browse-terms.topics > li > ul' ).remove();
			}

			var slug = term.data( 'slug' );

			$( '#load-more-impact-reports' ).hide();

			term.addClass( 'active' );

			$.ajax({
				url: impacts.ajaxurl,
				type: 'post',
				data: {
					action: 'ajax_post_request',
					type: type,
					term: slug,
				},
				beforeSend: function() {
					term.text( 'Loading...' );
					if ( typeof id !== 'undefined' ) {
						$.ajax({
							url: impacts.ajaxurl,
							type: 'post',
							data: {
								action: 'ajax_taxonomy_request',
								type: type,
								id: id,
							},
							success: function( html ) {
								term.parent( 'li' ).append( html );
							}
						})
					}
				},
				success: function( html ) {
					$( '#impact-reports' ).html( html );
					term.text( term.data( 'name' ) );
				}
			})

		} else {

			var loaded = $( '#load-more-impact-reports a' ).data( 'loaded' );

			term.removeClass( 'active' );

			if ( 'top' == term.data( 'level' ) ) {
				$( '.browse-terms.topics > li > ul' ).remove();
			}

			$.ajax({
				url: impacts.ajaxurl,
				type: 'post',
				data: {
					action: 'ajax_post_request',
					reset: loaded,
				},
				success: function( html ) {
					$( '#impact-reports' ).html( html );
					if ( next_page <= total_pages ) {
						$( '#load-more-impact-reports' ).show();
					}
				}
			})

		}

	})

})(jQuery);