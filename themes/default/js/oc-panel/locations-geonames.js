function JSONscriptRequest(fullUrl) {
    // REST request path
    this.fullUrl = fullUrl; 
    // Keep IE from caching requests
    this.noCacheIE = '&noCacheIE=' + (new Date()).getTime();
    // Get the DOM location to put the script tag
    this.headLoc = document.getElementsByTagName("head").item(0);
    // Generate a unique script tag id
    this.scriptId = 'YJscriptId' + JSONscriptRequest.scriptCounter++;
}

// Static script ID counter
JSONscriptRequest.scriptCounter = 1;

// buildScriptTag method
//
JSONscriptRequest.prototype.buildScriptTag = function () {

    // Create the script tag
    this.scriptObj = document.createElement("script");

    // Add script object attributes
    this.scriptObj.setAttribute("type", "text/javascript");
    this.scriptObj.setAttribute("src", this.fullUrl + this.noCacheIE);
    this.scriptObj.setAttribute("id", this.scriptId);
}

// removeScriptTag method
// 
JSONscriptRequest.prototype.removeScriptTag = function () {
    // Destroy the script tag
    this.headLoc.removeChild(this.scriptObj);  
}

// addScriptTag method
//
JSONscriptRequest.prototype.addScriptTag = function () {
    // Create the script tag
    this.headLoc.appendChild(this.scriptObj);
}

var whos = 'continent';

function getPlaces(gid,src)
{   
    whos = src;
    lang = $('#auto_locations_lang').val();

    var request = "https://www.geonames.org/childrenJSON?geonameId="+gid+"&callback=listPlaces&style=long&lang="+lang;
    aObj = new JSONscriptRequest(request);
    aObj.buildScriptTag();
    aObj.addScriptTag();    
}

function listPlaces(jData)
{
    var import_items = [];
    counts = jData.geonames.length < jData.totalResultsCount ? jData.geonames.length : jData.totalResultsCount;
    who = document.getElementById(whos);
    who.options.length = 0;
    
    $('#group-'+whos).show();

    if (counts)
    {
        who.options[who.options.length] = new Option('Select','');
    }
    else
    {
        who.options[who.options.length] = new Option('No Data Available','NULL');
    }

    for(var i=0;i<counts;i++) {
        who.options[who.options.length] = new Option(jData.geonames[i].name,jData.geonames[i].geonameId);
        import_items.push({name:jData.geonames[i].name,lat:jData.geonames[i].lat,long:jData.geonames[i].lng,id_geoname:jData.geonames[i].geonameId,fcodename_geoname:whos});
    }

    $("#auto_locations_import").html($('label[for='+ whos +']').data('action'));
    $('#auto_locations').val(JSON.stringify(import_items));

    jData = null;
}

$(function  ()
{
    $('#group-continent').hide();
    $('#group-country').hide();
    $('#group-region').hide();
    $('#group-province').hide();
    $('#group-city').hide();

    if ($('#current_location_id_geoname').val() && $('#current_location_fcodename_geoname').val()) {
        switch ($('#current_location_fcodename_geoname').val()) {
            case 'continent':
                whos = "country";
                break;
            case 'country':
                whos = "province";
                break;
            case 'province':
                whos = "region";
                break;
            case 'region':
                whos = "city";
                break;
            case 'city':
                whos = "continent";
                break;
        }
        getPlaces($('#current_location_id_geoname').val(), whos);
    }
    else {
        getPlaces(6295630, 'continent');
    }
    
    $("#auto_locations_import_reset").click(function() {
        $('#group-country').hide();
        $('#group-region').hide();
        $('#group-province').hide();
        $('#group-city').hide();
        getPlaces(6295630,'continent');
    });
});
