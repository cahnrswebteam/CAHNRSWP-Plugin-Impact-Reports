jQuery(document).ready( function($) {
	$('#impact-report-pdf-archive > dd').hide();
	$('#impact-report-pdf-archive dt').click( function() {
		$(this).next('dd').toggle().parents('dl').toggleClass('disclosed');
	})
});