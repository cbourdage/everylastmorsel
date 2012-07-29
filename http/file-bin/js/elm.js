
/**
 * Globals
 */
/**
 * @TODO move into scrips head
 */
var Elm = {
	domain : 'http://local.Elm-1-0.com/',
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
					console.log(response);
				} else {
					alert('error in request');
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
 * checks response for location and redirects
 *
 * @param response
 */
window.Elm.success = function(response) {
	if (response.location) {
		window.location = response.location;
		return false;
	}
	return true;
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


/**
 * Helper functions
 */
jQuery.extend({
	serializeJSON : function(json) {
		var string = JSON.stringify(json);
		return string.replace(/([{}"])/g, '').replace(/:/g, '=').replace(/,/g, '&');
	},

	/**
	 * <div class="alert hide fade in">
		  <a class="close" data-dismiss="alert">×</a>
		  message
		</div>
	 */
	createAlert : function(type, message) {
		return jQuery('<div class="alert hide fade in ' + type + '"><a class="close" data-dismiss="alert">×</a>' + message + '</div>');
	},

	createErrorAlert : function(message) {
		return jQuery.createAlert('alert-error', message);
	},

	createInfoAlert : function(message) {
		return jQuery.createAlert('alert-info', message);
	},

	createWarningAlert : function(message) {
		return jQuery.createAlert('alert-warning', message);
	},

	createSuccessAlert : function(message) {
		return jQuery.createAlert('alert-success', message);
	},

	formReset : function(form) {
		var formEls = form.get(0).elements;
		for (var i = 0; i < formEls.length; i++) {
			switch (formEls[i].type.toLowerCase()) {
				case "text":
				case "password":
				case "textarea":
					formEls[i].value = "";
					break;
				case "radio":
				case "checkbox":
					if (formEls[i].checked) {
						formEls[i].checked = false;
					}
					break;
				case "select-one":
				case "select-multi":
					formEls[i].selectedIndex = -1;
					break;
				default:
					break;
			}
		}
	}
});
