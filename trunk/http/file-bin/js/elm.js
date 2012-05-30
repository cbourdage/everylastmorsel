jQuery.noConflict();


/**
 * Globals
 */
var elm = {
	domain : 'http://local.elm-1-0.com/',
	environment : 'development',
	api : 'AIzaSyB5wrozaPkDDIO0Kh6tNyHEru-2gOvO40w'
};

window.elm = elm;


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
		window.location = elm.domain + 'coming-soon/';
	}

	/**
	 * Init position
	 *
	 * @param position
	 */
	function initPosition(position) {
		elm.myPosition = {
			lat : position.coords.latitude,
			long : position.coords.longitude
		};

		/**
		 * Request to get users location if have coordinates - will store the data on session and in db
		 */
		jQuery.ajax({
			url: '/index/init-location/',
			data: jQuery.serializeJSON(elm.myPosition),
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
 * Elm specifics
 */

/**
 * checks response for location and redirects
 *
 * @param response
 */
window.elm.success = function(response) {
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
window.elm.error = function(message, $el, location) {
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



(function ($) {

	/**
	 * Refactor into the bootstrap plugin called
	 * @TODO: $.fn.editable.Constructor = Editable
	 */
	elm.editables = function() {
		var editable = '[data-edit="editable"]',
			$form = null,
			$modal = null;

		// bind on click to editable trigger
		$('body').on('click', editable, function(e) {
			var editableEls = $('.editable');
			$modal = $($(this).attr('data-target'));
			$form = $modal.find('form');

			if ($(this).hasClass('editing')) {
				$(this).removeClass('editing').html('Edit');
				editableEls.each(function(key, item) {
					var $el = $(item);
					if ($el.next().hasClass('update-button')) {
						$el.next().remove();
					}
				});
			} else {
				$(this).addClass('editing').html('Done Editing');
				editableEls.each(function(key, item) {
					var $el = $(item);
					if (!$el.next().hasClass('update-button')) {
						$el.after('<button class="btn btn-warning btn-mini update-button">Update</button>');
					}
				});
			}
		});


		$('body').on('click', '.update-button', function(e) {
			e.preventDefault();
			var $el = $(this).prev(),
				dataType = $el.attr('data-type'),
				$input = null;

			switch (dataType) {
				case 'textarea':
					$input = $('<textarea name="' + $el.attr('data-name') + '" id="">' +  $el.html().trim() + '</textarea>');
					break;
				default:
					$input = $('<input type="' + dataType + '" name="' + $el.attr('data-name') + '" id="" value="' +  $el.html().trim() + '" />');
					break;
			}

			$form.find('.wrapper').html($input);
			$form.find('[data-name="update"]').val($el.attr('data-name'));
			console.log($form.find('[data-name="update"]').val());
			$modal.find('h3').html($el.attr('data-title'));
			$modal.modal('show');
		});


		$('body').on('click', '[data-submit="form"]', function(e) {
			e.preventDefault();
			var $form = $($(this).attr('data-form'));
			var $alert = $form.find('.alert');

			if ($alert.length) {
				$alert.alert('close');
			}

			$.ajax({
				url: $form.attr('action'),
				type: 'post',
				data: $form.serialize(),
				success: function(response) {
					if (!elm.success(response)) {
						return;
					}

					if (response.success) {
						$alert = jQuery.createSuccessAlert(response.message).hide();

						var inputName = $form.find('[data-name="update"]').val();
						console.log($form.find('[data-name="update"]').val());
						$('.editable').each(function(key, item) {
							var $el = $(item);
							if ($el.attr('data-name') == inputName) {
								$el.html(response.value);
							}
						});

						window.setTimeout(function() {
							$modal.modal('hide');
							$.formReset($form);
							$alert.alert('close');
						}, 2000);
					} else {
						$alert = jQuery.createErrorAlert(response.message).hide();
					}

					if ($alert.length) {
						$form.prepend($alert);
						$alert.fadeIn();
					}
				},
				error: function() {
					elm.error("Oops! We've encountered some troubles. Try again shortly!", $form, 'prepend');
				}
			});
		});
	};

	$(function() {

		/**
		 * Login form clicks/actions
		 */
		$('body').on('click', '[href*="profile/login/"]', function(e) {
			e.preventDefault();
			$('#headerLoginForm').stop(true).slideDown(function() {
				$(this).find('.cancel').on('click', function(e) {
					e.preventDefault();
					$('#headerLoginForm').stop(true).slideUp();
				})
			});
		});
	});
}(jQuery));
