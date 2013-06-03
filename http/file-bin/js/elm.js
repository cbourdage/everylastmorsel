
/**
 * Globals
 */


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



jQuery.extend(window.Elm, {
    /**
     * Injects a message into the specified location relative to the
     * passed in element
     *
     * @param $message
     * @param $el
     * @param location
     */
    injectElement : function($message, $el, location) {
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
    },

    /**
     * checks response for location and redirects
     *
     * @param response
     */
    success : function(response) {
        if (response.location) {
            window.location = response.location;
            return;
        } else if (response.update_areas) {
            response.update_areas.forEach(function(id) {
                jQuery('#' + id).html(response.html[id]);
            });
        }

        if (response.message && arguments.length > 1) {
            var $message = jQuery.createSuccessAlert(response.message).hide();
            Elm.injectElement($message, arguments[1], arguments[2]);
        }

        return;
    },

    /**
     * Handles logging errors for application
     *
     * @param message
     * @param $el
     * @param location
     */
    error : function(message, $el, location) {
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
    }
});

jQuery.extend(window.Elm, {
    Auth : {
        Login : function($form) {
            Elm.Auth.Send($form, $form.serialize());
        },

        Create : function($form) {
            Elm.Auth.Send($form, $form.serialize());
        },

        Send : function($form, params) {
            var $modal = $form.parents('.authenticate'),
                $content = $modal.find('.modal-body'),
                $loader = jQuery('<span class="loader">Loading...</span>');

            $form.find('.alert').slideUp('fast', function() { jQuery(this).remove(); });
            $form.find('.submit').attr('disable', 'disable').after($loader);

            jQuery.ajax({
                url: $form.attr('action') + '?isAjax=1',
                type: 'post',
                data: params,
                complete: function(response) {
                    $loader.remove();
                    $content.find('button').attr('disable', '');
                },
                success: function(response) {
                    if (response.success) {
                        location.href = response.location;
                    } else {
                        Elm.error(response.message, $form, 'prepend');
                    }
                },
                error: function() {
                    Elm.error("Oops! We've encountered some troubles. Try again shortly!", $form, 'prepend');
                }
            });
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

        $('form.labelify').labelify({
            dim : true,
            opacity : '.5'
        });

        /**
         * Left column of profiles/plots adjustment
         */
        var $content = $('#content'), $leftColumn = $('#column-left');
        $leftColumn.find('#main-image img').one('load', function() {
            window.setTimeout(function() {
                if ($content.length && $leftColumn.length && ($leftColumn.outerHeight(true) > $content.outerHeight())) {
                    $content.height($leftColumn.outerHeight(true));
                }
            }, 500);
        }).each(function() {
            if($(this).complete){
                $(this).load();
            }
        });

        /**
         * Contact button click to open modal & submit events.
         * Contact button must be .btn.contact and contain a data-to field
         * with the user id of which sending to.
         */
        $('body').on('click', '.btn.contact', function(e) {
            e.preventDefault();
            if ($(this).data('to')) {
                $($(this).attr('href')).find('input[name="user_to_id"]').val($(this).data('to'));
            }

            // Refresh labelify?
            /*window.setTimeout(function() {
                $('#contact-modal-form').labelify('reset');
            }, 500);*/
        });

        $('.contact-modal-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this),
                $modal = $form.parents('.modal'),
                $content = $modal.find('.modal-body'),
                $loader = $('<span class="loader">Loading...</span>');

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
                        $form.find('name=["message"]').val('');
                        $form.find('name=["subject"]').val('');
                        Elm.success(response.message, $form, 'prepend');

                        window.setTimeout(function() {
                            $modal.modal('hide');
                        }, 1000);
                    } else if (response.location) {
                        Elm.success(response);
                    } else {
                        Elm.error(response.message, $form, 'prepend');
                    }
                },
                error: function() {
                    Elm.error("Oops! We've encountered some troubles. Try again shortly!", $form, 'prepend');
                }
            });
            return false;
        });



        return;



















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

