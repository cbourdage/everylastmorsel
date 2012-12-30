
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
 * Injects a message into the specified location relative to the
 * passed in element
 *
 * @param $message
 * @param $el
 * @param location
 */
window.Elm.injectElement = function($message, $el, location) {
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

/**
 * checks response for location and redirects
 *
 * @param response
 */
window.Elm.success = function(response) {
	if (response.location) {
		window.location = response.location;
		return;
	} else if (response.update_areas) {
		response.update_areas.forEach(function(id) {
			jQuery('#' + id).html(response.html[id]);
		});
	}

	if (response.message) {
		var $message = jQuery.createSuccessAlert(response.message).hide();
		Elm.injectElement($message, arguments[1], arguments[2]);
	}

	return;
};

/**
 * Handles logging errors for application
 *
 * @param message
 * @param $el
 * @param location
 */
window.Elm.error = function(message, $el, location) {
	var $message = jQuery.createErrorAlert(message).hide();
	Elm.injectElement($message, $el, location);
	return;

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
		 * Involve me button click to open modal
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
		 * Add yields form submit
		 */
		jQuery('#add-yield-form').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this),
				$modal = $('#addYieldModal'),
				$loader = $('<span class="loader green">Loading...</span>');

			$modal.find('.alert').slideUp('fast', function() {
				$(this).remove();
			});
			$modal.find('button').attr('disable', 'disable').after($loader);

			$.ajax({
				url : $form.attr('action'),
				data : $form.serialize(),
				type : 'post',
				dataType : 'json',
				complete: function(response) {
					$loader.remove();
					$modal.find('button').attr('disable', '');
				},
				success : function(response) {
					if (response.success) {
						Elm.success(response, $form, 'prepend');
						window.setTimeout(function(e) {
							$modal.modal('hide');
							$modal.find('.alert').remove();
							$.formReset($form);
						}, 2000);
					} else {
						Elm.error(response.message, $form, 'prepend');
					}
				},
				error : function() {
					Elm.error('There was an error with your request.', $form, 'prepend');
				}
			});
		});

		/**
		 * Set for sale form submit
		 */
		jQuery('#yield-sell-these-form').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this),
				$modal = $('#sellThese'),
				$loader = $('<span class="loader green">Loading...</span>');

			$modal.find('.alert').slideUp('fast', function() {
				$(this).remove();
			});
			$modal.find('button').attr('disable', 'disable').after($loader);

			jQuery.ajax({
				url : $form.attr('action'),
				data : $form.serialize(),
				type : 'post',
				dataType : 'json',
				success : function(response) {
					if (response.success) {
						Elm.success(response, $form, 'prepend');
						window.setTimeout(function(e) {
							$modal.modal('hide');
							$modal.find('.alert').remove();
							$.formReset($form);
						}, 2000);
					} else {
						Elm.error(response.message, $form, 'prepend');
					}
				},
				error : function() {
					Elm.error('There was an error with your request.', $form, 'prepend');
				}
			})
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

