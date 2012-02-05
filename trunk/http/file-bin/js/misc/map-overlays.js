var map;
var overlay;

function initialize() {
    var myLatLng = new google.maps.LatLng(62.323907, -150.109291);
    var myOptions = {
        zoom: 11,
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    var swBound = new google.maps.LatLng(62.281819, -150.287132);
    var neBound = new google.maps.LatLng(62.400471, -150.005608);
    var bounds = new google.maps.LatLngBounds(swBound, neBound);
    var srcImage = 'images.jpeg';

    overlay = new USGSOverlay(bounds, srcImage, map);
    
    google.maps.event.addListener(map, 'click', overlay.toggle());
}

function USGSOverlay(bounds, image, map) {
    // Now initialize all properties.
    this.bounds_ = bounds;
    this.image_ = image;
    this.map_ = map;

    // We define a property to hold the image's div. We'll actually create this div
    // upon receipt of the add() method so we'll leave it null for now.
    this.div_ = null;

    // Explicitly call setMap() on this overlay
    this.setMap(map);
}

USGSOverlay.prototype = new google.maps.OverlayView();

USGSOverlay.prototype.onAdd = function() {
    // Create the div and set some basic attributes.
    var div = document.createElement('div');
    div.style.border = "solid";
    div.style.borderWidth = "3px";
    div.style.position = "absolute";

    // Create an img element and attach it to the div.
    var img = document.createElement("img");
    img.src = this.image_;
    img.style.width = "100%";
    img.style.height = "100%";
    div.appendChild(img);

    // Set the overlay's div_ property to this DIV
    this.div_ = div;

    // We add an overlay to a map via one of the map's panes.
    // We'll add this overlay to the overlayImage pane.
    var panes = this.getPanes();
    panes.floatPane.appendChild(div);
}

USGSOverlay.prototype.draw = function() {
    // Size and position the overlay. We use a southwest and northeast
    // position of the overlay to peg it to the correct position and size.
    // We need to retrieve the projection from this overlay to do this.
    var overlayProjection = this.getProjection();

    // Retrieve the southwest and northeast coordinates of this overlay
    // in latlngs and convert them to pixels coordinates.
    // We'll use these coordinates to resize the DIV.
    var sw = overlayProjection.fromLatLngToDivPixel(this.bounds_.getSouthWest());
    var ne = overlayProjection.fromLatLngToDivPixel(this.bounds_.getNorthEast());

    // Resize the image's DIV to fit the indicated dimensions.
    var div = this.div_;
    div.style.left = sw.x + 'px';
    div.style.top = ne.y + 'px';
    div.style.width = (ne.x - sw.x) + 'px';
    div.style.height = (sw.y - ne.y) + 'px';
}

USGSOverlay.prototype.onRemove = function() {
    this.div_.parentNode.removeChild(this.div_);
    this.div_ = null;
}

// Note that the visibility property must be a string enclosed in quotes
USGSOverlay.prototype.hide = function() {
    if (this.div_) {
        this.div_.style.visibility = "hidden";
    }
}

USGSOverlay.prototype.show = function() {
    if (this.div_) {
        this.div_.style.visibility = "visible";
    }
}

USGSOverlay.prototype.toggle = function() {
    if (this.div_) {
        if (this.div_.style.visibility == "hidden") {
            this.show();
        } else {
            this.hide();
        }
    }
}

USGSOverlay.prototype.toggleDOM = function() {
    if (this.getMap()) {
        this.setMap(null);
    } else {
        this.setMap(this.map_);
    }
}


// Bind to the loading
google.maps.event.addDomListener(window, 'load', initialize);