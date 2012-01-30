
var map;
var initialLocation;
var marker;
var zoomLevel = 15;
//var browserSupportFlag = new Boolean();

function initialize() {
    var options = {
        zoom: zoomLevel,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("mapCanvas"), options);
	window.map = map;

	// Setup initial location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			map.setCenter(initialLocation);
			initializeMarker(initialLocation);
        }, function() {
            handleNoGeolocation(true);
        });
    }
	else if (google.gears) {
        var geo = google.gears.factory.create('beta.geolocation');
        geo.getCurrentPosition(function(position) {
            initialLocation = new google.maps.LatLng(position.latitude, position.longitude);
            map.setCenter(initialLocation);
        }, function() {
            handleNoGeolocation(true);
        });
    }
    else {
        handleNoGeolocation(true);
    }

	function initializeMarker(initialLocation) {
		var isFirst = true;
		marker = new google.maps.Marker({
			map: map,
			draggable: true,
			animation: google.maps.Animation.DROP,
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
				lat : event.latLng.Oa,
				long : event.latLng.Pa
			};

			//console.log(coords);
			jQuery.ajax({
			  	url: '/index/plot-point/',
			  	data: jQuery.serializeJSON(coords),
				dataType: 'html',
			  	success: function(response) {
					// @TODO create lighbox object for entire project - hide, show, update, html, close
				  	elm.overlayUpdate(response);
				  	elm.overlayShow();
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

function handleNoGeolocation(errorFlag) {
	if (errorFlag == true) {
		alert("Geolocation service failed.");
		return;
	}

	var options = {
		map: map,
		position: initialLocation,
		content: 'Supported'
	};

	var infowindow = new google.maps.InfoWindow(options);
	map.setCenter(initialLocation);
}
