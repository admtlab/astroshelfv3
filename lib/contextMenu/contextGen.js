var contextRa;
var contextDec;
var contextObjects;
var objectsIndexByAnnotLabel = new Array();
var objectsIndexByDetailsLabel = new Array();
var objectsIndexByBookmarkLabel = new Array();
var stillProcessing = false;

/*------- set-ups for right-click context menu---------*/
$(function(){
	//asynchronous click handler
	$('.skyPanel-context-menu').on('mouseup', function(e){
		if(e.which == 3){
			var $this = $(this);

			var raDec = skyView.getCoordinate(e.clientX, e.clientY); //The convert here seems wrong!
			contextRa = raDec.x;
			contextDec = raDec.y;
			
			//var _offset = $this.offset(),
			position = {x: e.clientX, y: e.clientY}
			//store a callback on the trigger
			$this.data('generateContextMenu', generateContextMenu);
			getObjectsAndCreateMenu(raDec.x, raDec.y, position, $this);
		}
	});
	
	document.oncontextmenu = function() {
		return false;
	};
    
	//setup context menu
	$.contextMenu({
		selector: '.skyPanel-context-menu',
		trigger: 'none',
		zIndex: 3,
		build: function($trigger, e){
			//pull a callback from the trigger
			return $trigger.data('generateContextMenu')();
		}
	});
});

function getObjectsAndCreateMenu(ra, dec, position, $this){
    var scale = parseFloat($("#Scale").text());
    var ra_from = ra-(0.5*scale), ra_to = ra+(0.5*scale), dec_from = dec-(0.5*scale), dec_to = dec+(0.5*scale);
    
    if(!stillProcessing){
		if(isUserLoggedIn()) {
			stillProcessing = true;
			$("#skyPanelDiv").addClass("waiting");
			$.ajax({
				type: 'GET',
				url: secureRESTbase + "object/search/coordinates",
				data: "ra_from="+ra_from+"&ra_to="+ra_to+"&dec_from="+dec_from+"&dec_to="+dec_to,
				beforeSend: setAuthHeaders,
				success: function(data, textStatus, jqXHR) {
					$("#skyPanelDiv").removeClass("waiting");
					stillProcessing = false;
					contextObjects = data;
					$this.contextMenu(position);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#skyPanelDiv").removeClass("waiting");
					console.log("Error:  " + errorThrown);
					console.log(textStatus);
					console.log(jqXHR);
				},
				dataType: "json",
				crossDomain: true
			});
		} else {
			$("#login_dialog").dialog("open");
		}
    }
}

