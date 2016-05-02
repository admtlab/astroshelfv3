/*
Di Bao
Summer 2012
The JS file for Search Objects side tab
*/
jQuery.fn.search_obj_directsql = function(){

	/*-----setups------*/
	var click_counter = 0;
	var radio_id = "radio_sdss_tab2";
	var the_abort_tab2;//to abort the current ajax call
	
	$("#textarea_tab2").val("Please input your query here...");	
	$("#search_tab2").attr("disabled", "disabled");//disable the search button
	$("#cancel_tab2").attr("disabled", "disabled");//disable the cancel button
	
	var the_base = 0;
	var the_offset = 5;
	
	$("#prev_tab3").hide();
	/*-----end-----*/

	var FUNCS2 = FUNCS2 || {};
	FUNCS2.search = function(the_survey, query){
		if(arguments.length == 1)	query = "";
		var the_url = "";
		var the_table = "";
		var the_query = "";
		var preference = '';
		
		if(!query){
			the_query = $("#textarea_tab2").val();
		}else{
			the_query = query;
		}
		
		if(the_survey == "SDSS"){
			the_url = "./lib/db/remote/searchSDSS.php";
		}else if(the_survey == "FIRST"){
			the_url = "./lib/db/local/queryFIRST.php";
		}else if(the_survey == "LSST"){
			the_url = "./lib/db/remote/queryLSST.php";
			var the_table_pos = the_query.indexOf("SimRefObject");
			if(the_table_pos == -1){
				var the_table = "Object";
			}else{
				var the_table = "SimRefObject";
			}
		}else{
			;
		}
		
		if(!query){
			$("#radio_sdss_tab2").attr("disabled", "disabled");
			$("#radio_first_tab2").attr("disabled", "disabled");
			$("#radio_lsst_tab2").attr("disabled", "disabled");
			$("#search_tab2").attr("disabled", "disabled");
			$("#reset_tab2").attr("disabled", "disabled");
			$("#cancel_tab2").removeAttr("disabled");
		
			/*--------use preference--------*/
			if($("#OBJ_pref_tab2").attr("checked")){
				preference = "get user's "+the_survey+" catalog preference from REST API";
				alert(preference);
			}else{
				preference = '';
				//alert("here");
			}
			/*----end----*/
		}
		
		var formatted_time = gFUNCS.create_time();
		$("#results_content #tab1 p:last").hide();
		$("#results_content #tab1").append('<div></div>');
		$("#results_content #tab1 div:last").append('<label class="search_object_label"><b>Search Objects - request on '+the_survey+' <br/>(' + formatted_time + ')</b></label>');
		$("#results_content #tab1 div:last").append('<p>Processing...</p><br/><br/><a href="#" class ="toggle">Hide/Show</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="remove">Remove from the list</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="overlay">Create overlay</a><br/><br/>');
		$("#results_content #tab1 div:last").append('<a href="#" class="thumbnail">Thumbnails</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="trendImage">Trend Image</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="storage">Store Result</a><br/>');
		//<a href="#" class="download" style="visibility:hidden">Download</a><hr/>
		$("#results_content #tab1 a:last").prev().prev().data("source", the_survey);
		$("#results_content #tab1 a:last").prev().prev().data("query", the_query);
		$("#results_content #tab1 a:last").prev().data("source", the_survey);
		$("#results_content #tab1 a:last").prev().data("query", the_query);
		$("#results_content #tab1 a:last").data("source", the_survey);
		$("#results_content #tab1 a:last").data("query", the_query);
		
		the_abort_tab2 = $.ajax({type: "POST",
							url: the_url,
							data: {query: the_query, pref: preference, table: the_table},
							dataType: "json"
		}).done(function(json_msg){
			if(json_msg.error){
				$("#results_content #tab1 div:last").find('p').html(json_msg["error"]);
				$("#results_content #tab1 div:last").find('a[class=overlay]').remove();
				$("#results_content #tab1 div:last").find('a[class=download]').remove();
				$("#results_content #tab1 div:last").find('a[class=thumbnail]').remove();
				$("#results_content #tab1 div:last").find('a[class=storage]').remove();
			}else{
				var the_id = the_survey+"_table" + restab_base.obj_base;
				restab_base.obj_base++;
				$("#results_content #tab1 div:last").find('p').html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="'+the_id+'"></table>');
				var oTable = $("#results_content #tab1 div:last #"+the_id).dataTable(json_msg);
				var oTableTools = new TableTools(oTable, {
					"sSwfPath": "./lib/DataTables-1.9.1/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
				});	
				$('#'+the_id+'_wrapper').before(oTableTools.dom.container);
			}
			/*-----end------*/
			
			//reset search_objects tab
			//$("#search_object_form_content").show();
			if(!query){
				$("#radio_sdss_tab2").removeAttr("disabled");
				$("#radio_first_tab2").removeAttr("disabled");
				$("#radio_lsst_tab2").removeAttr("disabled");
				$("#search_tab2").removeAttr("disabled");
				$("#cancel_tab2").attr("disabled", "disabled");
				$("#reset_tab2").removeAttr("disabled");
			}
			
			if($('#object_tab').hasClass('open')){
				$('#object_handle').trigger("click");
			}
			if(!$('#results').hasClass('open')){
				$('#results_handle').trigger("click");
			}
		});
		
		//query history
		if(!query){
			gFUNCS.insert_query(the_survey, the_query);
		}
	};	
	
	/*---------direct SQL----------*/
	//for the radio button
	$("#tabs2_top > p > input[name=radio2]").bind('click', function(){
		//alert("get here");
		var current_id = $(this).attr("id");
		if(current_id == radio_id){
			;
		}else{
			$("#textarea_tab2").removeClass("search_box2");
			$("#textarea_tab2").addClass("search_box1");
			$("#textarea_tab2").val("Please input your query here...");

			$("#search_tab2").attr("disabled", "disabled");		
			$("#OBJ_pref_tab2").removeAttr("checked");
			
			click_counter = 0;	
			radio_id = current_id;
		}
	});
	
	//for the search button
	$("#search_tab2").bind('click', function(){		
		if($("#radio_sdss_tab2:checked").val()!=null)	FUNCS2.search("SDSS");
		if($("#radio_first_tab2:checked").val()!=null)	FUNCS2.search("FIRST");
		if($("#radio_lsst_tab2:checked").val()!=null)	FUNCS2.search("LSST");
	});
	/*-----end------*/
	
	//for the cancel button
	$("#cancel_tab2").bind('click', function(){
		$("#results_content #tab1 div:last").find('p').html('Cancelled...');
		$("#results_content #tab1 div:last").find('a[class=overlay]').remove();
		$("#results_content #tab1 div:last").find('a[class=download]').remove();
		$("#results_content #tab1 div:last").find('a[class=thumbnail]').remove();
		$("#results_content #tab1 div:last").find('a[class=storage]').remove();
		
		$("#radio_sdss_tab2").removeAttr("disabled");
		$("#radio_first_tab2").removeAttr("disabled");
		$("#radio_lsst_tab2").removeAttr("disabled");
		$("#search_tab2").removeAttr("disabled");
		$("#cancel_tab2").attr("disabled", "disabled");
		$("#reset_tab2").removeAttr("disabled");
		
		$("#radio_sdss").removeAttr("disabled");
		$("#radio_first").removeAttr("disabled");
		$("#radio_lsst").removeAttr("disabled");//weird influence...
		
		the_abort_tab2.abort();
	});
	/*---------end----------*/
	
	//for the reset buttion
	$("#reset_tab2").bind('click', function(){
		$("#radio_sdss_tab2").attr("checked", "checked");
		$("#radio_first_tab2").removeAttr("checked");
		$("#radio_lsst_tab2").removeAttr("checked");
		
		$("#textarea_tab2").removeClass("search_box2");
		$("#textarea_tab2").addClass("search_box1");
		$("#textarea_tab2").val("Please input your query here...");

		$("#search_tab2").attr("disabled", "disabled");		
		$("#OBJ_pref_tab2").removeAttr("checked");
		
		click_counter = 0;
	});
	/*-----end------*/
	
	$("#textarea_tab2").bind('click', function(){
		if(click_counter == 0){
			$(this).removeClass("search_box1");
			$(this).addClass("search_box2");
			$("#textarea_tab2").val('');
			
			click_counter = 1;
		}
	});
	
	$("#textarea_tab2").bind('keyup', function(){
		var min = 20;
		var length = $("#textarea_tab2").val().length;
		if(length > min){
			$("#search_tab2").removeAttr("disabled");
		}
	});
	
	/*---------query history-----------*/
	$("#login_tab3").bind('click', function(e){
		e.preventDefault();
		var status = $(this).text();
		//alert(status);
		if(status == "login"){
			$('#loginout').trigger("click");
		}else if(status == "logout"){
			$('#loginout').trigger("click");
		}else{
			;
		}
	});

	$("a[name=restore_tabs3]").livequery('click', function(e){
		//alert("clicked!");
		e.preventDefault();
		var the_survey = $(this).parent().prev().text();
		var the_query = $(this).text();
		
		FUNCS2.search(the_survey, the_query);
	});
	
	var prepareRefresh = function(e) {
		e.preventDefault();
		$("#prev_tab3").hide();
		$("#next_tab3").hide();
		$("#reload_tab3").attr("disabled", "disabled");
		$("#the_table_tabs3").find('tr').each(function(){
			if($(this).attr("id") == "title"){
				;
			}else{
				$(this).remove();
			}
		});
	};
	
	$("#search_objs-history").bind('click', function(e) {
		prepareRefresh(e);
		the_base = 0;
		$.ajax({type: "GET",
			url: "./lib/db/local/queryASTRO.php",
			data: {userid: getUserId(), select: 1, base: the_base, offset: the_offset+1},
			dataType: "html"
		}).done(function(html){
			if($(html).length == the_offset+1) {
				html = $(html).splice(0,the_offset);
				$("#next_tab3").show();
			}
			$("#the_table_tabs3").append(html);
			$("#reload_tab3").removeAttr("disabled");
		});
	});
	
	$("#next_tab3").bind('click', function(e){
		prepareRefresh(e);
		the_base = the_base + 5; //increase the base
		$.ajax({type: "GET",
			url: "./lib/db/local/queryASTRO.php",
			data: {userid: getUserId(), select: 1, base: the_base, offset: the_offset+1},
			dataType: "html"
		}).done(function(html){
			if($(html).length == the_offset+1) {
				html = $(html).splice(0, the_offset);
				$("#next_tab3").show();
			}
			$("#prev_tab3").show();
			$("#the_table_tabs3").append(html);
			$("#reload_tab3").removeAttr("disabled");
		});
	});
	
	$("#prev_tab3").bind('click', function(e){
		prepareRefresh(e);
		the_base = the_base - 5; //decrease the base
		$.ajax({type: "GET",
			url: "./lib/db/local/queryASTRO.php",
			data: {userid: getUserId(), select: 1, base: the_base, offset: the_offset},
			dataType: "html"
		}).done(function(html){
			$("#the_table_tabs3").append(html);
			$("#reload_tab3").removeAttr("disabled");
			if(the_base == 0){
				$("#prev_tab3").hide();
				$("#next_tab3").show();
			}else{
				$("#prev_tab3").show();
				$("#next_tab3").show();		
			}
		});	
	});

	$("#reload_tab3").bind('click', function(e){
		prepareRefresh(e);
		the_base = 0;
		$.ajax({type: "GET",
			url: "./lib/db/local/queryASTRO.php",
			data: {userid: getUserId(), select: 1, base: the_base, offset: 5},
			dataType: "html"
		}).done(function(html){
			$("#the_table_tabs3").append(html);
			$("#reload_tab3").removeAttr("disabled");
			$("#next_tab3").show();
		});		
	});

	$("#my_delete_tab3").bind('click', function(){
		$("#my_delete_tab3").attr("disabled", "disabled");
		
		$("#the_table_tabs3").find("input[type=checkbox][name=delete_tabs3]").each(function(){
			if(this.checked==true){
				var id = $(this).attr("value");
				
				$.ajax({type: "POST", url: "./lib/db/local/queryASTRO.php", data: {id: id, update: 1}});
				$(this).parent().parent().remove();
			}
		});
		
		$("#my_delete_tab3").removeAttr("disabled");
	});
	
	$("input[name=copy_to_direct]").livequery('click', function(){		
		var query_copy = $(this).parent().parent().find('a').text();
		var query_survey = $(this).parent().parent().prev().text();
		if(query_survey == "SDSS"){
			$("#radio_sdss_tab2").trigger('click');
		}else if(query_survey == "FIRST"){
			$("#radio_first_tab2").trigger('click');
		}else if(query_survey == "LSST"){
			$("#radio_lsst_tab2").trigger('click');
		}else{
			;
		}
		$("#textarea_tab2").trigger('click');
		$("#textarea_tab2").val(query_copy);
		$("#textarea_tab2").keyup();
		$('a[href="#tabs-2"]').trigger('click');
	});
};