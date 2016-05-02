/*
Eric Gratta
Fall 2015
JS file for Bookmarks side tab
*/
var bookmarks_fn = bookmarks_fn || {};
jQuery.fn.bookmarks = function(){

	bookmarks_fn.setupBookmarkTables = function() {
		//Get all bookmarks
		$.ajax({type: "GET",
			url: "./lib/db/local/queryASTRO.php",
			data: { get_bookmarks:1, objects:1, user_id:getUserId() },
			dataType: "json"
		}).done(function(json_msg) {
			var delete_button = "<button class='tiny_button delete_bookmark'><span class='ui-button-icon-primary ui-icon ui-icon-close'></span></button>"
			//Location bookmarks
			var loc_bookmarks = json_msg["bookmarks"][1];
			data_table = {"bPaginate": true, "bLengthChange": true, "bFilter": false, "aaSorting" : [], "aaData":[]};
			if(loc_bookmarks.length > 0) {
				data_table.aoColumns = [{"sTitle": "Title", "bSortable": true}, {"sTitle": "Time Created", "bSortable": true}, {"sTitle": "RA", "bSortable": true}, {"sTitle": "dec", "bSortable":true}, {"sTitle":"", "bSortable":false, "width":"3%"}];
			}
			for(var i = 0; i < loc_bookmarks.length; i++) {
				var title_span = "<span data-id=" + loc_bookmarks[i].id + ">" + loc_bookmarks[i].title + "</span>";
				var ra_link = "<a href='#' data-ra=" + loc_bookmarks[i].ra + " data-dec=" + loc_bookmarks[i].dec + ">" + loc_bookmarks[i].ra.toFixed(5) + "</a>";
				var dec_link = "<a href='#' data-ra=" + loc_bookmarks[i].ra + " data-dec=" + loc_bookmarks[i].dec + ">" + loc_bookmarks[i].dec.toFixed(5) + "</a>";
				if(loc_bookmarks[i].user_id == 126) {
					data_table.aaData.push([title_span, loc_bookmarks[i].ts_created, ra_link, dec_link, ""]);
				} else {
					data_table.aaData.push([title_span, loc_bookmarks[i].ts_created, ra_link, dec_link, delete_button]);
				}
			}
			if($("#bookmarks_tab1 div:first table").hasClass("dataTable")) {
				$("#bookmarks_tab1 div:first table").dataTable().fnDestroy();
			}
			$("#bookmarks_tab1 div:first table").dataTable(data_table).width("100%");
			
			//Object bookmarks
			var obj_bookmarks = json_msg["bookmarks"][0];
			var data_table = {"bPaginate": true, "bLengthChange": true, "bFilter": false, "aaSorting" : [], "aaData":[]};
			if(obj_bookmarks.length > 0) {
				data_table.aoColumns = [{"sTitle": "Title", "bSortable": true}, {"sTitle": "Time Created", "bSortable": true}, {"sTitle": "Object Name", "bSortable": false}, {"sTitle":"", "bSortable":false, "width":"3%"}];
			}
			for(var i = 0; i < obj_bookmarks.length; i++) {
				var title_span = "<span data-id=" + obj_bookmarks[i].id + ">" + obj_bookmarks[i].title + "</span>";
				var object_link = "<a href='#' data-id=" + obj_bookmarks[i].obj_id + " data-ra=" + obj_bookmarks[i].ra + " data-dec=" + obj_bookmarks[i].dec + ">" + obj_bookmarks[i].name + "</a>";
				if(obj_bookmarks[i].user_id == 126) {
					data_table.aaData.push([title_span, obj_bookmarks[i].ts_created, object_link, ""]);
				} else {
					data_table.aaData.push([title_span, obj_bookmarks[i].ts_created, object_link, delete_button]);
				}
			}
			if($("#bookmarks_tab2 div:first table").hasClass("dataTable")) {
				$("#bookmarks_tab2 div:first table").dataTable().fnDestroy();
			}
			$("#bookmarks_tab2 div:first table").dataTable(data_table).width("100%");
			
			//Annotation bookmarks
			var anno_bookmarks = json_msg["bookmarks"][2];
			data_table = {"bPaginate": true, "bLengthChange": true, "bFilter": false, "aaSorting" : [], "aaData":[]};
			if(anno_bookmarks.length > 0) {
				data_table.aoColumns = [{"sTitle": "Title", "bSortable": true}, {"sTitle": "Time Created", "bSortable": true}, {"sTitle": "Anno Title", "bSortable": true}, {"sTitle": "Value", "bSortable":true}, {"sTitle":"", "bSortable":false, "width":"3%"}];
			}
			for(var i = 0; i < anno_bookmarks.length; i++) {
				var title_span = "<span data-id=" + anno_bookmarks[i].id + ">" + anno_bookmarks[i].title + "</span>";
				var anno_title_link = "<a href='#' data-id=" + anno_bookmarks[i].anno_id + " data-ra=" + anno_bookmarks[i].ra + " data-dec=" + anno_bookmarks[i].dec + ">" + anno_bookmarks[i].anno_title + "</a>";
				var anno_value = anno_bookmarks[i].anno_value;
				if(anno_value.length > 128) {
					anno_value = anno_value.substring(0,128) + "...";
				}
				var anno_value_link = "<a href='#' data-id=" + anno_bookmarks[i].anno_id + " data-ra=" + anno_bookmarks[i].ra + " data-dec=" + anno_bookmarks[i].dec + ">" + anno_value + "</a>";				
				if(anno_bookmarks[i].user_id == 126) {
					data_table.aaData.push([title_span, anno_bookmarks[i].ts_created, anno_title_link, anno_value_link, ""]);
				} else {
					data_table.aaData.push([title_span, anno_bookmarks[i].ts_created, anno_title_link, anno_value_link, delete_button]);
				}
			}
			if($("#bookmarks_tab3 div:first table").hasClass("dataTable")) {
				$("#bookmarks_tab3 div:first table").dataTable().fnDestroy();
			}
			$("#bookmarks_tab3 div:first table").dataTable(data_table).width("100%");
			
			$(".delete_bookmark").livequery(function(){
				$(this).qtip({
				   content: {text: 'click to delete this bookmark'},
				   position: {my: 'bottom left', at: 'top right', target: $(this)},
				   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
				   show: {event: 'mouseover'},
				   hide: {event: 'mouseout mouseup'}
				});
			});
			$(".delete_bookmark").livequery('click', function(e) {
				e.preventDefault();
				var bookmark_id = $(this).parent().parent().find("td:first span").data("id");
				var parent_table_id = $(this).parent().parent().parent().parent().attr("id");
				var type = "loc";
				if(parent_table_id == "DataTables_Table_2") {
					type = "obj";
				} else if(parent_table_id == "DataTables_Table_3") {
					type = "anno";
				}
                
				var data = {
                    type:type
                };
				$.ajax({type: "DELETE",
					url: secureRESTbase + "bookmark/" + bookmark_id,
                    crossDomain:true,
                    beforeSend: function(xhr) {
                        setAuthHeaders(xhr);
                    },
                    dataType: "json",
					data: data
				}).done(function(json_msg) {
					bookmarks_fn.setupBookmarkTables();
				});
			});
		}).error(function(jqXHR, textStatus, errorThrown) {
			console.log("Error:  " + errorThrown);
			console.log(textStatus);
			console.log(jqXHR);
		});
	};
	
	bookmarks_fn.setupBookmarkTables();
	$("#bookmarks_tab1 table a").livequery(function(){
		$(this).qtip({
			content: {text: 'click to jump to this location'},
			position: {my: 'bottom left', at: 'top right', target: $(this)},
			style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
			show: {event: 'mouseover'},
			hide: {event: 'mouseout'}
		});
	});
	$("#bookmarks_tab2 table a, #bookmarks_tab3 table a").livequery(function(){
		$(this).qtip({
		   content: {text: 'click to see object details'},
		   position: {my: 'bottom left', at: 'top right', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});
	
	$("#bookmarks_tab1 table a").livequery('click', function(e){
		e.preventDefault();
		var ra = $(this).data("ra");
		var dec = $(this).data("dec");
		skyView.jump(parseFloat(ra), parseFloat(dec));
		locator.show();
	});
	
	$("#bookmarks_tab2 table a").livequery('click', function(e){
		e.preventDefault();
		var ra = $(this).data("ra");
		var dec = $(this).data("dec");
		//using $(this).data("id") corrupts the value - maybe due to integer overflow?
		FUNCS4.showSDSSDetails($(this).attr("data-id"), ra, dec);
		skyView.jump(parseFloat(ra), parseFloat(dec));
		locator.show();
	});
	
	$("#bookmarks_tab3 table a").livequery('click', function(e){
		e.preventDefault();
		var annoid = $(this).data("id");
		var ra = $(this).data("ra");
		var dec = $(this).data("dec");
		FUNCS4.showAnnoDetails(annoid, ra, dec);
		skyView.jump(parseFloat(ra), parseFloat(dec));
		locator.show();
	});
	
	$("#bookmark_current_loc").livequery(function(){
		$(this).qtip({
		   content: {text: 'click to bookmark these coordinates'},
		   position: {my: 'bottom left', at: 'top right', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});
	
	$("#bookmark_current_loc").livequery('click', function(e) {
		e.preventDefault();
		var coords = $("#RA-Dec").text().split(", ");
		var data = {
			"type":"loc",
			"ra":coords[0],
			"dec":coords[1]
		};
		$("#bookmark_dialog").bookmarkDialog("setTarget", data);
		$("#bookmark_dialog").bookmarkDialog("open");
	});
};
