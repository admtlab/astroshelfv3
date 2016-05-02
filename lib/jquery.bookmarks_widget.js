$(function(){
    $.widget("ui.bookmarkDialog", {
        options: {
            targetObj: null
        },
    
        _create: function(){
			var widget = this;
            $(this.element).attr("title", "Bookmark");
            $(this.element).dialog({ 
                autoOpen: false,
                buttons: [
					{
						text: "Ok",
						click: function(){ 
							var success = widget.createBookmark();
							if(success) {
								$(this).dialog("close");
							}
						}
					},
					{
						text: "Cancel",
						click: function(){
							$(this).dialog("close");
						}
					}
                ]
             });
			
			this.element.append($('<p><b>Title:</b></p>'));
			this.element.append($('<textarea id="bookmark_title" rows="1" cols="35" maxlength="128"></textarea>'));
			this.element.append($('<div id="bookmark_info"/>'));
        },
		    
        createBookmark: function() {
			var target = this.options.target;
			var data = { add_bookmark:1, user_id: getUserId(), type: target.type, title: $("#bookmark_title").val() };
			if(target.type == "loc") {
				data["ra"] = target.ra;
				data["dec"] = target.dec;
			} else if(target.type == "obj" || target.type == "anno") {
				data["id"] = target.id;
			}
			if(target.qtip) {
				data["SDSS_id"] = true;
			}
			console.log(data);
			$.ajax({
				type: 'POST',
				url: "./lib/db/local/queryASTRO.php",
				data: data,
				success: function(data, textStatus, jqXHR) {
					console.log(data);
					$("#bookmark_dialog").dialog("close");
					bookmarks_fn.setupBookmarkTables();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log("Error:  " + errorThrown);
					console.log(textStatus);
					console.log(jqXHR);
				}
			});
		},
        
        open: function(){
            $(this.element).dialog("open");
		},
        
        setTarget: function(target){
            this._setOption("target", target);
			$("#bookmark_info").empty();
			if(target.type == "obj") {
				$("#bookmark_info").append($('<p><b>Object:</b> ' + target.name +'<p/>'));
			} else if(target.type == "anno") {
				$("#bookmark_info").append($('<p><b>Annotation:</b> ' + target.name +'<p/>'));
			}
			$("#bookmark_info").append($('<p><b>RA:</b> ' + target.ra +'<p/>'))
				.append($('<p><b>dec:</b> ' + target.dec +'<p/>'));
        },
        
        reset: function(){
            $("#bookmark_title").val("");
        },
    
        _refresh: function(){
            this.reset();
            this._trigger("change");
        },
		
        // _setOption is called for each individual option that is changing
        _setOption: function(key, value){
            // prevent invalid color values
            if (/targetObj/.test(key) && (value == null)){
                return;
            }
            // in 1.9 would use _super
            $.Widget.prototype._setOption.call(this, key, value);
            this._refresh();
        },
		
        // _setOptions is called with a hash of all options that are changing
        // always refresh when changing options
        _setOptions: function(){
            // in 1.9 would use _superApply
            $.Widget.prototype._setOptions.apply(this, arguments);
            this._refresh();
        }
    });
});