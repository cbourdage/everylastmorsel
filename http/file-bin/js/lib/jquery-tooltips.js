(function ($) {
    var methods = {

        // Define plugin namespace for data and other uses
        namespace   :  'toolTipper',

        // Plugin default options
        defaults:{
            opacity     :   0.8,            //
            speedIn     :   150,            //fadeIn speed
            speedOut    :   300,            //fadeOut speed
            delay       :   null,           //delay before closing
            toolTip     :   '.tool-tip',    //class of actual tooltip
            trackMouse  :   false           //have tooltip follow cursor movement
        },
		
        init:function (options) {
            var $this = $(this),
                data = $this.data(methods.namespace);

            //determine if data is attached
            if (!data) {
                var opts = $.extend({}, methods.defaults, options);
                $this.data(methods.namespace, opts);
            }

            $this.on({
                mouseenter: function () {
                    $this.children($(data.toolTip)).fadeIn(data.speedIn, function(){

                    });
                },
                mousemove: function (e) {
                    if(data.trackMouse){
                        $this.children($(data.toolTip)).css({
                           'top': e.pageY,
                           'left': e.pageX
                        });
                    }
                }
            });
        },

        hide: function(){
            var $this = $(this),
                data = $this.data(methods.namespace);

            $this.on({
                mouseleave: function(){
                    $this.children($(data.toolTip)).fadeOut(data.speedOut, function(){

                    });
                }
            });
        }
    };

    $.fn.toolTipper = function (option) {
        return this.each(function () {
            if (typeof option === 'object') {
                methods.init.apply(this, [option]);
            } else if (typeof option === 'string' && methods[option]) {
                methods[option].apply(this);
            } else {
                alert('Method ' + option + ' does not exist in ' + methods.namespace);
            }
        });
    }
})(jQuery);


/**
 * Below is a bunch of sample code used in 3flat
 * for delaying mouse out, etc.

var inToolTip = false;
var toolTips = $container.find('.tooltip'),
    toolTipTriggers = $container.find('.tooltip-trigger');

/**
 * Bind tool tip trigger hover events

toolTipTriggers.hoverIntent({
    over:function (e) {
        showToolTip($j(this).next(), $j(this));
    },
    out:function (e) {
        initHideToolTip($j(this).next(), $j(this), inToolTip);
    },
    timeout:100,
    sensitivity:2,
    interval:100
});

/**
 * Bind tool tip content hover events

toolTips.hover(function () {
    inToolTip = true;
}, function () {
    var $content = $j(this);
    var $trigger = $j(this).prev();
    inToolTip = false;
    initHideToolTip($content, $trigger, inToolTip);
});

/**
 * Shows a tool tip
 *
 * @param $contentEl - containing element
 * @param $trigger - tool tip trigger element

function showToolTip($contentEl, $trigger) {
    $trigger.addClass('active');

    // Hotspots have different markup - accomodate for z-index fixes
    if ($trigger.parent().hasClass('tooltip')) {
        // Fix to identify the default/origional 'shape' element added by ie7 belatedPNG fix
        if ($j.browser.msie && $j.browser.version < 8) {
            $trigger.parent().find('> shape').addClass('original');
        }

        // If less ie9 and a hotspot tooltip trigger need to reset styles and apply png fix
        if ($j.browser.msie && $j.browser.version < 9) {
            $trigger.fixPNGs();
        }
    }

    // Decide how to show
    if ($j('body').hasClass('windows-xp')) {
        $contentEl.show();
    } else {
        $contentEl.fadeIn();
    }

    if ($contentEl.data('timeout')) {
        clearTimeout($contentEl.data('timeout'));
    }
}

/**
 * Initializes the closing of a tool tip - sets timeout
 * and delays before closing
 *
 * @param $contentEl
 * @param $trigger
 * @param inToolTip

function initHideToolTip($contentEl, $trigger, inToolTip) {
    if (!inToolTip) {
        if ($contentEl.data('timeout')) {
            clearTimeout($contentEl.data('timeout'));
        }

        $contentEl.data('timeout', window.setTimeout(function () {
            closeToolTip($contentEl, $trigger);
        }, 300));
        //closeToolTip($contentEl, $trigger);
    }
}

/**
 * Closes tool tips
 *
 * @param $tipContent
 * @param $trigger

function closeToolTip($tipContent, $trigger) {
    $trigger.removeClass('active');

    // Hotspots have different markup - accomodate for z-index fixes
    if ($trigger.parent().hasClass('tooltip')) {
        // Fix to remove the 'shape' element added by ie7 belatedPNG fix for the .active class
        if ($j.browser.msie && $j.browser.version < 8) {
            $trigger.parent().find('> shape').not('.original').remove();
        }

        // If less ie9 and a hotspot tooltip trigger need to reset styles and apply png fix
        if ($j.browser.msie && $j.browser.version < 9) {
            $trigger.attr('style', '').fixPNGs();
        }
    }

    // Decide how to hide
    if ($j('body').hasClass('windows-xp')) {
        $tipContent.hide();
    } else {
        $tipContent.stop(true, true).fadeOut();
    }
}
