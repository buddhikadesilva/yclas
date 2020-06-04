$(function(){

    //favorites system
	$('.add-favorite, .remove-favorite').click(function(event) {
		  event.preventDefault();
		  $this = $(this);
		  $.ajax({ url: $this.attr('href'),
				}).done(function ( data ) {

                    //favorites counter
                    countname = 'count'+$this.data('id');
                    if(document.getElementById(countname))
                    {
                        currentvalue = parseInt($('#'+countname).html(),10);
                        if($('#'+$this.data('id')+' a').hasClass('add-favorite remove-favorite'))
                            $('#'+countname).html(currentvalue-1);
                        else
                            $('#'+countname).html(currentvalue+1);
                    }

					$('#'+$this.data('id')+' a').toggleClass('add-favorite remove-favorite');
					$('#'+$this.data('id')+' a i').toggleClass('glyphicon-star-empty glyphicon-star');
				});
	});

});

$(function(){

    //notification system
    var favicon = false

    /*if ($('link[rel="shortcut icon"]')[0].href.indexOf("cdn.yclas") === -1) {
        favicon = new Favico({
        animation : 'popFade'
    });
    }*/

    $('#contact-notification').click(function(event) {
        $.get($(this).data('url'));
        $(document).mouseup(function (e)
        {
            var contact = $("#contact-notification");

            if (!contact.is(e.target) // if the target of the click isn't the container...
                && contact.has(e.target).length === 0) // ... nor a descendant of the container
            {
                //$("#contact-notification").slideUp();
                $("#contact-notification span").hide();
                $("#contact-notification i").removeClass('fa-bell').addClass('fa-bell-o');
                $("#contact-notification-dd" ).remove();

                if ( favicon !== false ) {
                favicon.badge(0);
            }
            }
        });
    });

    //intial value
    if ( favicon !== false ) {
        favicon.badge($('#contact-notification span').first().text());
    }

});

//validate auth pages
$(function(){

    $.validator.addMethod(
        "emaildomain",
        function(value, element, domains) {
            if (domains.length === 0)
                return true;

            for (var i = 0; i < domains.length; i++) {
                if (value.indexOf(("@" + domains[i]), value.length - ("@" + domains[i]).length) !== -1) {
                    return true;
                }
            }

            return false;
        }
    );

    $.validator.addMethod(
        "nobannedwords",
        function(value, element, words) {
            if (words.length === 0)
                return true;

            for (var i = 0; i < words.length; i++) {
                if (value.indexOf(words[i]) !== -1) {
                    return false;
                }
            }

            return true;
        }
    );

    var $params = {rules:{}, messages:{}};
    $params['rules']['email'] = {required: true, email: true};

    $(".auth").each(function() {
        $(this).validate($params)
    });

    var $register_params = {rules:{}, messages:{}};
    $register_params['rules']['email'] = {required: true, email: true, emaildomain: $('.register :input[name="email"]').data('domain')};
    $register_params['rules']['password1'] = {required: true};
    $register_params['rules']['password2'] = {required: true};
    $register_params['messages']['email'] = {"emaildomain" : $('.register :input[name="email"]').data('error')};
    $register_params['rules']['captcha'] = {"remote" : {url: $(".register").attr('action'),
                                                        type: "post",
                                                        data: {ajaxValidateCaptcha: true}}};
    $register_params['messages']['captcha'] = {"remote" : $('.register :input[name="captcha"]').data('error')};

    $(".register").each(function() {
        $(this).validate($register_params)
    });

});

