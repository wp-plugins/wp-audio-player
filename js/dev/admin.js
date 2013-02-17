(function($) {
	"use strict";
	
	$(function() {
		
		$('#wp-audio-player-media').click(function() {

			if( null !== $(this).val() && '' !== $.trim( $(this).val() ) ) {
				$('input[type="text"]#wp_audio_url').val( $(this).val() );
			} // end if
			
		});
		
	});
	
}(jQuery));