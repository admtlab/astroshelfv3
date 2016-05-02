/*
Di Bao
Summer 2012
The JS file for Search Objects side tab
*/
jQuery.fn.search_obj_guided = function(){
	
	$("#copy_inner").click(function(){
		var survey = $('#survey_set > p > input[type="radio"]:checked').val();
		var content = $("#" + survey + "_query_display").find("p").text();
		window.prompt("Copy to clipboard: Ctrl+C / Cmd+C, Enter", content)
	});
	
	/*-------------Caching jQuery objects-------------*/
	var $CACHING = $CACHING || {SDSS:{}, FIRST:{}, LSST:{}};
	
	$CACHING.SDSS.checkbox_obj = $("#SDSS_form1").find('input[value="p.objid"]');
	$CACHING.SDSS.checkbox_radec = $("#SDSS_form1").find('input[value="p.ra, p.dec"]');
	$CACHING.SDSS.checkbox_all = $("#SDSS_form1").find('input[name="all_check"]');
	$CACHING.SDSS.checkbox_none = $("#SDSS_form1").find('input[name="none_check"]');
	$CACHING.SDSS.checkbox_default = $("#SDSS_form1").find('input[name="default_check"]');
	$CACHING.SDSS.checkbox_selsts = $("#SDSS_form1").find('input[name="selsts"]');
	$CACHING.SDSS.query = $("#SDSS_query_display").find('p');
	
	$CACHING.FIRST.checkbox_radec = $("#FIRST_form1").find('input[value="RA, Declination"]');
	$CACHING.FIRST.checkbox_all = $("#FIRST_form1").find('input[name="all_check"]');
	$CACHING.FIRST.checkbox_none = $("#FIRST_form1").find('input[name="none_check"]');
	$CACHING.FIRST.checkbox_default = $("#FIRST_form1").find('input[name="default_check"]');
	$CACHING.FIRST.checkbox_selsts = $("#FIRST_form1").find('input[name="selsts"]');
	$CACHING.FIRST.query = $("#FIRST_query_display").find('p');
	
	$CACHING.LSST.checkbox_obj1 = $("#LSST_form1").find('input[value="refObjectId"]');
	$CACHING.LSST.checkbox_radec1 = $("#LSST_form1").find('input[value="ra, decl"]');
	$CACHING.LSST.checkbox_obj2 = $("#LSST_form1").find('input[value="objectId"]');
	$CACHING.LSST.checkbox_radec2 = $("#LSST_form1").find('input[value="ra_PS, decl_PS"]');
	$CACHING.LSST.checkbox_all = $("#LSST_form1").find('input[name="all_check"]');
	$CACHING.LSST.checkbox_none = $("#LSST_form1").find('input[name="none_check"]');
	$CACHING.LSST.checkbox_default = $("#LSST_form1").find('input[name="default_check"]');
	$CACHING.LSST.checkbox_selsts = $("#LSST_form1").find('input[name="selsts"]');
	$CACHING.LSST.query = $("#LSST_query_display").find('p');
	/*-------------End---------------*/
	
	/*-------------Define useful functions--------------*/
	var FUNCS = FUNCS || {};
	
	FUNCS.addBtn1 = function(the_survey){
		if(the_survey == "SDSS"){
			var $handler = $CACHING.SDSS;
		}else if(the_survey == "FIRST"){
			var $handler = $CACHING.FIRST;
		}else if(the_survey == "LSST"){
			var $handler = $CACHING.LSST;
		}else{
			;
		}
		
		var string = '';
		if(the_survey == "LSST"){
			var table = $("#LSST_table_select").find("option:selected").val();
			$handler.checkbox_selsts.each(function(index){
				if(table == " SimRefObject "){
					if(this.checked && index < 10)	string += (this.value + ', ');	
				}else if(table == " Object "){
					if(this.checked && index >= 10)	string += (this.value + ', ');
				}else{
					;
				}
			});		
		}else{
			$handler.checkbox_selsts.each(function(){
				if(this.checked)	string += (this.value + ', ');
			});
		}
		if(!string){
			alert("Invalid Parameters!");
		}else{
			string = string.substring(0, string.length-2);
			var query = $handler.query.text();
			if(the_survey == "SDSS"){
				var the_pos = query.indexOf("TOP");
				if(the_pos){
					var the_rep = query.indexOf("FROM");
                    console.log(the_rep);
					var output = query.substring(0, the_rep-1) + ' ' + string + ' ';
					output += query.substring(the_rep);
				}else{
					var output = query.substring(0, 6) + ' ' + string;
					output += query.substring(6);		
				}
			}else{
				var output = query.substring(0, 6) + ' ' + string;
				output += query.substring(6);	
			}
			//alert(output);
			$handler.query.text(output);
			$("#"+ the_survey +"_addbtn1").attr("disabled", "disabled");
			$("#"+ the_survey +"_table_select").attr("disabled", "disabled");
			if(the_survey == "SDSS" && menuIndex1 > 0){
				$("#search_res").removeAttr("disabled");//active the search button
			}else if(the_survey == "FIRST" && menuIndex2 > 0){
				$("#search_res").removeAttr("disabled");//active the search button
			}else if(the_survey == "LSST" && menuIndex3 > 0){
				$("#search_res").removeAttr("disabled");//active the search button
			}else{
				;
			}
			
			//section extend and shrink
			$("#condi_set").show(500);
			$("#limit_set").show(500);
			$("#section3").find('label').html("-- Conditions:");
			$("#section6").find('label').html("-- Limitation:");
		}
	};
	
	FUNCS.addBtn2 = function(the_survey){
		if(the_survey == "SDSS"){
			var $handler = $CACHING.SDSS;
			var menuIndex = menuIndex1;
			var parentElement = parentElement1;
		}else if(the_survey == "FIRST"){
			var $handler = $CACHING.FIRST;
			var menuIndex = menuIndex2;
			var parentElement = parentElement2;
		}else if(the_survey == "LSST"){
			var $handler = $CACHING.LSST;
			var menuIndex = menuIndex3;
			var parentElement = parentElement3;
		}else{
			;
		}

		menuIndex++;
		childElement='.content_'+menuIndex;
		//alert(childElement1);
		//alert($('#menu'+parentElement1).html());
		var iter = 0;
		var condi = '';
		var param = '';
		var oper = '';
		var val = '';
		
		$('#'+ the_survey +'_menu'+parentElement).find('select').each(function(){
			if(iter==0){
				if($handler.query.text().indexOf('WHERE ()') >= 0){
					condi = '';
				}else{
					condi = this.value;
				}
			}
			if(iter==1){
				param = this.value;
			}
			if(iter==2){
				oper = this.value;
			}
			iter++;
		});
		val = $('#'+ the_survey +'_menu'+parentElement).find('input[type="text"]').val();
		
		if(oper == "between"){
			var the_pattern = '[\s]*[,:][\s]*';
			var the_reg = new RegExp(the_pattern);
			var new_val = val.replace(the_reg, " and ");
			if(new_val == val){
				var the_pattern = '[ ]+';
				var the_reg = new RegExp(the_pattern);
				//alert(the_reg.exec(val));
				var val = val.replace(the_reg, " and ");
			}else{
				val = new_val;
			}
		}
		
		if(param && oper && val){
			var string =$handler.query.text();
			
			if(the_survey == "SDSS"){
				string = string.substring(0, string.length-1);
				string += condi+param+' '+oper+' '+val + ')';
			}else{
				var the_pos = string.indexOf("LIMIT");
				if(the_pos == -1){
					string = string.substring(0, string.length-1);
					string += condi+param+' '+oper+' '+val + ')';
				}else{
					the_tail = string.substring(the_pos);
					string = string.substring(0, the_pos-2);
					string += condi+param+' '+oper+' '+val + ') ' + the_tail;
					//alert(string);
				}		
			}
			
			$handler.query.text(string);
		
			var clone=$('#'+ the_survey +'_menu'+parentElement).clone(true);
			clone.attr('class','content_'+menuIndex);
			clone.insertAfter($('#'+ the_survey +'_menu'+parentElement));

			$(parentElement+' #'+ the_survey +'_addBtn2').attr("disabled","disabled");
			$("#"+ the_survey +"_table_select").attr("disabled", "disabled");
			if($("#"+ the_survey +"_addbtn1").attr("disabled")){
				$("#search_res").removeAttr("disabled");//active the search button
			}
			$(parentElement+' #'+ the_survey +'_condition_select').attr("disabled","disabled");
			$(parentElement+' #'+ the_survey +'_parameter_select').attr("disabled","disabled");
			$(parentElement+' #'+ the_survey +'_operation_select').attr("disabled","disabled");
			$(parentElement+' #'+ the_survey +'_value_input').attr("disabled","disabled");//disable the first row of selectors
			$(childElement+' #'+ the_survey +'_condition_select').removeAttr("disabled");//active the next condition selector
			$(childElement+' #'+ the_survey +'_value_input').val('');
				
			parentElement = childElement;
			
			if(the_survey == "SDSS"){
				$("#SDSS_extend1").hide();
				$("#SDSS_extend2").hide();//hide the explanations
			}else if(the_survey == "LSST"){
				$("#LSST_extend1").hide();//hide the explanations
			}else{
				;
			}
			
			//section extend and shrink
			$("#"+ the_survey +"_query_display").show(500);
			$("#copy_to_clipboard").show();
			$("#section4").find('label').html("-- SQL query:");
		}else{
			menuIndex--;
			alert("Invalid conditions - the query cannot be submitted.");
		}
		
		if(the_survey == "SDSS"){
			menuIndex1=  menuIndex;
			parentElement1 = parentElement;
		}else if(the_survey == "FIRST"){
			menuIndex2=  menuIndex;
			parentElement2 = parentElement;
		}else if(the_survey == "LSST"){
			menuIndex3 =  menuIndex;
			parentElement3 = parentElement;
		}else{
			;
		}	
	};

	FUNCS.minBtn2 = function(the_survey, $el){
		if(the_survey == "SDSS"){
			var $handler = $CACHING.SDSS;
		}else if(the_survey == "FIRST"){
			var $handler = $CACHING.FIRST;
		}else if(the_survey == "LSST"){
			var $handler = $CACHING.LSST;
		}else{
			;
		}
		//alert("here");
		var $curr_line = $el.parent().parent();
		//alert($curr_line.html());
		var prefix = $curr_line.find("#"+the_survey+"_condition_select").val();
		var fir_part = $curr_line.find("#"+the_survey+"_parameter_select").val();
		var sec_part = $curr_line.find("#"+the_survey+"_operation_select").val();
		var thr_part = $curr_line.find("#"+the_survey+"_value_input").val();
		if(sec_part == "between"){
			var the_pattern = '[\s]*[,:][\s]*';
			var the_reg = new RegExp(the_pattern);
			var new_val = thr_part.replace(the_reg, " and ");
			if(new_val == thr_part){
				var the_pattern = '[ ]+';
				var the_reg = new RegExp(the_pattern);
				var thr_part = thr_part.replace(the_reg, " and ");
			}else{
				thr_part = new_val;
			}
		}
		
		if(!$curr_line.find("#"+the_survey+"_addBtn2").attr("disabled")){
			//alert("here");
		}else{
			var str_to_remove = prefix + fir_part + ' ' + sec_part + ' ' + thr_part;
			//alert(str_to_remove);return;
			var original_str = $handler.query.text();
			var the_offset = original_str.indexOf(str_to_remove);
			if(the_offset == -1){
				//alert(str_to_remove.slice(0, -5)+ "hhh");
				if(original_str.indexOf(fir_part + ' ' + sec_part + ' ' + thr_part + ' AND ') != -1){
					the_offset = 1;
					str_to_remove = fir_part + ' ' + sec_part + ' ' + thr_part + ' AND ';
				}
				if(original_str.indexOf(fir_part + ' ' + sec_part + ' ' + thr_part + ' OR ') != -1){
					the_offset = 1;
					str_to_remove = fir_part + ' ' + sec_part + ' ' + thr_part + ' OR ';
				}
				if(original_str.indexOf(fir_part + ' ' + sec_part + ' ' + thr_part + ') AND (') != -1){
					the_offset = 1;
					str_to_remove = fir_part + ' ' + sec_part + ' ' + thr_part + ') AND (';
				}
				if(original_str.indexOf(fir_part + ' ' + sec_part + ' ' + thr_part + ') OR (') != -1){
					the_offset = 1;
					str_to_remove = fir_part + ' ' + sec_part + ' ' + thr_part + ') OR (';
				}
				//the_offset = original_str.indexOf(str_to_remove);
				if(the_offset == -1){
					//alert(str_to_remove.slice(0, -5)+ "ddd");
					str_to_remove = fir_part + ' ' + sec_part + ' ' + thr_part;
					the_offset = original_str.indexOf(str_to_remove);
				}
			}
			if(the_offset >= 0){
				//alert(the_offset);
				var new_str = original_str.replace(str_to_remove, '');
				//alert(new_str);
				$handler.query.text(new_str);
				$curr_line.hide();
			}
		}
	};
	
	FUNCS.search = function(the_survey){	
		var the_url = "";
		var the_table = "";
		
		if(the_survey == "SDSS"){
			var $handler = $CACHING.SDSS;
			the_url = "./lib/db/remote/searchSDSS.php";
			
			//Data subsample
			var sample_value = $("#SDSS_sample_select").find("option:selected").val();
			//sample_value = parseFloat(sample_value);
			var sample_clause = " AND (p.htmid*37 & 0x000000000000FFFF) < (650 * ";
			var sample = null;
			if(sample_value == "-1")	sample = "";
			else	sample = sample_clause + sample_value + ")";
			/*-----end-----*/
		}else if(the_survey == "FIRST"){
			var $handler = $CACHING.FIRST;
			the_url = "./lib/db/local/queryFIRST.php";
			
			//randomization
			var random_clause = " ORDER BY RAND()";
			var random = null;
			if($("#FIRST_random_check").attr("checked"))	random = random_clause;
			else	random = "";
			/*-----end-----*/
		}else if(the_survey == "LSST"){
			var $handler = $CACHING.LSST;
			the_url = "./lib/db/remote/queryLSST.php";
			var the_table_pos = $handler.query.text().indexOf("SimRefObject");
			if(the_table_pos == -1){
				var the_table = "Object";
			}else{
				var the_table = "SimRefObject";
			}
			
			//randomization
			var random_clause = " ORDER BY RAND()";
			var random = null;
			if($("#LSST_random_check").attr("checked"))	random = random_clause;
			else	random = "";
			/*-----end-----*/
		}else{
			;
		}
		
		$("#radio_sdss").attr("disabled", "disabled");
		$("#radio_first").attr("disabled", "disabled");
		$("#radio_lsst").attr("disabled", "disabled");
		$("#search_res").attr("disabled", "disabled");
		$("#reset_input").attr("disabled", "disabled");
		$("#cancel_ajax").removeAttr("disabled");

		var the_query = $handler.query.text();
		if(the_survey == "SDSS")	the_query += sample;
		if(the_survey == "FIRST"){
			var pos = the_query.indexOf("LIMIT");
			if(pos == -1)	the_query += random;
			else{
				tmp = the_query.substring(0, pos-1) + random + the_query.substring(pos-1, the_query.length);
				the_query = tmp;
			}
		}
		if(the_survey == "LSST"){
			var pos = the_query.indexOf("LIMIT");
			if(pos == -1)	the_query += random;
			else{
				tmp = the_query.substring(0, pos-1) + random + the_query.substring(pos-1, the_query.length);
				the_query = tmp;
			}
		}
		
		/*--------use preference--------*/
		var preference = '';
		if($("#OBJ_pref").attr("checked")){
			preference = "get user's "+ the_survey +" catalog preference from REST API";
			alert(preference);
		}else{
			preference = '';
		}
		/*----end----*/
		
		var formatted_time = gFUNCS.create_time();
		//curr_time = curr_time.getHours() + ':' + curr_time.getMinutes() + ':' + curr_time.getSeconds();
		$("#results_content #tab1 p:last").hide();
		$("#results_content #tab1").append('<div></div>');
		$("#results_content #tab1 div:last").append('<label class="search_object_label"><b>Search Objects - request on '+ the_survey +' <br/>(' + formatted_time + ')</b></label>');
		$("#results_content #tab1 div:last").append('<p>Processing...</p><br/><br/><a href="#" class ="toggle">Hide/Show</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="remove">Remove from the list</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="overlay">Create overlay</a><br/><br/>');
		$("#results_content #tab1 div:last").append('<a href="#" class="thumbnail">Thumbnails</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="trendImage">Trend Image</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="storage">Store Result</a><br/>');
			//<a href="#" class="download" style="visibility:hidden">Download</a><hr/>
		$("#results_content #tab1 a:last").prev().prev().data("source", the_survey);
		$("#results_content #tab1 a:last").prev().prev().data("query", the_query);
		$("#results_content #tab1 a:last").prev().data("source", the_survey);
		$("#results_content #tab1 a:last").prev().data("query", the_query);
		$("#results_content #tab1 a:last").data("source", the_survey);
		$("#results_content #tab1 a:last").data("query", the_query);
		
		var data = {query: the_query, pref: preference, table: the_table};
		the_abort = $.ajax({
			type: "POST",
			url: the_url,
			data: {query: the_query, pref: preference, table: the_table},
			dataType: "json"
		}).done(function(json_msg){
			/*--------for the results tab----------*/
			if(json_msg.error){
				$("#results_content #tab1 div:last").find('p').html(json_msg["error"]);
				$("#results_content #tab1 div:last").find('a[class=overlay]').remove();
				$("#results_content #tab1 div:last").find('a[class=download]').remove();
				$("#results_content #tab1 div:last").find('a[class=thumbnail]').remove();
				$("#results_content #tab1 div:last").find('a[class=storage]').remove();
			}else{
				var the_id = the_survey + "_table" + restab_base.obj_base;
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
			$("#radio_sdss").removeAttr("disabled");
			$("#radio_first").removeAttr("disabled");
			$("#radio_lsst").removeAttr("disabled");
			$("#search_res").removeAttr("disabled");
			$("#cancel_ajax").attr("disabled", "disabled");
			$("#reset_input").removeAttr("disabled");
			if($('#object_tab').hasClass('open')){
				$('#object_handle').trigger("click");
			}
			if(!$('#results').hasClass('open')){
				$('#results_handle').trigger("click");
			}
		}).error(function(jqXHR, textStatus, errorThrown) {
			console.log(jqXHR);
			console.log(textStatus);
			console.log(errorThrown);
		});
		//query history
		gFUNCS.insert_query(the_survey, the_query);
	};
	
	FUNCS.reset = function(the_survey){
		if(the_survey == "SDSS"){
			var $handler = $CACHING.SDSS;
			var menuIndex = menuIndex1;
			menuIndex1 = 0;
			parentElement1='.content_0';	
			
			$handler.checkbox_selsts.attr('checked', false);
			$handler.checkbox_obj.attr('checked', true);
			$handler.checkbox_radec.attr('checked', true);
            $("#SDSS_table_select")[0].selectedIndex = 0;
		}else if(the_survey == "FIRST"){
			var $handler = $CACHING.FIRST;
			var menuIndex = menuIndex2;
			menuIndex2 = 0;
			parentElement2='.content_0';
			
			$handler.checkbox_selsts.attr('checked', false);
			$handler.checkbox_radec.attr('checked', true);
		}else if(the_survey == "LSST"){
			var $handler = $CACHING.LSST;
			var menuIndex = menuIndex3;
			menuIndex3 = 0;
			parentElement3='.content_0';
			
			$handler.checkbox_selsts.attr('checked', false);
			$handler.checkbox_obj1.attr('checked', true);
			$handler.checkbox_radec1.attr('checked', true);
			$handler.checkbox_obj2.attr('checked', true);
			$handler.checkbox_radec2.attr('checked', true);
		}else{
			;
		}
		
		$handler.checkbox_all.attr('checked', false);
		$handler.checkbox_none.attr('checked', false);
		$handler.checkbox_default.attr('checked', true);
		
		$("#"+the_survey+"_addbtn1").removeAttr("disabled");
		
		//parentElement='.content_0';
		var currElement = '.content_'+menuIndex;
		while(menuIndex){
			$('#'+the_survey+'_menu'+currElement).remove();			
			menuIndex--;
			currElement = '.content_'+menuIndex;
		}
		$('#'+the_survey+'_menu.content_0').show();
		$(currElement+' #'+the_survey+'_addBtn2').removeAttr("disabled");
		$(currElement+' #'+the_survey+'_condition_select').attr("disabled","disabled");
		$(currElement+' #'+the_survey+'_parameter_select').removeAttr("disabled");
		$(currElement+' #'+the_survey+'_operation_select').removeAttr("disabled");
		$(currElement+' #'+the_survey+'_value_input').removeAttr("disabled");
		$(currElement+' #'+the_survey+'_parameter_select').val('');
		$(currElement+' #'+the_survey+'_operation_select').val('');
		$(currElement+' #'+the_survey+'_value_input').val('');
		
		$handler.query.text("SELECT");
		if(the_survey == "SDSS"){
			$handler.query.append(" FROM PhotoObj as p LEFT OUTER JOIN SpecObj as s ON p.objid = s.bestobjid");
		}else if(the_survey == "FIRST"){
			$handler.query.append(" FROM FIRSTcatalog");
		}else if(the_survey == "LSST"){
			$handler.query.append(" FROM SimRefObject");
		}else{
			;
		}
		$handler.query.append(" WHERE ()");
		
		//reset search button
		$("#search_res").attr("disabled", "disabled");
		$("#OBJ_pref").removeAttr("checked");
		$("#"+the_survey+"_limit_select option[value='unlimited']").attr("selected", true);
		$("#"+the_survey+"_table_select").removeAttr("disabled");
		
		if(the_survey == "SDSS")	$("#SDSS_sample_select option[value='-1']").attr("selected", true);
		if(the_survey == "FIRST")	$("#FIRST_random_check").removeAttr("checked");
		if(the_survey == "LSST")	$("#LSST_random_check").removeAttr("checked");
		
		if(the_survey == "LSST"){
			$("#LSST_table_select option[value=' SimRefObject ']").attr("selected", true);
			$("#LSST_form1 table").find('tr').each(function(index){
				if(index > 3 && index < 9){
					$(this).hide();
				}else{
					$(this).show();
				}
			});
			$("#LSST_parameter_select").find('option').each(function(index){
				if(index > 19){
					$(this).hide();
					$(this).attr("disabled", true);
				}else{
					$(this).show();
					$(this).removeAttr("disabled");
				}
			});
		}

		$("#survey_set").show(500);
		$("#table_set").show(500);
		$("#param_set").show(500);
		$("#condi_set").hide();
		$("#limit_set").hide();
		$("#SDSS_query_display").hide();
		$("#FIRST_query_display").hide();
		$("#LSST_query_display").hide();
		$("#copy_to_clipboard").hide();
		
		$("#section1").find('label').html("-- Surveys:");
		$("#section5").find('label').html("-- From:");
		$("#section2").find('label').html("-- Parameters:");
		$("#section3").find('label').html("+ Conditions:");
		$("#section6").find('label').html("+ Limitation:");
		$("#section4").find('label').html("+ SQL Query:");
	};
	/*-------------End---------------*/
	
	/*-----setups------*/	
	var menuIndex1=0;
	var parentElement1='.content_0';//for SDSS
	var menuIndex2=0;
	var parentElement2='.content_0';//for FIRST
	var menuIndex3=0;
	var parentElement3='.content_0';//for LSST
	var the_abort;//to abort the current ajax call
	
	//layout the sections
	$("#survey_set").show();
	$("#table_set").hide();
	$("#param_set").hide();
	$("#condi_set").hide();
	$("#limit_set").hide();
	
	//hide the FIRST and LSST forms
	$("#tabs-1").find("form").hide();
	$("#SDSS_form1").show();
	$("#SDSS_form2").show();
	$("#SDSS_form3").show();
	$("#SDSS_form4").show();

	//initialize checkboxs in form1
	$CACHING.SDSS.checkbox_obj.attr('checked', true);
	$CACHING.SDSS.checkbox_radec.attr('checked', true);
	$CACHING.FIRST.checkbox_radec.attr('checked', true);
	$CACHING.LSST.checkbox_obj1.attr('checked', true);
	$CACHING.LSST.checkbox_radec1.attr('checked', true);
	$CACHING.LSST.checkbox_obj2.attr('checked', true);
	$CACHING.LSST.checkbox_radec2.attr('checked', true);	
	
	$CACHING.SDSS.checkbox_obj.attr('disabled', true);
	$CACHING.SDSS.checkbox_radec.attr('disabled', true);
	$CACHING.FIRST.checkbox_radec.attr('disabled', true);
	$CACHING.LSST.checkbox_obj1.attr('disabled', true);
	$CACHING.LSST.checkbox_radec1.attr('disabled', true);
	$CACHING.LSST.checkbox_obj2.attr('disabled', true);
	$CACHING.LSST.checkbox_radec2.attr('disabled', true);

	//manually, don't know the reason of abnormal...
	$("#FIRST_form1").find('input[value="Ps"]').attr('checked', false);
	$("#FIRST_form1").find('input[value="Ps"]').removeAttr('disabled');
	$("#SDSS_form1").find('input[value="dbo.fSpecClassN(s.specclass) as specclass"]').attr('checked', false);
	$("#SDSS_form1").find('input[value="dbo.fSpecClassN(s.specclass) as specclass"]').removeAttr('disabled');
	$("#SDSS_form1").find('input[value="s.zconf"]').attr('checked', false);
	$("#SDSS_form1").find('input[value="s.zconf"]').removeAttr('disabled');
	
	$("#SDSS_condition_select").attr("disabled","disabled");
	$("#FIRST_condition_select").attr("disabled","disabled");
	$("#LSST_condition_select").attr("disabled","disabled");
	$("#SDSS_extend1").hide();
	$("#SDSS_extend2").hide();
	$("#LSST_extend1").hide();
	
	$("#SDSS_table_select").removeAttr("disabled");
	$("#FIRST_table_select").removeAttr("disabled");
	$("#LSST_table_select").removeAttr("disabled");
	$("#SDSS_limit_select").removeAttr("disabled");
	$("#FIRST_limit_select").removeAttr("disabled");
	$("#LSST_limit_select").removeAttr("disabled");
	
	$("#copy_to_clipboard").hide();
		
	$CACHING.SDSS.query.text("SELECT");
	$CACHING.SDSS.query.append(" FROM PhotoObj as p LEFT OUTER JOIN SpecObj as s ON p.objid = s.bestobjid");
	$CACHING.SDSS.query.append(" WHERE ()");//the generalized query structure for SDSS
	$CACHING.FIRST.query.text("SELECT");
	$CACHING.FIRST.query.append(" FROM FIRSTcatalog");
	$CACHING.FIRST.query.append(" WHERE ()");//the generalized query structure for FIRST
	$CACHING.LSST.query.text("SELECT");
	$CACHING.LSST.query.append(" FROM SimRefObject");
	$CACHING.LSST.query.append(" WHERE ()");//the generalized query structure for LSST
	$("#SDSS_query_display").hide();
	$("#FIRST_query_display").hide();
	$("#LSST_query_display").hide();

	$("#search_res").attr("disabled","disabled");//disable the search button
	$("#cancel_ajax").attr("disabled", "disabled");//disable the cancel button
	
	//LSST two tables' initialization
	$("#LSST_table_select option[value=' SimRefObject ']").attr("selected", true);
	$("#LSST_form1 table").find('tr').each(function(index){
		if(index > 3 && index < 9){
			$(this).hide();
		}else{
			$(this).show();
		}
	});
	$("#LSST_parameter_select").find('option').each(function(index){
		if(index > 19){
			$(this).hide();
			$(this).attr("disabled", true);
		}else{
			$(this).show();
			$(this).removeAttr("disabled");
		}
	});
	/*-------end--------*/
	
	/*------for the drop-downs--------*/
	$("#tabs-1 > div > label").bind('click', function(){
		var $label = $(this);
		var change_text = ($label.html().substring(0,1) == "+");
		if(change_text){
			$label.html("--" + $label.html().substring(1));
		}else{
			$label.html("+" + $label.html().substring(2));
		}
		//$label.html((change_text == "+") : ("--" + $label.html().substring(1)) ? ("+" + $label.html().substring(2)));
		$(window).resize();//for zcilp
		
		if($label.parent().attr('id') == "section4"){
			var the_survey = $('#survey_set > p > input[type="radio"]:checked').val();
			$("#"+ the_survey +"_query_display").slideToggle(500);
			$("#copy_to_clipboard").slideToggle(500);
		}else{
			$label.next().next().slideToggle(500);
		}
	});
	/*-------end---------*/
	
	/*------for section1, the surveys--------*/
	$("#survey_set > p > input[type='radio']").bind('click', function(){
		var the_survey = $(this).val();
		$("#tabs-1").find("form").hide();
		$("#" + the_survey + "_form1").show();
		$("#" + the_survey + "_form2").show();
		$("#" + the_survey + "_form3").show();
		$("#" + the_survey + "_form4").show();
		
		//reset the sections
		$("#survey_set").show();
		$("#table_set").hide();
		$("#param_set").hide();
		$("#condi_set").hide();
		$("#limit_set").hide();
		$("#SDSS_query_display").hide();
		$("#FIRST_query_display").hide();
		$("#LSST_query_display").hide();
		
		$("#copy_to_clipboard").hide();
		
		$("#section1").find('label').html("-- Surveys:");
		$("#section5").find('label').html("+ From:");
		$("#section2").find('label').html("+ Parameters:");
		$("#section3").find('label').html("+ Conditions:");
		$("#section6").find('label').html("+ Limitation:");
		$("#section4").find('label').html("+ SQL Query:");

		//reset the search button
		if(the_survey == "SDSS"){
			var menuIndex = (menuIndex1 != 0);
		}else if(the_survey == "FIRST"){
			var menuIndex = (menuIndex2 != 0);
		}else if(the_survey == "LSST"){
			var menuIndex = (menuIndex3 != 0);
		}else{
			;
		}
		
		if(menuIndex && $("#" + the_survey + "_addbtn1").attr("disabled")){
			$("#search_res").removeAttr("disabled");//active the search button
		}else{
			$("#search_res").attr("disabled", "disabled");//disable the search button
		}		
	});
	/*---------end-----------*/
	
	//For SDSS
    
    $("#SDSS_table_select").bind('change', function(){
        var old_query = $CACHING.SDSS.query.text();
        console.log(old_query);
        var startIndex = old_query.indexOf("FROM") + 5;
        var endIndex = old_query.indexOf("s.bestobjid") + 11;
        
        var selection = $(this).find("option:selected").val();
        var new_query = old_query.substring(0, startIndex) + selection + old_query.substring(endIndex);
        $CACHING.SDSS.query.text(new_query);
    });
	
	/*----for section2, the parameters----*/
	$CACHING.SDSS.checkbox_all.bind('click', function(){
		//alert("click target test!");
		$CACHING.SDSS.checkbox_none.attr('checked', false);
		$CACHING.SDSS.checkbox_default.attr('checked', false);		
		$CACHING.SDSS.checkbox_selsts.attr('checked', true);
	});//checkbox all
	
	$CACHING.SDSS.checkbox_none.bind('click', function(){
		//alert("click target test!");
		$CACHING.SDSS.checkbox_all.attr('checked', false);
		$CACHING.SDSS.checkbox_default.attr('checked', false);
		$CACHING.SDSS.checkbox_selsts.attr('checked', false);
		//for the demo propuse
		$CACHING.SDSS.checkbox_obj.attr('checked', true);
		$CACHING.SDSS.checkbox_radec.attr('checked', true);
	});//checkbox none
	
	$CACHING.SDSS.checkbox_default.bind('click', function(){
		//alert("click target test!");
		$CACHING.SDSS.checkbox_all.attr('checked', false);
		$CACHING.SDSS.checkbox_none.attr('checked', false);
		$CACHING.SDSS.checkbox_selsts.attr('checked', false);
		$CACHING.SDSS.checkbox_obj.attr('checked', true);
		$CACHING.SDSS.checkbox_radec.attr('checked', true);
	});//checkbox default
	
	//submit button for section2
	$("#SDSS_addbtn1").bind('click', function(){FUNCS.addBtn1("SDSS");});
	/*-------end--------*/
	
	/*for section3, the conditions*/
	//the submit button for section3
	$("#SDSS_addBtn2").bind('click', function(){FUNCS.addBtn2("SDSS");});
	
	$("#SDSS_minBtn2").livequery('click', function(){FUNCS.minBtn2("SDSS", $(this));});
	
	//trigger the explanation for the parameter selector
	$("#SDSS_parameter_select").bind('change', function(){
		var param = $(this).find("option:selected").val();
		//alert(condi);
		$("#SDSS_extend1").hide();
		$("#SDSS_extend2").hide();
		if(param=="p.type"){
			$("#SDSS_extend1").show(500);
		}else if(param=="s.specclass"){
			$("#SDSS_extend2").show(500);
		}else{
			;
		}
	});

	//For FIRST
	
	/*----for section2, the parameters----*/
	$CACHING.FIRST.checkbox_all.bind('click', function(){
		//alert("click target test!");
		$CACHING.FIRST.checkbox_none.attr('checked', false);
		$CACHING.FIRST.checkbox_default.attr('checked', false);		
		$CACHING.FIRST.checkbox_selsts.attr('checked', true);
	});//checkbox all
	
	$CACHING.FIRST.checkbox_none.bind('click', function(){
		//alert("click target test!");
		$CACHING.FIRST.checkbox_all.attr('checked', false);
		$CACHING.FIRST.checkbox_default.attr('checked', false);
		$CACHING.FIRST.checkbox_selsts.attr('checked', false);
		//for the demo propuse
		$CACHING.FIRST.checkbox_radec.attr('checked', true);
	});//checkbox none
	
	$CACHING.FIRST.checkbox_default.bind('click', function(){
		//alert("click target test!");
		$CACHING.FIRST.checkbox_all.attr('checked', false);
		$CACHING.FIRST.checkbox_none.attr('checked', false);
		$CACHING.FIRST.checkbox_selsts.attr('checked', false);
		$CACHING.FIRST.checkbox_radec.attr('checked', true);
	});//checkbox default
	
	//the submit button for section2
	$("#FIRST_addbtn1").bind('click', function(){FUNCS.addBtn1("FIRST");});
	/*--------end---------*/
	
	/*for section3, the conditions*/
	//the submit button for section3
	$("#FIRST_addBtn2").bind('click', function(){FUNCS.addBtn2("FIRST");});
	
	$("#FIRST_minBtn2").livequery('click', function(){FUNCS.minBtn2("FIRST", $(this));});
	/*--------end---------*/

	//For LSST
	
	/*----for section2, the parameters----*/
	$CACHING.LSST.checkbox_all.bind('click', function(){
		//alert("click target test!");
		$CACHING.LSST.checkbox_none.attr('checked', false);
		$CACHING.LSST.checkbox_default.attr('checked', false);		
		$CACHING.LSST.checkbox_selsts.attr('checked', true);
	});//checkbox all
	
	$CACHING.LSST.checkbox_none.bind('click', function(){
		//alert("click target test!");
		$CACHING.LSST.checkbox_all.attr('checked', false);
		$CACHING.LSST.checkbox_default.attr('checked', false);
		$CACHING.LSST.checkbox_selsts.attr('checked', false);
		//for the demo propuse
		$CACHING.LSST.checkbox_obj1.attr('checked', true);
		$CACHING.LSST.checkbox_radec1.attr('checked', true);
		$CACHING.LSST.checkbox_obj2.attr('checked', true);
		$CACHING.LSST.checkbox_radec2.attr('checked', true);
	});//checkbox none
	
	$CACHING.LSST.checkbox_default.bind('click', function(){
		//alert("click target test!");
		$CACHING.LSST.checkbox_all.attr('checked', false);
		$CACHING.LSST.checkbox_none.attr('checked', false);
		$CACHING.LSST.checkbox_selsts.attr('checked', false);
		$CACHING.LSST.checkbox_obj1.attr('checked', true);
		$CACHING.LSST.checkbox_radec1.attr('checked', true);
		$CACHING.LSST.checkbox_obj2.attr('checked', true);
		$CACHING.LSST.checkbox_radec2.attr('checked', true);
	});//checkbox default
	
	//the submit button for section2
	$("#LSST_addbtn1").bind('click', function(){FUNCS.addBtn1("LSST");});
	/*--------end---------*/
	
	/*for section3, the conditions*/
	//the submit button for section3
	$("#LSST_addBtn2").bind('click', function(){FUNCS.addBtn2("LSST");});
	
	$("#LSST_minBtn2").livequery('click', function(){FUNCS.minBtn2("LSST", $(this));});
	
	//trigger the explanation for the parameter selector
	$("#LSST_parameter_select").bind('change', function(){
		var param = $(this).find("option:selected").val();
		//alert(condi);
		if(param=="isStar"){
			$("#LSST_extend1").show(500);
		}else{
			$("#LSST_extend1").hide();
		}
	});
	
	$("#LSST_table_select").bind('change', function(){
		var table = $(this).find("option:selected").val();
		//alert(table);
		var flag = (table == " SimRefObject ");
		
		$("#LSST_form1 table").find('tr').each(function(index){
			if(index <= 3) $(this)[flag ? 'show' : 'hide']();
			if(index > 3 && index < 9) $(this)[flag ? 'hide' : 'show']();
			if(index == 9)	$(this).show();
		});
		
		$("#LSST_parameter_select").find('option').each(function(index){
			if(index == 0){
				$(this).show();
				$(this).removeAttr("disabled");
			}
			
			if(index > 0 && index <=19){
				$(this)[flag ? 'show' : 'hide']();
				flag ? ($(this).removeAttr("disabled")) : ($(this).attr("disabled", true));
			}
			
			if(index > 19){
				$(this)[flag ? 'hide' : 'show']();
				flag ? ($(this).attr("disabled", true)) : ($(this).removeAttr("disabled"));
			}
		});
		
		var old_query = $CACHING.LSST.query.text();
		if(flag){
			var new_query = old_query.replace("FROM Object WHERE", "FROM SimRefObject WHERE");
		}else{
			var new_query = old_query.replace("FROM SimRefObject WHERE", "FROM Object WHERE");
		}
		$CACHING.LSST.query.text(new_query);
	});
	/*--------end---------*/

	//for section 6, the limitation of queries
	$("#SDSS_limit_select").bind('change', function(){
		var limit = $(this).find("option:selected").val();
		if(limit == "unlimited")	limit = " ";
		var old_query = $CACHING.SDSS.query.text();
		//alert(condi);
		var top_array = [" TOP 100 ", " TOP 1000 ", " TOP 10000 ", " TOP 100000 "];
		var the_pos = old_query.indexOf("TOP");
		//alert(old_query);
		if(the_pos == -1){
			var new_query = old_query.substring(0, 6) + limit.substring(0,limit.length-1) + old_query.substring(6);
		}else{
			for(var i = top_array.length-1; i >= 0; i--){
				if(top_array[i] == limit)	continue;
				if(old_query.indexOf(top_array[i]) != -1){
					//alert("Get: "+top_array[i]);
					var new_query = old_query.replace(top_array[i], limit);
					break;
				}
			}
		}
		$CACHING.SDSS.query.text(new_query);
	});
	
	$("#FIRST_limit_select").bind('change', function(){
		var limit = $(this).find("option:selected").val();
		if(limit == "unlimited")	limit = " ";
		var old_query = $CACHING.FIRST.query.text();
		//alert(condi);
		var the_pos = old_query.indexOf("LIMIT");
		if(the_pos == -1){
			var new_query = old_query + limit.substring(0, limit.length-1);
		}else{
			var new_query = old_query.substring(0, the_pos-1) + limit.substring(0, limit.length-1);
		}
		$CACHING.FIRST.query.text(new_query);
	});
	
	$("#LSST_limit_select").bind('change', function(){
		var limit = $(this).find("option:selected").val();
		if(limit == "unlimited")	limit = " ";
		var old_query = $CACHING.LSST.query.text();
		//alert(condi);
		var the_pos = old_query.indexOf("LIMIT");
		if(the_pos == -1){
			var new_query = old_query + limit.substring(0, limit.length-1);
		}else{
			var new_query = old_query.substring(0, the_pos-1) + limit.substring(0, limit.length-1);
		}
		$CACHING.LSST.query.text(new_query);
	});
	
	//for the search button
	$("#search_res").bind('click', function(){		
		if($("#radio_sdss:checked").val()!=null)	FUNCS.search("SDSS");
		if($("#radio_first:checked").val()!=null)	FUNCS.search("FIRST");
		if($("#radio_lsst:checked").val()!=null)	FUNCS.search("LSST");
	});
	/*-----end------*/
	
	//for the cancel button
	$("#cancel_ajax").bind('click', function(){
		the_abort.abort();
		$("#results_content #tab1 div:last").find('p').html('Cancelled...');
		$("#results_content #tab1 div:last").find('a[class=overlay]').remove();
		$("#results_content #tab1 div:last").find('a[class=download]').remove();
		$("#results_content #tab1 div:last").find('a[class=thumbnail]').remove();
		$("#results_content #tab1 div:last").find('a[class=storage]').remove();
		$("#radio_sdss").removeAttr("disabled");
		$("#radio_first").removeAttr("disabled");
		$("#radio_lsst").removeAttr("disabled");
		
		$("#radio_sdss_tab2").removeAttr("disabled");
		$("#radio_first_tab2").removeAttr("disabled");
		$("#radio_lsst_tab2").removeAttr("disabled");//weird influence...
		
		$("#search_res").removeAttr("disabled");
		$("#cancel_ajax").attr("disabled", "disabled");
		$("#reset_input").removeAttr("disabled");
	});
	/*---------end----------*/
	
	//for the reset buttion
	$("#reset_input").bind('click', function(){
		if($("#radio_sdss:checked").val()!=null)	FUNCS.reset("SDSS");
		if($("#radio_first:checked").val()!=null)	FUNCS.reset("FIRST");
		if($("#radio_lsst:checked").val()!=null)	FUNCS.reset("LSST");		
	});
	/*-----end------*/
};