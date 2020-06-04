function getAddress() {

			var latlonspan = document.getElementById("latlonvalues");

			var geocoder = new google.maps.Geocoder();
			var address = document.getElementById('mapsearch').value;

			geocoder.geocode( { 'address': address}, function(results, status) {

			  if (status == google.maps.GeocoderStatus.OK) {
				var glatitude = results[0].geometry.location.lat();
				var glongitude = results[0].geometry.location.lng();

				latlonspan.innerHTML =  address+": "+glatitude+" "+glongitude+" [<a href='javascript:void(0);' title='Copy this values to the Region Code field' onclick='usethis("+glatitude+","+glongitude+")'>use this</a>]";

			  } else {
				latlonspan.innerHTML = 'Impossible to locate that Address';
			  }
			});
			}


function usethis(lat,lon) {
	var inp = document.addimap.cd;
	inp.value = lat+" "+lon;
}

function  latlonshow() {
	var e = document.getElementById('latlondiv');
    e.style.display = 'block';
}

function  latlonhide() {
	var e = document.getElementById('latlondiv');
    e.style.display = 'none';
}


function addPlaceToTable() {



	var code = document.addimap.cd.value.replace(/;/g,"");
	var title = document.addimap.c.value.replace(/;/g,".");
	var tooltip = document.addimap.t.value.replace(/;/g,".");
	var action = document.addimap.u.value.replace(/;/g,"&#59");// special char &#59 = ;
	var color = document.addimap.cl.value.replace(/;/g,"");

	var code = code.replace(/,/g," ");
	var title = title.replace(/,/g,"-");
	var tooltip = tooltip.replace(/,/g,"-");
	var action = action.replace(/,/g,"&#44");	// special char &#44 = ,
	var color = color.replace(/,/g,"");

	var newtext = code + ',' + title + ',' + tooltip + ',' + action + ',' + color + ';\n';
	document.addimap.places.value += newtext;
	document.addimap.cd.value = "";
	document.addimap.c.value = "";
	document.addimap.t.value = "";
	document.addimap.u.value = "";

	dataToTable();
}

