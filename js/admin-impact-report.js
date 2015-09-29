jQuery(document).ready(function($){

	// Duplicate the wptitlehint functionality
	var ir_label = $( '#impact-report-additional-title-prompt-text' ),
			ir_field = $( '#impact_report_additional_title' );

	ir_label.click( function() {
		$(this).addClass( 'screen-reader-text' ),
		ir_field.focus()
	}),
	ir_field.blur( function() {
		'' === this.value && ir_label.removeClass( 'screen-reader-text' )
	}).focus( function() {
		ir_label.addClass( 'screen-reader-text' )
	});

	// Force the user to input a title before saving
	$( '#post' ).submit( function() {
		if ( $( 'input[name="post_title"]' ).val() == '' ) {
			$( '#title' ).css( 'border', '1px solid #c60c30' );
			alert( 'Please enter a title.' ); 
			$( '#title' ).focus();
			$( '#ajax-loading' ).hide();
			$( '#publish' ).removeClass( 'button-primary-disabled' );
			return false;  
		}
	} ); 

	$( '#title' ).focus( function() {
		$( '#title' ).removeAttr( 'style' );
	} );

	// Upload handling.
	var custom_uploader;

	$( '#impact_report_images' ).on( 'click', '.upload-image-button', function(e) {

		e.preventDefault();

		var upload_link  = $(this),
				container    = upload_link.parents( '.upload-set-wrapper' ),
				upload_input = container.find( '.upload-image-id' ),
				remove_link  = container.find( '.remove-ir-image' );

		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});

		custom_uploader.on( 'select', function() {
			attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
			upload_input.val( attachment.id + '$S$' + attachment.url );
			if ( attachment.sizes.hasOwnProperty( 'thumbnail' ) ) {
				upload_link.html( '<img src="' + attachment.sizes.medium.url + '" />' );
			} else {
				upload_link.html( '<img src="' + attachment.url + '" />' );
			}
			if ( remove_link.length === 0 ) {
				upload_link.after( '<p class="hide-if-no-js"><a href="#" class="remove-ir-image">Remove ' + upload_link.attr('title') + '</a></p>' );
			}
		});

		custom_uploader.open();

	});

	// Upload "Remove" handling.
	$( '#impact_report_images' ).on( 'click', '.remove-ir-image', function(e) {

		e.preventDefault();

		var upload_input = $(this).parents( '.upload-set-wrapper' ).find( '.upload-image-id' ),
				upload_link  = $(this).parents( '.upload-set-wrapper' ).find( '.upload-image-button' );

		upload_input.val( '' );
		upload_link.html( 'Set ' + upload_link.attr( 'title' ) );
		$(this).parent( '.hide-if-no-js' ).remove();

	});

});