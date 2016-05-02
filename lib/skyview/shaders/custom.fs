<script id="shader-fs" type="x-shader/x-fragment">
precision highp float;
  uniform float alpha;
  uniform vec3 colorm;
  void main(void) {
      gl_FragColor = vec4(colorm, 1.0 * alpha);
 }   
</script>