function dataToTable(){


	var oldText = document.getElementById("places").value;
	var oldTextArr = oldText.split(";");
	oldTextArr.pop();
	var newText = "";
		for(i = 0; i < oldTextArr.length; i++){
		oldTextArr[i] = oldTextArr[i].replace(/\r/,"");
		oldTextArr[i] = oldTextArr[i].replace(/^\'/,"");
		oldTextArr[i] = oldTextArr[i].replace(/^\"/,"");
		oldTextArr[i] = oldTextArr[i].replace(/"$/,"");
		oldTextArr[i] = oldTextArr[i].replace(/'$/,"");

		var entry = oldTextArr[i].split(",");
		var colori = entry[4];
		oldTextArr[i] = oldTextArr[i] + "<div class='colorsample' style='background-color:"+colori+"'></div>";

		oldTextArr[i] = "<tr><td>"+oldTextArr[i]+"</td><td><input type='button' value='Edit' onclick='editPlace("+i+");' /> <input type='button' value='Delete' onclick='deletePlace("+i+");' /></td></tr>";
		}

	var linesep = ",";


	for(i = 0; i < oldTextArr.length; i++){
			oldTextArr[i] = oldTextArr[i].replace(new RegExp(linesep, "gi"), "</td><td>" );
			newText = newText + oldTextArr[i]+"\n";
	}

	var header = "<tr><th>Region Code</th><th>Title</th><th>Tooltip Text</th><th>Action Value</th><th>Color</th><th>&nbsp;</th></tr>";
	newText = "<table class='data-content-table'>\n"+header+"\n"+newText+"</table>\n";

	var span = document.getElementById("htmlplacetable");
	span.innerHTML = newText; // clear existing
	drawVisualization();
}

function updatePlace(placeid){

		//Get old text
		var oldText = document.getElementById("places").value;
		//Split into lines
		var oldTextArr = oldText.split(";");
		oldTextArr.pop();
		var newText ="";
		for(i = 0; i < oldTextArr.length; i++){

			if(i==placeid) {
			var updatecode = document.getElementById("input-"+placeid+"-0").value.replace(/,/g," ");
			var updatetitle = document.getElementById("input-"+placeid+"-1").value.replace(/,/g,".");
			var updatetooltip = document.getElementById("input-"+placeid+"-2").value.replace(/,/g,".");
			var updateaction = document.getElementById("input-"+placeid+"-3").value.replace(/,/g,"&#44");
			var updatecolor = document.getElementById("input-"+placeid+"-4").value.replace(/,/g,"");

			var updatecode = updatecode.replace(/;/g," ");
			var updatetitle = updatetitle.replace(/;/g,".");
			var updatetooltip = updatetooltip.replace(/;/g,".");
			var updateaction = updateaction.replace(/;/g,"&#59");
			var updatecolor = updatecolor.replace(/;/g,"");

			newText = newText + "\n" + updatecode + "," + updatetitle + "," + updatetooltip + "," + updateaction + ",#" + updatecolor + ";";
			}
			else {
			newText = newText+oldTextArr[i]+";";
			}
		}
		document.getElementById("places").value = newText;
		dataToTable();
}

function deletePlace(placeid){

		var conf = confirm('Are you sure you want to delete this Region entry?');
		if (conf==true)
  		{
		var oldText = document.getElementById("places").value;
		var oldTextArr = oldText.split(";");
		oldTextArr.pop();
		oldTextArr.splice(placeid,1);

		var newText ="";
		for(i = 0; i < oldTextArr.length; i++){
			newText = newText+oldTextArr[i]+";";
		}
		document.getElementById("places").value = newText;
		dataToTable();
		}
}




function editPlace(placeid){

	var oldText = document.getElementById("places").value;

	var oldTextArr = oldText.split(";");
	oldTextArr.pop();
	var newText = "";
	for(i = 0; i < oldTextArr.length; i++){
		oldTextArr[i] = oldTextArr[i].replace(/\r/,"");
		oldTextArr[i] = oldTextArr[i].replace(/^\'/,"");
		oldTextArr[i] = oldTextArr[i].replace(/^\"/,"");
		oldTextArr[i] = oldTextArr[i].replace(/"$/,"");
		oldTextArr[i] = oldTextArr[i].replace(/'$/,"");
		if(placeid == i) {


			var editArr = oldTextArr[i].split(",");
			oldTextArr[i] = "<tr class='editing-map-entry'>";
				for(ix = 0; ix < editArr.length; ix++){
					if(ix != 4 && ix != 2) {
						var ixvalue = editArr[ix].replace(/'/g,"&#39;");
						oldTextArr[i] = oldTextArr[i] + "<td><input type='text' id='input-"+placeid+"-"+ix+"' value='"+ixvalue+"'></td>\n";
						}

						if(ix == 2) {
						var ixvalue = editArr[ix].replace(/'/g,"&#39;");
						oldTextArr[i] = oldTextArr[i] + "<td><textarea id='input-"+placeid+"-"+ix+"'>"+ixvalue+"</textarea></td>\n";
						}

						if(ix == 4) {
						var colorinput = document.createElement("INPUT");
						var inputname = 'input-'+placeid+'-'+ix;
						var inputvalue = '#'+editArr[ix];
						 colorinput.type = 'text';
						 colorinput.id = String(inputname);
						 colorinput.value = String(inputvalue);
						var col = new jscolor.color(colorinput);
						oldTextArr[i] = oldTextArr[i] + "<td><span id='colori'></span></td>\n";
						}
				}


			oldTextArr[i] = oldTextArr[i] + "</td><td><input type='button' value='Done' onclick='updatePlace("+placeid+");' /><input type='button' value='Cancel' onclick='dataToTable();' /></td></tr>";

		} else {

		var entry = oldTextArr[i].split(",");
		var colori = entry[4];
		oldTextArr[i] = oldTextArr[i] + "<div class='colorsample' style='background-color:"+colori+"'></div>";
		oldTextArr[i] = "<tr><td>"+oldTextArr[i]+"</td><td><input type='button' value='Edit' onclick='editPlace("+i+");' /> <input type='button' value='Delete' onclick='deletePlace("+i+");' /></td></tr>";
		}

	}

	var linesep = ",";

		for(i = 0; i < oldTextArr.length; i++){

				oldTextArr[i] = oldTextArr[i].replace(new RegExp(linesep, "gi"), "</td><td>" );
				newText = newText + oldTextArr[i]+"\n";

		}

	var header = "<tr><th>Region Code</th><th>Title</th><th>Tooltip Text</th><th>Action Value</th><th>Color</th><th>&nbsp;</th></tr>";
	newText = "<table class='data-content-table'>\n"+header+"\n"+newText+"</table>\n";

	var span = document.getElementById("htmlplacetable");
	span.innerHTML = newText; // clear existing

	document.getElementById("colori").appendChild(colorinput);

}

    google.charts.load('42', {'packages':['geochart']});
google.charts.setOnLoadCallback(drawVisualization);
     //google.load('visualization', '1', {'packages': ['geochart']});
     //google.setOnLoadCallback(drawVisualization);

     function drawVisualization() {





		var bgcolor = document.getElementsByName('bg_color')[0].value;
		var stroke = document.getElementsByName('border_stroke')[0].value;
		var bordercolor = document.getElementsByName('border_color')[0].value;
		var incolor = document.getElementsByName('ina_color')[0].value;
		var width = document.getElementsByName('width')[0].value;
		var height = document.getElementsByName('height')[0].value;
		var responsiveck = document.getElementById('responsive');
		var aspratio = document.getElementById('aspratio');
		var tooltipcolor = document.getElementsByName('tooltip_color')[0].value;
		var interact = document.getElementById('interactive');
		var tooltipt = document.getElementById('tooltipt');
		var tooltipthtml = document.getElementById('tooltipthtml');
		var areacombo = document.getElementsByName('region')[0].value;
		var areashow = areacombo.split(",");
		var region = areashow[0];
		var resolution = areashow[1];
		var markersize = document.getElementsByName('marker_size')[0].value;
		var displaym = document.getElementsByName('display_mode')[0].value;
		var placestxt =  document.getElementsByName('places')[0].value.replace(/(\r\n|\n|\r)/gm,"");
		var action = document.getElementsByName('map_action')[0].value;
		var caction = document.getElementsByName('custom_action')[0].value;
		var apikey = document.getElementsByName('geoapi')[0].value;

		var displaymode = "regions";

		if(displaym == "markers" || displaym == "markers02" ) {
			displaymode = "markers";
		}

		if(displaym == "text" || displaym == "text02" ) {
			displaymode = "text";
		}


		var places = placestxt.split(";");

		var responsive = false;
		if(responsiveck.checked==true) {
			var responsive = true;
		}

		var ratio = false;
		if(aspratio.checked==true) {
			var ratio = true;
		}

		var interactive = 'true';
		if(interact.checked!=true) {
			var interactive = 'false';
		}

		var toolt = 'focus';
		if(tooltipt.checked!=true) {
			var toolt = 'none';
		}

		var htmltooltip = 'true';
		if(tooltipthtml.checked!=true) {
			var htmltooltip = 'false';
		}

		var json = '{"bgcolor":"'+bgcolor+'","stroke":"'+stroke+'","bordercolor":"'+bordercolor+'","incolor":"'+incolor+'","width":"'+width+'","height":"'+height+'","responsive":"'+responsive+'","aspratio":"'+ratio+'","tooltipcolor":"'+tooltipcolor+'","tooltip":"'+toolt+'","tooltiphtml":"'+htmltooltip+'","interact":"'+interactive+'","areacombo":"'+areacombo+'","markersize":"'+markersize+'","displaymode":"'+displaym+'","mapaction":"'+action+'","customaction":"'+escape(caction)+'","placestxt":"'+escape(placestxt)+'","geoapi":"'+apikey+'"}';

		document.addimap.current_settings.value = json;


		if (typeof(Storage) !== "undefined") {

   			if(json!='{"bgcolor":"#FFFFFF","stroke":"0","bordercolor":"#FFFFFF","incolor":"#f5f5f5","width":"600","height":"400","responsive":"false","aspratio":"true","tooltipcolor":"#444444","tooltip":"focus","tooltiphtml":"false","interact":"true","areacombo":"world,countries","markersize":"10","displaymode":"regions","mapaction":"none","customaction":"","placestxt":"","geoapi":""}') {

   				localStorage.removeItem("currensettings");
				localStorage.setItem("currensettings", json);

   			}
		}


		var jscodetxt = '';

		if(apikey!='') {
			jscodetxt = "<script src='https://maps.googleapis.com/maps/api/js?key="+apikey+"' type='text/javascript'></script>";
		}


	   jscodetxt = jscodetxt + "<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script> \n<script type='text/javascript' src='https://www.google.com/jsapi'></script>\n";

	  jscodetxt = jscodetxt + 	"<script type='text/javascript'>";
	    jscodetxt = jscodetxt + 	"google.charts.load('42', {'packages':['geochart']});\n";
      jscodetxt = jscodetxt + 	"google.charts.setOnLoadCallback(drawVisualization);\n";


	  jscodetxt = jscodetxt + 	"\n  function drawVisualization() {";
	  jscodetxt = jscodetxt + 	"var data = new google.visualization.DataTable();\n";

	  //if markers02 add 2 extra column
	  if(displaym == "markers02") {
		  jscodetxt = jscodetxt + 	"\n data.addColumn('number', 'Lat'); ";
		  jscodetxt = jscodetxt + 	"\n data.addColumn('number', 'Lon'); ";
	  }

		 jscodetxt = jscodetxt + 	"\n data.addColumn('string', 'Country');";
		jscodetxt = jscodetxt + 	"\n data.addColumn('number', 'Value'); ";

		if(htmltooltip=='false')
		jscodetxt = jscodetxt + 	"\n data.addColumn({type:'string', role:'tooltip'});";
		else {
			jscodetxt = jscodetxt + 	"\n data.addColumn({type:'string', role:'tooltip', p:{html:true}});";
		}

       var data = new google.visualization.DataTable();

	     if(displaym == "markers02") {
	     data.addColumn('number', 'Lat');
	     data.addColumn('number', 'Long');
		 }

		data.addColumn('string', 'Country'); // Implicit domain label col.
		data.addColumn('number', 'Value'); // Implicit series 1 data col.


		if(htmltooltip=='false')
		data.addColumn({type:'string', role:'tooltip'}); //
		else {
			data.addColumn({type:'string', role:'tooltip', p:{html:true}}); //
		}


			var ivalue = new Array();
			var colorsmap = [];
			var colorsmapecho = "";

			jscodetxt = jscodetxt + "var ivalue = new Array();\n";

			//places.length-1 to eliminate empty value at the end
			for (var i = 0; i < places.length-1; i++) {
			var entry = places[i].split(",");

			//If data != markers02
			if(displaym != "markers02") {

			data.addRows([[{v:entry[0],f:entry[1]},i,entry[2]]]);
			jscodetxt = jscodetxt + "\n data.addRows([[{v:'"+addslashes(entry[0])+"',f:'"+addslashes(entry[1])+"'},"+i+",'"+addslashes(entry[2])+"']]);";

			var index = entry[0];
			}
			else {
			var trim = entry[0].replace(/^\s+|\s+$/g,"");
			var latlon = trim.split(/ /);
			var lat = parseFloat(latlon[0]);
			var lon = parseFloat(latlon[1]);

			data.addRows([[lat,lon,entry[1],i,entry[2]]]);

			jscodetxt = jscodetxt + "\n data.addRows([["+addslashes(lat)+","+addslashes(lon)+",'"+addslashes(entry[1])+"',"+i+",'"+addslashes(entry[2])+"']]);";

			var index = lat;

			}
			var colori = entry[4];

			ivalue[index] = entry[3].replace(/&#59/g,";");
			ivalue[index] = ivalue[index].replace(/&#44/g,",");


			jscodetxt = jscodetxt + "\n ivalue['"+index+"'] = '"+ivalue[index]+"';\n";


			colorsmapecho = colorsmapecho + "'"+colori+"',";
			colorsmap.push(colori);
			ivalue.push(ivalue);
			}
			//colorsmap.pop();
			ivalue.pop();

		defmaxvalue = 0;
		if ((places.length-2) > 0) {
		defmaxvalue = places.length-2;
		}

		boolhtmltooltip = false;
		if(htmltooltip=='true') {
			boolhtmltooltip = true;
		}

		if(responsive==true) {
			width = null;
			height = null;
		}

		var options = {
			backgroundColor: {fill:bgcolor,stroke:bordercolor ,strokeWidth:stroke },
			colorAxis:  {minValue: 0, maxValue: defmaxvalue,  colors: colorsmap},
			legend: 'none',
			backgroundColor: {fill:bgcolor,stroke:bordercolor ,strokeWidth:stroke },
			datalessRegionColor: incolor,
			displayMode: displaymode,
			enableRegionInteractivity: interactive,
			resolution: resolution,
			sizeAxis: {minValue: 1, maxValue:1,minSize:markersize,  maxSize: markersize},
			region:region,
			keepAspectRatio: ratio,
			width:width,
			height:height,
			tooltip: {textStyle: {color: tooltipcolor}, trigger:toolt, isHtml: boolhtmltooltip}
			};



			if(colorsmapecho.substr(-1) == ',') {
				colorsmapecho = colorsmapecho.substr(0, colorsmapecho.length - 1);
			}



			jscodetxt = jscodetxt + 	"\n var options = {";
			jscodetxt = jscodetxt + 	"\n backgroundColor: {fill:'"+bgcolor+"',stroke:'"+bordercolor+"' ,strokeWidth:"+stroke+" },";
			jscodetxt = jscodetxt + 	"\n colorAxis:  {minValue: 0, maxValue: "+defmaxvalue+",  colors: ["+colorsmapecho+"]},";
			jscodetxt = jscodetxt + 	"\n legend: 'none',	";
			jscodetxt = jscodetxt + 	"\n datalessRegionColor: '"+incolor+"',";
			jscodetxt = jscodetxt + 	"\n displayMode: '"+displaymode+"', ";
			jscodetxt = jscodetxt + 	"\n enableRegionInteractivity: '"+interactive+"', ";
			jscodetxt = jscodetxt + 	"\n resolution: '"+resolution+"',";
			jscodetxt = jscodetxt + 	"\n sizeAxis: {minValue: 1, maxValue:1,minSize:"+markersize+",  maxSize: "+markersize+"},";
			jscodetxt = jscodetxt + 	"\n region:'"+region+"',";
			jscodetxt = jscodetxt + 	"\n keepAspectRatio: "+ratio+",";
			jscodetxt = jscodetxt + 	"\n width:"+width+",";
			jscodetxt = jscodetxt + 	"\n height:"+height+",";
			jscodetxt = jscodetxt + 	"\n tooltip: {textStyle: {color: '"+tooltipcolor+"'}, trigger:'"+toolt+"', isHtml: "+htmltooltip+"}	";
			jscodetxt = jscodetxt + 	"\n };";


		var uniqueid = new Date().valueOf();

        var chart = new google.visualization.GeoChart(document.getElementById('visualization'));

		jscodetxt = jscodetxt + 	"\n  var chart = new google.visualization.GeoChart(document.getElementById('map_"+uniqueid+"')); ";


		if(interactive == "true") {

		google.visualization.events.addListener(chart, 'select', function() {
		var selection = chart.getSelection();


    	if (selection.length == 1) {
			var selectedRow = selection[0].row;
            var selectedRegion = data.getValue(selectedRow, 0);
			if(ivalue[selectedRegion] != "") { alert(ivalue[selectedRegion]); }
			}
			});

			var iaction = "";
			if(action == "i_map_action_open_url") {
				iaction = "document.location = ivalue[selectedRegion]; ";
			}
			if(action == "i_map_action_open_url_wix") {
				iaction = "window.parent.location = ivalue[selectedRegion]; ";
			}
			if(action == "i_map_action_alert") {
				iaction = "alert(ivalue[selectedRegion]);";
			}
			if(action == "i_map_action_open_url_new") {
				iaction = "window.open(ivalue[selectedRegion]); ";
			}
			if(action == "i_map_action_custom") {
				iaction = caction;
			}

					if(action!="none") {
					jscodetxt = jscodetxt + 	"\n  google.visualization.events.addListener(chart, 'select', function() {";
					jscodetxt = jscodetxt + 	"\n  var selection = chart.getSelection();";
			    	jscodetxt = jscodetxt + 	"\n  if (selection.length == 1) {";
					jscodetxt = jscodetxt + 	"\n  var selectedRow = selection[0].row;";
					jscodetxt = jscodetxt + 	"\n  var selectedRegion = data.getValue(selectedRow, 0);";
					jscodetxt = jscodetxt + 	"\n  if(ivalue[selectedRegion] != '') { "+ iaction +" }";
					jscodetxt = jscodetxt + 	"\n  }";
					jscodetxt = jscodetxt + 	"\n  });";
					}
		}

		chart.draw(data, options);
		jscodetxt = jscodetxt + "\n chart.draw(data, options);";

		jscodetxt = jscodetxt + "\n }";

		if(responsive == true) {

			jscodetxt = jscodetxt + "\n window.onresize = function(event) {";
			jscodetxt = jscodetxt + "\n     drawVisualization();";
			jscodetxt = jscodetxt + "\n };";


		}



		jscodetxt = jscodetxt + "\n </script>";
		jscodetxt = jscodetxt + "\n <div id='map_"+uniqueid+"'></div>";
		document.getElementsByName('jscode')[0].value = jscodetxt;



    }


	function loadSettings() {
		var JSONObject = document.getElementsByName('load_settings')[0].value;

		if(JSONObject==''){
			drawVisualization();
			dataToTable();
			isolink();
			return;
		}

		var settings = eval ("(" + JSONObject + ")");

		document.getElementsByName('bg_color')[0].value =  settings.bgcolor;
		document.getElementsByName('border_stroke')[0].value = settings.stroke;
		document.getElementsByName('border_color')[0].value = settings.bordercolor;
		document.getElementsByName('ina_color')[0].value = settings.incolor;
		document.getElementsByName('width')[0].value =  settings.width;
		document.getElementsByName('height')[0].value = settings.height;
		document.getElementsByName('tooltip_color')[0].value = settings.tooltipcolor;
		document.getElementsByName('region')[0].value = settings.areacombo;
		document.getElementsByName('marker_size')[0].value = settings.markersize;
		document.getElementsByName('display_mode')[0].value = settings.displaymode;
		document.getElementsByName('map_action')[0].value = settings.mapaction;
		document.getElementsByName('custom_action')[0].value = unescape(settings.customaction);
		document.getElementsByName('places')[0].value = unescape(settings.placestxt);
		document.getElementById('geoapi').value = settings.geoapi;


		if(settings.mapaction == "i_map_action_custom") {
			customoptionshow();
		}
		else {
			 customoptionhide();
		}

		if(settings.tooltip == "focus") {
			document.getElementById('tooltipt').checked = true;
       		}
		else {
			document.getElementById('tooltipt').checked = false;
			}

		if(settings.tooltiphtml == "true") {
			document.getElementById('tooltipthtml').checked = true;
       		}
		else {
			document.getElementById('tooltipthtml').checked = false;
			}

		if(settings.responsive == "true") {
			document.getElementById('responsive').checked = true;
       		}
		else {
			document.getElementById('responsive').checked = false;
			}

		if(settings.aspratio == "true") {
			document.getElementById('aspratio').checked = true;
       		}
		else {
			document.getElementById('aspratio').checked = false;
			}



		if(settings.interact == "true") {
			document.getElementById('interactive').checked = true;
       		}
		else {
			document.getElementById('interactive').checked = false;
			}


		drawVisualization();
		dataToTable();
		isolink();

		}



	function showsimple() {
		document.getElementById('simple-table').style.display='block';
		document.getElementById('advanced-table').style.display='none';
		document.getElementById("shsimple").setAttribute("class", "activeb");
		document.getElementById("shadvanced").setAttribute("class", "inactiveb");
	}

	function showadvanced() {
		document.getElementById('simple-table').style.display='none';
		document.getElementById('advanced-table').style.display='block';
		document.getElementById("shsimple").setAttribute("class", "inactiveb");
		document.getElementById("shadvanced").setAttribute("class", "activeb");
	}


	function showcurrent() {
		document.getElementById('current-settings').style.display='block';
		document.getElementById('load-settings').style.display='none';
		document.getElementById("shcurr").setAttribute("class", "activeb");
		document.getElementById("shload").setAttribute("class", "inactiveb");
	}

	function showload() {
		document.getElementById('current-settings').style.display='none';
		document.getElementById('load-settings').style.display='block';
		document.getElementById("shcurr").setAttribute("class", "inactiveb");
		document.getElementById("shload").setAttribute("class", "activeb");
	}



	function  customoptionshow() {
		var e = document.getElementById('custom-action');
       e.style.display = 'block';
	}

	function  customoptionhide() {
		var e = document.getElementById('custom-action');
       e.style.display = 'none';
	}

	function isolink() {

		var display = document.getElementsByName('display_mode')[0].value;
	  	var areacombo = document.getElementsByName('region')[0].value;
		var mapaction = document.getElementsByName('map_action')[0].value;
		var areashow = areacombo.split(",");
		var region = areashow[0];
		var resolution = areashow[1];
		var span = document.getElementById("iso-code-msg");

	  if(resolution == 'countries' && display == "regions")	{
	  	latlonhide();

		span.innerHTML = '<b>' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b>- To create your interactive regions, when using the "Regions" display mode, use a country name as a string, or an uppercase <a href="http://en.wikipedia.org/wiki/ISO_3166-1">ISO-3166-1</a> code or its English text equivalent (for example, <i>GB</i> or <i>United Kingdom</i>). Check Google\'s <a href="https://developers.google.com/chart/interactive/docs/gallery/geochart#Continent_Hierarchy" target="_blank">Continents and Countries</a> list for aditional resources.';
	  }

	   if(resolution == 'provinces' && display == "regions")	{
	   	latlonhide();
		var ct = areashow[0].length;
		var linkiso;
		if(ct>2) {
		linkiso = "<a href='http://en.wikipedia.org/wiki/ISO_3166-2:US'>ISO-3166-2:US</a>";
		} else {
		linkiso = "<a href='http://en.wikipedia.org/wiki/ISO_3166-2:"+areashow[0]+"'>ISO-3166-2:"+areashow[0]+"</a>";
		}
		span.innerHTML = '<b>' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b> - To create your interactive regions, use the '+linkiso+' codes.';
	  	}

	  if(resolution == 'metros' && display == "regions") {

		 span.innerHTML = '<b>' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b> - To create your interactive regions, use these three-digit <a href="http://developers.google.com/adwords/api/docs/appendix/metrocodes" target="_blank">metropolitan area codes</a> as the region codes.'; //
			latlonhide();
		 }

		 if(resolution == 'continents' && display == "regions") {

		 span.innerHTML = '<b>' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b> - Region Codes for Continents (When using Regions Display Mode): <br /> Africa - 002 | Europe - 150 | Americas - 019 | Asia - 142 | Oceania - 009'; //
			latlonhide();
		 }

		  if(resolution == 'subcontinents' && display == "regions") {

		 span.innerHTML = '<b>' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b> - Region codes for Subcontinents (When using Regions Display Mode): <br />Africa - Northern Africa: 015, Western Africa: 011, Middle Africa: 017, Eastern Africa: 014, Southern Africa: 018;<br />Europe - Northern Europe: 154, Western Europe: 155, Eastern Europe: 151, Southern Europe: 039;<br />Americas - Northern America: 021, Caribbean: 029, Central America: 013, South America: 005;<br />Asia - Central Asia: 143, Eastern Asia: 030, Southern Asia: 034, South-Eastern Asia: 035, Western Asia: 145;<br />Oceania - Australia and New Zealand: 053, Melanesia: 054, Micronesia: 057, Polynesia: 061;'; //
			latlonhide();
		 }



		  if(display == 'markers') {

		 span.innerHTML = '<b>Markers (Text Location)</b> - When using the Markers display mode, a colored bubble will be added to the specified region. When this mode is selected you can also use a a specific string address (for example, "1600 Pennsylvania Ave") or Berlin Germany as a Region Code. DO NOT use commas (,) or quotes("").'; //
		 latlonhide();
		   }

		   if(display == 'markers02') {

		 span.innerHTML =  '<b>Markers (Coordinates)</b> - When using the Markers display mode, a colored bubble will be added to the specified region. When the Coordinates mode is chosen, you should insert the coordinates values in the Region Code, in this format: latitude longitude. <strong>Do not use commas, use a space to separate de values</strong>. Example:34.3071438 -53.7890625'; //
		 latlonshow();
		   }


		    if(mapaction == 'i_map_action_open_url') {

		 span.innerHTML =  span.innerHTML + '<br /><br /><b>Action - Open URL</b> - The URL you specify in the "Action Value" field will open in the same window, after the user clicked on that region.'; //
		  customoptionhide();

		   }

		   if(mapaction == 'i_map_action_open_url_wix') {

			span.innerHTML =  span.innerHTML + '<br /><br /><b>Action - Open URL (WIX)</b> - The URL you specify in the "Action Value" field will open in the same window, after the user clicked on that region.'; //
			 customoptionhide();

			  }


		   if(mapaction == 'i_map_action_open_url_new') {

		 span.innerHTML =  span.innerHTML + '<br /><br /><b>Action - Open URL (new window)</b> - The URL you specify in the "Action Value" field will open in a new window, after the user clicked on that region.'; //
		  customoptionhide();

		   }

		    if(mapaction == 'i_map_action_alert') {

		 span.innerHTML =  span.innerHTML + '<br /><br /><b>Action - Alert</b> - An alert message will display with the text you specify in the "Action Value" field.'; //
		  customoptionhide();

		   }

		  if(mapaction == 'i_map_action_custom') {

		 span.innerHTML =  span.innerHTML + '<br /><br /><b>Action - Custom</b> - Create your custom action.'; //
		 customoptionshow();
		   }

		   if(mapaction == 'none') {

		  customoptionhide();
		   }


	}

	function isolinkcheck() {
		drawVisualization();
		isolink();

	}

	function initmap() {
		datastored();
		isolink();
		dataToTable();


	}

	function addslashes( str ) {
    return (str+'').replace(/([\\"'])/g, "\\$1").replace(/\0/g, "\\0");
	}

	function datastored() {



		if (typeof(Storage) !== "undefined") {
   			console.log('Browser supports LocalStorage');
   			var currensettings = localStorage.getItem("currensettings");

   			console.log(currensettings);
   				document.getElementsByName('load_settings')[0].value = currensettings;
   				loadSettings();



		} else {
		    console.log('Browser cannot store current settings');
		}


	}

	window.onload=initmap;
	window.onload = loadSettings;