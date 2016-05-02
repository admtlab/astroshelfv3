$(function(){
    $.widget("ui.annotateDialog", {
        options: {
            targetObj: null,
			access: null
        },
    
        _create: function(){
            var me = this;
            var base = this.element;
            $(this.element).attr("title", "Annotate");
            $(this.element).dialog({
				width: 500,
                autoOpen: false,
				resizable: false,
                buttons: [
					{
						text: "Ok",
						click: function(){ 
							me._submitAnnotation();
							$(base).dialog("close");
						}
					},
					{
						text: "Cancel",
						click: function(){
							$(base).dialog("close");
						}
					}
                ]
             }); 
    
			this.element.append($('<p><b>Title:</b></p><textarea id="annotation_title" rows="1" cols="40" maxlength="128"></textarea>'));
			this.element.append($("<div id='annotation_access_selector'><span class='ui-icon ui-icon-home'/><select><option>Private</option><option selected>Shared</option><option>Public</option></select></div>"));
			var instructions = $('<div/>').attr("id","instructions").html("Select an annotation type and fill out the form:<br/><br/>");
            this.element.append($('<br/><br/>'));
			this.element.append(instructions);
			
            me.$tabs = $('<div/>').addClass("annotate-tabs").attr('id','tabs');
            this._initAnnotateTabs(me.$tabs);
            this.element.append(me.$tabs);
			
			$("#annotation_access_selector select").change(function(){
				$("#annotation_access_selector span").attr("class", "");
				switch($(this).val()) {
					case "Private":
						$("#annotation_access_selector span").addClass("ui-icon ui-icon-locked"); break;
					case "Shared":
						$("#annotation_access_selector span").addClass("ui-icon ui-icon-home"); break;
					case "Public":
						$("#annotation_access_selector span").addClass("ui-icon ui-icon-unlocked");
				}
				me._setOption("access", $(this).val());
			});
        },

        _initAnnotateTabs: function(parent){
            var list = $('<ul/>').html("<li><a href='#tabs-1'>Text</a></li>"+
                "<li><a href='#tabs-2'>Tag</a></li>"+
                "<li><a href='#tabs-4'>Link</a></li>");
            parent.append(list);

            var tab1 = $("<div/>").attr('id', 'tabs-1');
            $(tab1).html("<label for='annoText'>Text</label><br/><br/>"+
            "<textarea id='annoText' name='annoText' rows='4' cols='64' style='resize:none;'>Type the annotation value here.</textarea>");
            parent.append(tab1);

            var tab2 = $("<div/>").attr('id', 'tabs-2');
            $(tab2).html("<label for='tag'>Tags/Keywords</label><br/><br/><input id='tag' type='text' name='tag' size='64'/>");
            parent.append(tab2);
			
            var tab4 = $("<div/>").attr('id', 'tabs-4');
            $(tab4).html("<label for='link'>URI</label><br/><br/><input id='link' type='text' name='link' size='64'/>");
            parent.append(tab4);

            $(parent).tabs();
        },
		    
        _submitAnnotation: function(){
            var selected = $(this.$tabs).tabs("option", "selected");
			var value, type, targetType, targetArea;
			console.log(this.options.targetObj);
            if(this.options.targetObj.object_id) {
                targetType = "object";
            } else if(this.options.targetObj.anno_id) {
                targetType = "annotation";
            } else {
				targetType = "area/point";
				var targetObj = this.options.targetObj;
				if(targetObj["area-box"]) {
					targetArea = {
						"RA_bl": targetObj["area-box"].maxRA,
						"Dec_bl": targetObj["area-box"].minDec,
						"RA_tr": targetObj["area-box"].minRA,
						"Dec_tr": targetObj["area-box"].maxDec
					}
				}
			}
            
            switch(selected){
                case 0:
                    //text
                    value = $('#annoText', this.$tabs).val();
                    type = {annoTypeId: 1};
                    break;
                case 1:
                    //tag
                    value = $('#tag', this.$tabs).val();
                    type = {annoTypeId: 2};
                    break;
                case 2:
                    //link
                    value = $('#link', this.$tabs).val();
                    type = {annoTypeId: 4};
                    break;
            }
			
            var annoData = {
				title: $("#annotation_title").val(),
                value: value,
				access: this.options.access ? this.options.access.toUpperCase() : "SHARED",
                typeId: type.annoTypeId,
                targetType: targetType
            }
			
			if(targetType == "object") {
				annoData.objectId = this.options.targetObj.object_id;
			} else if(targetType == "annotation") {
				annoData.annoId = this.options.targetObj.anno_id;
			} else if(targetType == "area/point") {
				if(targetArea) {
					annoData.area = targetArea;
				} else {
					annoData.point = targetObj;
				}
			}
			
			console.log(annoData);
			$.ajax({
				type: "POST",
				url: secureRESTbase + "annotation/add",
				crossDomain: true,
				beforeSend: function(xhr) {
					setAuthHeaders(xhr);
				},
				dataType: "json",
				data: annoData,
				success: function(data){
					console.log("success");
					annotations_fn.setupAnnotationTables();
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
        
        setTargetObj: function(target){
			console.log(target);
            this._setOption("targetObj", target);
        },
        
        reset: function(){
            $("#tabs-1 textarea").val("");
            $("#tabs-2 input").val("");
            $("#tabs-4 input").val("");
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