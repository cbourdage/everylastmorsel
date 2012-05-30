
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

		var contentString = '<div id="content">'+
			'<div id="siteNotice">'+
			'</div>'+
			'<h2 id="firstHeading" class="firstHeading">Uluru</h2>'+
			'<div id="bodyContent">'+
			'<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large ' +
			'sandstone rock formation in the southern part of the '+
			'Northern Territory, central Australia. It lies 335 km (208 mi) '+
			'south west of the nearest large town, Alice Springs; 450 km '+
			'(280 mi) by road. Kata Tjuta and Uluru are the two major '+
			'features of the Uluru - Kata Tjuta National Park. Uluru is '+
			'sacred to the Pitjantjatjara and Yankunytjatjara, the '+
			'Aboriginal people of the area. It has many springs, waterholes, '+
			'rock caves and ancient paintings. Uluru is listed as a World '+
			'Heritage Site.</p>'+
			'<p>Attribution: Uluru, <a href="http://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">'+
			'http://en.wikipedia.org/w/index.php?title=Uluru</a> (last visited June 22, 2009).</p>'+
			'</div>'+
			'</div>';

		var infowindow = new google.maps.InfoWindow({
			content: contentString,
			maxWidth: 400
		});

		// Trigger infowindow when map is loaded
		google.maps.event.addDomListener(window, 'load', function() {
			infowindow.open(map, initialMarker);

			/*console.log(infowindow.content);
			console.log(infowindow.content.parentNode);
			console.log(infowindow.content.parentNode.parentNode);
			if (infowindow.content && infowindow.content.parentNode && infowindow.content.parentNode.parentNode) {
				if (infowindow.content.parentNode.parentNode.previousElementSibling) {
					infowindow.content.parentNode.parentNode.previousElementSibling.className = 'my-custom-popup-container-css-classname';
				}
			}*/
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
