function initMap() {
    try {
        console.log('Debug: Initializing Google Maps');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    console.log('Debug: Geolocation success');
                    if (lastAddress) {
                        console.log('Debug: Using last address');
                        initAutocomplete();
                        getLatLongPosition(lastAddress_latitude, lastAddress_longitude);
                    } else {
                        console.log('Debug: Using current position');
                        initAutocomplete();
                        getLatLongPosition(position.coords.latitude, position.coords.longitude);
                    }
                },
                function (error) {
                    console.error('Debug: Geolocation error:', error);
                    // Fallback to default location if geolocation fails
                    initAutocomplete();
                    getLatLongPosition(23.8103, 90.4125); // Default coordinates
                }
            );
        } else {
            console.warn('Debug: Geolocation not supported');
            alert("Sorry, your browser does not support HTML5 geolocation.");
            // Fallback initialization
            initAutocomplete();
            getLatLongPosition(23.8103, 90.4125);
        }
    } catch (error) {
        console.error('Debug: Error in initMap:', error);
    }
}

function getLatLongPosition(latitude, longitude) {
    const myLatlng = {
        lat: parseFloat(latitude),
        lng: parseFloat(longitude)
    };
    const map = new google.maps.Map(document.getElementById("googleMap"), {
        zoom: 15,
        center: myLatlng,
    });

    // Create the initial InfoWindow.
    let infoWindow = new google.maps.InfoWindow({
        content: "Click the map to get latitude & longitude!",
        position: myLatlng,
    });

    infoWindow.open(map);
    // Configure the click listener.
    var marker;
    let total = 0;
    map.addListener("click", (mapsMouseEvent) => {
        // Close the current InfoWindow.
        infoWindow.close();
        // Create a new InfoWindow.
        infoWindow = new google.maps.InfoWindow({
            position: mapsMouseEvent.latLng,
        });

        var latLng = mapsMouseEvent.latLng.toJSON();

        var latlng = new google.maps.LatLng(latLng.lat, latLng.lng);
        var geocoder = geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            'latLng': latlng
        }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    $('#autocomplete-input').val(results[1].formatted_address);

                }
            }
        });


        // Pickup-only restaurant - no delivery charges
        total = subtotal - parseInt(couponAmount);
        
        sessionStorage.removeItem('charge');
        $('#lat').val(latLng.lat);
        $('#long').val(latLng.lng);
        $('#total').text(parseFloat(total).toFixed(2));
         const mode = sessionStorage.getItem('charge');
        // console.log(mode);

        if (marker)
            marker.setMap(null);
        marker = new google.maps.Marker({
            position: myLatlng,
            map,
            draggable: true,
            title: "Your current location.",
        });

        changeMarkerPosition(latLng, marker)

    });

    // Pickup-only restaurant - no delivery charges
    total = subtotal - parseInt(couponAmount);
    sessionStorage.removeItem('charge');
    $('#total').text(parseFloat(total).toFixed(2));
    $('#lat').val(latitude);
    $('#long').val(longitude);
    const mode = sessionStorage.getItem('charge');
    // console.log(mode);



    marker = new google.maps.Marker({
        position: myLatlng,
        map,
        draggable: true,
        title: "Your current location.",
    });
}

function changeMarkerPosition(latLng, marker) {
    var latlng = new google.maps.LatLng(latLng.lat, latLng.lng);
    marker.setPosition(latlng);
}

var mapLat = mapLat;
var mapLong = mapLong;

function initAutocomplete() {
    try {
        console.log('Debug: Initializing autocomplete');
        
        if (lastAddress) {
            getLocation(lastAddress_latitude, lastAddress_longitude);
        } else {
            if (typeof mapLat !== 'undefined' && typeof mapLong !== 'undefined' && mapLat != '' && mapLong != '') {
                getLocation(mapLat, mapLong);
            } else {
                getLocation(null, null);
            }
        }

        var input = document.getElementById('autocomplete-input');
        if (!input) {
            console.warn('Debug: Autocomplete input not found');
            return;
        }
        
        var autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener('place_changed', function () {
            try {
                var place = autocomplete.getPlace();
                if (place && place.geometry) {
                    console.log('Debug: Place selected:', place.formatted_address);
                    getLatLongPosition(place.geometry.location.lat(), place.geometry.location.lng());
                    $('#lat').val(place.geometry.location.lat());
                    $('#long').val(place.geometry.location.lng());
                } else {
                    console.warn('Debug: Place has no geometry');
                }
            } catch (error) {
                console.error('Debug: Error in place_changed:', error);
            }
        });

        if ($('.modalAddressSearch')[0]) {
            setTimeout(function () {
                try {
                    $(".pac-container").prependTo("#autocomplete-container");
                } catch (error) {
                    console.error('Debug: Error moving pac-container:', error);
                }
            }, 300);
        }
    } catch (error) {
        console.error('Debug: Error in initAutocomplete:', error);
    }
}
var geocoder;

