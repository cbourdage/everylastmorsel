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
            opacity : '.5',
            ignoreClass: 'ignore'
        },

        /**
         * Constructor/Initialize method
         *
         * @param options
         */
        init : function(options) {
            // Initialize this instance properties
            var $form = $(this),
                data = $form.data(methods.namespace);

            var inputs = $('input[type="text"], textarea, input[type="password"], input[type="email"], input[type="url"]', $form).filter(':visible');

            if (!data) {
                var opts = $.extend({}, methods.defaults, options);
                $form.data(methods.namespace, opts);
            }

            // iterate over each input and setup events
            inputs.each(function(key, item) {
                var $input = $(this),
                    $label = $input.parent().prev('label');

                // Check value for initial label state
                console.log($input.val().length);
                if ($input.val().length < 1) {
                    methods['show'].apply($label.parents('form'), [$label]);
                }
            });

            /**
             * Set the focus event on each input
             */
            $form.on('focus', 'input[type="text"], textarea, input[type="password"], input[type="email"], input[type="url"]', function(e) {
                var $input = $(this),
                    $label = $input.parent().prev('label');

                methods[$input.val().length > 0 ? 'hide' : 'dim'].apply($label.parents('form'), [$label]);
            });

            /**
             * Set the keyup to hide the label
             */
            $form.on('keyup', 'input[type="text"], textarea, input[type="password"], input[type="email"], input[type="url"]', function(e) {
                var $input = $(this),
                    $label = $(this).parent().prev('label');

                if ($input.val().length > 0) { //} && $label.is(':visible')) {
                    methods['hide'].apply($label.parents('form'), [$label]);
                } else if ($(this).val().length < 1) {
                    methods['dim'].apply($label.parents('form'), [$label]);
                    //this[this._opts.dim ? 'dim' : 'hide']($label);
                }
            });

            /**
             * Set the blur to reset the label
             */
            $form.on('blur', 'input[type="text"], textarea, input[type="password"], input[type="email"], input[type="url"]', function(e) {
                var $input = $(this),
                    $label = $(this).parent().prev('label');

                if ($input.val().length < 1) {
                    methods['show'].apply($label.parents('form'), [$label]);
                }
            });

            // If any inputs are focused set properly
            inputs.filter(':focus').trigger('focus');
        },

        /**
         * Dims label if dim option is set
         *
         * @param $label
         */
        dim : function($label) {
            if (!methods.assertTransformValid($label)) {
                return;
            }
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
         * @param $label
         */
        hide : function($label) {
            if (!methods.assertTransformValid($label)) {
                return;
            }

            $label.animate({ opacity : 0 }, 100).css('display', 'none');
        },

        /**
         * Shows label
         *
         * @param $label
         */
        show : function($label) {
            if (!methods.assertTransformValid($label)) {
                return;
            }

            $label.animate({ opacity : 1 }, 100).css('display', 'block');
        },

        reset : function() {
            //this.inputs.
        },

        /**
         * Determines if a transformation should be applied
         *
         * @param $label
         */
        assertTransformValid : function($label) {
            var $form = $label.parents('form'),
                opts = $form.data(methods.namespace);

            return $label.length && !$label.hasClass(opts.ignoreClass);
            //return $label.length ? $label.length && !$label.hasClass(opts.ignoreClass) : $label.length;
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
    };
})(window.jQuery);