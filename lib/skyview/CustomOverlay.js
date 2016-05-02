var CustomOverlay = (function(){
    function CustomOverlay(skyview, color, info, label){
	// New webgl code.

	// Shader initialization
	var fragmentShader = skyview.getShader("./lib/skyview/shaders/custom.fs", skyview.gl.FRAGMENT_SHADER);
	var vertexShader = skyview.getShader("./lib/skyview/shaders/custom.vs", skyview.gl.VERTEX_SHADER);

	// Program
	var shader_program = skyview.gl.createProgram();
	skyview.gl.attachShader(shader_program, vertexShader);
	skyview.gl.attachShader(shader_program, fragmentShader);
	skyview.gl.linkProgram(shader_program);
	if (!skyview.gl.getProgramParameter(shader_program, skyview.gl.LINK_STATUS)) {
	    alert("Could not initialise shaders");
	}
	this.shader = shader_program;

	// Set up uniform/attrib pointers
	this.shader.vertexPositionAttribute = skyview.gl.getAttribLocation(this.shader, "aVertexPosition");
	this.shader.pMatrixUniform = skyview.gl.getUniformLocation(this.shader, "uPMatrix");
        skyview.gl.enableVertexAttribArray(this.shader.vertexPositionAttribute);
	this.shader.alphaUniform = skyview.gl.getUniformLocation(this.shader, "alpha");
	this.shader.colorUniform = skyview.gl.getUniformLocation(this.shader, "colorm");
	
	// Set up triangle markers (for now) per point.
	var gl = skyview.gl;
	this.triangleVertexPositionBuffer = gl.createBuffer();

	// Other stuff needed for skyview.
	this.info = info;
	this.name = label;
	this.alpha = 1.0;
	this.survey = "custom";
	this.skyview = skyview;
	skyview.dirty = true;
	var colors = this.translateHexColor(color);	
	gl.useProgram(this.shader);
	skyview.gl.uniform3fv(this.shader.colorUniform,new Float32Array(colors));
	skyview.gl.uniform1f(this.shader.alphaUniform, this.alpha);
	gl.useProgram(this.skyview.shaderProgram);

	
    }
    CustomOverlay.prototype.translateHexColor = function(str){

	var ret = [];
	str.replace(/(..)/g, function(str){
	    ret.push( parseInt( str, 16 )/255.0 );
	});
	return ret;
    }

    // Helper function for translation.
    CustomOverlay.prototype.RaDecToXYZ = function(ra, dec){
	var phi, theta, cosPhi, sinPhi, cosTheta, sinTheta, x, y, z;

	phi = (90 - dec) * Math.PI / 180.0;
	theta = 0;

	if (ra > 270) {
	    theta = (270 - ra + 360) * Math.PI / 180.0;
	} else {
	    theta = (270 - ra) * Math.PI / 180.0;
	}

	sinTheta = Math.sin(theta);
	cosTheta = Math.cos(theta);
	sinPhi = Math.sin(phi);
	cosPhi = Math.cos(phi);

	z = sinPhi * sinTheta;
	y = cosPhi;
	x = sinPhi * cosTheta;
	
	return [x,y,z];
    }

    CustomOverlay.prototype.deleteOverlay = function(){
	this.skyview.deleteOverlay(this.name);
	this.clear();
    }

    CustomOverlay.prototype.setAlpha = function(alpha){
	this.alpha = alpha;
	this.skyview.gl.useProgram(this.shader)
	this.skyview.gl.uniform1f(this.shader.alphaUniform, this.alpha);
	this.skyview.gl.useProgram(this.skyview.shaderProgram);
	this.skyview.dirty = true;
    }

    // Needed for Overlay interface. Do nothing.
    CustomOverlay.prototype.clear = function(){}
    CustomOverlay.prototype.refresh = function(){}

    CustomOverlay.prototype.draw = function(){
	var gl = this.skyview.gl;
	gl.useProgram(this.shader);
	gl.disableVertexAttribArray(1); // Disable because of weird issue when switching programs..
	var vertices = [];
	gl.bindBuffer(gl.ARRAY_BUFFER, this.triangleVertexPositionBuffer);

	// Set up all ze triangles!
	for(var i = 0; i < this.info.length; i++){
	    var coords = this.RaDecToXYZ(this.info[i][0], this.info[i][1]);
	    mat4.multiplyVec3(this.skyview.mvMatrix, coords);

	    var x = coords[0], y = coords[1], z = coords[2];
	    var SCALE = .0001; // Triangles end up huge without this...

	    // Normal triangle + original position. Basically a translation around the ra, dec area..
	    vertices.push(x + 0.0 * SCALE, y + 1.0 * SCALE, z + 0.0 * SCALE);
	    vertices.push(x + -1.0 * SCALE, y + -1.0 * SCALE, z +  0.0 * SCALE);
	    vertices.push(x + 1.0 * SCALE,  y + -1.0 * SCALE,  z + 0.0 * SCALE);
	}

	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertices), gl.STATIC_DRAW);
	this.triangleVertexPositionBuffer.itemSize = 3; // Number of floats per vertex.
	this.triangleVertexPositionBuffer.numItems = this.info.length * 3; // Number of vertices per triangle.

	gl.bindBuffer(gl.ARRAY_BUFFER, this.triangleVertexPositionBuffer);
	gl.vertexAttribPointer(this.shader.vertexPositionAttribute, this.triangleVertexPositionBuffer.itemSize, gl.FLOAT, false, 0, 0);

	// Setting these to identity seems to make large triangle. Are these then wrong? More on this soon...
        gl.uniformMatrix4fv(this.shader.pMatrixUniform, false, this.skyview.pMatrix);
	for(var i = 0; i < this.triangleVertexPositionBuffer.numItems; i+=3){
	    gl.drawArrays(gl.LINE_LOOP, i,  3);	    
	}



	// Switch back to normal shader program.
	gl.useProgram(this.skyview.shaderProgram);
	gl.enableVertexAttribArray(1);

	
     }
    return CustomOverlay;
})();
