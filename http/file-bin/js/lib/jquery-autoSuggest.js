
/**
 * Form input auto suggest/complete plugin
 *
 * Assumes the following markup:
 *
	<input id="idValue" type="text" name="nameValue" value="" />
 *
 * Creates the following markup:
 *
 * <input id="idValue" type="text" name="nameValue" value="" />
   <ul class="auto-suggest-results">
       <li></li>
       <li></li>
   </ul>
 */
(function($) {
	/**
	 * Plugin methods defined below
	 */
	var methods = {
		// Define plugin namespace for data and other uses
		namespace : 'autoSuggest',

		// Plugin default options
		defaults : {
			dataType : 'json',
			resultList : null,
			minValueLength : 2,
			visibleLiCount : -1,
			keyboardNavigation : true,		// @TODO
			hiddenInput : null,
			highlight : false,
			onSelectCallback : null
		},

		requests : [],

		/**
		 * Constructor/Initialize method
		 *
		 * @param Object options
		 */
		init : function(options) {
            // Initialize this instance properties
			var $input = $(this),
				data = $input.data(methods.namespace);

			if (!data) {
				var opts = $.extend({}, methods.defaults, options);

				if (!opts.resultList) {
					opts.resultList = $('<ul class="auto-suggest-results"></ul>');
					$input.after(opts.resultList);
				}

				$(this).data(methods.namespace, opts);
				data = $input.data(methods.namespace);
			}

			if (!data.url) {
				alert('Invalid configuration setup for ' + methods.namespace + '. Url required');
				return;
			}

			// create array to hold ajax requests
			data.requests = [];
			data.mouseOver = false;

			/**
			 * Bind mouse events to the result list to prevent closing when clicking on the ul
			 */
			data.resultList.on({
				mouseover : function () {
					data.mouseOver = true;
				},
				mouseout : function () {
					data.mouseOver = false;
				}
			});

			/**
			 * delegate to the result list anchors for click events
			 */
			data.resultList.on('click', 'li a', function () {
				methods['complete'].apply($input, [$(this)]);
			});

			/**
			 * Bind events to the search/input
			 */
			$input.on({
				keyup : function(e) {
					if ($(this).val().length >= data.minValueLength) {
						methods['process'].apply($input, [e]);
					} else {
						methods['reset'].apply($input);
					}
				},
				blur : function(e) {
					if (data.mouseOver) {
						return false;
					}
					data.resultList.hide();
				}
			});

			// Update with any changes to the data
			$(this).data(methods.namespace, data);
        },

		/**
		 * Processes the key click
		 * @param e - js event
		 */
		process : function(e) {
			var $input = $(this),
				data = $input.data(methods.namespace);

			//console.log('key: ' + e.keyCode);
			switch(e.keyCode) {
				case 13:	// enter
					methods['complete'].apply($input, [data.resultList.find('li.active a')]);
					break;
				case 38:	// arrow up
					e.preventDefault();
					methods['moveSelection'].apply($input, ["up"]);
					break;
				case 40:	// arrow up
					e.preventDefault();
					methods['moveSelection'].apply($input, ["down"]);
					break;
				default:
					// add each ajax request to the array, so we can abort them later if a new request is triggered
					data.requests.push(
						jQuery.ajax({
							url: data.url,
							data: $input.attr('name') + '=' + $input.val(),
							dataType: data.dataType,
							success: function (response) {
								// Check for results message box existence before proceeding
								if ($input.next().hasClass('auto-suggest-results-message')) {
									$input.next().remove();
								}

								switch (data.dataType) {
									case 'html':
										// If items, show 'em
										if (response.length) {
											methods['buildHtmlResults'].apply($input, [response]);
										} else {
											$input.after('<p class="auto-suggest-results-message">' + response + '</p>');
										}
										break;
									case 'json':
									default:
										// If items, show 'em
										if (response.items) {
											methods['buildJsonResults'].apply($input, [response.items]);
										} else {
											$input.after('<p class="auto-suggest-results-message">' + response.message + '</p>');
										}
										break;
								}
							}
						})
					);
					break;
			}
		},

		/**
		 * Moves the list selection up and down based on direction
		 *
		 * @param direction
		 */
		moveSelection : function(direction) {
			var $input = $(this),
				data = $input.data(methods.namespace);

			if ($(":visible", data.resultList).length > 0) {
				var list = data.resultList.find("li"),
					$active = list.filter('.active:first'),
					$nextActive = null;

				if ($active.length) {
					if (direction == "down") {
						$nextActive = $active.next();
					} else {
						$nextActive = $active.prev();
					}
				}

				// Check if not set or if out of bounds and set to beginning
				if (!$nextActive || !$nextActive.length) {
					$nextActive = list.eq(0);
					if (direction == "up") {
						$nextActive = list.filter(":last");
					}
				}

				list.removeClass("active");
				$nextActive.addClass(function () {
					methods['fillInput'].apply($input, [$(this)]);
					return "active";
				});
			}
		},

		/**
		 * Appends data to resultList element
		 *
		 * @param items
		 */
		buildJsonResults : function(items) {
			var $input = $(this),
				data = $input.data(methods.namespace);

			// Reset
			methods['reset'].apply($input);

			var results = "",
				ctr = 0;
			// loop over each result and wrap them in html, and add to results <ul>
			$.each(items, function(index, value) {
				if (data.visibleLiCount == -1 || ctr++ <= data.visibleLiCount) {
					if (data.highlight) {
						value.label = value.label.replace($input.val(), '<span>' + $input.val() + '</span>');
					}
					results += '<li><a rel="' + value.value + '" href="#">'+ value.label + '</a></li>';
				}
			});

			data.resultList.html(results).show();
		},
		
		/**
		 * Appends data to resultList element
		 *
		 * @param html
		 */
		buildHtmlResults : function(html) {
			var $input = $(this),
				data = $input.data(methods.namespace);

			// Reset
			methods['reset'].apply($input);

			if (data.highlight) {
				html = html.replace($input.val(), '<span>' + $input.val() + '</span>');
			}
			data.resultList.html(html).show();
		},

		/**
		 * Fills the auto suggest input with the selected text
		 *
		 * @param val
		 */
		fillInput : function($anchor) {
			var data = $(this).data(methods.namespace);
			var value = $anchor.text();
			if (data.highlight) {
				value = value.replace('<span>', '').replace('</span>', '');
			}
			$(this).val(value);
		},

		/**
		 * Fills the auto suggest input with the selected/complete value
		 * and resets the drop down menu
		 *
		 * @param $anchor
		 */
		complete : function($anchor) {
			var data = $(this).data(methods.namespace);
			if (data.hiddenInput) {
				data.hiddenInput.val($anchor.attr('rel'));
			}

			// Complete the input with the value selected
			methods['fillInput'].apply($(this), [$anchor]);

			if (typeof data.onSelectCallback == 'function') {
				data.onSelectCallback();
			}
			
			// Reset/clear the suggestion box
			methods['reset'].apply($(this));
		},

		/**
		 * Resets the resultList box and clears out all remaining ajax
		 * requests from the queue
		 */
		reset : function() {
			var data = $(this).data(methods.namespace);

			//empty the ul holding the results
			data.resultList.hide().empty();

			// loop over pending ajax request and abort all existing
			for (var i = 0; i < data.requests.length; i++) {
				data.requests[i].abort();
			}
		}
	};

	$.fn.autoSuggest = function(option) {
		return this.each(function() {
			if (typeof option === 'object') {
				methods.init.apply(this, [option]);
			} else if (typeof option === 'string' && methods[option]) {
				methods[option].apply(this);
			} else {
				alert('Method ' + option + ' does not exist in ' + methods.namespace);
			}
		});
	}
})(window.jQuery);

