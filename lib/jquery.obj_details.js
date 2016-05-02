/*
Di Bao
Summer 2012
The JS file for Object details side tab
*/
jQuery.fn.obj_details = function(){

	/*----tooltip for Link----*/
	$('a[class=link]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to learn more from other source'},
		   position: {my: 'top right', at: 'bottom right', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});

	$("a[class=link]").livequery('click', function(e){
		e.preventDefault();
		
		var survey = $(this).attr('name');
		if(survey=="SDSS"){
			var objid = $(this).attr('rel');
			var url = "http://cas.sdss.org/astrodr7/en/tools/explore/obj.asp?id=" + objid;
			var windowName = "More details on SDSS";
			var windowSize = "width=800,height=800,scrollbars=yes";
			window.open(url, windowName, windowSize);
		}else if(survey=="FIRST"){
			var the_ra_dec = $(this).attr('rel');
			var url = "http://third.ucllnl.org/cgi-bin/firstcutout?RA=" + the_ra_dec + "&Dec=&Equinox=J2000&ImageSize=4.5&MaxInt=10";
			var windowName = "More details on FIRST";
			var windowSize = "width=800,height=800,scrollbars=yes";
			window.open(url, windowName, windowSize);			
		}else if(survey=="LSST"){
			var the_ra_dec = $(this).attr('rel');
			/*The LSST details -- the source of it has not be found yet*/
			alert("LSST Object: "+the_ra_dec);
		}else{
			alert("error!");
		}
	});

	/*----tooltip for ADD Annotation----*/
	$('a[class=add_anno]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to annotate above object'},
		   position: {my: 'top left', at: 'bottom right', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});
	
	$("a[class=add_anno]").livequery('click', function(e){
		e.preventDefault();
		var the_user_id = getUserId();
		if(the_user_id == -1) {
			alert('Please log in first...');
			return;
		}

		var survey = $(this).attr('name');
		var objid = $(this).attr('rel');
		var the_ra = $(this).data("the_ra");
		var the_dec = $(this).data("the_dec");
		if(survey == "SDSS") {
			var local_obj = {};
			$.ajax({
				type: 'GET',
				url: secureRESTbase + "object/sdss/" + objid,
				beforeSend: setAuthHeaders,
				success: function(json) {
					if(!json){
						alert("SDSS object: "+objid+" cannot be found in local database...");
					} else {
                        $("#annotate_dialog").annotateDialog("setTargetObj", json);
                        $("#annotate_dialog").annotateDialog("open");	
					}
				},
				error: function() {
					console.log("ERROR quering REST API...");
				},
				dataType: "json",
				crossDomain: true
			});
		} else if(survey == "FIRST") {
			alert("Annotate FIRST Object");
		}else if(survey == "LSST"){
			alert("Annotate LSST Object");
		}else{
			$("#annotate_dialog").annotateDialog("setTargetObj", {annoId: objid});
			$("#annotate_dialog").annotateDialog("open");
		}
	});

	/*----tooltip for SHOW Annotation----*/
	$('a[class=show_anno]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to retrieve annotations associated with above object'},
		   position: {my: 'top left', at: 'bottom left', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});
	
	$("a[class=show_anno]").livequery('click', function(e){
		e.preventDefault();
	
		var survey = $(this).attr('name');
		var objid = $(this).attr('rel');
		var the_ra = $(this).data("the_ra");
		var the_dec = $(this).data("the_dec");
		if(survey == "SDSS"){
			var cal = "ra=" + the_ra + "&dec=" + the_dec;
			var formatted_time = gFUNCS.create_time();
			$("#results_content #tab1 p:last").hide();
			$("#results_content #tab1").append('<div></div>');
			$("#results_content #tab1 div:last").append('<label class="search_annotation_label"><b>Search Annotations - request on AnnoDB <br/>(' + formatted_time + ')</b></label>');
			$("#results_content #tab1 div:last").append('<p>Processing...</p><br/><br/><a href="#" class ="toggle">Hide/Show</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="remove">Remove from the list</a>');
			$("#results_content #tab1 div:last").append('&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="overlay">Create overlay</a><hr/>');
			$("#results_content #tab1 a:last").data("source", "ANNO");
			$("#results_content #tab1 a:last").data("query", cal);
			
			ANNO_abort = $.ajax({type: "GET",
                url: secureRESTbase + "annotation/search",
                beforeSend: setAuthHeaders,
                data: cal,
                dataType: "json",
                success: function(json){
                    if(json.length==0){
                        $("#results_content #tab1 div:last").find('p').html('No rows returned...');
                        $("#results_content #tab1 div:last").find('a[class=overlay]').remove();
                    }else{		
                        annotations_fn.displayResult(json);
                    }
                },
                error: function(){
                    $("#results_content #tab1 div:last").find('p').html('Internal Server Error...');
                    $("#results_content #tab1 div:last").find('a[class=overlay]').remove();
                },
                crossDomain: true
			});
			
			if($('#object_tab').hasClass('open')){
				$('#object_handle').trigger("click");
			}
			if(!$('#results').hasClass('open')){
				$('#results_handle').trigger("click");
			}
		}else if(survey == "FIRST"){
			alert("Show Annotation on FIRST Object");
		}else if(survey == "LSST"){
			alert("Show Annotate on LSST Object");
		}else{
			;
		}
	});
	
	$('a[class=add_bookmark]').livequery(function(){
		$(this).qtip({
		   content: {text: 'click to bookmark above object'},
		   position: {my: 'top left', at: 'bottom right', target: $(this)},
		   style: {classes: 'ui-tooltip-blue ui-tooltip-shadow'},
		   show: {event: 'mouseover'},
		   hide: {event: 'mouseout'}
		});
	});
	
	$("a[class=add_bookmark]").livequery('click', function(e){
		e.preventDefault();
		var the_user_id = getUserId();
		if(the_user_id == -1) {
			alert('Please log in first...');
			return;
		}
		
		var data = $(this).data();
		if(!data.ra || !data.dec) {
			data.ra = $(this).parent().find("td:contains('ra')").next().text();
			data.dec = $(this).parent().find("td:contains('dec')").next().text();
		}
		$("#bookmark_dialog").bookmarkDialog("setTarget", data);
		$("#bookmark_dialog").bookmarkDialog("open");
	});
};