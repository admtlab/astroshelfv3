<script id="shader-vs" type="x-shader/x-vertex">
attribute vec3 aVertexPosition;

uniform mat4 uPMatrix;

void main(void) {
	gl_Position = uPMatrix *  vec4(aVertexPosition, 1.0); 
}
</script>