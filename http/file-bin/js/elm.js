
/**
 * Globals
 */
/**
 * @TODO move into scrips head
 */
var Elm = {
	domain : 'http://local.beta.everylastmorsel.com/',
	environment : 'development',
	api : 'AIzaSyB5wrozaPkDDIO0Kh6tNyHEru-2gOvO40w'
};

window.Elm = Elm;

// Initialize the users location
function initLocation() {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(initPosition, handleNoGeolocation);
	} else if (google.gears) {
		var geo = google.gears.factory.create('beta.geolocation');
		geo.getCurrentPosition(initPosition, handleNoGeolocation);
	} else {
		handleNoGeolocation();
	}

	/**
	 * No location
	 */
	function handleNoGeolocation() {
		window.location = Elm.domain + 'coming-soon/';
	}

	/**
	 * Init position
	 *
	 * @param position
	 */
	function initPosition(position) {
		Elm.myPosition = {
			lat : position.coords.latitude,
			long : position.coords.longitude
		};

		/**
		 * Request to get users location if have coordinates - will store the data on session and in db
		 */
		jQuery.ajax({
			url: '/index/init-location/',
			data: jQuery.serializeJSON(Elm.myPosition),
			dataType: 'json',
			success: function(response) {
				if (response.location) {
					window.location = response.location;
				}

				if (response.success) {
					//console.log(response);
				} else {
					//alert('error in request');
				}
			},
			error: function() {
				// @TODO create a simple error handler function to display global message
				//alert('error in request');
			}
		});
	}
}

// Initialize location
initLocation();

/**
 * Saves a location
 *
 * @param position
 */
function saveLocation(position) {
	var location = position;
	if (position.coords) {
		location = {
			lat : position.coords.latitude,
			long : position.coords.longitude
		}
	}

	/**
	 * Request to get users location if have coordinates - will store the data on session and in db
	 */
	jQuery.ajax({
		url: '/index/save-location/',
		data: jQuery.serializeJSON(location),
		dataType: 'json',
		success: function(response) {
			if (response.location) {
				window.location = response.location;
			}

			if (response.success) {
				//console.log(response);
			} else {
				//alert('error in request');
			}
		},
		error: function() {
			// @TODO create a simple error handler function to display global message
			//alert('error in request');
		}
	});
}


/**
 * checks response for location and redirects
 *
 * @param response
 */
window.Elm.success = function(response) {
	if (response.location) {
		window.location = response.location;
		return;
	} else if (response.update_id) {
		/**
		 * @TODO create generic update regions selector/html
		 */


	} else {
		var $message = jQuery.createSuccessAlert(response.message).hide();

		var $el = arguments[1];
		switch(arguments[2]) {
		case 'after':
			$el.after($message);
			break;
		case 'before':
			$el.before($message);
			break;
		case 'append':
			$el.append($message);
			break;
		default:
		case 'prepend':
			$el.prepend($message);
			break;
	}

		$message.fadeIn();
	}


}

/**
 * Handles logging errors for application
 *
 * @param message
 * @param $el
 * @param location
 */
window.Elm.error = function(message, $el, location) {
	var $message = jQuery.createErrorAlert(message).hide();

	switch(location) {
		case 'after':
			$el.after($message);
			break;
		case 'before':
			$el.before($message);
			break;
		case 'append':
			$el.append($message);
			break;
		default:
		case 'prepend':
			$el.prepend($message);
			break;
	}

	$message.fadeIn();
};


!function ($) {
	$(function() {
		/**
		 * Invole me button click to open modal
		 */
		$('body').on('click', 'a[href="#involve-me"]', function(e) {
			e.preventDefault();
			$('#involveMeModal').modal('show');
		});

		/**
		 * Involve me form submit
		 */
		$('#getInvolvedModalForm').on('submit', function(e) {
			return;

			e.preventDefault();
			var $form = $(this),
				$modal = $('#involveMeModal'),
				$content = $modal.find('.modal-body'),
				$successModal = $('#involveMeSuccessModal');

			$content.find('.alert').slideUp('fast', function() {
				$(this).remove();
			});

			$.ajax({
				url: $form.attr('action'),
				type: 'post',
				data: $form.serialize(),
				success: function(response) {
					if (response.success) {
						$.formReset($form);
						$modal.modal('hide');
						$successModal.find('.modal-body h3').html(response.message);
						$successModal.modal('show').delay(3000, function(e) { });
					} else {
						Elm.error(response.message, $content, 'prepend');
					}
				},
				error: function() {
					Elm.error("Oops! We've encountered some troubles. Try again shortly!", $content, 'prepend');
				}
			});
			return false;
		});

		/**
		 * Contact button click to open modal
		 */
		$('body').on('click', '.contact-button', function(e) {
			e.preventDefault();
			$('#contactModal').modal('show');
			if ($(this).data('to')) {
				$('#contactModalForm').find('input[name="user_to_id"]').val($(this).data('to'));
			}
		});

		/**
		 * Involve me form submit
		 */
		$('#contactModalForm').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this),
				$modal = $('#contactModal'),
				$content = $modal.find('.modal-body'),
				$successModal = $('#contactSuccessModal'),
				$loader = $('<span class="loader green">Loading...</span>');

			$content.find('.alert').slideUp('fast', function() {
				$(this).remove();
			});
			$content.find('button').attr('disable', 'disable').after($loader);

			$.ajax({
				url: $form.attr('action'),
				type: 'post',
				data: $form.serialize(),
				complete: function(response) {
					$loader.remove();
					$content.find('button').attr('disable', '');
				},
				success: function(response) {
					if (response.success) {
						$.formReset($form);
						$modal.modal('hide');
						$successModal.find('.modal-body h3').html(response.message);
						$successModal.modal('show').delay(3000, function(e) { });
					} else {
						Elm.error(response.message, $content, 'prepend');
					}
				},
				error: function() {
					Elm.error("Oops! We've encountered some troubles. Try again shortly!", $content, 'prepend');
				}
			});
			return false;
		});

		/**
		 * Simple toggle to show additional content
		 */
		$('body').on('click', 'a.action[href^="#"]', function(e) {
			e.preventDefault();
			$($(this).attr('href')).show();
		})

		/**
		 * Login form clicks/actions
		 */
		$('body .header-container').on('click', '[href*="profile/login/"]', function(e) {
			e.preventDefault();
			$('#headerLoginForm').stop(true).slideDown(function() {
				$(this).find('.cancel').on('click', function(e) {
					e.preventDefault();
					$('#headerLoginForm').stop(true).slideUp();
				})
			});
		});
	});
}(window.jQuery);

