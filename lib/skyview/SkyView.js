window.requestAnimFrame = (function(){
    return  window.requestAnimationFrame       ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame    ||
        function( callback ){
            window.setTimeout(callback, 1000 / 60);
        };
})();
var xhr_pool = []
var BoxOverlay, SkyView,
__bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
__hasProp = {}.hasOwnProperty,
__extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

SkyView = (function(_super) {

    __extends(SkyView, _super);

    SkyView.prototype.gridBlocks = 0;

    SkyView.prototype.rotation = null;

    SkyView.prototype.translation = null;

    SkyView.prototype.renderMode = 0;
	
    SkyView.prototype.refresh_timeout = 0;

    SkyView.prototype.Math = null;
    
	SkyView.prototype.previousAlpha = null;
	
	
    function SkyView(options) {
        // todo: Remove bind, somehow. We will figure this out!!!
        this.keyPressed = __bind(this.keyPressed, this);
        this.getCoordinate = __bind(this.getCoordinate, this);
        this.getPosition = __bind(this.getPosition, this);
        this.getBoundingBox = __bind(this.getBoundingBox, this);
        this.getBoundingBoxes = __bind(this.getBoundingBoxes, this);
        this.notify = __bind(this.notify, this);
        this.register = __bind(this.register, this);
        this.sky_view_mouse_down = __bind(this.sky_view_mouse_down, this);
        this.sky_view_mouse_up = __bind(this.sky_view_mouse_up, this);
        this.sky_view_mouse_move = __bind(this.sky_view_mouse_move, this);
        this.jump = __bind(this.jump, this);
        this.panScroll = __bind(this.panScroll, this);
        this.panUp = __bind(this.panUp, this);
        this.panMove = __bind(this.panMove, this);
        this.panDown = __bind(this.panDown, this);
        this.render = __bind(this.render, this);
        this.deleteOverlay = __bind(this.deleteOverlay, this);
        this.addOverlay = __bind(this.addOverlay, this);
        this.setScale = __bind(this.setScale, this);
		this.resizeHandler = __bind(this.resizeHandler, this);
        this.refresh = __bind(this.refresh, this);
		this.keys = __bind(this.keys, this);
		this.scrollUp = __bind(this.scrollUp, this);
        this.main_loop = __bind(this.main_loop, this);
				
        $.xhrPool = [];
        $.xhrPool.abortAll = function() {
            $(this).each(function(idx, jqXHR) {
                jqXHR.abort();
            });
            $.xhrPool.length = 0
        };

        $.ajaxSetup({
            beforeSend: function(jqXHR) {
                $.xhrPool.push(jqXHR);
            },
            complete: function(jqXHR) {
                var index = $.xhrPool.indexOf(jqXHR);
                if (index > -1) {
                    $.xhrPool.splice(index, 1);
                }
            }
        }); 
        
        this.empty = function() {};
        this.dirty = true;
        
        ////////////////////////////
	this.binder = options;
        this.event_attach = options;
        this.mouse_down = this.sky_view_mouse_down;
        this.mouse_up = this.sky_view_mouse_up;
        this.mouse_move = this.sky_view_mouse_move;
        this._inner_mouse_move = this.empty;
        this._inner_mouse_up = this.panUp;
        this._inner_mouse_down = this.panDown;
        SkyView.__super__.constructor.call(this, options);
        this.mouse_coords = {
            'x': 0,
            'y': 0
        };
        this.handlers = {
            'translate': this.empty,
            'scale': this.empty
        };
        this.translation = [0.0, 0.0, 0.99333];
        this.rotation = [-1.1, -1.7, 0.0];
        this.renderMode = this.gl.TRIANGLES;
        this.Math = new math();
	
        this.overlays = [];
        $('#RA-Dec').text((-this.rotation[1]).toFixed(8) + ", " + (-this.rotation[0]).toFixed(8));
        $('#Scale').text(((-this.translation[2] + 1) * 15).toFixed(2));
        mat4.identity(this.mvMatrix);
        mat4.perspective(45, this.gl.viewportWidth / this.gl.viewportHeight, 0.001, 100.0, this.pMatrix);
        this.gl.viewport(0, 0, this.gl.viewportWidth, this.gl.viewportHeight);
        this.gl.uniformMatrix4fv(this.shaderProgram.pMatrixUniform, false, this.pMatrix);
	// activate texture 0
	this.gl.activeTexture(this.gl.TEXTURE0);
        this.gl.pixelStorei(this.gl.UNPACK_FLIP_Y_WEBGL, true);
	
	this.setTransforms(this.translation, this.rotation);
	this.resizeHandler();
        this.main_loop();

        return;
    }

    SkyView.prototype.main_loop = function(){
        if(this.dirty){
            this.render();
            this.dirty = false;
        }
        window.requestAnimFrame(this.main_loop);
    };

    SkyView.prototype.refresh = function() {
        var overlay, i, refresh_required, j, count, point, range, true_points;
     
        // Refresh if it does
        for (i = 0; i < this.overlays.length; i++) {
            overlay = this.overlays[i];
	    if(overlay.alpha == 0.0) continue;
	    overlay.refresh();		

        }
    };

    SkyView.prototype.withinView = function(range, ra, dec, bounding_box){

	var range_bounds = {
	    RAMin: math.min(ra),
	    RAMax: math.max(ra),
	    DecMin: math.min(dec),
	    DecMax: math.max(dec)
	};

	var within_box = bounding_box.DecMax < range_bounds.DecMin || range_bounds.DecMax < bounding_box.DecMin || bounding_box.RAMax < range_bounds.RAMin || range_bounds.RAMax < bounding_box.RAMin;

	return !within_box;
    }
    
    SkyView.prototype.setScale = function(value) {
	if(value > 0.0001 && value < .75){
            $('#Scale').text(value.toFixed(2));
            this.translation[2] = (-value / 15.0) + 1.0;
            this.notify('scale', value);
            this.dirty = true;
	    this.refresh();	    
	}
	else{
	    alert("Scale can only be within the values of 0.01 to .75");
	}

    };

    SkyView.prototype.addOverlay = function(overlay) {
        this.overlays.push(overlay);
    };

    SkyView.prototype.deleteOverlay = function(name) {
	var filter = function(element){
	    return name !== element.name;
	}

	this.overlays = this.overlays.filter(filter);

    };

    SkyView.prototype.render = function() {
        var overlay, tile, i, j;
        
		this.gl.clear(this.gl.COLOR_BUFFER_BIT | this.gl.DEPTH_BUFFER_BIT);
	
		for (i = 0; i < this.overlays.length; i++) {
		    
		    overlay = this.overlays[i];
		    			
		    // overlay turned off
		    //if(overlay.alpha == 0.0) continue;
			
		    if(overlay.alpha != this.previousAlpha)
	    		this.gl.uniform1f(this.shaderProgram.alphaUniform, overlay.alpha);
		    else
				this.previousAlpha = overlay.alpha;
	  	    
		    if (overlay.survey === "SDSS") {
				this.gl.enable(this.gl.DEPTH_TEST);
				this.gl.disable(this.gl.BLEND);
				this.gl.uniform1f(this.shaderProgram.survey, 0.0);
		    } 
		    else if(overlay.survey === "custom"){
			overlay.draw();
			continue;
		    }
		    else {
				this.gl.disable(this.gl.DEPTH_TEST);
				this.gl.enable(this.gl.BLEND);
				this.gl.blendFunc(this.gl.SRC_ALPHA, this.gl.ONE);
				this.gl.uniform1f(this.shaderProgram.survey, 1.0);
		    }
		    
		    for (j = 0; j < overlay.tiles.length; j++) {
			tile = overlay.tiles[j];
			var bounding_boxes = this.getBoundingBoxes();
			
			if(this.withinView(tile.range, tile.ra, tile.dec, bounding_boxes[0]) || 
		  	   (bounding_boxes.length > 1 && this.withinView(tile.range, tile.ra, tile.dec, bounding_boxes[1]))){
		    	    tile.bind(this.shaderProgram);
		    	    tile.render(this.renderMode);
			}
			else{
		    	    tile.deleteTexture();
			}
		    }
		}
    };

    SkyView.prototype.panDown = function(event) {
		$("#skyPanelDiv").addClass("grabbing");
        this._inner_mouse_move = this.panMove;
        this.mouse_coords.x = event.clientX;
        this.mouse_coords.y = event.clientY;
        $.xhrPool.abortAll();
    };

    SkyView.prototype.panMove = function(event) {
        var delta_x, delta_y;
        delta_x = event.clientX - this.mouse_coords.x;
        delta_y = event.clientY - this.mouse_coords.y;
        this.mouse_coords.x = event.clientX;
        this.mouse_coords.y = event.clientY;
        if (delta_y > 0) {
            this.rotation[0] -= delta_y * Config.PAN_SENSITIVITY;
        } else if (delta_y < 0) {
            this.rotation[0] += -delta_y * Config.PAN_SENSITIVITY;
        }
        if (delta_x > 0) {
            this.rotation[1] -= delta_x * Config.PAN_SENSITIVITY;
        } else if (delta_x < 0) {
            this.rotation[1] += -delta_x * Config.PAN_SENSITIVITY;
        }
        $('#RA-Dec').text((-this.rotation[1]).toFixed(8) + ", " + (-this.rotation[0]).toFixed(8));
        this.setTransforms(this.translation, this.rotation);
	this.notify('translate', {'x': -this.rotation[1], 'y': -this.rotation[0]});
        this.dirty = true;
    };

    SkyView.prototype.panUp = function(event) {
		$("#skyPanelDiv").removeClass("grabbing");
        var overlay, i;
        this._inner_mouse_move = this.empty;
	this.refresh();
    };

    SkyView.prototype.panScroll = function(event) {
		var delta;
        delta = 0;
        if (!event) {
            event = window.event;
        }

	// Get the delta scroll amount
        if (event.wheelDelta) {
            delta = event.wheelDelta / 60;
        } else if (event.detail) {
            delta = -event.detail / 2;
        }

        if (delta > 0 ) {
	    	if(this.translation[2] + Config.SCROLL_SENSITIVITY < 1.00001){
				this.translation[2] += Config.SCROLL_SENSITIVITY;		
		}

        } else {
	    	if(this.translation[2] - Config.SCROLL_SENSITIVITY > Config.UPPER_SCROLL_LIMIT){
           		this.translation[2] -= Config.SCROLL_SENSITIVITY;
	    }
	}
        $('#Scale').text(((-this.translation[2] + 1) * 15).toFixed(2));
        this.setTransforms(this.translation, this.rotation);
        this.refresh();
		this.dirty = true;
    };

    SkyView.prototype.jump = function(RA, Dec) {
		this.rotation[1] = -RA;
		this.rotation[0] = -Dec;
		
		$('#RA-Dec').text((-this.rotation[1]).toFixed(8) + ", " + (-this.rotation[0]).toFixed(8));
		$('#Scale').text(((-this.translation[2] + 1) * 15).toFixed(2));
			
		this._inner_mouse_move = this.empty;
		this.setTransforms(this.translation, this.rotation);
			
		this.refresh();
		this.dirty = true;
    };

    SkyView.prototype.mouseHandler = function() {
        this.hookEvent(this.event_attach, "mousedown", this.sky_view_mouse_down);
        this.hookEvent(this.event_attach, "mouseup", this.sky_view_mouse_up);
        this.hookEvent(this.event_attach, "mousewheel", this.panScroll);
		this.hookEvent(window, "keydown", this.keys)
        return this.hookEvent(this.event_attach, "mousemove", this.sky_view_mouse_move);
    };

    SkyView.prototype.keys = function(event){
	switch(event.keyCode){
	    case 38: // up arrow
	       this.scrollUp();
	       break;
	    case 40:
	       this.scrollDown();
	       break;
		}
    }

    SkyView.prototype.scrollUp = function(){
	if(this.translation[2] + Config.SCROLL_SENSITIVITY < 1.00001){
	    this.translation[2] += Config.SCROLL_SENSITIVITY;		
	}

	this.setTransforms(this.translation, this.rotation);		
	this.refresh();
        $('#Scale').text(((-this.translation[2] + 1) * 15).toFixed(2));
	this.dirty = true;
    }

    SkyView.prototype.scrollDown = function(){
	if(this.translation[2] - Config.SCROLL_SENSITIVITY > Config.UPPER_SCROLL_LIMIT){
            this.translation[2] -= Config.SCROLL_SENSITIVITY;
	}

	this.setTransforms(this.translation, this.rotation);		
	this.refresh();
        $('#Scale').text(((-this.translation[2] + 1) * 15).toFixed(2));
	this.dirty = true;
    }


    SkyView.prototype.resizeHandler = function(){
	var _this = this;
	this.hookEvent(window, "resize", function(){
	    _this.canvas.width = window.outerWidth;
	    _this.canvas.style.width = window.outerWidth + "px";
	    _this.canvas.style.height = window.outerHeight + "px";
	    _this.canvas.height = window.outerHeight;
	    _this.gl.viewportWidth = _this.canvas.width;
	    _this.gl.viewportHeight = _this.canvas.height;
            mat4.identity(_this.mvMatrix);
            mat4.perspective(45, _this.gl.viewportWidth / _this.gl.viewportHeight, 0.001, 100.0, _this.pMatrix);
            _this.gl.viewport(0, 0, _this.gl.viewportWidth, _this.gl.viewportHeight);
            _this.gl.uniformMatrix4fv(_this.shaderProgram.pMatrixUniform, false, _this.pMatrix);
	    
	});
    }

    SkyView.prototype.hookEvent = function(element, eventName, callback) {
        if (typeof element === "string") {
            element = document.getElementById(element);
        }
        if (element === null) {
            return;
        }
        if (element.addEventListener) {
            if (eventName === 'mousewheel') {
                element.addEventListener('DOMMouseScroll', callback, false);
            }
            return element.addEventListener(eventName, callback, false);
        } else if (element.attachEvent) {
            return element.attachEvent("on" + eventName, callback);
        }
    };

    SkyView.prototype.unhookEvent = function(element, eventName, callback) {
        if (typeof element === "string") {
            element = document.getElementById(element);
        }
        if (element === null) {
            return;
        }
        if (element.removeEventListener) {
            if (eventName === 'mousewheel') {
                element.removeEventListener('DOMMouseScroll', callback, false);
            }
            element.removeEventListener(eventName, callback, false);
        } else if (element.detachEvent) {
            element.detachEvent("on" + eventName, callback);
        }
    };

    SkyView.prototype.sky_view_mouse_move = function(event) {
        return this._inner_mouse_move(event);
    };

    SkyView.prototype.sky_view_mouse_up = function(event) {
        return this._inner_mouse_up(event);
    };

    SkyView.prototype.sky_view_mouse_down = function(event) {
        return this._inner_mouse_down(event);
    };

    SkyView.prototype.register = function(type, callback) {
        var oldLoaded;
        oldLoaded = this.handlers[type];
        if (this.handlers[type]) {
            return this.handlers[type] = function(view) {
                if (oldLoaded) {
                    oldLoaded(view);
                }
                return callback(view);
            };
        } else {
            return this.handlers[type] = callback;
        }
    };

    SkyView.prototype.notify = function(type, info) {
        if (this.handlers[type]) {
            return this.handlers[type](info);
        }
    };

    SkyView.prototype.getBoundingBox = function() {
        var max, min, range;
        max = this.getCoordinate(this.canvas.width, this.canvas.height);
        min = this.getCoordinate(0, 0);
        range = new Object();
        range.maxRA = max.x;
        range.minRA = min.x;
        range.maxDec = max.y;
        range.minDec = min.y;
        return range;
    };

    /*
      getBoundingBoxes: Guarantees a set of bounding boxes where the minRA and maxRA are such that
          minRA <= maxRA. Case arises when crossing the 0-360 degree boundary.
     */
    SkyView.prototype.getBoundingBoxes = function() {
	var range = this.getBoundingBox();
	var data = {
		RAMin: range.maxRA,
		RAMax: range.minRA,
		DecMin: range.maxDec,
		DecMax: range.minDec
	    };

	if(data.RAMax < data.RAMin){
	    var data2 = {
		    RAMin: range.maxRA,
		    RAMax: 360.000,
		    DecMin: range.maxDec,
		    DecMax: range.minDec
		};
	    data.RAMin = 0.0000;
	    return [data, data2]
	}
	else{
	    return [data];
	}
    }

    SkyView.prototype.getPosition = function() {
        var pos;
        pos = new Object();
        pos.ra = -this.rotation[1];
        pos.dec = -this.rotation[0];
        return pos;
    };

    SkyView.prototype.getCoordinate = function(x, y) {
        var Dec, RA, a, b, c, descrim, dir, far, intersection, inverse, matrices, near, origin, phi, raDec, success, t, theta;
        
        matrices = this.getMatrices();
        
        near = [];
        far = [];
        dir = [];
        
        success = GLU.unProject(x, this.gl.viewportHeight - y, 0.0, matrices[0], matrices[1], matrices[2], near);
        success = GLU.unProject(x, this.gl.viewportHeight - y, 1.0, matrices[0], matrices[1], matrices[2], far);
        
        dir = this.Math.subtract(far, near);
        
        origin = [0.0, 0.0, 0.0, 1.0];
        
        inverse = mat4.set(matrices[0], mat4.create());
        inverse = mat4.inverse(inverse);
        
        origin = this.Math.multiply(origin, inverse);
        dir = this.Math.norm(dir);
        
        dir.push(0.0);
        
        a = this.Math.dot([dir[0], dir[1], dir[2], 1.0], [dir[0], dir[1], dir[2], 1.0]);
        b = this.Math.dot([origin[0], origin[1], origin[2], 0.0], [dir[0], dir[1], dir[2], 1.0]) * 2.0;
        c = this.Math.dot([origin[0], origin[1], origin[2], 0.0], [origin[0], origin[1], origin[2], 0.0]) - 1;
        t = [0, 0];
        
        descrim = Math.pow(b, 2) - (4.0 * a * c);
        
        if (descrim >= 0) {
            t[0] = ((-1.0) * b - Math.sqrt(descrim)) / (2.0 * a);
            t[1] = ((-1.0) * b + Math.sqrt(descrim)) / (2.0 * a);
        }
        
        intersection = this.Math.add(origin, this.Math.mult(dir, t[1]));
        
        theta = Math.atan(intersection[2] / intersection[0]) * 57.29577951308323;
	
	if(theta >= 270.0){
            RA = 270.0-theta + 360.0;
            
	}else if(intersection[0] < 0.0){
            RA = 90-theta;
	}else{
	    RA = 270.0-theta;
	}
	
	phi = Math.acos( intersection[1] );        
        Dec = 90 - phi * 57.29577951308323;
        
        raDec = new Object();
        
        raDec.x = RA;
        raDec.y = Dec;
        
        return raDec;
    };

    SkyView.prototype.keyPressed = function(key) {};

    return SkyView;

})(WebGL);