function createCookie(name,value,seconds) {
    if (seconds) {
        var date = new Date();
        date.setTime(date.getTime()+(seconds*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = encodeURIComponent(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}

function initAutoLocate() {
    if ($('input[name="auto_locate"]').length) {
        jQuery.ajax({
            url: ("https:" == document.location.protocol ? "https:" : "http:") + "//cdn.jsdelivr.net/gmaps/0.4.25/gmaps.min.js",
            dataType: "script",
            cache: true
        }).done(function() {
            autoLocate();
        });
    }
}

function autoLocate() {
    $('#auto-locations').on('show.bs.modal', function () {
        $('.modal .modal-body').css('overflow-y', 'auto');
        $('.modal .modal-body').css('max-height', $(window).height() * 0.8);
    });

    $('#auto-locations').modal('show');

    if ( ! readCookie('cancel_auto_locate') && ( ! readCookie('mylat') || ! readCookie('mylng'))) {
        var lat;
        var lng;
        GMaps.geolocate({
            success: function(position) {
                lat = position.coords.latitude;
                lng = position.coords.longitude
                // 30 minutes cookie
                createCookie('mylat',lat,1800);
                createCookie('mylng',lng,1800);
                // show modal
                $.get($('meta[name="application-name"]').data('baseurl'), function(data) {
                    $('input[name="auto_locate"]').after($(data).find("#auto-locations"));
                    $('#auto-locations').modal('show');
                    $('#auto-locations .list-group-item').click(function(event) {
                        event.preventDefault();
                        $this = $(this);
                        $.post($('meta[name="application-name"]').data('baseurl'), {
                            user_location: $this.data('id')
                        })
                        .done(function( data ) {
                            window.location.href = $this.attr('href');
                        });
                    });
                })
            },
            error: function(error) {
                console.log('Geolocation failed: '+error.message);
                createCookie('cancel_auto_locate',1,1800);
            },
            not_supported: function() {
                console.log("Your browser does not support geolocation");
                createCookie('cancel_auto_locate',1,1800);
            },
        });
    }
}

$(function(){
    $('#auto-locations .list-group-item').click(function(event) {
        event.preventDefault();
        $this = $(this);
        $.post($('meta[name="application-name"]').data('baseurl'), {
            user_location: $this.data('id')
        })
        .done(function( data ) {
            window.location.href = $this.attr('href');
        });
    });

    $('#auto-locations .close').click( function(){
        createCookie('cancel_auto_locate',1,1800);
    });

    setInterval(function () {
        if ( ! navigator.onLine )
            $('.off-line').show();
        else
            $('.off-line').hide();
    }, 250);
});

$(function(){
    // Check for LocalStorage support.
    if (localStorage && $('#Widget_RecentlySearched')) {
        $('.Widget_RecentlySearched').hide();
        var recentSearches = [];

        if (localStorage["recentSearches"]) {
            $('.Widget_RecentlySearched').show();
            recentSearches = JSON.parse(localStorage['recentSearches']);

            var list = $('ul#Widget_RecentlySearched')

            $.each(recentSearches, function(i) {

                values = JSON.parse(this);
                var text = '';

                $.each(values, function(j) {
                    if (jQuery.type(this) === 'string' && this != '' && this != values.serialize)
                        text = text + this + ' - ';
                })

                text = text.slice(0,-3)

                var li = $('<li/>')
                    .appendTo(list);
                var a = $('<a/>')
                    .attr('href', $('#Widget_RecentlySearched').data('url') + '?' + values.serialize)
                    .text(text)
                    .appendTo(li);
            })
        }

        form = 'form[action*="' + $('#Widget_RecentlySearched').data('url') + '"]';

        // Add an event listener for form submissions
        $(form).on('submit', function() {

            var $inputs = $(this).find(':input:not(:button):not(:checkbox):not(:radio)');
            var values = {};

            $inputs.each(function() {
                if (this.name) {
                    values[this.name] = $(this).val();
                }
            });

            values['serialize'] = $(this).serialize();

            values = JSON.stringify(values);

            recentSearches.unshift(values);
            if (recentSearches.length > $('#Widget_RecentlySearched').data('max-items')) {
                recentSearches.pop();
            }

            localStorage['recentSearches'] = JSON.stringify(recentSearches);
        });

    }
});

function getlocale() {
    switch($('.curry').data('locale')){
        case 'en_US':
            siteCurrency = 'USD';
            break;
        case 'en_UK':
            siteCurrency = 'GBP';
            break;
        case 'ar':
            siteCurrency = 'AED';
            break;
        case 'bg_BG':
            siteCurrency = 'BGN';
            break;
        case 'bn_BD':
            siteCurrency = 'BDT';
            break;
        case 'ca_ES':
            siteCurrency = 'EUR';
            break;
        case 'cs_CZ':
            siteCurrency = 'CZK';
            break;
        case 'da_DK':
            siteCurrency = 'DKK';
            break;
        case 'de_DE':
            siteCurrency = 'EUR';
            break;
        case 'el_GR':
            siteCurrency = 'EUR';
            break;
        case 'en_UK':
            siteCurrency = 'GBP';
            break;
        case 'es_ES':
            siteCurrency = 'EUR';
            break;
        case 'fr_FR':
            siteCurrency = 'EUR';
            break;
        case 'hi_IN':
            siteCurrency = 'INR';
            break;
        case 'hr_HR':
            siteCurrency = 'HRK';
            break;
        case 'hu_HU':
            siteCurrency = 'HUF';
            break;
        case 'in_ID':
            siteCurrency = 'IDR';
            break;
        case 'it_IT':
            siteCurrency = 'EUR';
            break;
        case 'ja_JP':
            siteCurrency = 'JPY';
            break;
        case 'ml_IN':
            siteCurrency = 'INR';
            break;
        case 'nl_NL':
            siteCurrency = 'EUR';
            break;
        case 'no_NO':
            siteCurrency = 'NOK';
            break;
        case 'pl_PL':
            siteCurrency = 'PLN';
            break;
        case 'pt_PT':
            siteCurrency = 'EUR';
            break;
        case 'ro_RO':
            siteCurrency = 'RON';
            break;
        case 'ru_RU':
            siteCurrency = 'RUB';
            break;
        case 'sk_SK':
            siteCurrency = 'EUR';
            break;
        case 'sn_ZW':
            siteCurrency = 'USD';// ZWD not available
            break;
        case 'sq_AL':
            siteCurrency = 'ALL';
            break;
        case 'sr_RS':
            siteCurrency = 'RSD';
            break;
        case 'sv_SE':
            siteCurrency = 'SEK';
            break;
        case 'ta_IN':
            siteCurrency = 'INR';
            break;
        case 'tl_PH':
            siteCurrency = 'PHP';
            break;
        case 'tr':
            siteCurrency = 'TRY';
            break;
        case 'ur_PK':
            siteCurrency = 'PKR';
            break;
        case 'vi_VN':
            siteCurrency = 'VND';
            break;
        case 'zh_CN':
            siteCurrency = 'CNY';
            break;
        default:
            siteCurrency = 'USD';
            break;
    }
    return siteCurrency;
}

function getSiteCurrency() {
    return getlocale();
}

function getSavedCurrency() {
    siteCurrency = getlocale();
    savedCurrency = getCookie('site_currency');

    if (savedCurrency == undefined) {
        return siteCurrency;
    }

    return savedCurrency;
}

// Currency converter
$(function(){
    var savedRate, savedCurrency, siteCurrency;
    siteCurrency = getSiteCurrency();
    savedCurrency = getSavedCurrency();
    if (getCookie('site_currency') == undefined) {
        savedRate = 1;
        savedCurrency = siteCurrency;
    }
    else {
        savedRate = getCookie('site_rate');
        savedCurrency = getCookie('site_currency');
        rate = parseFloat(savedRate);
        var prices = $('.price-curry'), money;
        prices.each(function(){
            money = $(this).text();
            money = Number(money.replace(/[^0-9\.]+/g, ''));
            converted = rate * money;
            var symbols = ({
              'USD': '&#36;',
              'AUD': '&#36;',
              'CAD': '&#36;',
              'MXN': '&#36;',
              'BRL': '&#36;',
              'GBP': '&pound;',
              'EUR': '&euro;',
              'JPY': '&yen;',
              'INR': '&#8377;',
              'BDT': '&#2547;',
              'PHP': '&#8369;',
              'VND': '&#8363;',
              'CNY': '&#165;',
              'UAH': '&#8372;',
              'HKD': '&#36;',
              'SGD': '&#36;',
              'TWD': '&#36;',
              'THB': '&#3647;',
            });
            converted = Number(converted.toString().match(/^\d+(?:\.\d{2})?/));
            symbol = symbols[savedCurrency] || savedCurrency;
            $(this).text($(this).html(symbol + ' ' + converted).text());
        });
     }

    $(function(){
        if ($('.curry').length){
            $('.my-future-ddm').curry({
                change: true,
                target: '.price-curry',
                base: savedCurrency == undefined ? siteCurrency : savedCurrency,
                symbols: {}
            }).change(function(){
                var selected = $(this).find(':selected'), // get selected currency
                currency = selected.val(); // get currency name

                getRate(siteCurrency, currency);
                setCookie('site_currency', currency, { expires: 7, path: '' });
            });
        }
    });
});

function getRate(from, to) {
    var script = document.createElement('script');
    script.setAttribute('src', "https://query.yahooapis.com/v1/public/yql?q=select%20rate%2Cname%20from%20csv%20where%20url%3D'http%3A%2F%2Fdownload.finance.yahoo.com%2Fd%2Fquotes%3Fs%3D"+from+to+"%253DX%26f%3Dl1n'%20and%20columns%3D'rate%2Cname'&format=json&callback=parseExchangeRate");
    document.body.appendChild(script);
}

function parseExchangeRate(data) {
    var name = data.query.results.row.name;
    var rate = parseFloat(data.query.results.row.rate, 10);
    setCookie('site_rate', rate, { expires: 7, path: '' });
}

function setCookie(c_name,value,exdays)
{
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays==null) ? "" : ";path=/; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}
function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

$('.modal').on('hidden.bs.modal', function (e) {
    if($('.modal').hasClass('in')) {
    $('body').addClass('modal-open');
    }
});

$('.show-all-categories').click(function() {
    $.ajax({
        url: $('#modalAllCategories').data('apiurl'),
        data: {
            "id_category_parent": $(this).data('cat-id'),
            "sort": 'order',
        },
        success: function(result) {
            $('#modalAllCategories .modal-body .list-group').empty();
            $('#modalAllCategories').modal('show');
            $.each(result.categories, function (idx, category) {
                $("#modalAllCategories .modal-body .list-group").append('<li class="list-group-item"><a href="/' + category.seoname + '">' + category.name + '</a></li>');
            });
        }
    });
});

$(function(){
    $('#register_password').strength();
    $('#register_password_modal').strength();
});

/*!
 * strength.js
 * Original author: @aaronlumsden
 * Further changes, comments: @aaronlumsden
 * Licensed under the MIT license
 */
;(function ( $, window, document, undefined ) {
    var pluginName = "strength",
        defaults = {
            strengthClass: 'strength',
            strengthMeterClass: 'strength_meter',
            strengthButtonClass: 'button_strength',
            strengthButtonText: 'Show Password',
            strengthButtonTextToggle: 'Hide Password'
        };

    function Plugin( element, options ) {
        this.element = element;
        this.$elem = $(this.element);
        this.options = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    Plugin.prototype = {

        init: function() {

            var characters = 0;
            var capitalletters = 0;
            var loweletters = 0;
            var number = 0;
            var special = 0;

            var upperCase= new RegExp('[A-Z]');
            var lowerCase= new RegExp('[a-z]');
            var numbers = new RegExp('[0-9]');
            var specialchars = new RegExp('([!,%,&,@,#,$,^,*,?,_,~])');

            function GetPercentage(a, b) {
                return ((b / a) * 100);
            }

            function check_strength(thisval,thisid) {
                if (thisval.length > 8) { characters = 1; } else { characters = -1; };
                if (thisval.match(upperCase)) { capitalletters = 1} else { capitalletters = 0; };
                if (thisval.match(lowerCase)) { loweletters = 1}  else { loweletters = 0; };
                if (thisval.match(numbers)) { number = 1}  else { number = 0; };

                var total = characters + capitalletters + loweletters + number + special;
                var totalpercent = GetPercentage(7, total).toFixed(0);

                if (!thisval.length) {total = -1;}

                get_total(total,thisid);
            }

            function get_total(total,thisid){
                var thismeter = $('div[data-meter="'+thisid+'"]');
                    if (total <= 1) {
                   thismeter.removeClass();
                   thismeter.addClass('veryweak').html(getCFSearchLocalization('very_weak'));
                } else if (total == 2){
                    thismeter.removeClass();
                   thismeter.addClass('weak').html(getCFSearchLocalization('weak'));
                } else if(total == 3){
                    thismeter.removeClass();
                   thismeter.addClass('medium').html(getCFSearchLocalization('medium'));

                } else {
                     thismeter.removeClass();
                   thismeter.addClass('strong').html(getCFSearchLocalization('strong'));
                }
                if (total == -1) { thismeter.removeClass().html(getCFSearchLocalization('strength')); }
            }

            var isShown = false;
            var strengthButtonText = this.options.strengthButtonText;
            var strengthButtonTextToggle = this.options.strengthButtonTextToggle;

            thisid = this.$elem.attr('id');

            this.$elem.addClass(this.options.strengthClass).attr('data-password',thisid).after('<input style="display:none" class="'+this.options.strengthClass+'" data-password="'+thisid+'" type="text" name="" value=""><a data-password-button="'+thisid+'" href="" class="'+this.options.strengthButtonClass+'">'+this.options.strengthButtonText+'</a><div class="'+this.options.strengthMeterClass+'"><div data-meter="'+thisid+'">Strength</div></div>');

            this.$elem.bind('keyup keydown', function(event) {
                thisval = $('#'+$(this).attr('id')).val();
                $('input[type="text"][data-password="'+$(this).attr('id')+'"]').val(thisval);
                check_strength(thisval,$(this).attr('id'));

            });

             $('input[type="text"][data-password="'+thisid+'"]').bind('keyup keydown', function(event) {
                thisval = $('input[type="text"][data-password="'+thisid+'"]').val();
                console.log(thisval);
                $('input[type="password"][data-password="'+thisid+'"]').val(thisval);
                check_strength(thisval,thisid);

            });

            $(document.body).on('click', '.'+this.options.strengthButtonClass, function(e) {
                e.preventDefault();

                thisclass = 'hide_'+$(this).attr('class');
                if (isShown) {
                    $('input[type="text"][data-password="'+thisid+'"]').hide();
                    $('input[type="password"][data-password="'+thisid+'"]').show().focus();
                    $('a[data-password-button="'+thisid+'"]').removeClass(thisclass).html(strengthButtonText);
                    isShown = false;
                } else {
                    $('input[type="text"][data-password="'+thisid+'"]').show().focus();
                    $('input[type="password"][data-password="'+thisid+'"]').hide();
                    $('a[data-password-button="'+thisid+'"]').addClass(thisclass).html(strengthButtonTextToggle);
                    isShown = true;
                }
            });
        },
        yourOtherFunction: function(el, options) {
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );

function getResizeValue(value) {
    if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) {
        for (i = 1; i < 100; i++) {
            resizeValue = Math.round((value / i));
            if (resizeValue <= 400) {
                return resizeValue;
            }
        }
    } else {
        return value;
    }
}

$(function(){
    var user = $('#pusher-subscribe').data('user');
    var key = $('#pusher-subscribe').data('key');

    // subscribe user if is logged in
    if(user != undefined && user != ''){

        var pusher = new Pusher(key, {
          cluster: 'eu',
          encrypted: true
        });

        var channel = pusher.subscribe('user_'+user);
        channel.bind('my-event', function(data) {
            toastr.options = {
              "closeButton": true,
              "debug": false,
              "newestOnTop": true,
              "progressBar": false,
              "positionClass": "toast-top-right",
              "preventDuplicates": false,
              "onclick": null,
              "showDuration": "0",
              "hideDuration": "0",
              "timeOut": "0",
              "extendedTimeOut": "0",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
            }
            toastr.info('',data.message);
        });
    }
});

$(function(){
    if (typeof algolia != 'undefined')
    {
        var algoliaclient = algoliasearch(algolia.application_id, algolia.search_api_key);
        var algolia_branding = '';

        if (algolia.powered_by_enabled == 1)
            algolia_branding = '<div class="branding">Powered by <img src="https://www.algolia.com/assets/algolia128x40.png" /></div>';

        $('#aa-search-input').autocomplete({
            templates: {
                footer: algolia_branding
            }
        }, [
            {
              source: $.fn.autocomplete.sources.hits(algoliaclient.initIndex(algolia.autocomplete.indices.ads.name), { hitsPerPage: 3 }),
              displayKey: 'title',
              templates: {
                header: '<div class="aa-suggestions-ad">Ads</div>',
                suggestion: function(suggestion) {
                  return '<span>' +
                    suggestion._highlightResult.title.value + '</span><span>'
                      + suggestion._highlightResult.category.value + '</span>';
                }
              }
            },
            {
              source: $.fn.autocomplete.sources.hits(algoliaclient.initIndex(algolia.autocomplete.indices.categories.name), { hitsPerPage: 3 }),
              displayKey: 'name',
              templates: {
                header: '<div class="aa-suggestions-category">Categories</div>',
                suggestion: function(suggestion) {
                  return '<span>' +
                    suggestion._highlightResult.name.value + '</span><span></span>';
                }
              }
            },
            {
              source: $.fn.autocomplete.sources.hits(algoliaclient.initIndex(algolia.autocomplete.indices.locations.name), { hitsPerPage: 3 }),
              displayKey: 'name',
              templates: {
                header: '<div class="aa-suggestions-location">Locations</div>',
                suggestion: function(suggestion) {
                  return '<span>' +
                    suggestion._highlightResult.name.value + '</span><span></span>';
                }
              }
            },
            {
              source: $.fn.autocomplete.sources.hits(algoliaclient.initIndex(algolia.autocomplete.indices.users.name), { hitsPerPage: 3 }),
              displayKey: 'name',
              templates: {
                header: '<div class="aa-suggestions-user">Users</div>',
                suggestion: function(suggestion) {
                  return '<span>' +
                    suggestion._highlightResult.name.value + '</span><span></span>';
                }
              }
            }
        ]).on('autocomplete:selected', function(dataset, suggestion) {
          location.href = suggestion.permalink;
        });

        $('#aa-search-input-ad').autocomplete({
            templates: {
                footer: algolia_branding
            }
        }, [
            {
              source: $.fn.autocomplete.sources.hits(algoliaclient.initIndex(algolia.autocomplete.indices.ads.name), { hitsPerPage: 3 }),
              displayKey: 'title',
              templates: {
                header: '<div class="aa-suggestions-ad">Ads</div>',
                suggestion: function(suggestion) {
                  return '<span>' +
                    suggestion._highlightResult.title.value + '</span><span>'
                      + suggestion._highlightResult.category.value + '</span>';
                }
              }
            }
        ]).on('autocomplete:selected', function(dataset, suggestion) {
          location.href = suggestion.permalink;
        });

        $("input.aa-input-search").keydown(function(event){
            if(event.keyCode == 13) {
                $(this).parents('form:first').submit();
            }
        });
    }
});

function recaptchaCallback() {
    $('.hidden-recaptcha').valid();
}

$(function(){
    $('form').submit(function(event) {
        if ($(this).find('.g-recaptcha').length && $(this).attr('id') !== 'publish-new') {
            var response = grecaptcha.getResponse();
            if (!response) {
                event.preventDefault();
                $(this).attr('data-submit-please', 'true');
                grecaptcha.execute();
            } else {
                $(this).find('input[name="g-recaptcha-response"]').val(response);
            }
        }
    });
});

function recaptcha_submit(token) {
    var $form = $('form[data-submit-please="true"]');
    $form.find('input[name="g-recaptcha-response"]').val(token)
    if ($form.attr('id') === 'publish-new') {
        $('#processing-modal').modal('show');
    } else {
        $form.submit();
    }
}