function generateContextMenu(){
    var ra = contextRa, dec = contextDec;
    var raDecLabel = "RA:" + ra.toFixed(6) + " Dec:" + dec.toFixed(6);
        
    var menu = {
        "ra-dec":{
            name: raDecLabel, 
            items:{
				"bookmark":{
					name: "Bookmark this location",
					icon: "bookmark",
					callback: function(key, opt) {
						var data = { type:"loc", ra: ra, dec: dec };
						$("#bookmark_dialog").bookmarkDialog("setTarget", data);
						$("#bookmark_dialog").bookmarkDialog("open");
					}
				}
			}
        },
        "annotate":{
            name: "Annotate RA and Dec", 
            disabled: !isUserLoggedIn(),
            icon: "tag",
			callback: function(){
				var ra_dec = {'RA': ra, 'Dec': dec};
				$("#annotate_dialog").annotateDialog("setTargetObj", ra_dec);
				$("#annotate_dialog").annotateDialog("open");
			}
        },
        "trend-image":{
            name: "Generate Trend Image",
            items:{
                "trend-Gen":{
					name:"Trend Image Gerator", 
					disabled:true
				},
                "num-objects":{
                    name:"Number of objects:",
                    type:"text"
                },
                "sep1": "---------",
                "trend-star":{
                    name: "Filter for Star",
                    callback: function(key, opt){
                        var $this = this;
                        var tmp = $.contextMenu.getInputValues(opt, $this.data());
                        var numObjects = tmp['num-objects'];
                        contextGenTrendImg(numObjects, 1);
                        if(!$('#trend_tab').hasClass('open')){
                            $('#trend_handle').trigger("click");
                        }
                    }
                },//code: 1
                "trend-galaxy":{
                    name: "Filter for Galaxies",
                    callback: function(key, opt){
                        var $this = this;
                        var tmp = $.contextMenu.getInputValues(opt, $this.data());
                        var numObjects = tmp['num-objects'];
                        contextGenTrendImg(numObjects, 2);
                        if(!$('#trend_tab').hasClass('open')){
                            $('#trend_handle').trigger("click");
                        }
                    }
                },//code: 2
                "trend-quasi":{
                    name: "Filter for Quasi-stellar",
                    callback: function(key, opt){
                        var $this = this;
                        var tmp = $.contextMenu.getInputValues(opt, $this.data());
                        var numObjects = tmp['num-objects'];
                        contextGenTrendImg(numObjects, 3);
                        if(!$('#trend_tab').hasClass('open')){
                            $('#trend_handle').trigger("click");
                        }
                    }
                }//code: 3
            }
        },
        "sep2": "---------"
    };
    
    if(contextObjects.length == 0)
        menu['no-obj'] = { name: "No objects found nearby", disabled:true };
    else{
		var annoMenuItem = {
			name:"Annotate",
			icon: "tag",
			disabled: !isUserLoggedIn(),
			callback: function(key, opt){
				var obj = objectsIndexByAnnotLabel[key];
				$("#annotate_dialog").annotateDialog("setTargetObj", obj);
				$("#annotate_dialog").annotateDialog("open");
			}
		};
		var detailMenuItem = {
			name:"Object Details",
			icon: "info",
			callback: function(key, opt){
				var obj = objectsIndexByDetailsLabel[key];
				FUNCS4.showSDSSDetails(obj.survey_obj_id, obj.ra, obj.dec);
			}
		};
		var bookmarkMenuItem = {
			name:"Bookmark Object",
			icon:"bookmark",
			disabled: !isUserLoggedIn(),
			callback: function(key, opt){
				var obj = objectsIndexByBookmarkLabel[key];
				var data = { type: "obj", id: obj.object_id, name: obj.name, ra: obj.ra, dec: obj.dec };
				$("#bookmark_dialog").bookmarkDialog("setTarget", data);
				$("#bookmark_dialog").bookmarkDialog("open");
			}
		};
        for(var i = 0; i < contextObjects.length; i++){
            var o = contextObjects[i];
            var itemsArray = {};
			var label = "obj"+i;
            
			var annotLabel = label+"-annot";
            objectsIndexByAnnotLabel[annotLabel] = o;
			itemsArray[annotLabel] = annoMenuItem;
            
            var detailsLabel = label+"-details";
            objectsIndexByDetailsLabel[detailsLabel] = o;
			itemsArray[detailsLabel] = detailMenuItem;
			
			var bookmarkLabel = label+"-bookmark";
			objectsIndexByBookmarkLabel[bookmarkLabel] = o;
			itemsArray[bookmarkLabel] = bookmarkMenuItem;
                    
            var objM = {name: o.name, items: itemsArray};
            menu[label] = objM;
        }
    }
    
    menu['sep3'] = "---------";
    menu['vis-area'] = {name: "Visible Area", disabled: true}; //TODO3: what is it?
    menu["annotate-area"] = {
        name:"Annotate Visible Area",
        disabled: !isUserLoggedIn(),
        icon: "tag",
        callback: function(){
            var box = skyView.getBoundingBox();
			console.log(box);
            $("#annotate_dialog").annotateDialog("setTargetObj", {"area-box":box});
			$("#annotate_dialog").annotateDialog("open");
        }
    };
       
    return{
        callback: function(key, options){
            var m = "clicked: " + key;
            window.console && console.log(m) || alert(m);
            if(key=="start-box") {
                skyView.setState(View.BOX);
            }
            else if(key.substring(0,5)=="trend") {
                var $this = this;
                // export states to data store
                var tmp = $.contextMenu.getInputValues(options, $this.data());
                // this basically dumps the input commands' values to an object
                // like {name: "foo", yesno: true, radio: "3", …}
                console.log(tmp['num-objects']);
            }
        },
        items: menu
    }  
}