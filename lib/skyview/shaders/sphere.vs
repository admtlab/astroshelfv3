<script id="shader-vs" type="x-shader/x-vertex">

attribute vec3 aVertexPosition;
attribute vec2 aTextureCoord;

uniform mat4 uMVMatrix;
uniform mat4 uPMatrix;
uniform mat3 uNMatrix;
uniform float uSurvey;

varying vec2 vTextureCoord;

void main(void) {
    
	gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition, 1.0); 
	
	if(uSurvey == 1.0){
		vTextureCoord = vec2(aTextureCoord.s, 1.0-aTextureCoord.t);
	}
	else{
		vTextureCoord = vec2(aTextureCoord.s, aTextureCoord.t);
  }
}
</script>