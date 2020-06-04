function initLocationsGMap () {
    map = new GMaps({
        div: '#map',
        zoom: parseInt($('#map').attr('data-zoom')),
        lat: $('#map').attr('data-lat'),
        lng: $('#map').attr('data-lon')
    }); 
    map.addMarker({
        lat: $('#map').attr('data-lat'),
        lng: $('#map').attr('data-lon'),
        draggable: true,
    });
    var typingTimer;                //timer identifier
    var doneTypingInterval = 500;  //time in ms, 5 second for example
    //on keyup, start the countdown
    $('#address').keyup(function(){
        clearTimeout(typingTimer);
        if ($(this).val()) {
           typingTimer = setTimeout(doneTyping, doneTypingInterval);
        }
    });
    //user is "finished typing," refresh map
    function doneTyping () {
        GMaps.geocode({
            address: $('#address').val(),
            callback: function(results, status) {
                if (status == 'OK') {
                    var latlng = results[0].geometry.location;
                    map = new GMaps({
                        div: '#map',
                        lat: latlng.lat(),
                        lng: latlng.lng(),
                    }); 
                    map.setCenter(latlng.lat(), latlng.lng());
                    map.addMarker({
                        lat: latlng.lat(),
                        lng: latlng.lng(),
                        draggable: true,
                    });
                    $('#formorm_latitude').val(latlng.lat());
                    $('#formorm_longitude').val(latlng.lng());
                    $('#preview_lat').text(latlng.lat());
                    $('#preview_lon').text(latlng.lng());
                }
            }
        });
    }
    
    $('.gmap-submit').on('click', function(e) {
        e.preventDefault();
        $('#formorm_latitude').closest('form').submit();
    });
};