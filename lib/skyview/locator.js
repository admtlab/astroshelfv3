(function(){
	var setupLocator = function locator() {
		this.on = false;
		this.show = function() {
			var canvas = document.getElementById("locator_canvas");
			var width = $("#locator_canvas").width();
			var height = $("#locator_canvas").height();
			var context = canvas.getContext("2d");
			context.strokeStyle = "rgb(51,204,255)";
			context.lineWidth = 4;
			
			var path = new Path2D();
			path.arc(width/2, height/2, 50, .25*(Math.PI), .75*(Math.PI), false);
			context.stroke(path);
			
			path = new Path2D();
			path.arc(width/2, height/2, 50, .91*(Math.PI), 1.41*(Math.PI), false);
			context.stroke(path);
			
			path = new Path2D();
			path.arc(width/2, height/2, 50, 1.57*(Math.PI), 2.07*(Math.PI), false);
			context.stroke(path);
			
			path = new Path2D();
			path.moveTo(width/2, height/2 + 50 + 20);
			path.lineTo(width/2, height/2 + 80 + 20);
			path.moveTo(width/2, height/2 - 50 - 20);
			path.lineTo(width/2, height/2 - 80 - 20);
			context.stroke(path);
			
			this.on = true;
		};
		
		this.hide = function(on) {
			var canvas = document.getElementById("locator_canvas");
			var context = canvas.getContext("2d");
			context.clearRect(0, 0, canvas.width, canvas.height);
			
			this.on = false;
		};
		
		this.showHide = function() {
			if(!this.on) {
				this.show();
			} else {
				this.hide();
			}
		};
	};
	
	this.locator = new setupLocator();
})();