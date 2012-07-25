
/**
 * Custom Styled Select Plugin
 *
 * Creates the following markup:
 *
	<div class="select-wrap">
		<span class="customStyleSelectBox"><span class="customStyleSelectBoxInner">Text</span></span>
 		<select name="some_name">...</select>
	</div>
 */
(function($) {
	/**
	 * Plugin methods defined below
	 */
	var methods = {
		// Define plugin namespace for data and other uses
		namespace : 'customSelect',

		// Plugin default options
		defaults : {
			//'contentContainer' : '#fixedContent'
		},

		/**
		 * Constructor/Initialize method
		 *
		 * @param Object options
		 */
		init : function(options) {
			var $this = $(this),
				data = $this.data(methods.namespace);

			if (!data) {
				var opts = $.extend({}, methods.defaults, options);

				var currentSelected = $this.find(':selected');
				var wrapHtml = '<span class="customStyleSelectBox"><span class="customStyleSelectBoxInner">'+currentSelected.text()+'</span></span>';
				var $housing = $('<div class="select-wrap" />');

				// Set css props & apply
				$housing.css({
					'display' : 'inline-block',
					'position' : 'relative'
				});
				$this.wrap($housing);
				$this.before(wrapHtml).css({
					position : 'absolute',
					opacity : 0,
					left : 0,
					fontSize : $this.prev().css('font-size')
				}).addClass('styled-select');

				var $spanOuter = $this.prev('.customStyleSelectBox'),
					$spanInner = $spanOuter.children('.customStyleSelectBoxInner');

				// Set css props
				$spanOuter.css({display : 'inline-block'});
				$spanInner.css({display : 'inline-block'});

				/**
				 * Detect height - if spans have padding on top and bottom we need to apply
				 * this to the select so all heights match up
				 */
				if ($this.outerHeight(true) != $spanOuter.outerHeight(true)) {
					var paddingValue = 0;
					if ($spanOuter.outerHeight(true) != $spanOuter.height()) {
						paddingValue += (parseInt($spanOuter.css('padding-top')) + parseInt($spanOuter.css('padding-bottom'))) - (parseInt($spanOuter.css('border-top')) + parseInt($spanOuter.css('border-bottom')));
					}

					if ($spanInner.outerHeight(true) != $spanInner.height()) {
						paddingValue += (parseInt($spanInner.css('padding-top')) + parseInt($spanInner.css('padding-bottom'))) - (parseInt($spanInner.css('border-top')) + parseInt($spanInner.css('border-bottom')));
					}

					$this.css({
						'padding-top' : (paddingValue / 2) - 1,		// subtract 1 for select's border
						'padding-bottom' : (paddingValue / 2) - 1
					});
				}

				opts.outerWrapper = $this.parent();
				opts.spanOuter = $spanOuter;

				$(this).data(methods.namespace, opts);
			}

			// Calculate widths
			methods.calculateWidths.apply($this);

			// Detect change in selection
			$this.change(function() {
				methods.updateText.apply($(this));
			});
		},

		/**
		 * Updates the text inside of the select menu
		 */
		updateText : function() {
			var $this = $(this),
                data = $this.data(methods.namespace);

			if (!data) {
				methods.init.apply($this);
				data = $this.data(methods.namespace);
			}

			var $span = data.spanOuter.find('> span');
			var textValue = $this.find(':selected').text().trim();
			if (textValue.length < 1) {
				$span.html("&nbsp;").parent().addClass('changed');
			} else {
				$span.text(textValue).parent().addClass('changed');
			}
		},

		/**
		 * Calculates the widths of the spans
		 */
		calculateWidths : function() {
			var $this = $(this),
                data = $this.data(methods.namespace);

			var $spanOuter = data.spanOuter,
				$spanInner = data.spanOuter.find('> span');

			// If the select's width < span's default width - lets reset the selects width to match the span's
			console.log($this.attr('id'));
			console.log($this.width());
			console.log($this.css('width'));
			if ($this.outerWidth(true) < $spanOuter.outerWidth(true) && !$this.css('width')) {
				console.log($(this).attr('id') + ' SELECT RESIZE ---------------: ' + $spanOuter.outerWidth(true));
				$this.width($spanOuter.outerWidth(true));
			}

			// Calculate padding excess on spans
			var paddingExcess = ($spanOuter.outerWidth(true) - $spanOuter.width()) + ($spanInner.outerWidth(true) - $spanInner.width());
			$spanInner.css('width', $this.outerWidth(true) - paddingExcess);
		},

		/**
		 * Resets the style span back to the defaults
		 */
		reset : function() {
			var $this = $(this),
                data = $this.data(methods.namespace);

			if (!data) {
				methods.init.apply($this);
				return;
			}

			methods.calculateWidths.apply($this);
			methods.updateText.apply($this);
		},

		/**
		 * Removes the style span from the select
		 */
		remove : function() {
			var $this = $(this),
				data = $this.data(methods.namespace);

			if (!data) {
				return;
			}

			// console.log('Removing: ' + $this.attr('id'));
			var $select = data.outerWrapper.find('> select');

			data.outerWrapper.before($select).remove();
			//data = null;
			$this.data(methods.namespace, null);
		}
	};

	$.fn.customSelect = function(option) {
		return this.each(function() {
			if (option == null || typeof option === 'object') {
				methods.init.apply(this, [option]);
			} else if (typeof option === 'string' && methods[option]) {
				methods[option].apply(this);
			} else {
				alert('Method ' + option + ' does not exist in ' + methods.namespace);
			}
		});
	}
})(window.jQuery);
