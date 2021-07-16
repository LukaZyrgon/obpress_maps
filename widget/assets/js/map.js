jQuery(window).on("elementor/frontend/init", function () {
  //hook name is 'frontend/element_ready/{widget-name}.{skin} - i dont know how skins work yet, so for now presume it will
  //always be 'default', so for example 'frontend/element_ready/slick-slider.default'
  //$scope is a jquery wrapped parent element
  elementorFrontend.hooks.addAction(
    "frontend/element_ready/OBMap.default",
    function ($scope, $) {
      var map;
      var bounds;
      var locations;

      map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        gestureHandling: "greedy",
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        fullscreenControl: true,
        fullscreenControlOptions: {
          position: google.maps.ControlPosition.LEFT_TOP,
        },
      });

      bounds = new google.maps.LatLngBounds();

      var infowindow = new google.maps.InfoWindow();

      var marker, pos, i;
      bounds = new google.maps.LatLngBounds();

      locations = JSON.parse(jQuery(".obpress-map").attr("data-locations"));

      for (i = 0; i < locations.length; i++) {
        pos = new google.maps.LatLng(
          locations[i].hotelLat,
          locations[i].hotelLong
        );
        marker = new google.maps.Marker({
          position: pos,
          map: map,
        });

        bounds.extend(pos, 0);

        google.maps.event.addListener(
          marker,
          "click",
          (function (marker, i) {
            return function () {
              infowindow.setContent(locations[i].hotelName);
              infowindow.open(map, marker);
            };
          })(marker, i)
        );
      }

      map.setCenter(bounds.getCenter());

      map.fitBounds(bounds, 15);
      window.setTimeout(function () {
        google.maps.event.clearListeners(map, "bounds_changed");
      }, 100);
    }
  );
});
