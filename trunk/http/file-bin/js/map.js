
var map;
var initialLocation;
var initialMarker;
var zoomLevel = 15;
//var browserSupportFlag = new Boolean();

jQuery(function($) {
	// Init location
	_initRecurse();

	var locationTimeout;
	function _initRecurse() {
		if (typeof(elm.myPosition) == 'undefined') {
			initLocation();
			locationTimeout = window.setTimeout(function() { _initRecurse() }, 300);
		} else {
			// init location
			initialLocation = new google.maps.LatLng(elm.myPosition.lat, elm.myPosition.long);

			// init map on window load
			//google.maps.event.addDomListener(window, 'load', _initMap);
			_initMap();
		}
	}

	/**
	 * Initializes the map options and events.
	 */
	function _initMap() {

		var options = {
			center: initialLocation,
			zoom: zoomLevel,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			panControl: true,
			zoomControl: true,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.SMALL
		  	},
			streetViewControl: true,
			mapTypeControl: false,
			scaleControl: false,
			overviewMapControl: false
		};

		// Create map
		map = new google.maps.Map(document.getElementById("mapCanvas"), options);

		// Init marker
		initialMarker = new google.maps.Marker({
			map: map,
			draggable: true,
			animation: google.maps.Animation.DROP
			//position: initialLocation
		});

		google.maps.event.addListener(map, 'click', function(event) {
			placeMarker(event.latLng);

			var coords = {
				lat : event.latLng.lat(),
				long : event.latLng.lng()
			};

			var $modal = jQuery('#mapModal'),
				$content = $modal.find('.modal-body');

			$modal.modal('show');

			jQuery.ajax({
				url: '/index/plot-point/',
				data: jQuery.serializeJSON(coords),
				dataType: 'html',
				success: function(response) {
					$modal.html(response);
				},
				error: function() {
					elm.error("Oops! We've encountered some troubles. Try again shortly!", $content, 'prepend');
				},
				exception: function() {
					elm.error("Oops! We've encountered some troubles. Try again shortly!", $content, 'prepend');
				}
			});
		});

		google.maps.event.addListener(initialMarker, 'dragend', function(event) {
			placeMarker(event.latLng);
		});


		/**
		 * Places a marker at the provided latLng
		 *
		 * @param latLng
		 */
		function placeMarker(latLng) {
			console.log(latLng);
			initialMarker.setPosition(latLng);
			if (initialMarker.getAnimation() != null) {
				initialMarker.setAnimation(null);
			} else {
				initialMarker.setAnimation(google.maps.Animation.BOUNCE);
			}
		}
	}
});
