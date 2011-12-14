
var map;
var initialLocation;
var marker;
var browserSupportFlag = new Boolean();

function initialize() {
    var myOptions = {
        zoom: 14,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    if (navigator.geolocation) {
        browserSupportFlag = true;
        navigator.geolocation.getCurrentPosition(function(position) {
            initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			initializeMarker(initialLocation);
            map.setCenter(initialLocation);
        }, function() {
            handleNoGeolocation(browserSupportFlag);
        });
    }
	else if (google.gears) {
        browserSupportFlag = true;
        var geo = google.gears.factory.create('beta.geolocation');
        geo.getCurrentPosition(function(position) {
            initialLocation = new google.maps.LatLng(position.latitude, position.longitude);
            map.setCenter(initialLocation);
        }, function() {
            handleNoGeoLocation(browserSupportFlag);
        });
    }
    else {
        browserSupportFlag = false;
        handleNoGeolocation(browserSupportFlag);
    }

	function initializeMarker(initialLocation) {
		var isFirst = true;
		marker = new google.maps.Marker({
			map: map,
			draggable: true,
			animation: google.maps.Animation.DROP,
			position: initialLocation
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
		function tracking() {
			console.log(marker);
		}
		google.maps.event.addListener(map, 'click', function(event) {
			placeMarker(event.latLng);
	  	});
		google.maps.event.addListener(marker, 'dragend', function(event) {
			placeMarker(event.latLng);
	  	});
	}
}

google.maps.event.addDomListener(window, 'load', initialize);


function initPlotDrop() {
  for (var i =0; i < markerArray.length; i++) {
    setTimeout(function() {
      addMarkerMethod();
    }, i * 200);
  }
}

function handleNoGeolocation(errorFlag) {
        if (errorFlag == true) {
            alert("Geolocation service failed.");
        } else {
            alert("Your browser doesn't support geolocation. We've placed you in Siberia.");
        }

        var options = {
            map: map,
            position: initialLocation,
            content: 'Supported'
        };

        var infowindow = new google.maps.InfoWindow(options);
        map.setCenter(initialLocation);
    }
