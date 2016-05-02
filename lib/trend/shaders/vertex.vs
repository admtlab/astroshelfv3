<script id="trend-vs" type="x-shader/x-vertex">
	
	attribute vec2 aPositionCoord;
	attribute vec2 aTextureCoord;
	
	uniform vec2 u_Res;
			
	varying vec2 vTexCoord;
	
	void main(void)
	{
	   	
		vec2 zeroToOne = aPositionCoord / u_Res;

		// convert from 0->1 to 0->2
		vec2 zeroToTwo = zeroToOne * 2.0;
		
		// convert from 0->2 to -1->+1 (clipspace)
		vec2 clipSpace = zeroToTwo - 1.0;

		gl_Position = vec4(clipSpace, 0, 1);
		    
	    vTexCoord = aTextureCoord;
	}
	
</script>