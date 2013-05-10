
/**
 * Globals
 */

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

!function ($) {
	$(function() {

        /**
         * Contact button click to open modal & submit events.
         * Contact button must be .btn.contact and contain a data-to field
         * with the user id of which sending to.
         */
        $('body').on('click', '.btn.contact', function(e) {
            e.preventDefault();
            $('#contact-modal').modal('show');
            if ($(this).data('to')) {
                $('#contact-modal-form').find('input[name="user_to_id"]').val($(this).data('to'));
            }
        });

        $('#contact-modal-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this),
                $modal = $('#contact-modal'),
                $content = $modal.find('.modal-body'),
                $successModal = $('#contact-success-modal'),
                $loader = $('<span class="loader green">Loading...</span>');

            $content.find('.alert').slideUp('fast', function() { $(this).remove(); });
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
                        $form.find('#message').val('');
                        $modal.modal('hide');
                        $successModal.find('.modal-body h3').html(response.message);
                        $successModal.modal('show').delay(3000, function(e) { });
                    } else if (response.location) {
                        Elm.success(response);
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

