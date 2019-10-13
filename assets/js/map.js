/****************** FONCTION **********************/

/** Initialisation de la map */
$.initMap = function() {
    tab = [];
    geocoder = new google.maps.Geocoder;

    map = new google.maps.Map($('#map')[0], {
        center: {lat: 47.149241, lng: 2.296294},
        zoom: 5
    });

    var myLatlng = new google.maps.LatLng(47.149241,2.296294);
    marker = new google.maps.Marker({
        position: myLatlng,
        title:"Position"
    });

    google.maps.event.addListener(map, 'click', function (event) {
        marker.setMap(null);
        for (var i = 0; i < tab.length; i++ ) {
            tab[i].setMap(null);
        }

        geocodeLatLng(event);
    });
}

/** Appel API google Map */
function geocodeLatLng(event) {
    var latlng = {lat: event.latLng.lat(), lng: event.latLng.lng()};

    geocoder.geocode({'location': latlng}, function(results, status) {
        if (status === 'OK') {
            if (results[0]) {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map
                });
                map.setZoom(15);
                map.setCenter(new google.maps.LatLng(event.latLng.lat(),event.latLng.lng()));
                tab.push(marker);

                $('#city').val(results[1].formatted_address);
                $('#lat').val(event.latLng.lat());
                $('#lng').val(event.latLng.lng());
            } else {
                $.showErrors(['No results found'], '#alert-create');
            }
        } else {
            $.showErrors(['Geocoder failed due to: ' + status], '#alert-create');
        }
    });
}