// Generated by CoffeeScript 1.3.3
(function() {
  var BinaryTable, CompImage, File, HDU, Header, Image, Table;

  HDU = require('./fits.hdu');

  Header = require('./fits.header');

  Image = require('./fits.image');

  CompImage = require('./fits.compressedimage');

  Table = require('./fits.table');

  BinaryTable = require('./fits.binarytable');

  File = (function() {

    File.LINEWIDTH = 80;

    File.BLOCKLENGTH = 2880;

    function File(buffer) {
      switch (buffer.constructor.name) {
        case 'ArrayBuffer':
          this.initFromBuffer(buffer);
          break;
        case 'Object':
          this.initFromObject(buffer);
          break;
        default:
          throw 'fitsjs does not recognize the argument passed to the constructor';
      }
    }

    File.excessBytes = function(length) {
      return (File.BLOCKLENGTH - (length % File.BLOCKLENGTH)) % File.BLOCKLENGTH;
    };

    File.prototype.initFromBuffer = function(buffer) {
      var data, hdu, header, _results;
      this.length = buffer.byteLength;
      this.view = new jDataView(buffer, void 0, void 0, false);
      this.hdus = [];
      this.eof = false;
      _results = [];
      while (true) {
        header = this.readHeader();
        data = this.readData(header);
        hdu = new HDU(header, data);
        this.hdus.push(hdu);
        if (this.eof) {
          break;
        } else {
          _results.push(void 0);
        }
      }
      return _results;
    };

    File.prototype.initFromObject = function(buffer) {
      this.length = buffer.length;
      this.view = null;
      this.hdus = buffer.hdus;
      return this.eof = true;
    };

    File.prototype.readHeader = function() {
      var excess, header, line, linesRead;
      linesRead = 0;
      header = new Header();
      while (true) {
        line = this.view.getString(File.LINEWIDTH);
        linesRead += 1;
        header.readCard(line);
        if (line.slice(0, 4) === "END ") {
          break;
        }
      }
      excess = File.excessBytes(linesRead * File.LINEWIDTH);
      this.view.seek(this.view.tell() + excess);
      this.checkEOF();
      return header;
    };

    File.prototype.readData = function(header) {
      var data, excess;
      if (!header.hasDataUnit()) {
        return;
      }
      if (header.isPrimary()) {
        data = new Image(this.view, header);
      } else if (header.isExtension()) {
        if (header.extensionType === "BINTABLE") {
          if (header.contains("ZIMAGE")) {
            data = new CompImage(this.view, header);
          } else {
            data = new BinaryTable(this.view, header);
          }
        } else if (header.extensionType === "TABLE") {
          data = new Table(this.view, header);
        }
      }
      excess = File.excessBytes(data.length);
      this.view.seek(this.view.tell() + data.length + excess);
      this.checkEOF();
      return data;
    };

    File.prototype.checkEOF = function() {
      if (this.view.tell() === this.length) {
        return this.eof = true;
      }
    };

    File.prototype.count = function() {
      return this.hdus.length;
    };

    File.prototype.getHDU = function(index) {
      var hdu, _i, _len, _ref;
      if (index == null) {
        index = void 0;
      }
      if ((index != null) && (this.hdus[index] != null)) {
        return this.hdus[index];
      }
      _ref = this.hdus;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        hdu = _ref[_i];
        if (hdu.hasData()) {
          return hdu;
        }
      }
    };

    File.prototype.getHeader = function(index) {
      if (index == null) {
        index = void 0;
      }
      return this.getHDU(index).header;
    };

    File.prototype.getDataUnit = function(index) {
      if (index == null) {
        index = void 0;
      }
      return this.getHDU(index).data;
    };

    File.prototype.getData = function(index) {
      if (index == null) {
        index = void 0;
      }
      return this.getHDU(index).data.getFrame();
    };

    return File;

  })();

  if (typeof module !== "undefined" && module !== null) {
    module.exports = File;
  }

}).call(this);
