
/**
 * Fixed Position plugin
 *
 * Assumes the following markup:
 *
	<div id="containingWrapper">
		<div id="contentToFix"></div>
	</div>
 */
(function($) {
	/**
	 * Plugin methods defined below
	 */
	var methods = {
		// Define plugin namespace for data and other uses
		namespace : 'scrollFixedPosition',

		// Plugin default options
		defaults : {
			'contentContainer' : '#fixedContent'
		},

		/**
		 * Constructor/Initialize method
		 *
		 * @param Object options
		 */
		init : function(options) {
			// Initialize this instance properties
			var $fixedWrapper = $(this),
				data = $fixedWrapper.data(methods.namespace);

			if (!data) {
				var opts = $.extend({}, methods.defaults, options);
				$(this).data(methods.namespace, opts);
				data = $(this).data(methods.namespace);
			}

			// fixed content element
			var $fixedContent = $(data.contentContainer);

			// Scroll height default
			var _scrollHeight = 0;

			// Gets position of scrollable content on the page minus any margin that may be attached
			var _top = $fixedContent.offset().top - parseFloat($fixedContent.css('marginTop').replace(/auto/, 0));

			/**
			 * The following fixedHeight variable is only needed if the fixed content needs to also have a floor.
			 * This tells the JS to keep the content within a defined element, in this case fixedWrapper
			 * 	Note: Comment out if not needed
			 */
			var _fixedHeight = $fixedWrapper.height();

			// Bind to window scrolling event
			$(window).scroll(function (event) {
				//_scrollHeight = _fixedContent.height();
				_scrollHeight = $fixedContent.outerHeight(true);
				// get y position of the scroll
				var y = $(this).scrollTop();
				// This first if should be removed if the element does not have a floor
				if (y >= (_top + _fixedHeight) - _scrollHeight) {
					$fixedContent.removeClass('fixed');
					$fixedContent.addClass('fixedWrapper-stopped');
				} else if (y >= _top) {
					// if so, ad the fixed class
					$fixedContent.removeClass('fixedWrapper-stopped');
					$fixedContent.addClass('fixed');
				} else {
					// otherwise remove it
					$fixedContent.removeClass('fixed');
				}
			});
        }
	};

	$.fn.scrollFixedPosition = function(option) {
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
