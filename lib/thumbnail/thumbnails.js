var thumbnails = new Array();
var curr_thumb = null;
var curr_page = null;
var num_pages = null;

function scroll(dir){
	
	var lastPage = "page" + (num_pages);
	var firstPage = "page0";
	
	/* iterate next page */
	var previousPage = "page" + curr_page;
	
	$("#thumbnail_canvas").css("cursor", "default");
	
	if(dir == "next"){
		
		var nextPage = "page" + (++curr_page);
		console.log(nextPage);
		
		/* if off the first page, enable previous */
		if(previousPage == firstPage){
			$("#thumbnail_canvas").setLayer("previous", {
			  disableEvents:false,
			  fillStyle: "#00ffff"
			})
			.drawLayer("previous");
		}
				
		/* if on the last page, disable next */
		if(nextPage == lastPage){

			$("#thumbnail_canvas").setLayer("next", {
			  disableEvents:true,
			  fillStyle: "#a9a9a9"
			})
			.drawLayer("next");
		}
		
		/* set the last page invisible */

		$("#thumbnail_canvas").setLayerGroup(previousPage, {
			visible:false
		})
		.drawLayers();

		/* set the next page visible */

		$("#thumbnail_canvas").setLayerGroup(nextPage, {
			visible:true
		})
		.drawLayers();	
	}
	
	else if(dir == "previous"){
		
		var nextPage = "page" + (--curr_page);
		console.log(nextPage);
		
		/* if off the last page, enable next */
		if(previousPage == lastPage){
			$("#thumbnail_canvas").setLayer("next", {
			  disableEvents:false,
			  fillStyle: "#00ffff"
			})
			.drawLayer("next");
		}
		
		if(nextPage == firstPage){
			
			$("#thumbnail_canvas").setLayer("previous", {
			  disableEvents:true,
			  fillStyle: "#a9a9a9"
			})
			.drawLayer("previous");
		}
		
		/* set the last page invisible */
		$("#thumbnail_canvas").setLayerGroup(previousPage, {
			visible:false
		})
		.drawLayers();
		
		/* set the next page visible */
		$("#thumbnail_canvas").setLayerGroup(nextPage, {
			visible:true
		})
		.drawLayers();
			
	}
}

function viewThumbnails(raDecTable, skyView) {	  
	
    var table_size = raDecTable.length;
    var the_url = "http://skyservice.pha.jhu.edu/DR10/ImgCutout/getjpeg.aspx?";
     
	var c = document.getElementById("thumbnail_canvas");
	
	/* set height to match the parent */
	
	w = document.getElementById("thumbnail").clientWidth - 20; 
	h = document.getElementById("thumbnail").clientHeight - 40; 
	
	c.width = w;
	c.height = h;
	
	/* starting position of thumbnails */
	var mx = 35, my = 20;
	
	/* set flag to render first layer */
	curr_page = 0;
	
	/* number of layers and pages */
	num_pages = 0;
	
	/* init first layer name */
	
	var myPage = "page"+num_pages;
	var id = 0;
	
	/* iterate over the table results to get the thumbnails */
	for (var i=0; i < table_size; i++) {
		
		/* url of thumbnail */
		
		url = the_url + "ra=" + raDecTable[i][0] + "&" + "dec=" 
			+ raDecTable[i][1] + "&" +"scale=0.60" + "&" + "width=120" + "&" 
			+ "height=120" + "&opt=" + "&" + "query=";
	         
		/* create a new image and set its source to the url */
		
		var image = new Image();
	        
			image.src = url;
			image.bounds = { left: mx, right: mx+130, 
							 top: my, bot: my+130 };
			image.pageNum = myPage;
			image.ra = raDecTable[i][0];
			image.dec = raDecTable[i][1];
			image.objid = raDecTable[i][2];
			image.onload = function(){
				
				/* render */
				 $("#thumbnail_canvas")
					.drawImage({
						/* layer properties */
						layer: true,
						visible: (this.pageNum == "page0") ? true:false,
						name: this.pageNum + "_" + id++,
						group: this.pageNum,
						/* image properties */
						source: this.src,
					  	x: this.bounds['left'],
						y: this.bounds['top'],
						ra: this.ra,
						dec: this.dec,
						fromCenter:false,
					
						/* mouse events */
						
						 cursor: "pointer",
						
						 click: function(layer){
							skyView.jump(parseFloat(layer.ra), parseFloat(layer.dec));
							locator.show();
						},
						mousedown:function(layer){
						    doMouseDown(layer.event);
						},
						mouseover: function(layer) {
							$("#thumbnailCrosshairs").css({
								"top": (layer.y + 11) + "px",
								"left": (layer.x + 11) + "px",
								"display": "block"
							});
						},
						mousemove: function(layer) {
							$("#thumbnailCrosshairs").css({
								"top": (layer.y + 11) + "px",
								"left": (layer.x + 11) + "px",
								"display": "block"
							});
						},
						mouseout: function(layer) {
							$("#thumbnailCrosshairs").css({
								"display": "none"
							});
						},
					});
				};			
		
		/* position in the sky */					
		image.pos = {RA: raDecTable[i][0], Dec: raDecTable[i][1] };
	
		/* add thumbnail to array */
		thumbnails.push(image);
		
		/* move to the right */
	     mx += 130;
	        
		/* if off canvas, go to next row */
		if (mx > (c.width-130)) {
				my += 130;
	            mx = 35;
	    }
		/* rendering went off bottom */ 
		if(my > (c.height-130)){
			
			/* next layer */
			num_pages++;
			myPage = "page"+num_pages;
			
			id = 0;
			my = 20;
		}
	} // end for   
	
 	/* buttons to scroll between images */

	$("#thumbnail_canvas").drawText({
	  layer: true,
	  name: "previous",
	  disableEvents: true,
	  fillStyle: "#a9a9a9",
	  strokeStyle: "#000",
	  strokeWidth: 1,
	  x: 40, y: h-20,
	  text: "previous",
	  font: "12pt 'Trebuchet MS', sans-serif",
	  cursor: "pointer",
	  // Click link to open it
	  click: function(layer) {
	    scroll(layer.text);
	  }
	});
	
	$("#thumbnail_canvas").drawText({
	  layer: true,
	  name: "next",
	  fillStyle: "#00ffff",
	  strokeStyle: "#000",
	  strokeWidth: 1,
	  x: w-30, y: h-20,
	  text: "next",
	  font: "12pt 'Trebuchet MS', sans-serif",
	  cursor: "pointer",
	  // Click link to open it
	  click: function(layer) {
	    scroll(layer.text);
	  }
	});
	
};

function doMouseDown(event) {      
	
	/* fit the mouse into canvas space */
	var canvas_bounds = document.getElementById("thumbnail_canvas").getBoundingClientRect();
	var x = event.clientX - canvas_bounds.left;
	var y = event.clientY - canvas_bounds.top;
	    
      /* iterate over thumbnails and check if mouse click was within an image */
      for(var i = 0; i < thumbnails.length; i++){
			
      		if(x >= thumbnails[i].bounds['left'] && x <= thumbnails[i].bounds['right']
      			&& y >= thumbnails[i].bounds['top'] && y <= thumbnails[i].bounds['bot'])
      		{		
      		    curr_thumb = thumbnails[i];
				console.log(thumbnails[i].pos);
				break;
      		}
			else{
				curr_thumb = null;
			}
		}
};
