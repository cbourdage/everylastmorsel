
jQuery.noConflict();

(function ($) {
	var elm = {};
	elm = {
		/*
		 * Configuration settings and references to frequently-accessed elements.
		 */
		config: {
			displayErrors: true,
			overlay: null
		},

		/*
		 * Initializes required stuffs
		 */
		init: function() {
			elm.config.overlay = $('#overlay');
			//this.initMessages();
			//this.initSearch();
		},

		log: function(s) {
			if (elm.config.displayErrors) {
				console.log(s);
			}
		},

		exec: function(fn) {
			if (typeof fn === 'function') {
				try {
					fn();
				} catch (e) {
					elm.log(e);
				}
			}
		},

		/*
		 * Show the lightbox-style page-overlay.  Accepts a single, optional
		 * parameter of type function to be executed after the overlay is shown.
		 */
		overlayShow: function(fn) {
			$('#overlayBg').fadeIn('slow');
			elm.config.overlay.fadeIn('slow', function () {
				elm.exec(fn);
			});
		},

		/*
		 * Hide the lightbox-style page-overlay.  Accepts a single, optional
		 * parameter of type function to be executed after the overlay is
		 * hidden.
		 */
		overlayHide: function(fn) {
			$('#overlayBg').fadeOut('slow');
			elm.config.overlay.fadeOut('fast', function () {
				elm.exec(fn);
			});
		},

		overlayUpdate: function(s) {
			elm.config.overlay.find('.content').fadeOut('fast', function() {
				$(this).html(s);
				$(this).fadeIn('fast');
			});
		},

		overlayResize: function(s) {
			// resize in here
		},



		/**
		 * Search
		 */
		initSearchSearch: function() {
			var $searchInput = $('#fdt-search-player');
			var $searchResults = $('#fdt-search-player-results');
			var defaultText = $('#fdt-search-player').val();
			var lastSearch = '';

			// Submit button click event
			$('#fdt-search-player-submit').click(function(e) {
				$searchResults.css('display','none').html('');
				lastSearch = $searchInput.val();
				// TODO: Show player profile section
				return false;
			});

			// Search input focus and blur
			$searchInput.bind('focus', function(e) {
				if ($(this).val().toLowerCase() == defaultText.toLowerCase()) {
					$(this).val('');
				}
			});
			$searchInput.bind('blur', function(e) {
				if ($(this).val() == '') {
					$(this).val(defaultText);
				}
			})

			// Search input keyup for searching
			$searchInput.bind('keyup', function(e) {
				if ($(this).val().length > 1 && $(this).val() != lastSearch) {
					var sVal = $(this).val().toLowerCase();

					// make ajax call and return json
					var results = [
						{"id" : 123213, 'name' : 'Player 1'},
						{'id' : 234234, 'name' : 'Player 2'}
					];

					$searchResults.html('').css('display','block');
					jQuery.each(results, function(key, item) {
						item.name = item.name.toLowerCase().replace(sVal, '<span>' + sVal + '</span>');
						$searchResults.append('<li>' + item.name + '</li>');
					});

					// Bind the li click to display player and move name into search field
					$('#fdt-search-player-results li').click(function(e) {
						$searchResults.css('display','none').html('');
						$searchInput.val($(this).html());
						lastSearch = $searchInput.val();
						// TODO: Show player profile section
					});
				}
				else {
					$searchResults.css('display','none');
				}
			});
		},

		initMessages: function() {
			fdt.elems.message = $('#fdt-message');
			fdt.elems.message.hide()
				.removeClass('fdt-nodisplay');

			var $messages = $('#fdt-message').find('.msg');
			fdt.elems.message.find('a[href="#close"]').click(function(e) {
				var $temp = $(this);
				fdt.overlayHide(function() {
					$temp.closest('.msg').removeClass('fdt-active').addClass('fdt-nodisplay');
				});
			});
		},

		showMessage: function(selector) {
			var $msgEl = $(selector);

			// Position calculations
			var pos = $('#fdt-content').position();
			var left = $(window).width() - fdt.elems.message.outerWidth(true);
			left = parseInt(left / 2, 10) - pos.left;
			var top = $(window).height() - fdt.elems.message.outerHeight(true);
			top = parseInt(top / 2, 10) - pos.top - 150;

			fdt.elems.message.css('left', left + 'px');
			fdt.elems.message.css('top', top + 'px');

			fdt.overlayShow(function () {
				fdt.elems.message.show(function() {
					$msgEl.removeClass('fdt-nodisplay').addClass('fdt-active');
				});
			});
		}
	};

	/*
	 * Place fdt into the global namespace.
	 */
	window.elm = elm;


	/*
	 * Queue the top-level initialization function to be executed when the DOM
	 * is ready.
	 */
	$(document).ready(elm.init);

}(jQuery));


jQuery.extend({
	serializeJSON: function(json) {
		var string = JSON.stringify(json);
		return string.replace(/([{}"])/g, '').replace(/:/g, '=').replace(/,/g, '&');
	}
});
