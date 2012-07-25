(function ($) {

    var methods = {
        // Define plugin namespace for data and other uses
        namespace : 'gorillaSlider',

        // Plugin default options
        defaults : {
            controls    :   true,   //next and prev buttons
            speed       :   900,    //slide speed
            auto        :   true,   //start sliding on load
            timer       :   6000,   //delay between slides if set to auto
            navigation  :   false,  //ordered list of slides
            tagType     :   'img',  //could be div, li, a, or img
            onComplete  :   function () {} //callback function after animation is complete
        },

        init: function(options){
            var $this = $(this),
                data = $this.data(methods.namespace);

            //determine if data is attached
            if (!data) {
                var opts = $.extend({}, methods.defaults, options);
                opts.itemsTotal = $(opts.tagType, $this).length;
                opts.slideWidth = $(opts.tagType, $this).eq(0).outerWidth(true);
                opts.activeContainer = 0;
                $(this).data(methods.namespace, opts);
                data = $this.data(methods.namespace);
            }

            //position slides, and make them visible
            $(data.tagType, $this).css({"left": data.slideWidth});
            $(data.tagType, $this).eq(data.activeContainer).css({"z-index":2, "left":0}).fadeIn(function () {
                $(this).siblings().css({'display': 'block'});
            });

            //setup timer to auto slide
            if (data.auto) {
                data.timer = setInterval(function () {
                    methods.slide.apply($this, ['next']);
                }, data.timer);
            }

            //setup next and previous buttons
            if (data.controls) {
                var controlsHTML = '<a href="#" class="next">Next</a><a href="#" class="prev">Prev</a>';
                $this.parent().append(controlsHTML);

                var nextBtn = $this.parent().find('.next');
                var prevBtn = $this.parent().find('.prev');

                nextBtn.click(function () {
                    methods.slide.apply($this, ['next']);
                    clearInterval(data.timer);
                    return false;
                });

                prevBtn.click(function () {
                    methods.slide.apply($this, ['prev']);
                    clearInterval(data.timer);
                    return false;
                });
            }

        },

        //takes direction to slide, next or prev
        slide: function(direction){
            var $this = $(this),
                data = $this.data(methods.namespace);

            //don't continue if we are already animating
            if ($(data.tagType, $this).is(':animated')) {
                return false;
            }
            //store currently visible slide
            var prevactiveContainer = data.activeContainer;

            function resetZ() {
                $(data.tagType, $this).eq(prevactiveContainer).css({"z-index": 1});
            }

            //logic to determine which is the next or previous slide
            if (direction == "next") {
                data.activeContainer++;
                if (data.activeContainer == data.itemsTotal) {
                    data.activeContainer = 0;
                }
            } else if (direction == "prev") {
                data.activeContainer--;
                if (data.activeContainer < 0) {
                    data.activeContainer = data.itemsTotal - 1;
                }
            }
            //animations, to achieve the carousel effect we need to first set the activeContainer position to just off stage and animate it in
            if (direction == "next") {
                $(data.tagType, $this).eq(data.activeContainer).css({"left": data.slideWidth, "z-index":"10"}).animate({"left":0}, data.speed, 'swing', resetZ());
                $(data.tagType, $this).eq(prevactiveContainer).animate({"left": -data.slideWidth}, data.speed, 'swing', function(){
                    //optional callback
                    if(data.onComplete){
                        data.onComplete();
                    }
                });
            }
            if (direction == "prev") {
                $(data.tagType, $this).eq(data.activeContainer).css({"left": -data.slideWidth, "z-index":"10"}).animate({"left":0}, data.speed, 'swing', resetZ());
                $(data.tagType, $this).eq(prevactiveContainer).animate({"left": data.slideWidth}, data.speed, 'swing', function(){
                    if(data.onComplete){
                        data.onComplete();
                    }
                });
            }
        }
    };

    $.fn.gorillaSlider = function(option) {
        return this.each(function() {
            if (typeof option === 'object' || typeof option === 'undefined') {
                methods.init.apply(this, [option]);
            } else if (typeof option === 'string' && methods[option]) {
                methods[option].apply(this);
            } else {
                alert('Method ' + option + ' does not exist in ' + methods.namespace);
            }
        });
    };
})(jQuery);