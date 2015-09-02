/**
 * Impact Report character counter tinyMCE plugin.
 *
 * Counts and limits the number of characters for the sections of an impact report.
 */
jQuery(function($) {

	var whitelist_keys = [ 8, 16, 17, 37, 38, 38, 40, 46, 93, 224 ];

	tinymce.create( 'tinymce.plugins.impact_report_characters', {
		init : function(ed) {
			ed.on('KeyDown', function(ed, e) {

				if ( 16 == ed.keyCode /*|| 37 == ed.keyCode || 38 == ed.keyCode || 39 == ed.keyCode || 40 == ed.keyCode*/ ) {
					return true;
				}

				// Summary
				if ( tinyMCE.activeEditor.id == 'impact_report_summary' ) {
					count = tinymce.editors.impact_report_summary.getContent().replace( /<[^>]+>/g, '' ).length;
					$('.ir-summary-counter span').html( 140 - count );
					if ( count > 140 ) {
						$('.ir-summary-counter span').css({ 'color': '#c60c30', 'font-weight': 'bold' });
						if ( $.inArray( ( ed.keyCode || ed.which ), whitelist_keys ) == -1 ) {
							alert( 'You have exceeded the maximum character count for an Impact Report summary.' );
							ed.stopPropagation();
							ed.preventDefault();
						}
					} else {
						$('.ir-summary-counter span').removeAttr('style');
					}
				}

				// Main body sections
				if ( tinyMCE.activeEditor.id == 'impact_report_issue' || tinyMCE.activeEditor.id == 'impact_report_response' || tinyMCE.activeEditor.id == 'impact_report_impacts' ) {
					issue_count    = tinymce.editors.impact_report_issue.getContent().replace( /<[^>]+>/g, '' ).length;
					response_count = tinymce.editors.impact_report_response.getContent().replace( /<[^>]+>/g, '' ).length;
					impacts_count  = tinymce.editors.impact_report_impacts.getContent().replace(/<[^>]+>/g, '' ).length;
					total          = issue_count + response_count + impacts_count;
					$('.ir-main-counter span').html( 4500 - total );
					if ( total > 4500 ) {
						$('.ir-main-counter span').css({ 'color': '#c60c30', 'font-weight': 'bold' });
						if ( $.inArray( ( ed.keyCode || ed.which ), whitelist_keys ) == -1 ) {
							alert( 'You have exceeded the maximum amount of content for the main body of an Impact Report.' );
							ed.stopPropagation();
							ed.preventDefault();
						}
					} else {
						$('.ir-main-counter span').removeAttr('style');
					}
				}

				// Front page sidebar
				if ( tinyMCE.activeEditor.id == 'impact_report_numbers' ) {
					count = $.trim(tinymce.editors.impact_report_numbers.getContent().replace(/<[^>]+>/g,'')).length;
					$('.ir-front-sidebar-counter span').html( 900 - count );
					if( count > 900 ) {
						$('.ir-front-sidebar-counter span').css({ 'color': '#c60c30', 'font-weight': 'bold' });
						if ( $.inArray( ( ed.keyCode || ed.which ), whitelist_keys ) == -1 ) {
							alert( 'You have exceeded the maximum amount of content for the front page sidebar.' );
							ed.stopPropagation();
							ed.preventDefault();
						}
					} else {
						$('.ir-front-sidebar-counter span').removeAttr('style');
					}
				}

				// Back page sidebar
				if( tinyMCE.activeEditor.id == 'impact_report_quotes' || tinyMCE.activeEditor.id == 'impact_report_additional' ) {
					quotes_count     = $.trim(tinymce.editors.impact_report_quotes.getContent().replace(/<[^>]+>/g,'')).length;
					additional_count = $.trim(tinymce.editors.impact_report_additional.getContent().replace(/<[^>]+>/g,'')).length;
					total            = quotes_count + additional_count;
					$('.ir-back-sidebar-counter span').html( 900 - total );
					if( total > 900 ) {
						$('.ir-back-sidebar-counter span').css({ 'color': '#c60c30', 'font-weight': 'bold' });
						if ( $.inArray( ( ed.keyCode || ed.which ), whitelist_keys ) == -1 ) {
							alert( 'You have exceeded the maximum amount of content for the back page sidebar.' );
							ed.stopPropagation();
							ed.preventDefault();
						}
					} else {
						$('.ir-back-sidebar-counter span').removeAttr('style');
					}
				}

			});
		},
	});
	tinymce.PluginManager.add( 'impact_report_character_counter', tinymce.plugins.impact_report_characters );

});