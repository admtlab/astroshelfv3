/*
Di Bao
Summer 2012
The JS file for Results side tab
*/

var FUNCS4 = FUNCS4 || {};
var TrendImages = null;
jQuery.fn.result = function(){

	FUNCS4.details = function(the_survey, the_query, the_config){
		if(the_survey == "SDSS") {
			the_config.name = '';
		}

		var formatted_time = gFUNCS.create_time();
		$("#object_content p:last").hide();
		$("#object_content").append('<div></div>');
		$("#object_content div:last").append('<label><b>' + the_config.name +' object details: <br/>(' + formatted_time + ')</b></label>');
		$("#object_content div:last").append('<p>Processing...</p><br/><br/><a href="#" class="toggle">Hide/Show</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="remove">Remove from the list</a>');
		
		if(the_survey == "SDSS"){
			$("#object_content div:last").append('&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="link" name="SDSS" rel="' + the_config._objid + '">SDSS Explorer</a><br/><br/>');
			var _url = "./lib/db/remote/searchSDSS.php";
		}else if(the_survey == "FIRST"){
			$("#object_content div:last").append('&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="link" name="FIRST" rel="' + the_config.ra_dec + '">FIRST Cutout</a><hr/>');
			var _url = "./lib/db/local/queryFIRST.php";
		}else if(the_survey == "LSST"){
			$("#object_content div:last").append('&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="link" name="LSST" rel="' + the_config.ra_dec + '">LSST Details</a><hr/>');		
			var _url = "./lib/db/remote/queryLSST.php";
		}else if(the_survey == "anno"){
			var _url = secureRESTbase + "annotation/" + the_config._annoid;
		}else{
			;
		}
		
		if(the_survey == "SDSS"){
			$("#object_content div:last").append('<a href="#" class="add_anno" name="SDSS" rel="' + the_config._objid + '">ADD annotation</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a href="#" class="show_anno" name="SDSS" rel="' + the_config._objid + '">SHOW annotations</a>&nbsp;&nbsp;/&nbsp;&nbsp;');
			$("#object_content a:last").prev().data("the_ra", the_config._ra);
			$("#object_content a:last").prev().data("the_dec", the_config._dec);
			$("#object_content a:last").data("the_ra", the_config._ra);
			$("#object_content a:last").data("the_dec", the_config._dec);
			$("#object_content div:last").append('<a href="#" class="add_bookmark" data-type="obj" data-id=' + the_config._objid + ' data-name="' + the_config.name + '" data-ra=' + the_config._ra + ' data-dec=' + the_config._dec + '>ADD bookmark</a>');			
		}
		
		if(the_survey != "anno") {
			var _data = {query: the_query, more: 1, name: the_config._name};
			$("#object_content div:last").append("<hr/>");
		} else {
			var _data = {get_anno:1, anno_id: the_config._annoid};
		}
		
		var type = "POST";
		if(the_survey == "anno") {
			type = "GET";
		}
		
		$.ajax({
			type: type,
			url: _url,
			data: _data,
			dataType: "json",
			beforeSend: setAuthHeaders,
		}).done(function(json_msg) {
			/*--------for the Object details tab----------*/
			if(json_msg["error"]) {
				$("#object_content div:last").find('p').html('No rows returned...');
			} else {
				if(the_survey == "anno") {
					var temp_msg = json_msg;
					json_msg = {"bPaginate": false, "bLengthChange": false, "iDisplayLength": 25, "bFilter": false, "aaSorting" : [], 
						"aoColumns":[{"sTitle": "Attributes", "bSortable": false}, {"sTitle": "Values", "bSortable": false}], "aaData":[]}
					$.each(temp_msg, function(key, value) {
						FUNCS4.appendData(key, value, json_msg.aaData);
					});
					
					// Get ra/dec values for object, if object
					var ra = json_msg.aaData[15][1];
					var dec = json_msg.aaData[16][1];
					if(!ra) { // Annotation is of an area or point
						if(json_msg.aaData[20][1]) { // Annotation is of an area
							ra = (json_msg.aaData[20][1] + json_msg.aaData[18][1])/2;
							dec = (json_msg.aaData[21][1] + json_msg.aaData[19][1])/2;
						} else { // Annotation is of a point
							ra = json_msg.aaData[18][1];
							dec = json_msg.aaData[19][1];
						}
					}
					$("#object_content div:last").append('<br/><a href="#" class="add_anno" name="anno" rel="' + the_config._annoid + '">ADD annotation</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a href="#" class="show_anno" name="anno" rel="' + the_config._annoid + '">SHOW annotations</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a href="#" class="add_bookmark" data-type="anno" data-id=' + the_config._annoid + ' data-name="' + json_msg.aaData[2][1] + '" data-ra=' + ra + ' data-dec=' + dec + '>ADD bookmark</a>');			
					$("#object_content div:last").append("<hr/>");
				}
				$("#object_content div:last").find('p').html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="'+the_survey+'_od_table"></table>');
				$("#object_content div:last #"+the_survey+"_od_table").dataTable(json_msg);
				$("#object_content #"+the_survey+"_od_table_info").remove();
				
				if(the_survey == "SDSS"){
					var $get_name = $("#object_content div:last #SDSS_od_table tr:eq(1)");
					var SDSS_name = $get_name.find('td:last').text();
					$("#object_content div:last-child").find('b').html(SDSS_name+ " object details: <br/>(" + formatted_time + ")");
					$("#object_content div:last-child").find('a:last').data('name', SDSS_name);
				}
			}
			/*-----end------*/
			if($('#results').hasClass('open')){
				$('#results_handle').trigger("click");
			}
			if(!$('#object_tab').hasClass('open')){
				$('#object_handle').trigger("click");
			}
		});
	};
	
	FUNCS4.appendData = function(key, value, data_array) {
		if($.type(value) == "object") {
			$.each(value, function(nestedKey, nestedValue) {
				FUNCS4.appendData(nestedKey, nestedValue, data_array);
			});
		} else {
			data_array.push([key, value]);
		}
	};
	
	FUNCS4.showSDSSDetails = function(objid, ra, dec) {
		var my_query = "SELECT dbo.fIAUFromEq(p.ra, p.dec) as name, p.objid, p.ra, p.dec, s.z as redshift, s.zerr as rederr," +
		"s.zconf, dbo.fPhotoTypeN(p.type) as type, dbo.fSpecClassN(s.specclass) as specclass," +
		"p.u, p.err_u, p.g, p.err_g, p.r, p.err_r, p.i, p.err_i, p.z, p.err_z" +
		" FROM PhotoObj as p LEFT OUTER JOIN SpecObj as s ON p.objid = s.bestobjid WHERE (" + "p.objid = " + objid + ")";
		
		var ra_dec_obj = {_objid: objid, _ra: ra, _dec: dec};
		FUNCS4.details("SDSS", my_query, ra_dec_obj);
	}
	
	FUNCS4.showAnnoDetails = function(annoid) {
		var config = { _annoid: annoid, name: "Annotation"};
		FUNCS4.details("anno", null, config);
	}

	/*---tooltip of Hide/Show---*/
	$('a[class=toggle]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to hide/show above table'},
		   position: {my: 'top left', at: 'bottom right', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});

	$("a[class=toggle]").livequery('click', function(e){
		e.preventDefault();
		$(this).parent().find('p').slideToggle(500);
	});
	
	// toggle to hide/show trend image
	
	$('a[class=toggleTrend]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to hide/show above trend image'},
		   position: {my: 'top left', at: 'bottom right', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});

	$("a[class=toggleTrend]").livequery('click', function(e){
		e.preventDefault();
		//console.log( $(this).parent().children().eq(3) );
		$(this).parent().find("#canvasDiv").slideToggle(500);
	});

	/*---tooltip of Remove from the list---*/
	$('a[class=remove]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to remove above table'},
		   position: {my: 'top left', at: 'bottom left', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});
	
	$("a[class=remove]").livequery('click', function(e){
		e.preventDefault();
		//console.log($(this).parent());
		$(this).parent().remove();
	});
	
	/*---tooltip of Remove trend from the list---*/
	$('a[class=removeTrend]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to remove above trend image'},
		   position: {my: 'top left', at: 'bottom left', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});
	
	$("a[class=removeTrend]").livequery('click', function(e){
		e.preventDefault();
		$(this).parent().remove();
	});

	/*---tooltip of Create overlay---*/
	$('a[class=overlay]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to create overlay of above objects'},
		   position: {my: 'top left', at: 'bottom right', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});
	
	$("a[class=overlay]").livequery('click', function(e){
		e.preventDefault();
		var source = $(this).data("source");
		var query = $(this).data("query");
		var curr_time = new Date();
        
		$curr_table_selector = $(this).parent().find("table:first");
		var curr_table = $curr_table_selector.dataTable();
		var table_size = curr_table.fnGetData().length;		
		var num_th = $curr_table_selector.find('thead tr').children().size();
		var colArray = [];
		$curr_table_selector.find('thead th').each(function(index){
			if(source == "ANNO"){
				colArray.push($(this).text());
			}else{
				if(index == 0 || index == 1 || index == num_th-1)	;
				else	colArray.push($(this).text());
			}
		});
		var select_content = "<option value=''>select</option>";
		for(var i = 0; i < colArray.length; i++){
			select_content += "<option value='" + colArray[i] + "'>" + colArray[i] + "</option>";
		}
		
		var info_table = new Array(table_size);
		var selected_field = "";
		var field_max = "";
		var field_min = "";
		var not_colArray = new Array(4);
		not_colArray[0] = ["objid", "type", "name", "specclass"];
		not_colArray[1] = ["Field"];
		not_colArray[2] = ["refObjectId", "isStar", "objectId"];
		not_colArray[3] = ["tsCreated", "ra", "dec"];
		
		//create the overlay setting dialog
		var $dialog = $("#new_overlay_dialog");
		$dialog.empty();
		
		$dialog.html(
			"<form><br/>\
			<label>Overlay Name:&nbsp;&nbsp;</label><input id='overlay_name'/><br/><br/>" +
			"<label>Specific Field:&nbsp;&nbsp;</label>\
			<select id='overlay_select' style='width:45%; overflow:hidden; text-overflow:ellipsis'>"
				 + select_content + 
			"</select><br/><br/>" +
			"<label>Color:&nbsp;&nbsp;</label><input class='simple_color' value='#cc3333'/></form>"    
		); 
		
		$("#overlay_select").bind('change', function(){
			selected_field = $(this).find("option:selected").val();
			//alert(selected_field);
			$("#overlay_select").attr("disabled", "disabled");
			
			if(source == "ANNO"){
				var index_ra = 10;
				var index_dec = 11;
			}else if(source == "FIRST"){
				var index_ra = 2;
				var index_dec = 3;
			}else{
				var index_ra = 3;
				var index_dec = 4;
			}
			
			var field_index = 0;
			for(; field_index < colArray.length; field_index++){
				if(colArray[field_index] == selected_field)	break;
			}
			if(source == "ANNO")	;
			else	field_index += 2;
			
			var counter = 0;
			curr_table.$('tr').each(function(){
				var $curr_tr = $(this);
				info_table[counter] = new Array(3);		
				info_table[counter][0] = $curr_tr.children().eq(index_ra).text();
				info_table[counter][1] = $curr_tr.children().eq(index_dec).text();
				info_table[counter][2] = $curr_tr.children().eq(field_index).text();
				counter++;
			});
			//alert(info_table);
			
			if(source == "SDSS" && $.inArray(selected_field, not_colArray[0]) > -1){
				field_max = "N/A";
				field_min = "N/A";
			}else if(source == "FIRST" && $.inArray(selected_field, not_colArray[1]) > -1){
				field_max = "N/A";
				field_min = "N/A";			
			}else if(source == "LSST" && $.inArray(selected_field, not_colArray[2]) > -1){
				field_max = "N/A";
				field_min = "N/A";			
			}else if(source == "ANNO" && $.inArray(selected_field, not_colArray[3]) == -1){
				field_max = "N/A";
				field_min = "N/A";			
			}else{
				field_max = parseFloat(info_table[0][2]);
				field_min = parseFloat(info_table[0][2]);
				for(var k = 0; k < table_size; k++){
					if(parseFloat(info_table[k][2]) > field_max)	field_max = parseFloat(info_table[k][2]);
					if(parseFloat(info_table[k][2]) < field_min)	field_min = parseFloat(info_table[k][2]);
				}
			}
			//alert(field_max + " " + field_min);
			if(field_max == "N/A" && field_min == "N/A"){
				$("#overlay_select").after("<br/><br/>MAX:&nbsp;&nbsp;<input id='g_field_max' value='" + field_max + "' disabled='disabled'/>" +
				"<br/>MIN:&nbsp;&nbsp;&nbsp;<input id='overlay_field_min' value='" + field_min + "' disabled='disabled'/>");			
			}else{
				$("#overlay_select").after("<br/><br/>MAX:&nbsp;&nbsp;<input id='overlay_field_max' value='" + field_max + "'/>" +
				"<br/>MIN:&nbsp;&nbsp;&nbsp;<input id='overlay_field_min' value='" + field_min + "'/>");
			}
		});
		
		$('.simple_color', $dialog).simpleColor({
			boxWidth: 40,
			cellWidth: 10,
			cellHeight: 12,
			livePreview: true
		});

		$dialog.dialog({
			autoOpen: false,
			title: "Create a new overlay",
			buttons:[ 
				{
					text: "Create",
					click: function(){
						var color = $('.simple_color', $dialog).val();
						color = color.replace(/\#/g,"");
						var label = $("#overlay_name", $dialog).val();
						if(color && label);
						else{alert("Please input Overlay Name...");return;}
						
						if(selected_field == ""){alert("Please choose Specific Field...");return;}
						else{
							var fmax = $("#overlay_field_max").val();
							var fmin = $("#overlay_field_min").val();
							if(fmax && fmin)	;
							else{alert("Please input MAX/MIN value...");return;}
						}
						
						$('#overlays_content').newOverlay(curr_time, source, info_table, selected_field, fmax, fmin, color, label);
						$(this).dialog("close");
					}
				},
				{
					text: "Cancel",
					click: function(){
						$(this).dialog("close");
					}
				}
			]
		});
		$dialog.dialog("open");
	});
	
	/*---tooltip of trend image check query---*/
	$('a[class=checkQuery]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to modify the query'},
		   position: {my: 'top left', at: 'bottom left', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});	
	
	$("a[class=checkQuery]").livequery('click', function(e){
		e.preventDefault();
		$('#so_handle').trigger("click");
		$('#trend_handle').trigger("click");
	});
	
	/*---tooltip of Thumbnails---*/
	$('a[class=thumbnail]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to view thumbnails of above objects'},
		   position: {my: 'top left', at: 'bottom left', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});	
	
	$("a[class=thumbnail]").livequery('click', function(e){
				
		e.preventDefault();
				
		 if( $("#thumbnail_canvas").css("display") == "none" )
		 {	
			  $("#thumbnail_canvas").show();
			  
			  var index_objid = 2;
		 	  var index_ra = 3;
		      var index_dec = 4;
			  						
			  $curr_table_selector = $("#results_content #tab1").find("table:last");	  
			  var curr_table = $curr_table_selector.dataTable();
			  
		      var table_size = curr_table.fnGetData().length;
      
			  //alert(table_size);		
		      var raDecTable = new Array(table_size);
		      var counter = 0;
		      curr_table.$('tr').each(function(){
		          var $curr_tr = $(this);
		          raDecTable[counter] = new Array(3);		
		          raDecTable[counter][0] = parseFloat($curr_tr.children().eq(index_ra).text());
		          raDecTable[counter][1] = parseFloat($curr_tr.children().eq(index_dec).text());
				  raDecTable[counter][2] = $curr_tr.children().eq(index_objid).text();
		          counter++;
		          });
		      if(raDecTable[0][0] == "N/A"){
		          raDecTable = [];
		      }

		      viewThumbnails(raDecTable, skyView);
		}
		
		if($('#results').hasClass('open')){
			$('#results_handle').trigger("click");
		}
		
		if($('#trend_tab').hasClass('open')){
			$('#trend_handle').trigger("click");
		}
		
		if($('#search_object').hasClass('open')){
			$('#so_handle').trigger("click");
		}
		
	    $('#thumbnail_handle').trigger("click");
		
	});

	// Trend Image
	
	/*---tooltip of trend image---*/
	$('a[class=trendImage]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to generate trend image of objects'},
		   position: {my: 'top left', at: 'bottom left', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});	
	
	$("a[class=trendImage]").livequery('click', function(e){
		
		e.preventDefault();
      
	  $curr_table_selector = $("#results_content #tab1").find("table:last");
	  
	  var curr_table = $curr_table_selector.dataTable();
	  var table_size = curr_table.fnGetData().length;
	  
	  //alert(table_size);		
      var objID_table = new Array(table_size);      
	  var index_id = 2;
	  	  
      curr_table.$('tr').each(function(){
          var $curr_tr = $(this);
		  //console.log($curr_tr);
          objID_table.push($curr_tr.children().eq(index_id).text());
          });
      if(objID_table[0] == "N/A"){
         objID_table = [];
      }
	  if(TrendImages == null)
	  	TrendImages = [];
		
		$("#trend_image_content #tab1 div:last-child").find("#canvasDiv").hide();
		//console.log($("#trend_image_content #tab1 div:last")[0]);
		
		$("#trend_image_content #tab1").append('<div></div>');
		
		//console.log($("#trend_image_content #tab1 div:last")[0]);
		
		$("#trend_image_content #tab1 div:last-child").append('<label class="search_object_label"><b>Trend Image' + $("#results_content #tab1 div:last-child").find(".search_object_label").html().substring(18) + '</label>');
		$("#trend_image_content #tab1 div:last-child").append("<div id='canvasDiv'><br></div>");
		//console.log($("#trend_image_content #tab1 div:last")[0]);
		
	    var __handle = "<canvas id='hCanvas' class='center' width='450' height='150'></canvas>"; 
		$("#trend_image_content #tab1 div:last").append(__handle);
		
		//console.log($("#trend_image_content #tab1 div:last")[0]);
		
		$("#trend_image_content #tab1 div:last").append(
			"<canvas id='glCanvas' class='center' width='450' height='150' name='" + TrendImages.length + "'> </canvas>"); 
		
		var glDiv = $("#trend_image_content #tab1 div:nth-last-of-type(2)")[0]; 
		
	 	$(document).ready(function(){
	      	
			$(glDiv).find("#glCanvas").mouseout(function(){
	           	$('#mycursor').hide();
	           	return false;
	      	
			});
			
	      	$(glDiv).find("#glCanvas").mouseenter(function(){
	           	$('#mycursor').show();
	           	return false;
	      	});
			
	      	$(glDiv).find("#glCanvas").mousemove(function(e){
	           	$('#mycursor').css('left', e.clientX - 20).css('top', e.clientY + 7);
	      	});
		});
			
	 	TrendImages.push( new TrendImage({ids:objID_table, ind:TrendImages.length }) );
		
	});

	/*---tooltip of storage---*/
	$('a[class=storage]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to store current result set'},
		   position: {my: 'top right', at: 'bottom right', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});	
	
	// Result History STEP 1 - store
	$("a[class=storage]").livequery('click', function(e){
		e.preventDefault();
		//"Result History" cannot be used unless user logged in
		var the_user_id = getUserId();
		if(the_user_id == -1){
			alert('Please log in first...');
			return;
		}
		
		var source = $(this).data("source");
		var query = $(this).data("query");
		//alert(source + " " + query);
		if(source == "LSST"){
			if(query.indexOf("SimRefObject") == -1)	var the_name = "LSST_t2";
			else	var the_name = "LSST_t1";
		}else{
			var the_name = source;
		}

		$curr_table_selector = $(this).parent().find("table:first");
		var curr_table = $curr_table_selector.dataTable();
		
		var the_result_type = source;
		var the_result_size = curr_table.fnGetData().length;
		var the_result_content = '{"aoColumns":[';
		the_result_content += '{"sTitle": "&nbsp;&nbsp;", "bSortable": false}, {"sTitle": "&nbsp;&nbsp;", "bSortable": false},';
		
		var num_th = $curr_table_selector.find('thead tr').children().size();
		$curr_table_selector.find('thead th').each(function(index){
			if(index == 0 || index == 1 || index == num_th-1)	;
			else	the_result_content += '{"sTitle": "' + $(this).text() + '" , "sType": "html"},';
		});
		the_result_content += '{"sTitle": "Object details", "bSortable": false}';
		the_result_content += '], "aaData":[';
		
		curr_table.$('tr').each(function(){
			var $curr_tr = $(this);
			var num_td = $curr_tr.children().size();
			the_result_content += '[';
			the_result_content += '"<a href=\'#\' class=\'more\' name=\'' + the_name + '\'><span class=\'ui-icon ui-icon-info\'/></a>", ';
			the_result_content += '"<a href=\'#\' class=\'delete\' name=\'' + source + '\'><span class=\'ui-icon ui-icon-circle-close\'/></a>", ';
			$curr_tr.find('td').each(function(index){	
				if(index == 0 || index == 1 || index == num_td-1)	;
				else	the_result_content += '"' + $(this).html().replace(/"/g, '\\"') + '",';
			});
			the_result_content += '"<a href=\'#\' class=\'more\' name=\'' + the_name + '\'>more</a>"';
			the_result_content += '],';
		});
		the_result_content = the_result_content.substring(0, the_result_content.length-1);
		the_result_content += ']}';
		//alert(the_result_content);
		the_result_content = $.jSEND(the_result_content);
		
		//create the store result dialog
		var $dialog = $("#result_history_dialog");
		$dialog.empty();
		$dialog.html(
			"<form><br/><label>Result Name:&nbsp;&nbsp;</label><input id='result_his_name'/><br/><br/>" +
			"<label>Comments:&nbsp;&nbsp;</label>" +
			"<textarea id='result_his_comment' rows='4' cols='45' style='overflow-x:hidden;overflow-y:auto;resize:none'></textarea></form>"
		);
		$dialog.dialog({
			autoOpen: true,
			modal: true,
			draggable: false,
			resizable: false,
			title: "Store Result",
			buttons:{
				"Save": function(){
					var the_name = $("#result_his_name", $dialog).val();
					var the_comment = $("#result_his_comment", $dialog).val();
					if(the_name){
						if(the_comment)	;
						else	the_comment = "";
					}else{
						alert('Input the name of stored result...');
						return;
					}
					var record = {
						'user_id': the_user_id,
						'result_type': the_result_type,
						'result_name': the_name,
						'result_comment': the_comment,
						'result_size': the_result_size,
						'result_content': the_result_content
					};
					$.ajax({type: "POST", url: "./lib/db/local/queryASTRO.php", data: {record: record, insert_res: 1}});
					$(this).dialog("close");
				},
				"Cancel": function(){
					$(this).dialog("close");
				}
			}
		});
	});
	
	/*---short result history set-ups---*/
	$("#res_his_display").hide();
	$("#res_his_name").val('');
	/*---end---*/
	
	// Result History STEP 2 - retrieve
	$("#res_his_button").bind('click', function(e){
		//"Result History" cannot be used unless user logged in
		var the_user_id = getUserId();
		if(the_user_id == -1){
			alert('Please log in first...');
			return;
		}
		
		var the_search = $("#res_his_name").val();
		if(the_search)	;
		else{
			alert("Input result name for searching...");
			return;
		}
		$.ajax({
				type: "POST",
				url: "./lib/db/local/queryASTRO.php",
				data: {content: the_search, userId: the_user_id, select_res: 1},
				dataType: "html"
		}).done(function(html){
			$("#res_his_table").find('tr').each(function(){
				if($(this).attr("id") == "res_his_title")	;
				else	$(this).remove();
			});
			$("#res_his_table").next().remove();
			if(html)	$("#res_his_table").append(html);
			else	$("#res_his_display").append("<p><br/><br/>No result found...</p>");
			$("#res_his_display").show();
		});
	});
	
	// Result History STEP 3 - recover
	$("input[name=res_his_recover]").livequery('click', function(e){
		//"Result History" cannot be used unless user logged in
		var the_user_id = getUserId();
		if(the_user_id == -1){
			alert('Please log in first...');
			return;
		}

		var the_result_id = $(this).next().val();
		var the_survey = $(this).parent().parent().find("td:first").html();
		var the_result_name = $(this).parent().parent().children().eq(1).html();
		
		$.ajax({
				type: "POST",
				url: "./lib/db/local/queryASTRO.php",
				data: {resultId: the_result_id, userId: the_user_id, update_res: 1},
				dataType: "json"
		}).done(function(json_msg){
			console.log(json_msg);
			$("#results_content #tab1 p:last").hide();
			$("#results_content #tab1").append('<div></div>');
			$("#results_content #tab1 div:last").append('<label class="search_object_label"><b>Search Objects - result history "'+ the_result_name +'" on '+ the_survey +'</b></label>');
			$("#results_content #tab1 div:last").append('<p><br>Processing...</p><br/><br/><a href="#" class ="toggle">Hide/Show</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="remove">Remove from the list</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="overlay">Create overlay</a><br/><br/>');
			$("#results_content #tab1 div:last").append('<a href="#" class="thumbnail">Thumbnails</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="trendImage">Trend Image</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="storage">Store Result</a><br/>');
			
			var the_id = the_survey + "_table" + restab_base.obj_base;
			restab_base.obj_base++;
			$("#results_content #tab1 div:last").find('p').html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="'+the_id+'"></table>');
			var oTable = $("#results_content #tab1 div:last #"+the_id).dataTable(json_msg);
			var oTableTools = new TableTools(oTable, {
				"sSwfPath": "./lib/DataTables-1.9.1/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
			});	
			$('#'+the_id+'_wrapper').before(oTableTools.dom.container);

			if(the_survey == "LSST"){
				if($("#results_content #tab1 table:last").find('a[class="more"]:first').attr('name') == "LSST_t1"){
					var the_query = "SimRefObject query variant";
				}else	var the_query = "Object query variant";
			}else{
				var the_query = the_survey + " query variant";
			}
			$("#results_content #tab1 a:last").prev().prev().data("source", the_survey);
			$("#results_content #tab1 a:last").prev().prev().data("query", the_query);
			$("#results_content #tab1 a:last").prev().data("source", the_survey);
			$("#results_content #tab1 a:last").prev().data("query", the_query);
			$("#results_content #tab1 a:last").data("source", the_survey);
			$("#results_content #tab1 a:last").data("query", the_query);
			
			$('#results_content a[href="#tab1"]').trigger('click');
		});
	});

	/*---Upload Result---*/
	$("#upload_res_table").attr("disabled", "disabled");
	$("#upload_res_survey").bind('change', function(e){
		if($(this).find("option:selected").val() == "LSST")	$("#upload_res_table").removeAttr("disabled");
		else	$("#upload_res_table").attr("disabled", "disabled");
	});
	
	$("#upload_res_button").bind('click', function(e){
		$("#upload_res_file").attr("disabled", "disabled");
		$("#upload_res_button").attr("disabled", "disabled");
	
		var the_survey = $("#upload_res_survey").find("option:selected").val();
		var the_table = $("#upload_res_table").find("option:selected").val();
		var source = the_survey;
		if(source == "LSST"){
			if(the_table == "SimRefObject")	var the_name = "LSST_t1";
			else	var the_name = "LSST_t2";
		}else{
			var the_name = the_survey;
		}
		
		if(the_survey == ""){
			alert("select which survey the upload dataset belongs to...");
			$("#upload_res_file").removeAttr("disabled");
			$("#upload_res_button").removeAttr("disabled");
			return;
		}
		
		if(the_survey == "LSST" && the_table == ""){
			alert("select which table the upload LSST dataset belongs to...");
			$("#upload_res_file").removeAttr("disabled");
			$("#upload_res_button").removeAttr("disabled");
			return;
		}
	
		var file = document.getElementById('upload_res_file').files[0];
		var reader = new FileReader();
		reader.readAsText(file, 'UTF-8');
		
		reader.onload = function record(event){
			var content = event.target.result;
			var file_name = document.getElementById('upload_res_file').files[0].name;
			var content = content.replace(/"/g, "");
			
			/* OS detection for newline character */
			// Windows - CRLF \r\n
			// UNIX - LF \n
			// MAC - LF \n
			//alert(navigator.platform);
			if(navigator.platform.toUpperCase().indexOf('WIN') !== -1){
				var content_array = content.split("\r\n");
			}else{
				var content_array = content.split("\n");
			}
				
			var ra_array = ["ra", "RA", "ra_PS"];
			var dec_array = ["dec", "Declination", "decl", "decl_PS"];
			var ra_index = 0;
			var ra_name = "";
			var dec_index = 0;
			var dec_name = "";
			
			var json_msg = '{"aoColumns":[';
			json_msg += '{"sTitle": "&nbsp;&nbsp;", "bSortable": false}, {"sTitle": "&nbsp;&nbsp;", "bSortable": false},';
			
			var curr_title = content_array[0].split(",");
			for(var k = 0; k < curr_title.length; k++){
				if(0 === curr_title[k].length || /^\s*$/.test(curr_title[k]) || curr_title[k] == "Object details")	continue;
				else{
					//alert(curr_title[k]);
					if(jQuery.inArray(curr_title[k], ra_array) != -1){
						ra_index = k;
						ra_name = curr_title[k];
					}
					if(jQuery.inArray(curr_title[k], dec_array) != -1){
						dec_index = k;
						dec_name = curr_title[k];
					}
					json_msg += '{"sTitle": "' + curr_title[k] + '" , "sType": "html"},';
					
				}
			}
			json_msg += '{"sTitle": "Object details", "bSortable": false}';
			json_msg += '], "aaData":[';			
			for(var i = 1; i < content_array.length; i++){
				json_msg += '[';
				json_msg += '"<a href=\'#\' class=\'more\' name=\'' + the_name + '\'><span class=\'ui-icon ui-icon-info\'/></a>", ';
				json_msg += '"<a href=\'#\' class=\'delete\' name=\'' + source + '\'><span class=\'ui-icon ui-icon-circle-close\'/></a>", ';
				var curr_body = content_array[i].split(",");
				for(var j = 0; j < curr_body.length; j++){
					if(0 === curr_body[j].length || /^\s*$/.test(curr_body[j]) || curr_body[j] == "more")	continue;
					else{
						//alert(curr_body[j]);
						if(j == ra_index){
							json_msg += '"<a href=\'#\' class=\'jump\' name=\'' + ra_name + '\'>' + curr_body[j] + '</a>",';
						}else if(j == dec_index){
							json_msg += '"<a href=\'#\' class=\'jump\' name=\'' + dec_name + '\'>' + curr_body[j] + '</a>",';
						}else{
							json_msg += '"' + curr_body[j] + '",';
						}
					}
				}
				json_msg += '"<a href=\'#\' class=\'more\' name=\'' + the_name + '\'>more</a>"';
				json_msg += '],';
			}
			json_msg = json_msg.substring(0, json_msg.length-1);
			json_msg += ']}';
			
			json_msg = jQuery.parseJSON(json_msg);
			
			$("#results_content #tab1 div:last").hide();
			$("#results_content #tab1").append('<div></div>');
			$("#results_content #tab1 div:last").append('<label class="search_object_label"><b>Search Objects - upload result "'+ file_name +'" on '+ the_survey +'</b></label>');
			$("#results_content #tab1 div:last").append('<p>Processing...</p><br/><br/><a href="#" class ="toggle">Hide/Show</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="remove">Remove from the list</a><br/><br/>');
			$("#results_content #tab1 div:last").append('<a href="#" class="overlay">Create overlay</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="thumbnail">Thumbnails</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="storage">Store Result</a><hr/>');

			var the_id = the_survey + "_table" + restab_base.obj_base;
			restab_base.obj_base++;
			$("#results_content #tab1 div:last").find('p').html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="'+the_id+'"></table>');
			var oTable = $("#results_content #tab1 div:last #"+the_id).dataTable(json_msg);
			var oTableTools = new TableTools(oTable, {
				"sSwfPath": "./lib/DataTables-1.9.1/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
			});	
			$('#'+the_id+'_wrapper').before(oTableTools.dom.container);

			if(the_survey == "LSST"){
				if(the_name == "LSST_t1"){
					var the_query = "SimRefObject query variant";
				}else	var the_query = "Object query variant";
			}else{
				var the_query = the_survey + " query variant";
			}	
			$("#results_content #tab1 a:last").prev().prev().data("source", the_survey);
			$("#results_content #tab1 a:last").prev().prev().data("query", the_query);
			$("#results_content #tab1 a:last").prev().data("source", the_survey);
			$("#results_content #tab1 a:last").prev().data("query", the_query);
			$("#results_content #tab1 a:last").data("source", the_survey);
			$("#results_content #tab1 a:last").data("query", the_query);

			$('#results_content a[href="#tab1"]').trigger('click');	
		};
		
		$("#upload_res_file").removeAttr("disabled");
		$("#upload_res_button").removeAttr("disabled");
	});
	/*---End---*/

	/*---delete row from DataTable---*/
	$("a[class=delete]").livequery('click', function(e){
		e.preventDefault();
		$the_tr = $(this).parent().parent();
		var curr_table = $the_tr.parent().parent().dataTable();
		var the_pos = curr_table.fnGetPosition(this.parentNode.parentNode);
		curr_table.fnDeleteRow(the_pos);
	});

	/*---tooltip of Jumping---*/
	$('a[class=jump]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to navigate it in SkyView'},
		   position: {my: 'top right', at: 'bottom left', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});	
	
	$("a[class=jump]").livequery('click', function(e){
		e.preventDefault();
		var $the_node = $(this).parent();
		var the_name = $(this).attr('name');
		if(the_name == "ra" || the_name == "RA" || the_name == "ra_PS"){
			var $the_neighbor = $the_node.next();
			var ra = $the_node.find('a').text();
			var dec = $the_neighbor.find('a').text();
			skyView.jump(parseFloat(ra), parseFloat(dec));
			locator.show();
			if($('#search_object').hasClass('open')){
				$('#so_handle').trigger("click");
			}
			if($('#search_annotation').hasClass('open')){
				$('#sa_handle').trigger("click");
			}
		}else if(the_name == "dec" || the_name == "Declination" || the_name == "decl" || the_name == "decl_PS"){
			var $the_neighbor = $the_node.prev();
			var dec = $the_node.find('a').text();
			var ra = $the_neighbor.find('a').text();
			//alert(ra+ '  ' +dec);
			skyView.jump(parseFloat(ra), parseFloat(dec));
			locator.show();
			if($('#search_object').hasClass('open')){
				$('#so_handle').trigger("click");
			}
			if($('#search_annotation').hasClass('open')){
				$('#sa_handle').trigger("click");
			}
		}else{
			alert("error!");
		}
	});
	
	/*---tooltip of More---*/
	$('a[class=more]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to get object details'},
		   position: {my: 'top right', at: 'bottom left', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});
	
	$("a[class=more]").livequery('click', function(e){
		e.preventDefault();
		var $the_node = $(this).parent().parent();
		var the_name = $(this).attr('name');
		if(the_name == "SDSS"){
			var objid = $the_node.children().eq(2).text();
			var ra = $the_node.children().eq(3).text();
			if(!$.isNumeric(ra)) {
				ra = null;
			}
			var dec = $the_node.children().eq(4).text();
			if(!$.isNumeric(dec)) {
				dec = null;
			}
			FUNCS4.showSDSSDetails(objid, ra, dec);
		}else if(the_name == "FIRST"){
			var ra = $the_node.find("a[name='RA']").text();
			var dec = $the_node.find("a[name='Declination']").text();
			var EPSILLON = 0.0001;

			var my_query = "SELECT RA, Declination, Fpeak, Fint, RMS, Ps, Field, Maj, fMaj, Min, fMin, PA, fPA FROM FIRSTcatalog WHERE (";
			my_query += "RA > " + (parseFloat(ra) - parseFloat(EPSILLON)).toFixed(4) + " AND RA < " + (parseFloat(ra) + parseFloat(EPSILLON)).toFixed(4) +
			" AND Declination > " + (parseFloat(dec) - parseFloat(EPSILLON)).toFixed(4) + " AND Declination < " + (parseFloat(dec) + parseFloat(EPSILLON)).toFixed(4) + ")";
			
			/*---------RA, Dec convert-----------*/
			var ra_dec_obj = gFUNCS.convert_radec(ra, dec, "FIRST");
			FUNCS4.details("FIRST", my_query, ra_dec_obj);
		}else if(the_name.indexOf("LSST") != -1){
			var the_id = $the_node.children().eq(2).text();
			if(the_name == "LSST_t1"){
				var my_query = "SELECT refObjectId, ra, decl, isStar, varClass, htmId20, gLat, gLon, " +
				"sedName, uMag, gMag, rMag, iMag, zMag, yMag, muRa, muDecl, parallax, vRad ,redshift" +
				" FROM SimRefObject WHERE (refObjectId = " + the_id + ")";
				
				var ra = $the_node.find("a[name='ra']").text();
				var dec = $the_node.find("a[name='decl']").text();
			}else if(the_name == "LSST_t2"){
				var my_query = "SELECT objectId, ra_PS, decl_PS, htmId20, earliestObsTime, latestObsTime, " +
				"meanObsTime, flags, uNumObs, uFlux_Gaussian, uFlux_Gaussian_Sigma, gFlux_Gaussian, gFlux_Gaussian_Sigma, " +
				"rFlux_Gaussian, rFlux_Gaussian_Sigma, iFlux_Gaussian, iFlux_Gaussian_Sigma, zFlux_Gaussian, zFlux_Gaussian_Sigma, "+
				"zFlux_Gaussian, zFlux_Gaussian_Sigma" +
				" FROM Object WHERE (objectId = " + the_id + ")";

				var ra = $the_node.find("a[name='ra_PS']").text();
				var dec = $the_node.find("a[name='decl_PS']").text();
			}else	alert("error!");
			
			/*---------RA, Dec convert-----------*/
			var ra_dec_obj = gFUNCS.convert_radec(ra, dec, "LSST");
			FUNCS4.details("LSST", my_query, ra_dec_obj);
		} else if(the_name == "anno") {
			var annoid = $the_node.children().eq(1).text();
			FUNCS4.showAnnoDetails(annoid);
		} else {
			alert("error!");
		}
	});
};
