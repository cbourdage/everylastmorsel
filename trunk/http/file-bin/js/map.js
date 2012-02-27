
var map;
var initialLocation;
var marker;
var zoomLevel = 14;
//var browserSupportFlag = new Boolean();

function initialize() {
    var options = {
        zoom: zoomLevel,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map-canvas"), options);
	window.map = map;

	// Setup initial location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(initializeMarker);
    } else if (google.gears) {
        var geo = google.gears.factory.create('beta.geolocation');
        geo.getCurrentPosition(initializeMarker);
    }

	function initializeMarker(position) {
		var isFirst = true;
		var initialLocation = new google.maps.LatLng(position.latitude, position.longitude)
		marker = new google.maps.Marker({
			map: map,
			draggable: true,
			animation: google.maps.Animation.DROP
			//position: initialLocation
		});

		function placeMarker(latLng) {
			console.log(latLng);
			marker.setPosition(latLng);
			if (marker.getAnimation() != null) {
				marker.setAnimation(null);
			} else {
				marker.setAnimation(google.maps.Animation.BOUNCE);
			}
		}

		google.maps.event.addListener(map, 'click', function(event) {
			placeMarker(event.latLng);

			var coords = {
				lat : event.latLng.lat(),
				long : event.latLng.lng()
			};
			jQuery.ajax({
			  	url: '/index/plot-point/',
			  	data: jQuery.serializeJSON(coords),
				dataType: 'html',
			  	success: function(response) {
					jQuery('#overlayMap').find('.content').html(response);
					jQuery('#overlayMap').fadeIn();
				  	jQuery('#overlayBg').fadeIn();
				},
				error: function() {
					// @TODO create a simple error handler function to display global message
					alert('error in request');
				},
				exception: function() {
					// @TODO create a simple error handler function to display global message
					alert('error in request');
				}
			});
	  	});
		google.maps.event.addListener(marker, 'dragend', function(event) {
			placeMarker(event.latLng);
	  	});
	}
}

google.maps.event.addDomListener(window, 'load', initialize);