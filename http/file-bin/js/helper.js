
/**
 * Helper functions
 */
jQuery.extend({
    serializeJSON : function(json) {
        var string = JSON.stringify(json);
        return string.replace(/([{}"])/g, '').replace(/:/g, '=').replace(/,/g, '&');
    },

    /**
     * <div class="alert hide fade in">
     <a class="close" data-dismiss="alert">×</a>
     message
     </div>
     */
    createAlert : function(type, message) {
        return jQuery('<div class="alert hide fade in ' + type + '"><a class="close" data-dismiss="alert">×</a>' + message + '</div>');
    },

    createErrorAlert : function(message) {
        return jQuery.createAlert('alert-error', message);
    },

    createInfoAlert : function(message) {
        return jQuery.createAlert('alert-info', message);
    },

    createWarningAlert : function(message) {
        return jQuery.createAlert('alert-warning', message);
    },

    createSuccessAlert : function(message) {
        return jQuery.createAlert('alert-success', message);
    },

    formReset : function(form) {
        var formEls = form.get(0).elements;
        for (var i = 0; i < formEls.length; i++) {
            switch (formEls[i].type.toLowerCase()) {
                case "text":
                case "password":
                case "textarea":
                    formEls[i].value = "";
                    break;
                case "radio":
                case "checkbox":
                    if (formEls[i].checked) {
                        formEls[i].checked = false;
                    }
                    break;
                case "select-one":
                case "select-multi":
                    formEls[i].selectedIndex = -1;
                    break;
                default:
                    break;
            }
        }
    }
});



jQuery.extend(window.Elm, {
    /**
     * Injects a message into the specified location relative to the
     * passed in element
     *
     * @param $message
     * @param $el
     * @param location
     */
    injectElement : function($message, $el, location) {
        switch(location) {
            case 'after':
                $el.after($message);
                break;
            case 'before':
                $el.before($message);
                break;
            case 'append':
                $el.append($message);
                break;
            default:
            case 'prepend':
                $el.prepend($message);
                break;
        }

        $message.fadeIn();
    },

    /**
     * checks response for location and redirects
     *
     * @param response
     */
    success : function(response) {
        if (response.location) {
            window.location = response.location;
            return;
        } else if (response.update_areas) {
            response.update_areas.forEach(function(id) {
                jQuery('#' + id).html(response.html[id]);
            });
        }

        if (response.message && arguments.length > 1) {
            var $message = jQuery.createSuccessAlert(response.message).hide();
            Elm.injectElement($message, arguments[1], arguments[2]);
        }

        return;
    },

    /**
     * Handles logging errors for application
     *
     * @param message
     * @param $el
     * @param location
     */
    error : function(message, $el, location) {
        var $message = jQuery.createErrorAlert(message).hide();
        Elm.injectElement($message, $el, location);
        return;

        switch(location) {
            case 'after':
                $el.after($message);
                break;
            case 'before':
                $el.before($message);
                break;
            case 'append':
                $el.append($message);
                break;
            default:
            case 'prepend':
                $el.prepend($message);
                break;
        }

        $message.fadeIn();
    }
});

