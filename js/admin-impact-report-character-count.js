/**
 * Impact Report character counter tinyMCE plugin.
 *
 * Counts and limits the number of characters for the sections of an impact report.
 * 
 * References:
 *	http://stackoverflow.com/questions/11342921/limit-the-number-of-character-in-tinymce?rq=1
 *	http://stackoverflow.com/questions/12531113/is-there-a-way-to-apply-characters-limit-inside-wp-editor-function
 */
jQuery(function($) {

	var main_limit = 4500;

	tinymce.create( 'tinymce.plugins.impact_report_characters', {
		init : function(ed) {
			ed.on('KeyDown', function(ed, e) {

				if ( 8 == ed.keyCode || 32 == ed.keyCode || 190 == ed.keyCode ) {
					return true;
				}
 
				// Main body sections
				if ( tinyMCE.activeEditor.id == 'ir_issue' || tinyMCE.activeEditor.id == 'ir_response' || tinyMCE.activeEditor.id == 'ir_impacts' ) {
					issue_count    = $.trim(tinymce.editors.ir_issue.getContent().replace(/(<([^>]+)>)/ig,'')).length;
					response_count = $.trim(tinymce.editors.ir_response.getContent().replace(/(<([^>]+)>)/ig,'')).length;
					impacts_count  = $.trim(tinymce.editors.ir_impacts.getContent().replace(/(<([^>]+)>)/ig,'')).length;
					total          = issue_count + response_count + impacts_count;
					remainder      = main_limit - total;
					$('.ir-main-counter span').html( remainder );
					if ( total > main_limit ) {
						alert( 'You have exceeded the maximum amount of content for the main body of an impact report.' );
						ed.stopPropagation();
						ed.preventDefault();
					}
				}

				// Front page sidebar
				if ( tinyMCE.activeEditor.id == 'ir_numbers' ) {
					count = $.trim(tinymce.editors.ir_numbers.getContent().replace(/(<([^>]+)>)/ig,'')).length;
					$('.ir-front-sidebar-counter span').html( 900 - count );
					if( count > 900 ) {
						alert( 'You have exceeded the maximum amount of content for the front page sidebar.' );
						ed.stopPropagation();
						ed.preventDefault();
					}
				}

				// Back page sidebar
				if( tinyMCE.activeEditor.id == 'ir_quotes' || tinyMCE.activeEditor.id == 'ir_additional' ) {
					quotes_count     = $.trim(tinymce.editors.ir_quotes.getContent().replace(/(<([^>]+)>)/ig,'')).length;
					additional_count = $.trim(tinymce.editors.ir_additional.getContent().replace(/(<([^>]+)>)/ig,'')).length;
					total            = quotes_count + additional_count;
					$('.ir-back-sidebar-counter span').html( 900 - total );
					if( total > 900 ) {
						alert( 'You have exceeded the maximum amount of content for the back page sidebar.' );
						ed.stopPropagation();
						ed.preventDefault();
					}
				}

			});
		},
	});
	tinymce.PluginManager.add( 'impact_report_character_counter', tinymce.plugins.impact_report_characters );

});