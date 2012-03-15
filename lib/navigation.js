jQuery(document).ready(function($) {
	
	// Navigate prev/next slide with keyboard
	$(document).keydown(function(evt) {
		evt = evt || window.event;
		switch (evt.keyCode) {
			case 37:
				$shrdluprev = $('.prev a');
				if (1 == $shrdluprev.length) {
					window.location = $shrdluprev.attr('href');
				}

			break;
			case 39:
				$shrdlunext = $('.next a');
				if (1 == $shrdlunext.length) {
					window.location = $shrdlunext.attr('href');
				}
			break;
		}
	});
	
	// Mobile devices use swipe
	$(document).touchwipe({
		preventDefaultEvents: false,
	     wipeLeft: function() { 
				$shrdlunext = $('.next a');
				if (1 == $shrdlunext.length) {
					window.location = $shrdlunext.attr('href');
				}
	     },
	     wipeRight: function() { 
				$shrdluprev = $('.prev a');
				if (1 == $shrdluprev.length) {
					window.location = $shrdluprev.attr('href');
				}	     
	      },
	});
	
});