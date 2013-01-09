
// Filters clicking
jQuery('.left-filters').on('click', 'a', function(e) {
	var $this = jQuery(this),
		$loader = jQuery('#action-loader');

	$loader.show();
	jQuery.ajax({
		url : $this.attr('rel'),
		complete : function() {
			$loader.hide();
		},
		success : function(response) {
			if (response.success || response.location) {
				Elm.success(response);
			}
		},
		error : function() { }
	});
});

// Icon clicking
jQuery(document).on('click', '#content .icon', function(e) {
	var $icon = jQuery(this),
		$loader = jQuery('#action-loader'),
		$row = jQuery(this).parents('tr');

	if ($icon.hasClass('reply')) {
		return false;
	}

	if ($icon.hasClass('delete')) {
		if (!confirm('Are you sure you would like to delete this message?')) {
			return false;
		}
	}

	$loader.show();
	jQuery.ajax({
		url : $icon.attr('rel'),
		complete : function() {
			$loader.hide();
		},
		success : function(response) {
			if (response.success) {
				if ($row.length) {
					$row.remove();
				} else {
					Elm.success(response);
				}
			}
		},
		error : function() { }
	});
});

/**
 * Reply form submission
 */
jQuery(document).on('submit', '.message-form form', function(e) {
	e.preventDefault();
	var $form = jQuery(this),
		$loader = jQuery('#action-loader');

	// hide alert
	$form.find('.alert').slideUp('fast', function() {
		$(this).remove();
	});

	// check length and prevent send
	if ($form.find('.reply').val().length === 0) {
		return;
	}

	// disable btns
	$form.find('button').attr('disable', 'disable').after($loader);

	jQuery.ajax({
		url : $form.attr('action'),
		type : 'post',
		data : $form.serialize(),
		complete : function() {
			$loader.hide();
			$form.find('button').attr('disable', '');
		},
		success : function(response) {
			if (response.success) {
				Elm.success(response, $form.parent().prev(), 'append');
				$form.find('.reply').val('');
				$form.parent().hide();
				$form.parent().prev().find('.alert').delay(3000).fadeOut('fast', function() {
					jQuery(this).remove();
				});
			} else if (response.location) {
				Elm.success(response);
			} else {
				Elm.error(response.message, $form, 'prepend');
			}
		},
		error : function() { }
	});
	return false;
});

// Row clicking
jQuery(document).on('click', 'table.communication-messages td', function(e) {
	var $row = jQuery(this).parent(),
		$loader = jQuery('#action-loader');

	$loader.show();
	jQuery.ajax({
		url : $row.data('url'),
		complete : function() {
			$loader.hide();
			location.hash = 'view:' + $row.data('id');
		},
		success : function(response) {
			if (response.success || response.location) {
				Elm.success(response);
			}
		},
		error : function() { }
	});
});

// Checking for hash
jQuery(function() {
	if (location.hash) {
		if (location.hash.match(/view:/)) {
			var id = location.hash.substr(location.hash.indexOf(':') + 1);
			jQuery('table.communication-messages tr#r\\:' + id + ' td:first-child').trigger('click');
		} else {
			jQuery('.left-filters a[href="' + location.hash + '"]').trigger('click');
		}
	}
});