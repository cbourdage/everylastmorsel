
/**
 * Form input & textarea inline labels
 *
 * Assumes the following markup:
 *
	<label for="input" />
	<div>
		<input id="input" />
	</div>
 */
(function($) {
	/**
	 * Plugin methods defined below
	 */
	var methods = {
		// Define plugin namespace for data and other uses
		namespace : 'labelify',

		// Plugin default options
		defaults : {
			dim : false,
			opacity : '.5'
		},

		/**
		 * Constructor/Initialize method
		 *
		 * @param Object options
		 */
		init : function(options) {
            // Initialize this instance properties
			var $form = $(this),
				data = $form.data(methods.namespace);
            var inputs = $('input, textarea', this.$form).filter(':visible');

			if (!data) {
				var opts = $.extend({}, methods.defaults, options);
				$(this).data(methods.namespace, opts);
			}

			// iterate over each input and setup events
            inputs.each(function(key, item) {
                // find label
                var $label = $(this).parent().prev();

				// Check value for initial label state
                if ($(this).val().length > 0) {
					methods['hide'].apply($label.parents('form'), [$label]);
				}
			});

			/**
			 * Set the focus event on each input
			 */
			$form.on('focus', 'input, textarea', function(e) {
				//e && e.preventDefault();
				var $label = $(this).parent().prev();
				methods[$(this).val().length > 0 ? 'hide' : 'dim'].apply($label.parents('form'), [$label]);
			});

			/**
			 * Set the keyup to hide the label
			 */
			$form.on('keyup', 'input, textarea', function(e) {
				var $label = $(this).parent().prev();
				if ($(this).val().length > 0) {//} && $label.is(':visible')) {
					methods['hide'].apply($label.parents('form'), [$label]);
				} else if ($(this).val().length < 1) {
					methods['dim'].apply($label.parents('form'), [$label]);
					//this[this._opts.dim ? 'dim' : 'hide']($label);
				}
			});

			/**
			 * Set the blur to reset the label
			 */
			$form.on('blur', 'input, textarea', function(e) {
				var $label = $(this).parent().prev();
				if ($(this).val().length < 1) {
					methods['show'].apply($label.parents('form'), [$label]);
				}
			});

			// If any inputs are focused set properly
            inputs.filter(':focus').trigger('focus');
        },

		/**
		 * Dims label if dim option is set
		 *
		 * @param Object $label
		 */
        dim : function($label) {
			var $form = $label.parents('form'),
				opts = $form.data(methods.namespace);
			if (opts.dim) {
				$label.animate({ opacity : opts.opacity }, 100).css('display', 'block');
			} else {
				methods['hide'].apply($form, [$label]);
			}
        },

		/**
		 * Hides label
		 *
		 * @param Object $label
		 */
		hide : function($label) {
			$label.animate({ opacity : 0 }, 100).css('display', 'none');
		},

		/**
		 * Shows label
		 *
		 * @param Object $label
		 */
		show : function($label) {
			$label.animate({ opacity : 1 }, 100).css('display', 'block');
		},

        reset : function() {
			//this.inputs.
        }
	};

	$.fn.labelify = function(option) {
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

