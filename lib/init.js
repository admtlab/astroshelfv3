/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*-------- Create Overlay-------------*/
//type: SDSS, FIRST, ANNO
$.fn.newOverlay = function(curr_time, source, infoArray, field, fMax, fMin, color, label){

	/*---this is the testing code by Di, please delete it later---*/	
	// return;
	/*---end---*/
		
	/* put here to fix the 'no method .replace' issue cause by the new time format */
		
	var curr_time_clean = curr_time.toLocaleTimeString();	
	var id = curr_time_clean.replace(" ","_");
	id = id.replace(/:/g,"_");
        var $block = $("<div/>").addClass("sliderBlock").attr("id",id);
        var $label = $("<div/>").addClass("sliderLabel").text(label);
        var $sliderElement = $("<div/>").addClass("slider").attr("id",id+"_slider"); 
        var $icon = $("<div/>").addClass("ui-icon ui-icon-circle-close").css('position', 'relative').css('top', '7px');
	
        $block.append($label);
        $block.append($sliderElement);
        $block.append($icon);
        $(this).append($block);
	
        $label.livequery
		


		//console.log(infoArray);
       /////////////////////////////////////////////////
       // compute min and max of RA of data subset
       var maxRa = parseFloat(infoArray[0][0] );
       var minRa = parseFloat(infoArray[0][0] );
	   
       var maxDec= parseFloat(infoArray[0][1] );
       var minDec = parseFloat(infoArray[0][1] );
	   
       for(var i=1; i<infoArray.length; i++){
          if( parseFloat(infoArray[i][0]) > maxRa)	
		  	maxRa = parseFloat(infoArray[i][0]);
          if(infoArray[i][0] < minRa)	
		  	minRa = infoArray[i][0].slice(0);
       }

       for(var i=1; i<infoArray.length; i++){
		   if(infoArray[i][1] > maxDec) 
		   		maxDec = parseFloat(infoArray[i][1]);
     	   	if(parseFloat(infoArray[i][1]) < minDec)	
				minDec = parseFloat(infoArray[i][1]);
       }
    var overlayObj = new CustomOverlay(skyView, color, infoArray, label);			   
    
    //click on overlay name to change attributes
    $label.bind("click", function(){
	//console.log("click label");
	overlayObj.customize();

      var $dialog = $("#customize_overlay");
      var select_content = "<option value=''>select</option>";
      $dialog.empty();
      $dialog.html(
	  "<form><br/><label>Name:&nbsp;&nbsp;</label><input id='new_oname'/><br/><br/>" + 
	  "<label>Symbol Shape:&nbsp;&nbsp;</label><select id='new_oshape' style='width:45%;overflow:hidden;text-overflow:ellipsis'>" + select_content + "</select><br/><br/>" + 
	  "<label>Symbol Size:&nbsp;&nbsp;</label>" + 
	  "<label>Symbol Color:&nbsp;&nbsp;</label><input class='new_ocolor' value='#cc3333'/></form>"
      );

      $('.new_ocolor', $dialog).simpleColor({
	  boxWidth: 65,
	  cellWidth: 12,
	  cellHeight: 12,
	  livePreview: true
      });

      $dialog.dialog({
	  autoOpen: true,
	  draggable: true,
	  resizeable: false,
	  title: 'Customize the Overlay',
	  buttons:{
	      "Customize": function(){
		  var color = $('.new_ocolor', $dialog).val();
		  color = color.replace(/\#/g,"");
		  var label = $("#new_oname", $dialog).val();
	      },
	      "Cancel": function(){
		  $(this).dialog("close");
	      }
	  }
      });

    });

    //delete a custom overlay
       $icon.bind("click", function(){
           $block.remove();
           overlayObj.deleteOverlay(); 
       });
    
    //initialize the slider and add the overlay to the existing overlay array
       var sel = "#"+id + " .slider";
       var $sl = $( sel );
       $sl.slider({
           range: "min",
           value: 100,
           min: 1,
           max: 100,
           slide: function( event, ui ){
               overlayObj.setAlpha(ui.value/100);
           }
       });
	   skyView.addOverlay(overlayObj);
}
/*-------- end ---------*/

function initSkyView(skyViewHeight) {
	$('#skyPanelDiv').height(skyViewHeight+'px');

	skyView = new window.SkyView(document.getElementById("skyPanelDiv"));
	
	var components = URI.parse(document.URL);
        if(components.query != null){
	    var query = URI.parseQuery(components.query);
	    if(query.debug == "true"){
		if(query.sdss == "true"){
	    	    SDSSOverlay = new Overlay(skyView, "SDSS", null, "");
		    skyView.addOverlay(SDSSOverlay);
		}
		if(query.lsst == "true"){
		    LSSTOverlay = new Overlay(skyView, "LSST", null, "");	
		    skyView.addOverlay(LSSTOverlay);
		}
		if(query.first == "true"){
		    FIRSTOverlay = new Overlay(skyView, "FIRST", null, "");
		    skyView.addOverlay(FIRSTOverlay);
		}

		if((query.ra != undefined && query.ra != null) && (query.dec != undefined && query.ra != null)){
		    skyView.jump(parseFloat(query.ra), parseFloat(query.dec));
		}
		if(query.scale != undefined && query.scale != null){
		    skyView.setScale(parseFloat(query.scale));
		}
	    }
	}
        else{
	    SDSSOverlay = new Overlay(skyView, "SDSS", null, "");
	    FIRSTOverlay = new Overlay(skyView, "FIRST", null, "");
	    LSSTOverlay = new Overlay(skyView, "LSST", null, "");	
	    
	    skyView.addOverlay(SDSSOverlay);
	    skyView.addOverlay(FIRSTOverlay);
	    skyView.addOverlay(LSSTOverlay);
		    
	}
        URI.parse

	skyView.mouseHandler();
	skyView.render();

	$('.editable').editable(function(value, settings){                     
		if($(this).attr('id') == "RA-Dec") {
			var raDec = new Array();
			var re = new RegExp("([0-9]+)[\\s:h]+([0-9]+)[\\s:m]+([0-9]+\.?[0-9]*)[s]?[\\s,]+([-+]?)*([0-9]+)[\\s:d]+([0-9]+)[\\s:m]+([0-9]+\\.?[0-9]*)[s]?");
			var m = re.exec(value);
			console.log(value);
			console.log(m);
			if(m==null){            
				if(value.indexOf(",") > 0)
					raDec = value.split( "," );
				else
					raDec = value.split( " " );
			}else{               
				raDec = convertRaDec(m[1], m[2], m[3], m[5], m[6], m[7], m[4]);
			}
			skyView.jump(parseFloat(raDec[0]), parseFloat(raDec[1]));
		}else if($(this).attr('id') == "Scale"){
			var scale = parseFloat(value);
			skyView.setScale(scale);
		}else
			return(value);
	}, { 
		type: 'text',
		style: 'display: inline; size: 10'
	});
}

function initLocator() {
	$('#skyPanelDiv').append($('<canvas id="locator_canvas" width="'+$('#skyPanelDiv').width()+'" height="'+$('#skyPanelDiv').height()+'"></canvas>'));
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$.fn.textWidth = function(){
    var html_org = $(this).html();
    var html_calc = '<span>' + html_org + '</span>';
    $(this).html(html_calc);
    var width = $(this).find('span:first').width();
    $(this).html(html_org);
    return width;
};

$.fn.loading = function(on){
    if(on){
        this.empty();
        this.addClass("loading");
    } else {
        this.removeClass("loading");
    }
}

function initCrosshairButton() {
	$("#crosshairs").button({label:"Crosshairs", icons: {primary:"ui-icon-carat-2-e-w"}});
	$("#crosshairs").button().on("click", function(){
        locator.showHide();
    });
}

function initGroupManagementButton() {
	
	$.ajax({
		type: "GET",
		url: "./lib/db/local/queryASTRO.php",
		data: { get_role:1, user_id:getUserId() }
	}).done(function(json_msg) {
		if(json_msg == "admin") {
			$("#crosshairs").after('<button id="groupmanage"></button>');
			$("#groupmanage").button({label:"Group Management", icons:{primary:"ui-icon-person"}});
			$("#groupmanage_dialog").dialog({
				title: "Group Management",
				draggable: false,
				resizable: false,
				autoOpen: false,
				height: 400,
				width: 600
			});
			$("#groupmanage").button().click(function(){
				$("#groupmanage_dialog").dialog("open");
			});
			
			$.ajax({
				type:"GET",
				url:"./lib/db/local/queryASTRO.php",
				data: { get_groupmanage_info:1 },
				dataType:"json"
			}).done(function(data){
				var default_str = " selected";
				var groupList = "";
				for(var i = 0; i < data.groups.length; i++) {
					if(i == 1) {
						default_str = "";
					}
					groupList += "<option value='" + data.groups[i].id + "'" + default_str + ">" + data.groups[i].name + "</option>";
				}
				$("#groupmanage_dialog_group_list").html(groupList);
				var userList = "";
				default_str = " selected";
				for(var i = 0; i < data.users.length; i++) {
					if(i == 1) {
						default_str = "";
					}
					userList += "<option value='" + data.users[i].id + "'" + default_str + ">" + data.users[i].username + "</option>";
				}
				$("#groupmanage_dialog_user_list").html(userList);
				
				$("#groupmanage_dialog_group").html($("#groupmanage_dialog_group_list option:selected").text());
				$("#groupmanage_dialog_user").html($("#groupmanage_dialog_user_list option:selected").text());
				$("#groupmanage_dialog_group_list").change(function() {
					$("#groupmanage_dialog_group").html($("#groupmanage_dialog_group_list option:selected").text());
				});
				$("#groupmanage_dialog_user_list").change(function() {
					$("#groupmanage_dialog_user").html($("#groupmanage_dialog_user_list option:selected").text());
				});
				
				$("#groupmanage_dialog_add_button").click(function() {
					var addButton = $(this);
					addButton.prop("disabled", true);
					$("#groupmanage_dialog_add_result").html("");
					$("#groupmanage_dialog").css("cursor", "wait");
					$("#groupmanage_dialog *").css("pointer-events", "none");
					$.ajax({
						type: "GET",
						url:"./lib/db/local/queryASTRO.php",
						data: {
							add_user_to_group: 1,
							user: $("#groupmanage_dialog_user").text(),
							group: $("#groupmanage_dialog_group").text(),
							user_id: Number($("#groupmanage_dialog_user_list option:selected").val()),
							group_id: Number($("#groupmanage_dialog_group_list option:selected").val())
						}
					}).done(function(data) {
						console.log(data);
						addButton.prop("disabled", false);
						$("#groupmanage_dialog_add_result").html(data);
						$("#groupmanage_dialog").css("cursor", "default");
						$("#groupmanage_dialog *").css("pointer-events", "auto");
					}).error(function(data, textStatus, errorThrown) {
						console.log(data);
						console.log(textStatus);
						console.log(errorThrown);
						addButton.prop("disabled", false);
						$("#groupmanage_dialog_add_result").html("The AJAX request failed.");
						$("#groupmanage_dialog").css("cursor", "default");
						$("#groupmanage_dialog *").css("pointer-events", "auto");
					});
				});
				
			}).error(function(data, textStatus, errorThrown) {
				console.log(data);
				console.log(textStatus);
				console.log(errorThrown);
			});
		}
	}).error(function(data, textStatus, errorThrown) {
		console.log(data);
		console.log(textStatus);
		console.log(errorThrown);
	});
	
}

function initCounter(){
    $("#notifications_dialog").dialog({
		title: "LiveAnnotations Notifications",
        draggable: false,
		resizable: false,
        autoOpen: false,
        height: 400,
        width: 400
    });
	
    $("#counter").notycount({onClick: function(counter){
        counter.resetCounter();
        var position = $(counter.element).offset();
        var top = position.top + $(counter.element).height() + 8;
        var left = window.innerWidth - 400 - 10;
        $("#notifications_dialog").dialog("option", "position", [left, top]);
        $("#notifications_dialog").dialog("open");
    }});
}

function initOverlayTab(){
	$("#SDSS_slider").slider({
		range: "min",
		value: 100,
		min: 1,
		max: 100,
		slide: function( event, ui ){ 
			SDSSOverlay.setAlpha(ui.value/100); 
			skyView.dirty = true;
			skyView.refresh();
		}
	});
	
	FIRSTOverlay.setAlpha(1/100.0);
	$("#FIRST_slider").slider({
			range: "min",
			value: 1,
			min: 1,
			max: 100,
			slide: function( event, ui ){ 
				FIRSTOverlay.setAlpha(ui.value/100.0);
				skyView.dirty = true; 
				skyView.refresh();
			}
		});
	
	LSSTOverlay.setAlpha(1/100);
	$("#LSST_slider").slider({
			range: "min",
			value: 1,
			min: 1,
			max: 100,
			slide: function( event, ui ){ 
				LSSTOverlay.setAlpha(ui.value/100); 
				skyView.dirty = true;
				skyView.refresh();	
			}
		});
}

function initTabs(skyViewHeight){
		
	var containerHeight = skyViewHeight - 80;
	var trendHeight = (containerHeight) / 2.0;
	
	$('#search_object').tabSlideOut({
		tabHandle: '#so_handle',                     //class of the element that will become your tab
		//pathToTabImage: 'images/contact_tab.gif', //path to the image for the tab //Optionally can be set using css
		//imageHeight: '122px',                     //height of tab image           //Optionally can be set using css
		//imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
		tabLocation: 'left',                      //side of screen where tab lives, top, right, bottom, or left
		speed: 300,                               //speed of animation
		action: 'click',                          //options: 'click' or 'hover', action to trigger animation
		topPos: '20px',                          //position from the top/ use if tabLocation is left or right
		leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
		containerHeight: containerHeight+'px',
		fixedPosition: false                      //options: true makes it stick(fixed position) on scroll
	});	

	$('#search_annotation').tabSlideOut({
		tabHandle: '#sa_handle',                     //class of the element that will become your tab
		//pathToTabImage: 'images/contact_tab.gif', //path to the image for the tab //Optionally can be set using css
		//imageHeight: '122px',                     //height of tab image           //Optionally can be set using css
		//imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
		tabLocation: 'left',                      //side of screen where tab lives, top, right, bottom, or left
		speed: 300,                               //speed of animation
		action: 'click',                          //options: 'click' or 'hover', action to trigger animation
		topPos: '160px',                          //position from the top/ use if tabLocation is left or right
		leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
		containerHeight: containerHeight+'px',
		fixedPosition: false                      //options: true makes it stick(fixed position) on scroll
	});

	$('#bookmarks').tabSlideOut({
		tabHandle: '#bookmarks_handle',                     //class of the element that will become your tab
		//pathToTabImage: 'images/contact_tab.gif', //path to the image for the tab //Optionally can be set using css
		//imageHeight: '122px',                     //height of tab image           //Optionally can be set using css
		//imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
		tabLocation: 'left',                      //side of screen where tab lives, top, right, bottom, or left
		speed: 300,                               //speed of animation
		action: 'click',                          //options: 'click' or 'hover', action to trigger animation
		topPos: '330px',                          //position from the top/ use if tabLocation is left or right
		leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
		containerHeight: containerHeight+'px',
		fixedPosition: false                      //options: true makes it stick(fixed position) on scroll
	});

	$('#overlays').tabSlideOut({
		tabHandle: '#overlays_handle',                     //class of the element that will become your tab
		//pathToTabImage: 'images/contact_tab.gif', //path to the image for the tab //Optionally can be set using css
		//imageHeight: '122px',                     //height of tab image           //Optionally can be set using css
		//imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
		tabLocation: 'right',                      //side of screen where tab lives, top, right, bottom, or left
		speed: 300,                               //speed of animation
		action: 'click',                          //options: 'click' or 'hover', action to trigger animation
		topPos: '10px',                          //position from the top/ use if tabLocation is left or right
		leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
		containerHeight: containerHeight+'px',
		fixedPosition: false                      //options: true makes it stick(fixed position) on scroll
	});

	$('#results').tabSlideOut({
		tabHandle: '#results_handle',                     //class of the element that will become your tab
		//pathToTabImage: 'images/contact_tab.gif', //path to the image for the tab //Optionally can be set using css
		//imageHeight: '122px',                     //height of tab image           //Optionally can be set using css
		//imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
		tabLocation: 'right',                      //side of screen where tab lives, top, right, bottom, or left
		speed: 300,                               //speed of animation
		action: 'click',                          //options: 'click' or 'hover', action to trigger animation
		topPos: '100px',                          //position from the top/ use if tabLocation is left or right
		leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
		containerHeight: containerHeight+'px',
		fixedPosition: false                      //options: true makes it stick(fixed position) on scroll
	});

	$('#object_tab').tabSlideOut({
		tabHandle: '#object_handle',                     //class of the element that will become your tab
		//pathToTabImage: 'images/contact_tab.gif', //path to the image for the tab //Optionally can be set using css
		//imageHeight: '122px',                     //height of tab image           //Optionally can be set using css
		//imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
		tabLocation: 'right',                      //side of screen where tab lives, top, right, bottom, or left
		speed: 300,                               //speed of animation
		action: 'click',                          //options: 'click' or 'hover', action to trigger animation
		topPos: '180px',                          //position from the top/ use if tabLocation is left or right
		leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
		containerHeight: containerHeight+'px',
		fixedPosition: false                      //options: true makes it stick(fixed position) on scroll
	});

	$('#trend_tab').tabSlideOut({
		tabHandle: '#trend_handle',                     //class of the element that will become your tab
		//pathToTabImage: 'images/contact_tab.gif', //path to the image for the tab //Optionally can be set using css
		//imageHeight: '122px',                     //height of tab image           //Optionally can be set using css
		//imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
		tabLocation: 'right',                      //side of screen where tab lives, top, right, bottom, or left
		speed: 300,	                               //speed of animation
		action: 'click',                          //options: 'click' or 'hover', action to trigger animation
		topPos: '305px',                          //position from the top/ use if tabLocation is left or right
		leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
		containerHeight: containerHeight+'px',
		fixedPosition: false                      //options: true makes it stick(fixed position) on scroll
	});
	
	$('#thumbnail').tabSlideOut({
		tabHandle: '#thumbnail_handle',
		tabLocation: 'right',
		speed: 300,
		action: 'click',
		topPos: '425px',
		leftPos: '20px',
		containerHeight: containerHeight+'px',
		fixedPosition: false
	});

	//hover states on the static widgets
	$('.handle').hover(
		function() {$(this).addClass('ui-state-hover');}, 
		function() {$(this).removeClass('ui-state-hover');}
	);
}
