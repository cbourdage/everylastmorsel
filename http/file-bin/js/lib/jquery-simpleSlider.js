/**
 *
 */
!function($) {
	/**
	 * Plugin methods defined below
	 */
    var methods = {
		// Define plugin namespace for data and other uses
		namespace : 'simpleSlider',

		// Plugin default options
		defaults : {
			sliderDiv : '> div',	// element selector string
            speed : 500,
            nextTag : '.next',		// element selector string
            prevTag : '.prev',		// element selector string
            slideCount : 1
		},

		/**
		 * Constructor/Initialize method
		 *
		 * @param Object options
		 */
        init: function(options) {
			var $this = $(this),
				data = $this.data(methods.namespace);

			// Initialize this instance properties
			if (!data) {
				var opts = $.extend({}, methods.defaults, options);

				var width = 0;
				var items = $(opts.sliderDiv, $this).children('img, a, div, li');
				// establish total width
				console.log(items.length);
				items.each(function(key, item) {
					width += $(this).outerWidth(true);
				});
				console.log(width);

				opts.currentIdx = 0;
				opts.leftOffset = 0;
				opts.itemCount = items.length;
				opts.width = width;

				$this.data(methods.namespace, opts);
				data = $this.data(methods.namespace);
			}

			// set width of slider div for slide transition
			$(data.sliderDiv, $this).width(data.width);

			/**
			 * If show nav is set and the next tag that is passed in (or default)
			 * is empty we need to build the nav elements
			 */
			if (!$(data.nextTag, $this).length) {
				var htmlOut = '<a class="arrow ' + data.prevTag.replace('.', '') + '" href="#"></a><a class="arrow ' + data.nextTag.replace('.', '') + '" href="#"></a>';
				$(data.sliderDiv, $this).after(htmlOut);
			}

			// if we have more than 1 show next button
			if (data.itemCount > 1) {
				$(data.nextTag, $this).toggleClass("show");
			}

			/**
			 * Next button click
			 */
			$(data.nextTag, $this).on('click', function(e) {
				e.preventDefault();
				methods.moveNext.apply($this, []);
			});

			/**
			 * Previous button click
			 */
			$(data.prevTag, $this).on('click', function(e) {
				e.preventDefault();
				methods.movePrev.apply($this, []);
            });
        },

		/**
		 * Calculate the items and slide to the next position
		 */
        moveNext: function() {
            var $this = $(this),
                data = $this.data(methods.namespace);
            var nextIdx = data.currentIdx + data.slideCount;

            // Only animate if necessary
            if (nextIdx < data.itemCount) {
                methods.slide.apply($this, [nextIdx]);
            }
        },

		/**
		 * Calculate the items and slide to the previous position
		 */
        movePrev: function() {
            var $this = $(this),
                data = $this.data(methods.namespace);
            var prevIdx = data.currentIdx - data.slideCount;

            // Only animate if necessary
            if (prevIdx >= 0) {
                methods.slide.apply($this, [prevIdx]);
            }
        },

		/**
		 * Animate the slide frame/transition to next slide index
		 *
		 * @param int nextIdx
		 */
        slide: function(nextIdx) {
            var $this = $(this),
				data = $this.data(methods.namespace),
                $slider = $(data.sliderDiv, $this);

            var items = $slider.children('img, a, div, li');

            if (data.currentIdx > nextIdx) {
                data.leftOffset = eval(data.leftOffset + $(items.get(nextIdx)).outerWidth(true));
            } else {
                data.leftOffset = eval(data.leftOffset - $(items.get(data.currentIdx)).outerWidth(true));
            }

            data.currentIdx = nextIdx;
            $slider.animate({"left" : (data.leftOffset * data.slideCount) + "px"}, data.speed, function() {
                /**
                 * Properly toggle css classes on arrows
                 */
                var $nextBtn = $(data.nextTag, $this);
                var $prevBtn = $(data.prevTag, $this);

                if (data.currentIdx > 0 && !$prevBtn.hasClass('show')) {
                    $prevBtn.addClass("show");
                }

                if (data.currentIdx == 0 && $prevBtn.hasClass('show')) {
                    $prevBtn.removeClass("show");
                }

                if (data.currentIdx < data.itemCount && !$nextBtn.hasClass('show')) {
                    $nextBtn.addClass("show");
                }

                if (data.currentIdx >= data.itemCount - data.slideCount && $nextBtn.hasClass('show')) {
                    $nextBtn.removeClass("show");
                }
            });
        }
    };

	$.fn.simpleSlider = function(option) {
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

}(window.jQuery);
