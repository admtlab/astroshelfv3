// Generated by CoffeeScript 1.3.2
var Projection,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

Projection = (function() {

  function Projection(Math) {
    this.Math = Math;
    this.convertradec = __bind(this.convertradec, this);

    this.unprojectTAN = __bind(this.unprojectTAN, this);

    this.unprojectSIN = __bind(this.unprojectSIN, this);

    this.init = __bind(this.init, this);

    this.parameters = null;
  }

  Projection.prototype.init = function(image, fits, Tile, survey) {
    var FITS, coords, size,
      _this = this;
    if (survey === "SDSS") {
      size = [1984, 1361]; //DR7 JPEG image size
      //size = [2048,1489]; //DR7,DR10 FITS image size, also DR10 JPEG image size
    } else if (survey === "LSST") {
      size = [4072, 4000];
    } else if (survey === 'FIRST') {
      size = [1550, 1160];
    }
    //FITS = require('fits');
    this.parameters = new Object;
	
    if (survey === "LSST" ){
				
      $.ajaxSetup({
        'async': false
      });
	  
      $.getJSON("./lib/skyview/imageHeader.php?url=" + fits + "&survey=" + survey + "&type=JPEG", function(data) {
        return $.each(data, function(key, val) {
			
		  if (key === "CRVAL_1") {
            _this.parameters.crval1 = val;
          }
          if (key === "CRVAL_2") {
            _this.parameters.crval2 = val;
          }
          if (key === "CRPIX_1") {
            _this.parameters.crpix1 = val;
          }
          if (key === "CRPIX_2") {
            _this.parameters.crpix2 = val;
          }
          if (key === "CD1_1") {
            _this.parameters.cd11 = val;
          }
          if (key === "CD1_2") {
            _this.parameters.cd12 = val;
          }
          if (key === "CD2_1") {
            _this.parameters.cd21 = val;
          }
          if (key === "CD2_2") {
            _this.parameters.cd22 = val;
          }
          if (key === "CTYPE_1") {
            _this.parameters.ctype1 = val;
          }
          if (key === "CTYPE_2") {
            _this.parameters.ctype2 = val;
          }
          if (key === "CDELT_1") {
            _this.parameters.cdelt1 = val;
          }
          if (key === "CDELT_2") {
            return _this.parameters.cdelt2 = val;
          }
        });
      });
      
	  $.ajaxSetup({
        'async': true
      });

     coords = this.unprojectTAN(size[0], size[1]);
     Tile.initTexture("./" + image);
     Tile.createTile(coords[0], coords[1]);
      
  } else if (survey === "FIRST") {
		
		  _this.parameters = {
	  	
			  crval1: parseFloat(fits[0]),
			  crval2: parseFloat(fits[1]),
			  crpix1: parseFloat(fits[2]),
			  crpix2: parseFloat(fits[3]),
			  ctype1: fits[4],
			  ctype2: fits[5],
			  cdelt1: parseFloat(fits[6]),
			  cdelt2: parseFloat(fits[7])  
			  
		  };
		  		  
        coords = this.unprojectSIN(size[0], size[1]);
        Tile.initTexture(image);
        Tile.createTile(coords[0], coords[1]);
    } else if (survey === "SDSS") {
 
	  _this.parameters = {
	  	
		  crval1: fits[0],
		  crval2: fits[1],
		  crpix1: 1000.5,//fits[2],   //DR7 JPEG=1000.5, fits[2], fits[3] in header txt set to
		  crpix2: 680.5,//fits[3],    //DR7 JPEG=680.5    DR7 FITS central pixels.  Remove these tweaks when upgrading to DR10.
		  cd11:   fits[4],
		  cd12:   fits[5],
		  cd21:   fits[6],
		  cd22:   fits[7],
		  ctype1: fits[8],
		  ctype2: fits[9],
		  naxis1: fits[10],
		  naxis2: fits[11]  
		
	  };
      coords = this.unprojectTAN(size[0], size[1]);

	  //console.log('Ra,Dec from coords 0,0: ', coords[0][0], coords[1][0]);
      //console.log('Ra,Dec at coord 0,0: ', this.Math.xyToRaDec(0,0,_this.parameters));      
      //console.log('Ra,Dec at coord 1235,1201: ', this.Math.xyToRaDec(1235,1201,_this.parameters));
      //console.log('Ra,Dec at coord 657,1117: ', this.Math.xyToRaDec(657,1117,_this.parameters));
      //console.log('Ra,Dec at coord 765,1349: ', this.Math.xyToRaDec(765,1349,_this.parameters));
      //console.log('Ra,Dec at coord 35,579: ', this.Math.xyToRaDec(35,579,_this.parameters));
      //console.log('Ra,Dec at coord 325,875: ', this.Math.xyToRaDec(325,875,_this.parameters));
      //console.log('Ra,Dec at coord 299,909: ', this.Math.xyToRaDec(299,909,_this.parameters));
      
      Tile.initTexture(image);
      Tile.createTile(coords[0], coords[1]);
    } // end if sdss
  }; // end init

  Projection.prototype.unprojectSIN = function(xsize, ysize) {
    var coords, crval, dec, dtor, i, index, indices, j, lat, long, r, ra, rtod, tmp, x, xpix, y, ypix, _i, _j, _k, _results, _results1;
    rtod = 57.29577951308323;
    dtor = 0.0174532925;
    xpix = (function() {
      _results = [];
      for (var _i = 1; 1 <= xsize ? _i <= xsize : _i >= xsize; 1 <= xsize ? _i++ : _i--){ _results.push(_i); }
      return _results;
    }).apply(this);
    ypix = (function() {
      _results1 = [];
      for (var _j = 1; 1 <= ysize ? _j <= ysize : _j >= ysize; 1 <= ysize ? _j++ : _j--){ _results1.push(_j); }
      return _results1;
    }).apply(this);
    ra = [0, 1, 2, 3];
    dec = [0, 1, 2, 3];
    indices = [[0, 0], [0, ysize - 1], [xsize - 1, ysize - 1], [xsize - 1, 0]];
    crval = [this.parameters.crval1, this.parameters.crval2];
    /*
    		console.log "crval1,2: ", crval
    		console.log "cd11: ",@parameters.cd11
    		console.log "cd12: ",@parameters.cd12
    		console.log "cd21: ",@parameters.cd21
    		console.log "cd22: ",@parameters.cd22
    */

    for (index = _k = 0; _k <= 3; index = ++_k) {
      i = indices[index][0];
      j = indices[index][1];
      x = this.parameters.cdelt1 * (xpix[i] - this.parameters.crpix1);
      y = this.parameters.cdelt2 * (ypix[j] - this.parameters.crpix2);
      if (this.parameters.ctype1 === "DEC--SIN") {
        tmp = x;
        x = y;
        y = tmp;
        if (index === 0) {
          crval = this.Math.rotate(crval);
        }
      }
      /*			
      			 #JPEG - do manually for some regions
      			tmp=x
      			x=y
      			y=tmp
      			if index == 0
      				crval = @Math.rotate(crval)
      */

      long = this.Math.arg(-y, x);
      r = Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2));
      lat = Math.acos(r / rtod);
      coords = this.convertradec(long, lat, crval);
      ra[index] = coords[0];
      dec[index] = coords[1];
    }
    return [ra, dec];
  };

  Projection.prototype.unprojectTAN = function(xsize, ysize) {
    var coords, crval, dec, dtor, i, index, indices, j, lat, long, r, ra, rtod, tmp, x, xpix, y, ypix, _i, _j, _k, _results, _results1;
    rtod = 57.29577951308323;
    dtor = 0.0174532925;
    xpix = (function() {
      _results = [];
      for (var _i = 1; 1 <= xsize ? _i <= xsize : _i >= xsize; 1 <= xsize ? _i++ : _i--){ _results.push(_i); }
      return _results;
    }).apply(this);
    ypix = (function() {
      _results1 = [];
      for (var _j = 1; 1 <= ysize ? _j <= ysize : _j >= ysize; 1 <= ysize ? _j++ : _j--){ _results1.push(_j); }
      return _results1;
    }).apply(this);
    ra = [0, 1, 2, 3];
    dec = [0, 1, 2, 3];
    indices = [[0, 0], [0, ysize - 1], [xsize - 1, ysize - 1], [xsize - 1, 0]];
    crval = [this.parameters.crval1, this.parameters.crval2];
	
    /*
    		console.log "crval1,2: ", crval
    		console.log "cd11: ",@parameters.cd11
    		console.log "cd12: ",@parameters.cd12
    		console.log "cd21: ",@parameters.cd21
    		console.log "cd22: ",@parameters.cd22
    */

    for (index = _k = 0; _k <= 3; index = ++_k) {
      i = indices[index][0];
      j = indices[index][1];
      x = this.parameters.cd11 * (xpix[i] - this.parameters.crpix1) + this.parameters.cd12 * (ypix[j] - this.parameters.crpix2);
      y = this.parameters.cd21 * (xpix[i] - this.parameters.crpix1) + this.parameters.cd22 * (ypix[j] - this.parameters.crpix2);
      if (this.parameters.ctype1 === " DEC--TAN") {
        tmp = x;
        x = y;
        y = tmp;
        if (index === 0) {
          crval = this.Math.rotate(crval);
        }
      }
      /*			
      			 #JPEG - do manually for some regions
      			tmp=x
      			x=y
      			y=tmp
      			if index == 0
      				crval = @Math.rotate(crval)
      */

      long = this.Math.arg(-y, x);
      lat = (Math.PI / 2.0) * dtor;
      r = Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2));
      if (r > 0.0) {
        lat = Math.atan((180.0 / Math.PI) / r);
      }
      lat = Math.atan(rtod / r);
      coords = this.convertradec(long, lat, crval);
      ra[index] = coords[0];
      dec[index] = coords[1];
    }
    return [ra, dec];
  };

  Projection.prototype.convertradec = function(long, lat, crval) {
    var dec, decpole, dtor, l, latpole, lonpole, lp, m, mat, mp, n, np, phi, r11, r12, r13, r21, r22, r23, r31, r32, r33, ra, rapole, rtod, theta;
    rtod = 57.29577951308323;
    dtor = 0.0174532925;
    l = Math.cos(lat) * Math.cos(long);
    m = Math.cos(lat) * Math.sin(long);
    n = Math.sin(lat);
    phi = 0.0;
    theta = 90.0 * dtor;
    lonpole = crval[1] > theta * rtod ? 0.0 : 180.0 * dtor;
    latpole = 90.0 * dtor;
    rapole = crval[0] * dtor;
    decpole = crval[1] * dtor;
    r11 = -1.0 * Math.sin(rapole) * Math.sin(lonpole) - Math.cos(rapole) * Math.cos(lonpole) * Math.sin(decpole);
    r12 = Math.cos(rapole) * Math.sin(lonpole) - Math.sin(rapole) * Math.cos(lonpole) * Math.sin(decpole);
    r13 = Math.cos(lonpole) * Math.cos(decpole);
    r21 = Math.sin(rapole) * Math.cos(lonpole) - Math.cos(rapole) * Math.sin(lonpole) * Math.sin(decpole);
    r22 = -1.0 * Math.cos(rapole) * Math.cos(lonpole) - Math.sin(rapole) * Math.sin(lonpole) * Math.sin(decpole);
    r23 = Math.sin(lonpole) * Math.cos(decpole);
    r31 = Math.cos(rapole) * Math.cos(decpole);
    r32 = Math.sin(rapole) * Math.cos(decpole);
    r33 = Math.sin(decpole);
    mat = [[r11, r21, r31], [r12, r22, r32], [r13, r23, r33]];
    lp = mat[0][0] * l + mat[0][1] * m + mat[0][2] * n;
    mp = mat[1][0] * l + mat[1][1] * m + mat[1][2] * n;
    np = mat[2][0] * l + mat[2][1] * m + mat[2][2] * n;
    dec = Math.asin(np) * rtod;
    ra = Math.atan2(mp, lp) * rtod;
    if (ra < 0.0) {
      ra += 360.0;
    } else if (ra > 360.0) {
      ra -= 360;
    }
    return [ra, dec];
  };

  return Projection;

})();