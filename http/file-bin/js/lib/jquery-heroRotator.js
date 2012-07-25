/*
    HERO ROTATOR (version 1.0.1)
    updated: 6.27.2011

    Unlike the previous version, which only accepted <img> tags, ver. 1.0.1 now accepts any html tag.  This allows
    rotation of html objects for more complex homepages.

    Update includes a new option type : tagType.  By default it is set to rotate through images.  Using the tagType
    will allow you to overwrite the default with any html tag you specify.
*/
(function($) {
	/**
	 * Plugin methods defined below
	 */
    var methods = {
		// Define plugin namespace for data and other uses
		namespace : 'heroRotator',

		// Plugin default options
		defaults : {
			rotateDelay   : 5000,
            fadeInSpeed   : 1000,
            enableNav     : true,
            tagType       : 'img' //<img> are set by default can be over written.
		},

        init : function(options) {
			var $this = $(this),
				data = $this.data(methods.namespace); 	//variables attached to the objects data

			// If it hasn't been initialized
			if (!data) {
				var opts = $.extend({}, methods.defaults, options);
				opts.itemCount = $(opts.tagType, $this).length;
				opts.startingZIndex = parseInt($(opts.tagType, $this).eq(0).css('z-index'));
				$(this).data(methods.namespace, opts);
				data = $this.data(methods.namespace);

				// set each image/tag to hidden by default to prevent overlap of child elements before first rotation
				$(data.tagType, $this).css({'z-index': data.startingZIndex - 1, 'display': 'none'});
				// set the first images z-index up one
				$(data.tagType, $this).eq(0).css('z-index', data.startingZIndex + 1).css('display', 'block');

				if (data.itemCount > 1 && !data.nextInterval) {
					// if enabled, build navigation
					if (data.enableNav) {
						var navHtml = "<ul class='hero-nav'>";
						for(var i = 0; i < data.itemCount; i++){
							navHtml += i == 0 ? "<li class='active'>&nbsp;</li>" : "<li>&nbsp;</li>"
						}
						navHtml += "</ul>";

						// insert navigation
						$($this).append(navHtml);

						// Bind click
						$('ul.hero-nav li', $this).on('click', function(e) {
							e.preventDefault();
							if (!$(this).hasClass("active") && data.currentIdx != $('.hero-nav li', $this).index(this)) {
								if (data.nextInterval) {
									clearTimeout(data.nextInterval);
								}
								methods.rotateImage.apply($this, [$('.hero-nav li', $this).index(this), false]);
							}
						});
					}

					data.currentIdx = 0;

					//set the rotate timeout
					data.nextInterval = setTimeout(function() {
						methods.rotateImage.apply($this, [-1, true])
					}, data.rotateDelay);
				}
			}
        },

		/**
		 * Rotates the container div element
		 *
		 * @param nextIndex
		 * @param autoRotate
		 */
        rotateImage : function(nextIndex, autoRotate) {
            var $this = $(this),
                data = $this.data(methods.namespace);

            if (data.itemCount > 1) {
                var $current = $($(data.tagType, $this).get(data.currentIdx)); //get the current image

                // if a specific index wasn't requested, get the next in line
                if (nextIndex < 0) {
                    nextIndex = (data.currentIdx == data.itemCount - 1) ? 0 : data.currentIdx + 1;
                }

                data.currentIdx = nextIndex;

                //transition between images
                var $nextImage = $($(data.tagType, $this).get(nextIndex));
                $nextImage.css('z-index', data.startingZIndex + 2).fadeIn(data.fadeInSpeed, function() {
                    $current.css('display','none').css('z-index', data.startingZIndex);
                    $(this).css('z-index', data.startingZIndex + 1);
                    $('.hero-nav li', $this).removeClass('active');
                    $($('.hero-nav li').get(data.currentIdx)).addClass('active');
                });

                // if still in autorotate (meaning a nav wasn't clicked), reset the timeout
                if (autoRotate) {
                    try {
                        data.nextInterval = setTimeout(function() {
                            methods.rotateImage.apply($this, [-1, true])
                        }, data.rotateDelay);
                    } catch(e){}
                }
                return false;
            }
        }
    };

	$.fn.heroRotator = function(option) {
		return this.each(function() {
			if (typeof option === 'object') {
				methods.init.apply(this, [option]);
			} else if (typeof option === 'string' && methods[option]) {
				methods[option].apply(this);
			} else {
				alert('Method ' + option + ' does not exist in ' + methods.namespace);
			}
		});
	};
})(window.jQuery);
