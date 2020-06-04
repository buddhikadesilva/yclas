/*!
 * Curry currency conversion jQuery Plugin v0.8.3
 * https://bitbucket.org/netyou/curry-currency-ddm
 *
 * Copyright 2017, NetYou (http://curry.netyou.co.il)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function($) {

  $.fn.curry = function(options) {

    // Setup a global cache for other curry plugins
    if (!window.jQCurryPluginCache)
      window.jQCurryPluginCache = [{}, false];

    var output = '',
      rates = {},
      t = this,
      requestedCurrency = window.jQCurryPluginCache[1],
      $document = $(document),
      dropDownMenu, value,
      item, keyName,
      i, l, rate;

    // Create some defaults, extending them with any options that were provided
    var settings = $.extend({
      target: 'price-curry',
      change: true,
      base: getSiteCurrency(),
      symbols: {}
    }, options);

    this.each(function() {

      var $this = $(this),
        id = $this.attr('id'),
        classes = $this.attr('class'),
        attrs = '',
        tempHolder;

      // Add class or id if replaced element had either of them
      attrs += id ? ' id="' + id + '"' : '';

      if (classes) {

        attrs += ' class="curry-ddm form-control';

        if (classes)
          attrs += ' ' + classes + '"';
        else
          attrs += '"';

      } else {

        attrs += '';

      }

      // keep any classes attached to the original element
      output = '<select' + attrs + '></select>';

      // Replace element with generated select
      tempHolder = $(output).insertAfter($this);
      $this.detach();

      // Add new drop down to jquery list (jquery object)
      dropDownMenu = !dropDownMenu ? tempHolder : dropDownMenu.add(tempHolder);

    });

    // Create the html for the drop down menu
    var generateDDM = function(rates) {

      output = '';

      // Change all target elements to drop downs
      dropDownMenu.each(function() {

        for (i in rates) {

          rate = rates[i];

          output += '<option value="' + i + '" data-rate="' + rate + '">' + i + '</option>';

        }

        $(output).appendTo(this);

        $('.curry-ddm').select2({"language": "es"}).select2('destroy').select2({"language": "es"});
      });

    };

    if (!settings.customCurrency) {

      // Only get currency hash once
      if (!requestedCurrency) {
        var query = '';
        var selected_currencies = $('.curry').data('currencies');
        selected_currencies = selected_currencies.split(',');

        var major_currencies = 'USD,EUR,GBP,JPY,CAD,CHF,AUD,ZAR,';
        var european_currencies = 'ALL,BGN,BYR,CZK,DKK,EUR,GBP,HRK,HUF,ISK,NOK,RON,RUB,SEK,UAH,';
        var skandi_currencies = 'DKK,SEK,NOK,';
        var asian_currencies = 'JPY,HKD,SGD,TWD,KRW,PHP,IDR,INR,CNY,MYR,THB,';
        var americas_currencies = 'USD,CAD,MXN,BRL,ARS,CRC,COP,CLP,';
        
        // Request currencies from yahoo finance
        if(selected_currencies == '') {
          query = 'AUD,BGN,BRL,CAD,CHF,CNY,CZK,DKK,GBP,HKD,HRK,HUF,IDR,ILS,INR,JPY,KRW,MXN,MYR,NOK,NZD,PHP,PLN,RON,RUB,SEK,SGD,THB,TRY,ZAR,EUR';
        } else {
          query = '';
          for (i = 0; i < selected_currencies.length; i++) { 
            selected_currencies[i] = selected_currencies[i].trim();
            if (selected_currencies[i] == 'major')
              query += major_currencies;
            else if (selected_currencies[i] == 'european')
              query += european_currencies;
            else if (selected_currencies[i] == 'skandi')
              query += skandi_currencies;
            else if (selected_currencies[i] == 'asian')
              query += asian_currencies;
            else if (selected_currencies[i] == 'american')
              query += americas_currencies;
            else
              query += selected_currencies[i]+',';
          }
          query = query.slice(0, -1);
        }

        // Request currencies from yahoo finance
        var jqxhr = $.ajax({
          url: ('https:' == document.location.protocol ? 'https:' : 'http:') + '//data.fixer.io/api/latest',
          dataType: 'jsonp',
          data: {
            symbols: query,
            base: settings.base,
            access_key: $('.curry').data('apikey')
          }
        });

        // Set global flag so we know we made a request
        window.jQCurryPluginCache[1] = true;

        jqxhr
          .done(function(data) {

            var initrates = data.rates;

            // Add the base currency to the rates
            rates[settings.base] = 1;

            for ( var currency in initrates ) {

              value = initrates[currency];

              rates[currency] = value;

            }

            generateDDM(rates);

            window.jQCurryPluginCache[0] = rates;
            $document.trigger('jQCurryPlugin.gotRates');

          })
          .fail(function(err) {

            console.log(err);

          });

      } else {

        $document.on('jQCurryPlugin.gotRates', function() {

          generateDDM(window.jQCurryPluginCache[0]);

        });

      }

    } else {

      generateDDM(settings.customCurrency);

    }

    // only change target when change is set by user
    if (settings.change) {

      // Add default currency symbols
      var symbols = $.extend({
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
        }, settings.symbols),
        $priceTag, symbol;

      $document.on('change', this.selector, function() {

        var $target = $(settings.target),
          $option = $(this).find(':selected'),
          rate = $option.data('rate'),
          has_comma = false,
          money, result, l = $target.length;

        for (var i = 0; i < l; i++) {

          $price = $($target[i]);
          money = $price.text();

          // remove currency symbol and letters
          money = money.replace(/[^0-9.,]/g, '');

          // Check if field has comma instead of decimal and replace with decimal
          if ( money.indexOf(',') !== -1 ){
            has_comma = true;
            money = money.replace( ',' , '.' );
          }

          // Remove anything but the numbers and decimals and convert string to Number
          // money = Number(money.replace(/[^0-9\.]+/g, ''));

          if ($price.data('base-figure')) {

            // If the client changed the currency there should be a base stored on the element
            result = rate * $price.data('base-figure');

          } else {

            // Store the base price on the element
            $price.data('base-figure', money);
            result = rate * money;
          }

          // Parse as two decimal number with .
          result = Number(result.toString().match(/^\d+(?:\.\d{2})?/));

          // Replace decimal with comma after calculations
          if ( has_comma ){
            result = result.toString().replace( '.' , ',' );
            has_comma = false;
          }

          symbol = symbols[$option.val()] || $option.val();

          $price.html('<span class="symbol">' + symbol + '</span>' + result);

        }

      });

    }

    // Returns jQuery object for chaining
    return dropDownMenu;

  };

})(jQuery);
