<script id="trend-fs" type="x-shader/x-fragment">
	
	precision mediump float;		
	
	uniform sampler2D trendImage;
	uniform vec2 iMouse; // mouse positions
	uniform vec2 uTexSize; // texture Size
	
	uniform float lens;
	
	varying vec2 vTexCoord;
	
	float PI = 3.14159;
	
	void main() {
		
		
		vec2 p = gl_FragCoord.xy / uTexSize.xy;
		vec2 m = iMouse.xy / uTexSize.xy;
		
		float lensSize = lens /  uTexSize.y;
		
		vec2 d = p - m;
		vec2 uv = p;
				
		float r = sqrt(dot(d, d)); // distance of pixel from mouse
		
		// if outside the lens scope     
		if ( r > lensSize) {
			uv = p;

		}else{

			//uv = m + d.x * sin(r * PI * 0.5);
			uv = m + normalize(d) * sin(r * PI * 0.5);
			
		}//else{

			//uv = vec2(p.x,m.y) + vec2(d.x * abs(d.x), d.y * abs(d.y) ); // + vec2(d.x * abs(d.x), d.y * abs(d.y));//squareXY
		//	uv = m + normalize(d) * sin(r * PI * 0.34);
		//}
				// set color
		gl_FragColor = texture2D(trendImage, uv.st);	
	}	
</script>

 
