(function ($) {
    var methods = {

        // Define plugin namespace for data and other uses
        namespace : 'gorillaModal',

        // Plugin default options
        defaults : {
            opacity     :   0.8,            //overlay opacity
            speedIn     :   150,            //fadeIn speed
            speedOut    :   300,            //fadeOut speed
            ajax        :   {},             //optional data to be sent w/ ajax request
            content     :   null,           //optional content should be html
            onClose     :   function () {}, //callback function after modal has been closed
            onComplete  :   function () {}  //callback function after modal content has loaded
        },

        init:function (options) {
            var $this = $(this),
                data = $this.data(methods.namespace);

            //determine if data is attached
            if (!data) {
                var opts = $.extend({}, methods.defaults, options);
                opts.url = $this.attr("href");
                opts.container = $('<div id="modal-container"/>');
                opts.overlay = $('<div id="modal-overlay"/>');
                opts.loader = $('<div id="modal-loader"/>');
                opts.close = $('<a id="modal-close" href="#">Close</a>');
                opts.busy = false;
                $this.data(methods.namespace, opts);
                data = $this.data(methods.namespace);
            }

            $this.click(function (e) {
                //append overlay and bind hide methods
                e.preventDefault();
                $("body").append(
                    data.overlay.click(function () {
                        if (data.container.length && data.busy == false) {
                            methods.hide.apply($this);
                        }
                    })
                );
                //append loader, fadeIn overlay and trigger the show method
                $("body").append(data.loader);
                data.overlay.css("opacity", data.opacity).fadeIn(data.speedIn);
                methods.show.apply(this);
            });
        },

        show:function () {
            var $this = $(this),
                data = $this.data(methods.namespace);
            //in progress
            data.busy = true;
            //determine if we are dealing with an inline or ajax call
            if(data.url.indexOf("#") === 0){
                var $temp = $('<div id="temp-'+ data.url.substr(1) +'" />').hide();
                $temp.insertBefore($(data.url));
                $("body").append(data.container.html($(data.url).show()));
                methods.placement.apply($this);
            }else if(data.content !== 'undefined' && data.content !== null){
                $("body").append(data.container.html(data.content));
                methods.placement.apply($this);
            }else{
                $.ajax({
                    url: data.url,
                    data: data.ajax,
                    success:function (response) {
                        $("body").append(data.container.html(response));
                        methods.placement.apply($this);
                    },
                    error:function () {
                        var msg = "<p>An error occurred while processing your request. Please try again later.</p>";
                        $("body").append(data.container.html(msg));
                        methods.placement.apply($this);
                    }
                });
            }

        },

        hide:function () {
            var $this = $(this),
                data = $this.data(methods.namespace),
                $temp;
            if(data.url.indexOf('#') != -1){
                //must be an inline call
                $temp = $("#temp-" + data.url.substr(1));
                var $content = data.container.contents();
                //fadeout the container and overlay, determine if we have an inline placeholder if so put content back
                if(data.busy == false){
                    data.container.add(data.overlay).fadeOut(data.speedOut, function () {
                        if($temp.length){
                            $temp.replaceWith($content.hide());
                        }
                        $(this).add(data.close).remove();
                        data.onClose($this);
                    });
                }
            }else{
                if(data.busy == false){
                    data.container.add(data.overlay).fadeOut(data.speedOut, function () {
                        $(this).add(data.close).remove();
                        data.onClose($this);
                    });
                }
            }
        },

        placement:function(){
            var $this = $(this),
                data = $this.data(methods.namespace);

            //reset container offset
            data.container.css({"margin-left": 0, "margin-top": 0 });

            //get dimensions of container, create offsets for centering
            var modalWidth = data.container.outerWidth(true) / 2,
                modalHeight = data.container.outerHeight(true) / 2;

            data.container.css({"margin-left":-modalWidth, "margin-top":-modalHeight});
            data.loader.remove();
            data.close.click(function(e){
                e.preventDefault();
                methods.hide.apply($this);
            });
            data.container.prepend(data.close.show());
            data.container.fadeIn(data.speedIn, function(){
                //done loading
                data.busy = false;
                data.onComplete($this);
            });

        }

    };

    $.fn.gorillaModal = function(option) {
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