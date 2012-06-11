
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
			position: initialLocation,
			icon: '/file-bin/images/orange-pin.png',
			draggable: true,
			animation: google.maps.Animation.DROP
		});

		var contentString = '<div class="content">'+
			'<h2 class="heading">Uluru</h2>'+
			'<div class="bodyContent">'+
			'<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large ' +
			'sandstone rock formation in the southern part of the '+
			'Northern Territory, central Australia. It lies 335 km (208 mi) '+
			'south west of the nearest large town, Alice Springs; 450 km '+
			'(280 mi) by road. Kata Tjuta and Uluru are the two major '+
			'Heritage Site.</p>'+
			'<p>Attribution: Uluru, <a href="http://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">'+
			'http://en.wikipedia.org/w/index.php?title=Uluru</a> (last visited June 22, 2009).</p>'+
			'</div>'+
			'</div>';

		/*var boxText = document.createElement("div");
        boxText.style.cssText = "border: 1px solid black; margin-top: 8px; background: red; padding: 5px;";
        boxText.innerHTML = "City Hall, Sechelt<br>British Columbia<br>Canada";*/

        var options = {
		 	content: contentString,
			//disableAutoPan: false,
			maxWidth: 400,
			pixelOffset: new google.maps.Size(50, -150),
			//zIndex: null,
			boxStyle: {
			  	background: "url('tipbox.gif') no-repeat",
				opacity: 0.75,
				width: "400px"
			},
			closeBoxMargin: "10px 2px 2px 2px",
			closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
			infoBoxClearance: new google.maps.Size(1, 1),
			//isHidden: false,
			pane: "floatPane",
			//enableEventPropagation: false
        };

        var infowindow = new InfoBox(options);
		// Trigger infowindow when map is loaded
		google.maps.event.addDomListener(window, 'load', function() {
			infowindow.open(map, initialMarker);
		});


		/*google.maps.event.addListener(map, 'click', function(event) {
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
				}
			});
		});*/

		google.maps.event.addListener(initialMarker, 'dragend', function(event) {
			placeMarker(event.latLng);
			infowindow.setContent('<div class="content">new content</div>');
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
