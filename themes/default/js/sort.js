$(function (){
	$('.toolbar').each(function(){
		var id = '#'+$('.user-toolbar-options',this).attr('id');
		$(this).toolbar({
	        content: id,
	        hideOnClick: true,
	    });
	});
	$('#toolbar-all').toolbar({
        content: '#user-toolbar-options-all',
        hideOnClick: true,
    });

});
var glyphicon_list = "<span class='glyphicon glyphicon-list-alt'></span> ";
var caret = " <span class='caret'></span>";
$('#sort-list li').each(function(){
  var replace_text = $('a', this).text();
  var href_text = $('a', this).attr('href').replace('?sort=','');
  if($('#sort').attr('data-sort') == href_text){
    $('#sort').html(glyphicon_list+replace_text+caret);
  }
});

$( "#sort-distance" ).click(function(e) {
    e.preventDefault()
    var url = $(this).attr('href');
    if(!readCookie('mylat') || !readCookie('mylng')) {
        var lat;
        var lng;
        GMaps.geolocate({
            success: function(position) {
                lat = position.coords.latitude;
                lng = position.coords.longitude
                setCookie('mylat',lat);
                setCookie('mylng',lng);
                window.location.href = url;
            },
            error: function(error) {
                alert('Geolocation failed: '+error.message);
            },
            not_supported: function() {
                alert("Your browser does not support geolocation");
            },
        });
    }
    else {
        window.location.href = $(this).attr('href');
    }
});

function initLocationsGMap() {
    if (document.getElementById('myLocationBtn') || document.getElementById('listingMap')) {
        jQuery.ajax({
            url: ("https:" == document.location.protocol ? "https:" : "http:") + "//cdn.jsdelivr.net/g/gmaps@0.4.15,maplace.js@0.1.3,jquery.geocomplete@1.6.5",
            dataType: "script",
            cache: true
        }).done(function() {
            locationsGMap();
        });
    }
}

function locationsGMap() {
    var geocoder = new google.maps.Geocoder();
    var latLng;
    var map;
    var marker;
    var typingLocationTimer;
    var doneLocationTypingInterval = 500;

    function geocodePosition(pos) {
        geocoder.geocode({
            latLng: pos
        }, function(responses) {
            if (responses && responses.length > 0) {
                updateMarkerAddress(responses[0].formatted_address);
            } else {
                updateMarkerAddress($('#myLocationBtn').data('marker-error'));
            }
        });
    }

    function updateMarkerPosition(latLng) {
        $('#myLatitude').val(latLng.lat()).removeAttr("disabled");
        $('#myLongitude').val(latLng.lng()).removeAttr("disabled");
    }

    function updateMarkerAddress(str) {
        $('#myAddress').val(str);
    }

    function myLocationInit() {
        if (readCookie('mylat') && readCookie('mylng')) {
            latLng = new google.maps.LatLng(readCookie('mylat'), readCookie('mylng'));
        }
        else {
            latLng = new google.maps.LatLng(0, 0);
        }

        map = new google.maps.Map(document.getElementById('mapCanvas'), {
            zoom: 15,
            center: latLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        marker = new google.maps.Marker({
            position: latLng,
            title: $('#myLocationBtn').data('marker-title'),
            map: map,
            draggable: true
        });

        // Update current position info
        updateMarkerPosition(latLng);
        geocodePosition(latLng);

        // Add events listeners
        google.maps.event.addListener(marker, 'drag', function() {
            updateMarkerPosition(marker.getPosition());
        });

        google.maps.event.addListener(marker, 'dragend', function() {
            geocodePosition(marker.getPosition());
        });
    }

    function doneLocationTyping () {
        GMaps.geocode({
            address: $('#myAddress').val(),
            callback: function (results, status) {
                if (status == 'OK') {
                    var latLng = results[0].geometry.location;
                    map.setCenter(latLng);
                    marker.setPosition(latLng);
                    updateMarkerPosition(latLng);
                }
            }
        });
    }

    $('#myLocation').on('shown.bs.modal', function (e) {
        myLocationInit();
        $("#myLocation .input-group-btn .dropdown-menu li a").click(function(e) {
            e.preventDefault();
            $('.btn-distance:first-child').html($(this).html() + ' <span class="caret"></span>');
            $('#myDistance').val($(this).data('value')).removeAttr("disabled");
        });
        $(".pac-container").css("z-index", $("#myLocation").css("z-index"));
    })

    //on keyup, start the countdown
    $('#myAddress').keyup(function () {
        clearTimeout(typingLocationTimer);
        if ($(this).val()) {
            typingLocationTimer = setTimeout(doneLocationTyping, doneLocationTypingInterval);
        }
    });

    $('#myAddress').geocomplete()
        .bind("geocode:result", function(){
            doneLocationTyping();
    });

    $("#setMyLocation").click(function(e) {
        setCookie('mylat', $("#myLatitude").val());
        setCookie('mylng', $("#myLongitude").val());
        setCookie('mydistance', $("#myDistance").val());
        $('#myLocation').modal('hide');
        //$('#myLocationBtn').html('<i class="glyphicon glyphicon-map-marker"></i> ' + $("#myAddress").val());
        window.location.href = $('#myLocationBtn').data('href');
    });
}

$(function() {
    $('#listingMap').on('shown.bs.modal', function (e) {
        new Maplace({
            locations: locations,
            controls_on_map: false,
            pan_on_click: false
        }).Load();
    })
});

