
jQuery.extend({
	serializeJSON: function(json) {
		var string = JSON.stringify(json);
		return string.replace(/([{}"])/g, '').replace(/:/g, '=').replace(/,/g, '&');
	},

	formReset: function(form) {
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