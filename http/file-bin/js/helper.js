
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