function getLocation(lat, long) {

    geocoder = new google.maps.Geocoder();
    if (navigator.geolocation) {
        if (lat && long) {
            showGetPosition(lat, long)
        } else {
            navigator.geolocation.getCurrentPosition(showPosition);
        }
    } else {
        var msg = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
    var Latitude = position.coords.latitude;
    var Longitude = position.coords.longitude;
    $('#lat').val(Latitude);
    $('#long').val(Longitude);
    getLatLongPosition(Latitude, Longitude);

    var latlng = new google.maps.LatLng(Latitude, Longitude);
    geocoder.geocode({
        'latLng': latlng
    }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                $('#autocomplete-input').val(results[0].formatted_address);
            }
        }
    })

}

function showGetPosition(lat, long) {
    var Latitude = lat;
    var Longitude = long;
    $('#lat').val(Latitude);
    $('#long').val(Longitude);


    var latlng = new google.maps.LatLng(Latitude, Longitude);
    geocoder.geocode({
        'latLng': latlng
    }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                $('#autocomplete-input').val(results[0].formatted_address);
            }
        }
    })

}



//modal
function editBtn(id) {
    let editurl = $('#edit' + id).attr('data-url');
    let updateurl = $('#edit' + id).attr('data-attr');

    $.ajax({
        type: 'GET',
        url: editurl,
        dataType: "html",
        success: function (data) {
            let address = JSON.parse(data);
            $("#addressForm").attr('action', updateurl);
            $("#formMethod").val('PUT');
            $("#autocomplete-input").val(address.address);
            $("#lat").val(address.latitude);
            $("#long").val(address.longitude);
            $("#id").val(address.id);
            $("#apartment").val(address.apartment);
            $("#label").val(address.label);
            $("#label_name").val(address.label_name);
            if (address.label == 15) {
                $('.label-name').show();
            }
        }
    });
}


$(document).on('click', '#add-new', function (event) {
    let href = $('#add-new').attr('data-attr');
    modalshow(href);
});

$(document).on('click', '#address-btn', function (event) {
    event.preventDefault();
    var apartmentValue = document.getElementById("apartment").value.trim();
    var labelNameValue = document.getElementById("label_name").value.trim();
    var label = document.getElementById("label").value.trim();
    var jsalertDiv = document.querySelector('.jsalert');

    if (apartmentValue == "" || label == "" ) {
        event.preventDefault();
        jsalertDiv.innerText = "Please fill out all required fields.";
        return false;
    } else if ($('#label').val() == 15 && labelNameValue == "") {
        event.preventDefault();
        jsalertDiv.innerText = "Please fill out all required fields.";
        return false;
    } else {
        $("#address-btn").attr('data-bs-dismiss', 'modal');
        document.getElementById("addressForm").submit();
    }
});


if ($('.check-errors1').text() != "" || $('.check-errors2').text() != "") {

    let href = $('#edit-url').attr('data-attr');
    if ($("#formMethod").val() == 'PUT') {
        href = $('#edit-url').attr('data-attr');
    } else {
        href = $('#add-new').attr('data-attr');
    }
    modalshow(href);
}

function modalshow(href) {
    $('#addressModal').modal('show');
    $("#addressForm").attr('action', href);
}

$(document).on('click', '#modalClose', function (event) {
    // Pickup-only restaurant - no delivery charges
    let total = subtotal - parseInt(couponAmount);
    $('#total').text(parseFloat(total).toFixed(2));
});


if ($('#label').val() == 15) {
    $('.label-name').show();
} else {
    $('.label-name').hide();
}

$('#label').on('change', function () {
    if ($('#label').val() == 15) {
        $('.label-name').show();
    } else {
        $('.label-name').hide();
    }
});


$(document).ready(function() {
    $(".moreAddress").hide();
    $("#moreAddressShow").click(function() {

        $(".moreAddress").toggle(200);
    });
});


function distance(lat1, lon1, lat2, lon2) {
    var radlat1 = Math.PI * lat1 / 180
    var radlat2 = Math.PI * lat2 / 180
    var theta = lon1 - lon2
    var radtheta = Math.PI * theta / 180
    var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
    dist = Math.acos(dist)
    dist = dist * 180 / Math.PI
    dist = dist * 60 * 1.1515
    dist = dist * 1.609344
    return dist
}


function deliveryAddress(latitude, longitude) {
    sessionStorage.removeItem('charge');
    // Pickup-only restaurant - no delivery charges
    let total = subtotal - parseInt(couponAmount);
    $('#total').text(parseFloat(total).toFixed(2));
    const mode = sessionStorage.getItem('charge');
    // console.log(mode);
}
