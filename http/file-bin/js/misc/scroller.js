
// Bootstrap console
if (window.console == null) {
    window.console = {
        log: function(s){
            alert(s);
        },
        warn: function(){},
        info: function(){}
    };
}

(function($){
    var rotator = {
		/**
		 * Namespace of data object
		 */
        namespace: 'herorotator',

		/**
		 * Initializes the rotator object
		 * @param cfg
		 */
        init: function(cfg) {
            return this.each(function() {
                var $this = $(this),
                    data = $this.data(rotator.namespace); // variables attached to the objects data

                var tabs = $('#hero-tabs', this).find('> li');
                var contents = $('#hero-content', this).find('> li');
                //var nestedTabs = $('#nested-tabs', this).find('li');

                // If it hasn't been initialized
                if (!data) {
                    // store the necessary variables
                    $this.data(rotator.namespace, {
                        itemCount: tabs.length,
                        currentIdx: -1,
						nestedIdx: cfg.nestedIdx,
                        nextInterval: null          // rotate timeout function
                    });
                    data = $this.data(rotator.namespace);
                }

                // Set z-indexes for reference
                data.zIndexDefault = parseInt(contents.eq(0).css('z-index')),
                data.zIndexMax = data.zIndexDefault + 2;

                // Initial state of tabs
                tabs.eq(cfg.startIdx).addClass('active');
                contents.eq(cfg.startIdx).css('z-index', data.zIndexMax);

				// bind clicks to tabs
				tabs.find('a').on('click', function(e) {
					var $tab = $(this).parent();
					if (data.nextInterval) {
						clearTimeout(data.nextInterval);
					}
					console.log('clicked on index: ' + tabs.index($tab));
					if (tabs.index($tab) != data.currentIdx) {
						rotator.rotate.apply($this, [cfg, tabs.index($tab), 0]);
					}
				});

				if (cfg.nested) {
					data.nestedCount = contents.eq(cfg.startIdx).find('.nested-tabs li').length;
					contents.eq(cfg.startIdx).find('.nested-tabs li').eq(cfg.nestedIdx).addClass('active');
					contents.eq(cfg.startIdx).find('.nested-content li').eq(cfg.nestedIdx).addClass('active');

					// bind clicks to tabs
					contents.find('.nested-tabs').each(function(key, item) {
						var nestedTabs = $(this).find('li');
						$(this).find('a').on('click', function(e) {
							var $tab = $(this).parent();
							if (data.nextInterval) {
								clearTimeout(data.nextInterval);
							}
							console.log('clicked on nested index: ' + nestedTabs.index($tab));
							if (nestedTabs.index($tab) != data.nestedIdx) {
								rotator.rotate.apply($this, [cfg, data.currentIdx, nestedTabs.index($tab)]);
							}
						});
					});
				}

                if (data.itemCount > 1 && !data.nextInterval) {
                    try {
                        // if enabled, build navigation
                        if (cfg.enableNav) {
                            var navHtml = "<ul class='hero-nav'>";
                            for(var i = 0; i < data.itemCount; i++){
                                navHtml += i == 0 ? "<li class='active'>&nbsp;</li>" : "<li>&nbsp;</li>"
                            }
                            navHtml += "</ul>";
                            $($this).append(navHtml).addClass('hero-nav');

							// Bind click to 'next/prev' buttons
							$('ul.hero-nav li', $this).bind(function(e) {
								if (!$(this).hasClass("active") && data.currentIdx != $('.hero-nav li').index(this)) {
									if(data.nextInterval) {
										clearTimeout(data.nextInterval);
									}
									rotator.rotate.apply($this, [cfg, data.currentIdx + 1, data.nestedIdx + 1]);
								}
							});
                        }

                        // Initialize first display
                        rotator.rotate.apply($this, [cfg, cfg.startIdx, cfg.nestedIdx]);
                    } catch(e){}
                }
            });
        },

		/**
		 * Rotates the main content and tabs
		 *
		 * @param cfg
		 * @param nextIdx
		 * @param nestedIdx
		 */
		rotate: function(cfg, nextIdx, nestedIdx) {
			console.log('rotating: ', nextIdx, nestedIdx);

            var $this = $(this),
                data = $this.data(rotator.namespace);

            var tabs = $('#hero-tabs', this).find('> li'),
                contents = $('#hero-content', this).find('> li');

            var $currentTab = tabs.eq(data.currentIdx),
                $currentContent = contents.eq(data.currentIdx);

			// Nested content
			if (cfg.nested) {
				var $nestedTabs = $currentContent.find('.nested-tabs li'),
					$nestedContents = $currentContent.find('.nested-content li');

				// Check nested index exists
				if (nestedIdx >= data.nestedCount) {
					nestedIdx = 0;
					nextIdx++;
				} else {
					nestedIdx = nestedIdx;
					nextIdx = data.currentIdx;
				}
			}


			// if nested - lets transition next nested item
			if (nestedIdx != data.nestedIdx && nextIdx == data.currentIdx) {
				var $currentNestedTab = $nestedTabs.eq(data.nestedIdx),
					$currentNestedContent = $nestedContents.eq(data.nestedIdx);

				var $nextNestedTab = $currentContent.find('.nested-tabs li').eq(nestedIdx),
					$nextNestedContent = $currentContent.find('.nested-content li').eq(nestedIdx);

				rotator.transitionNestedContent.apply($this, [cfg, $currentContent, nestedIdx]);
				data.currentIdx = nextIdx == -1 ? cfg.startIdx : nextIdx;
			}
			else {
				// Confirm index exists
				nextIdx = (nextIdx >= data.itemCount) ? 0 : nextIdx;
				var $nextTab = tabs.eq(nextIdx),
					$nextContent = contents.eq(nextIdx);

				if (cfg.nested) {
					rotator.resetNestedTabs.apply($this, [cfg, $nextContent]);
				}

				// Main tab fade
				$nextContent.css('z-index', data.zIndexMax)
					.stop(true)
					.fadeIn(cfg.fadeInSpeed, function() {
						if (data.currentIdx > -1) {
							$currentContent.css('z-index', data.zIndexDefault).css('display', 'none');
							contents.removeClass('active');
							tabs.removeClass('active');

							if (cfg.nested) {
								$nestedTabs.removeClass('active');
								$nestedContents.removeClass('active');
							}
						}
						tabs.eq(nextIdx).addClass('active');
						$(this).addClass('active').css('z-index', data.zIndexDefault + 1);

						rotator.resetProgress.apply($this, [cfg]);
						data.currentIdx = nextIdx;
						data.nestedIdx = nestedIdx;
						data.nestedCount = $nextContent.find('.nested-tabs li').length;

						console.log('updated: ', data.currentIdx, data.nestedIdx);
					});
			}

            try {
                data.nextInterval = setTimeout(function() {
                    rotator.rotate.apply($this, [cfg, data.currentIdx, data.nestedIdx + 1]);
                }, cfg.interval);
            } catch(e){}
        },

		/**
		 * Transitions the nested tabs and content
		 *
		 * @param cfg
		 * @param $currentContent
		 * @param nextIdx
		 */
		transitionNestedContent: function(cfg, $currentContent, nextIdx) {
			var $this = $(this),
				data = $this.data(rotator.namespace);

			var $currentNestedTab = $currentContent.find('.nested-tabs li').eq(data.nestedIdx),
				$currentNestedContent = $currentContent.find('.nested-content li').eq(data.nestedIdx);

			var $nextNestedTab = $currentContent.find('.nested-tabs li').eq(nextIdx),
				$nextNestedContent = $currentContent.find('.nested-content li').eq(nextIdx);

			$nextNestedContent.css('z-index', data.zIndexMax)
				.stop(true)
				.fadeIn(cfg.fadeInSpeed, function() {
					$currentNestedContent.css('z-index', data.zIndexDefault).css('display', 'none');
					$currentNestedContent.removeClass('active');
					$currentNestedTab.removeClass('active');

					$nextNestedTab.addClass('active');
					$(this).addClass('active').css('z-index', data.zIndexDefault + 1);

					rotator.resetProgress.apply($this, [cfg]);
					data.nestedIdx = nextIdx;
				});
		},

		resetNestedTabs: function(cfg, $currentContent) {
			var $this = $(this),
				data = $this.data(rotator.namespace);

			$currentContent.find('.nested-tabs li').removeClass('active')
			$currentContent.find('.nested-content li')
				.removeClass('active')
				.css('z-index', data.zIndexDefault)
				.css('display', 'none');

			$currentContent.find('.nested-tabs li').eq(0).addClass('active');
			$currentContent.find('.nested-content li').eq(0)
				.addClass('active')
				.css('z-index', data.zIndexMax)
				.css('display', 'block');
		},

		/**
		 * Resets the progress bar
		 *
		 * @param cfg
		 */
        resetProgress: function(cfg) {
			if (cfg.progressBar.length > 0) {
				var $this = $(this);
				$(cfg.progressBar, $this).find('div')
					.stop()
					.width('0')
					.animate({ width: '100%'}, cfg.interval - cfg.fadeInSpeed);
			}
        }
    };

    // namespace = 'heroRotator'
    $.fn.heroRotator = function(opts, method) {
        // Defaults
        var config = {
            autoAdvance: true,
            interval: 5000,
            fadeInSpeed: 1000,
            enableNav: true,
            startIdx: 0,
			nestedIdx: 0,
            progressBar: '',
            nested: false
        };

        // Overrides
        config = $.extend(config, opts);

        // Call the method from the array of functions
        // based on the optional method parameter.
        // This allows devs to call specific functions externally (like rotateImage)
        if (rotator[method]) {
            return rotator[method].apply(this, a);
        } else if (typeof method === 'object' || !method) {
            return rotator.init.apply(this, [config]);
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.heroRotator' );
        }
    };
})(jQuery);
