// selectize for location select
$(function(){

    if($('#location-chained').length > 0){
        // create 1st location select
        location_select = createLocationSelect();
        // remove hidden class
        $('#location-chained .select-location[data-level="0"]').parent('div').removeClass('hidden');

        // load options for 1st location select
        location_select.load(function(callback) {
            $.ajax({
                url: $('#location-chained').data('apiurl'),
                type: 'GET',
                data: {
                    "id_location_parent": 1,
                    "sort": 'order',
                },
                success: function(results) {
                    callback(results.locations);
                    if (results.locations.length === 0)
                        $('#location-chained').closest('.form-group').hide();
                },
                error: function() {
                    callback();
                }
            });
        });
    }

});

function createLocationSelect () {

    // count how many location selects we have rendered
    num_location_select = $('#location-chained .select-location[data-level]').length;

    // clone location select from template
    $('#select-location-template').clone().attr('id', '').insertBefore($('#select-location-template')).find('select').attr('data-level', num_location_select);

    // initialize selectize on created location select
    location_select = $('.select-location[data-level="'+ num_location_select +'"]').selectize({
        valueField:  'id_location',
        labelField:  'name',
        searchField: 'name',
        onChange: function (value) {

            if (!value.length) return;

            // update #location-selected input value
            $('#location-selected').attr('value', value);

            // get current location level
            current_level = $('#location-chained .option[data-value="'+ value +'"]').closest('.selectize-control').prev().data('level');

            destroyLocationChildSelect(current_level);

            // create location select
            location_select = createLocationSelect();

            // load options for location select
            location_select.load(function (callback) {
                $.ajax({
                    url: $('#location-chained').data('apiurl'),
                    data: {
                        "id_location_parent": value,
                        "sort": 'order',
                    },
                    type: 'GET',
                    success: function (results) {
                        if (results.locations.length > 0)
                        {
                            callback(results.locations);
                            $('#location-chained .select-location[data-level="' + (current_level + 1) + '"]').parent('div').removeClass('hidden');
                        }
                        else
                        {
                            destroyLocationChildSelect(current_level);
                        }
                    },
                    error: function () {
                        callback();
                    }
                });
            });
        }
    });

    // return selectize control
    return location_select[0].selectize;
}

function destroyLocationChildSelect (level) {
    if (level === undefined) return;
    $('#location-chained .select-location[data-level]').each(function () {
        if ($(this).data('level') > level) {
            $(this).parent('div').remove();
        }
    });
}

$('#location-edit button').click(function(){
    $('#location-chained').removeClass('hidden');
    $('#location-edit').addClass('hidden');
});

//datepicker in case date field exists
if($('.cf_date_fields').length != 0){
    $('.cf_date_fields').datepicker();
}

$('.fileinput').on('change.bs.fileinput', function() {
    //check whether browser fully supports all File API
    if (FileApiSupported())
    {
        //get the file size and file type from file input field
        var $input = $(this).find('input[name^="image"]');
        var image = $input[0].files[0];
        var max_size = $('.images').data('max-image-size')*1048576 // max size in bites
        var $closestFileInput = $(this).closest('.fileinput');

        //resize image
        canvasResize(image, {
            width: $('.images').data('image-width'),
            height: $('.images').data('image-height'),
            crop: false,
            quality: $('.images').data('image-quality'),
            callback: function(data, width, height) {

                var base64Image = new Image();
                base64Image.src = data;

                if (base64Image.size > max_size)
                {
                    swal({
                        title: '',
                        text: $('.images').data('swaltext'),
                        type: "warning",
                        allowOutsideClick: true
                    });

                    $closestFileInput.fileinput('clear');
                }
                else
                {
                    $('<input>').attr({
                    type: 'hidden',
                    name: 'base64_' + $input.attr('name'),
                    value: data
                    }).appendTo('.upload_image');
                }
            }
        });

        // Fixes exif orientation on thumbnail
        var thumbnail = $(this).find('.thumbnail > img');
        var rotation = 1;
        var rotate = {
            1: 'rotate(0deg)',
            2: 'rotate(0deg)',
            3: 'rotate(180deg)',
            4: 'rotate(0deg)',
            5: 'rotate(0deg)',
            6: 'rotate(90deg)',
            7: 'rotate(0deg)',
            8: 'rotate(270deg)'
        };

        loadImage.parseMetaData(
            image,
            function (data) {
                if (data.exif) {
                    rotation = data.exif.get('Orientation');
                    thumbnail.css('transform', rotate[rotation]);
                    // Safari fix
                    thumbnail.css("-webkit-transform", rotate[rotation]);
                }
            }
        );
    }

    //unhide next box image after selecting first
    $(this).next('.fileinput').removeClass('hidden');
});

