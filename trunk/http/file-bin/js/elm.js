
jQuery.noConflict();
(function ($) {

	$(function() {
		/**
		 * Contact form button and overlay clicks
		 */
		var $contactOverlay = $('#overlayContact');
		if ($contactOverlay.length > 0) {
			var $contactForm = jQuery('#contact-form');
			$contactForm.on('submit', function(e) {
				e.preventDefault();
				var $contactForm = $(this);
				$.ajax({
					url: $contactForm.attr('action'),
					type: 'post',
					data: $contactForm.serialize(),
					success: function(response) {
						// if error - show above form
						if (response.error) {
							$contactForm.find('.form-message').html(response.message).fadeIn();
						} else {
							$contactOverlay.find('.content').html(response.message);
							$contactOverlay.animate({dummy : 1}, 4000, function() {
								$(this).fadeOut();
								$('#overlayBg').fadeOut();
							});
						}
					},
					error: function() {
						alert('error in request');
					},
					exception: function() {
						alert('error in request');
					}
				});
				return false;
			});

			// Bind clicks
			$('.contact-button').on('click', function(e) {
				$contactOverlay.fadeIn();
				$('#overlayBg').fadeIn();
			});
		}
	});
}(jQuery));


/**
 * Resizes an element
 *
 * @param $object
 */
function ResizeOverlay($object) {
	var $overlay = jQuery($object);
	return;
}

/**
 * Closes overlay
 *
 * @param $anchor
 */
function CloseOverlay($anchor) {
	jQuery($anchor).parent().fadeOut();
	jQuery('#overlayBg').fadeOut();
	return false;
}








/**
 * Not used...
 */
var Overlay = {
	/*
	 * Show the lightbox-style page-overlay.  Accepts a single, optional
	 * parameter of type function to be executed after the overlay is shown.
	 */
	overlayShow: function(fn) {
		$('#overlayBg').fadeIn('slow');
		elm.config.overlay.fadeIn('slow', function () {
			elm.exec(fn);
		});
	},

	/*
	 * Hide the lightbox-style page-overlay.  Accepts a single, optional
	 * parameter of type function to be executed after the overlay is
	 * hidden.
	 */
	overlayHide: function(fn) {
		$('#overlayBg').fadeOut('slow');
		elm.config.overlay.fadeOut('fast', function () {
			elm.exec(fn);
		});
	},

	overlayUpdate: function(s) {
		elm.config.overlay.find('.content').fadeOut('fast', function() {
			$(this).html(s);
			$(this).fadeIn('fast');
		});
	},

	overlayResize: function(s) {
		// resize in here
	}
};

//Overlay.prototype = new Overlay($object);