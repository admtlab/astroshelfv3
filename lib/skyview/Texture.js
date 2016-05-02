var loadTexture = (function() {
	
    var MAX_CACHE_IMAGES = 16;

    var textureImageCache = new Array(MAX_CACHE_IMAGES);
    var cacheTop = 0;
    var remainingCacheImages = MAX_CACHE_IMAGES;
    var pendingTextureRequests = [];


    var TextureImageLoader = function(loadedCallback) {
        var self = this;

        this.gl = null;
        this.texture = null;
        this.callback = null;

        this.image = new Image();
        this.image.addEventListener("load", function() {
            var gl = self.gl;
            
			gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
			 
			gl.bindTexture(gl.TEXTURE_2D, self.texture);
			gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, self.image);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
			gl.bindTexture(gl.TEXTURE_2D, null)

            loadedCallback(self);
            if(self.callback) { self.callback(self.texture); }
        });
    };


    TextureImageLoader.prototype.loadTexture = function(gl, src, texture, callback) {
        this.gl = gl;
        this.texture = texture;
        this.callback = callback;
        this.image.src = src;
    };


    var PendingTextureRequest = function(gl, src, texture, callback) {
        this.gl = gl;
        this.src = src;
        this.texture = texture;
        this.callback = callback;
    };


    function releaseTextureImageLoader(til) {
        var req;
        if(pendingTextureRequests.length) {
            req = pendingTextureRequests.shift();
            til.loadTexture(req.gl, req.src, req.texture, req.callback);
        } else {
            textureImageCache[cacheTop++] = til;
        }
    }

    return function(gl, src, callback) {
        var til;
        var texture = gl.createTexture();


        if(cacheTop) {
            til = textureImageCache[--cacheTop];
            til.loadTexture(gl, src, texture, callback);
        } else if (remainingCacheImages) {
            til = new TextureImageLoader(releaseTextureImageLoader);
            til.loadTexture(gl, src, texture, callback);
            --remainingCacheImages;
        } else {
            pendingTextureRequests.push(new PendingTextureRequest(gl, src, texture, callback));
        }

        return texture;
    };
})();