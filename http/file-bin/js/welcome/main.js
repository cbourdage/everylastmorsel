jQuery(function($) {
	var $email = $('#email'),
		$region = $('#region');

	$('form[role="newsletter"] input').on({
		'focus' : function(e) {
			if ($(this).val() == $(this).data('placeholder')) {
				$(this).val('');
			}
		},
		'blur' : function(e) {
			if ($(this).val() != $(this).data('placeholder') && $(this).val().length < 1) {
				$(this).val($(this).data('placeholder')).removeClass('invalid');
			}

			if ($(this).val().length > 0 && $(this).val() != $(this).data('placeholder')) {
				if ($(this).attr('id') == 'email') {
					if (!validEmail($(this))) {
						$(this).addClass('invalid');
					} else {
						$(this).removeClass('invalid');
					}
				}

				if ($(this).attr('id') == 'region') {
					if (!validRegion($(this))) {
						$(this).addClass('invalid');
					} else {
						$(this).removeClass('invalid');
					}
				}
			}
		}
	});

	$('form[role="newsletter"]').on('submit', function(e) {
		//e.preventDefault();
		if (!validEmail($email)) {
			$email.addClass('invalid');
		} else {
			$email.removeClass('invalid');
		}

		if (!validRegion($region)) {
			$region.addClass('invalid');
		} else {
			$region.removeClass('invalid');
		}

		if ($email.hasClass('invalid') || $region.hasClass('invalid')) {
			return false;
		}
		return true;
	});

	// Smooth scroll
    $('a[href="#how-it-works"]').click(function(e) {
        e.preventDefault();
        var pos = $('div' + $(this).attr('href')).position();
        $('html,body').animate({scrollTop: pos.top}, 600);
    });
});


function validEmail($email) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return !re.test($email.val()) ? false : true;
}

function validRegion($region) {
	if ($region.val() == $region.data('placeholder') || $region.val().length < 2) {
		return false;
	}
	return true;
}