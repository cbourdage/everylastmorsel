
var map;
var initialLocation;
var marker;
//var browserSupportFlag = new Boolean();

function initialize() {
    var options = {
        zoom: 15,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map-anvas"), options);

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
		function tracking() {
			console.log(marker);
		}

		google.maps.event.addListener(map, 'click', function(event) {
			placeMarker(event.latLng);

			console.log(event.latLng);
			// @TODO display next step
			jQuery.ajax({
			  	url: '/index/plotpoint/',
			  	data: $(event).serialize(),
				dataType: 'html',
			  	success: function(response) {
					$('#overlayMap').html(response);

					// @TODO create lighbox object for entire project - hide, show, update, html, close
					$('#overlayBg').fadeIn();
					$('#overlayMap').fadeIn();
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