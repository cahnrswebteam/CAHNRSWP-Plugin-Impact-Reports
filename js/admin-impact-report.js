var extension_impact_reports;

jQuery(document).ready(function($){

	extension_impact_reports = new impact_reports();
	extension_impact_reports.handle_media_uploader();

	// Duplicate the wptitlehint functionality
	var ir_label = $('#impact-report-additional-title-prompt-text'),
			ir_field = $('#ir_additional_title');
	ir_label.click(function(){
		$(this).addClass('screen-reader-text'),
		ir_field.focus()
	}),
	ir_field.blur(function() {
		"" === this.value && ir_label.removeClass('screen-reader-text')
	}).focus(function() {
		ir_label.addClass('screen-reader-text')
	});

	// Force the user to input a title before saving
	$('#post').submit(function() {
		if ( $('input[name="post_title"]').val() == '' ) {
			$('#title').css('border','1px solid #c60c30');
			alert( 'Please enter a title.' ); 
			$('#title').focus();
			$('#ajax-loading').hide();
			$('#publish').removeClass('button-primary-disabled');
			return false;  
		}
	}); 
	$( "#title" ).focus(function() {
		$( '#title' ).removeAttr('style');
	});

});

function impact_reports(){

	// Handling for additional images
	this.handle_media_uploader = handle_media_uploader;
	function handle_media_uploader(){
		var custom_uploader;
		// Set image
		jQuery('body').on('click', '.upload-image-button', function(e) {
			e.preventDefault();
			var upload_input = jQuery(this).parents('.upload-set-wrapper').find('.upload-image-id'),
					upload_link  = jQuery(this).parents('.upload-set-wrapper').find('.upload-image-button');
			
			//Extend the wp.media object
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});
			//When a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on('select', function() {
				attachment = custom_uploader.state().get('selection').first().toJSON();
				upload_input.val(attachment.id + '$S$' + attachment.url);
				upload_link.html('<img src="' + attachment.url + '" class="active" />');
				upload_link.after('<p class="hide-if-no-js"><a href="#" class="remove-ir-image">Remove ' + upload_link.attr('title') + '</a></p>');
			});
			//Open the uploader dialog
			custom_uploader.open();
		});
		// Remove image
		jQuery('body').on('click', '.remove-ir-image', function(e) {
			e.preventDefault();
			var upload_input = jQuery(this).parents('.upload-set-wrapper').find('.upload-image-id'),
					upload_link  = jQuery(this).parents('.upload-set-wrapper').find('.upload-image-button');

			// Clear the input value
			upload_input.val("");
			// Replace image with link "title" value
			upload_link.html('Set ' + upload_link.attr('title'));
			// Remove the "remove..." link
			jQuery(this).parent('.hide-if-no-js').remove();
		});
	}

}