
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
jQuery('table').on('click', '.icon', function(e) {
	var $icon = jQuery(this),
		$loader = jQuery('#action-loader'),
		$row = jQuery(this).parents('tr');

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
				$row.remove();
			}
		},
		error : function() { }
	});
});

// Row clicking
jQuery(document).on('click', 'table.communication-messages td', function(e) {
	var $row = jQuery(this).parent(),
		$loader = jQuery('#action-loader');

	$loader.show();
	console.log($row.data('url'));
	jQuery.ajax({
		url : $row.data('url'),
		complete : function() {
			$loader.hide();
			location.hash = 'view:' + $row.data('id');
		},
		success : function(response) {
			if (response.success) {
				Elm.success(response);
				jQuery('#messageModal').modal('show');
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