$('.fileinput').on('clear.bs.fileinput', function() {
    var $input = $(this).find('input[name^="image"]');
    $('input[name="base64_' + $input.attr('name') + '"]').remove();
});

$(".upload_image").submit( function(event) {
    if (FileApiSupported()) {
        clearFileInput($('input[name="image0"]'));
        return true;
    }
});

function clearFileInput($input) {
    if ($input.val() == '') {
        return;
    }
    // Fix for IE ver < 11, that does not clear file inputs
    if (/MSIE/.test(navigator.userAgent)) {
        var $frm1 = $input.closest('form');
        if ($frm1.length) {
            $input.wrap('<form>');
            var $frm2 = $input.closest('form'),
                $tmpEl = $(document.createElement('div'));
            $frm2.before($tmpEl).after($frm1).trigger('reset');
            $input.unwrap().appendTo($tmpEl).unwrap();
        } else {
            $input.wrap('<form>').closest('form').trigger('reset').unwrap();
        }
    } else if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) {
        $input.replaceWith($input.clone());
    } else {
        $input.val('');
    }
}

// check whether browser fully supports all File API
function FileApiSupported() {
    if (window.File && window.FileReader && window.FileList && window.Blob)
        return true;

    return false;
}

function initLocationsGMap() {
    jQuery.ajax({
        url: ("https:" == document.location.protocol ? "https:" : "http:") + "//cdn.jsdelivr.net/gmaps/0.4.25/gmaps.min.js",
        dataType: "script",
        cache: true
    }).done(function() {
        locationsGMap();
    });
}

function locationsGMap() {
    // google map set marker on address
    if ($('#map').length !== 0){
        new GMaps({
            div: '#map',
            zoom: parseInt($('#map').attr('data-zoom')),
            lat: $('#map').attr('data-lat'),
            lng: $('#map').attr('data-lon')
        });
        var typingTimer;                //timer identifier
        var doneTypingInterval = 500;  //time in ms, 5 second for example
        //on keyup, start the countdown
        $('#address').keyup(function () {
            clearTimeout(typingTimer);
            if ($(this).val()) {
               typingTimer = setTimeout(doneTyping, doneTypingInterval);
            }
        });
        //user is "finished typing," refresh map
        function doneTyping () {
            GMaps.geocode({
                address: $('#address').val(),
                callback: function (results, status) {
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
                            dragend: function(event) {
                                var lat = event.latLng.lat();
                                var lng = event.latLng.lng();
                                GMaps.geocode({
                                    lat: lat,
                                    lng: lng,
                                    callback: function(results, status) {
                                        if (status == 'OK') {
                                            $("input[name='address']").val(results[0].formatted_address)
                                        }
                                    }
                                });
                                $('#profile-latitude').val(lat).removeAttr("disabled");
                                $('#profile-longitude').val(lng).removeAttr("disabled");
                            },
                        });
                        $('#profile-latitude').val(latlng.lat()).removeAttr("disabled");
                        $('#profile-longitude').val(latlng.lng()).removeAttr("disabled");
                    }
                }
            });
        }
    }

    // auto locate user
    $('.locateme').click(function() {
        var lat;
        var lng;
        GMaps.geolocate({
            success: function(position) {
                lat = position.coords.latitude;
                lng = position.coords.longitude
                map = new GMaps({
                    div: '#map',
                    lat: lat,
                    lng: lng,
                });
                map.setCenter(lat, lng);
                map.addMarker({
                    lat: lat,
                    lng: lng,
                });
                $('#profile-latitude').val(lat).removeAttr("disabled");
                $('#profile-longitude').val(lng).removeAttr("disabled");
                GMaps.geocode({
                    lat: lat,
                    lng: lng,
                    callback: function(results, status) {
                        if (status == 'OK') {
                            $("input[name='address']").val(results[0].formatted_address)
                        }
                    }
                });
            },
            error: function(error) {
                alert('Geolocation failed: '+error.message);
            },
            not_supported: function() {
                alert("Your browser does not support geolocation");
            },
        });
    });
}
