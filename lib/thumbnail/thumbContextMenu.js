var contextObjects;
var clickPos;
/*------- set-ups for right-click context menu---------*/
$(function(){
	//asynchronous click handler
	$('.thumbnail-context-menu').on('mouseup', function(e){
		if(e.which == 3){
			
			if(curr_thumb == null)
				return;
			
			var $this = $(this);
			clickPos = {x: e.clientX, y: e.clientY}
			
			$this.data('runCallbackThingie', generateThumbContextMenu);
			createThumbMenu(clickPos, $this);			
		}
	});
    
	//setup context menu
	$.contextMenu({
		selector: '.thumbnail-context-menu',
		trigger: 'none',
		zIndex: 3,
		build: function($trigger, e){
			//pull a callback from the trigger
			return $trigger.data('runCallbackThingie')();
		}
	});
});

function createThumbMenu(position, $this){
        
        $this.contextMenu(position);  

		$('.context-menu-list').on("remove", function() {
			$("#thumbnail").addClass('just-closed-context-menu');
		});
    
} // end CreateMenu

function generateThumbContextMenu(){
        
    var menu = {
        "ra-dec":{
            name: "RA:" + parseFloat(curr_thumb.pos.RA).toFixed(5) +  " Dec:"
                + parseFloat(curr_thumb.pos.Dec).toFixed(5),
			callback: function(key, opt) {
				skyView.jump(curr_thumb.ra, curr_thumb.dec);
			}
        },
		"details":{
			name:"Object Details",
			icon: "info",
			callback: function(key, opt){
				FUNCS4.showSDSSDetails(curr_thumb.objid, curr_thumb.ra, curr_thumb.dec);
				$('#thumbnail_handle, #object_handle').trigger("click");
			}
		}
    };
       
    return{
        callback: function(key, options){
            var m = "clicked: " + key;
            window.console && console.log(m);
            if(key=="Ra Dec") {
             	console.log("ra dec clicked");
            }
			else if(key =="Delete"){
				
			}
        },
        items: menu
    }  
} // end generateContextMenu