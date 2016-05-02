/**
 * jQuery notification Widget v0.1
 * Author: Panickos Neophytou (http://panickos.com)
 *
 * Licensed under the MIT licenses:
 * http://www.opensource.org/licenses/mit-license.php
 *
 **/
 
 $(function() {
	// the widget definition, where "custom" is the namespace,
	// "colorize" the widget name
	$.widget( "ui.notification", {
		// default options
		options: {
			notification: {},
			gotoHandler: function(notificationObj){},
			markAsRead: function(notificationObj){}
		},
		
		_create: function() {
			console.log("notification widget created");
			var base = this.element;
			var me = this;
		
			this.element
				// add a class for theming
				.addClass( "notification-list-item" )
				// prevent double click to select text
				.disableSelection();
			
			var offset = $(base).offset();
			
			var n = me.options.notification;
			
			base.$dialog = $("<div/>").addClass("notification-box").attr("id", 'notification-box-' + this.options.notification.annotation.annotationId);
			base.append(base.$dialog);
			
			$(base.$dialog).dialog({ autoOpen: false , title: "Target: "+this.options.notification.annotationTarget.targetName, position: [offset.left-250, offset.top], width: 250, minHeight: 100, resizable: false});
			
			$(base.$dialog).hover(function() {
					clearTimeout(base.$dialog.t);
				},
				function(){
					clearTimeout(base.$dialog.t);
					base.$dialog.t = setTimeout(function(){
						$(base.$dialog).dialog("close");
			    	}, 400);
				}
			);
			
			
			$(base).hover(function(){
				var offset = $(base).offset();
				$(base.$dialog).dialog("option", "position", [offset.left-250, offset.top]);

				clearTimeout(base.$dialog.t);
      			base.$dialog.t = setTimeout(function(){
					$(base.$dialog).dialog("open");
			    }, 400);
			},
			function(){
				clearTimeout(base.$dialog.t);
				base.$dialog.t = setTimeout(function(){
					$(base.$dialog).dialog("close");
			    }, 400);
			});
			
			$(base).click(function() {
				var callback = me.options.markAsRead;
				if ($.isFunction(callback)) callback(n);
			});
			
			this._refresh();
		},
		
		_refresh: function() {
			this.element.text(this.options.notification.annotation.timestamp + ":" + this.options.notification.annotationTarget.id);
			this.refreshDialog();
			
			this._trigger("change");
		},
		
		refreshDialog: function() {
			var base = this;
			var d = this.element.$dialog;
			var n = this.options.notification;
			var content = $('<div/>').attr("id", 'annotation-container-'+n.annotation.annotationId);
			var gotoButton = $('<button/>').button().addClass("ui-button-success notification-goto-button").text("GoTo").click(function() {
				var callback = base.options.gotoHandler;
				if ($.isFunction(callback)) callback(n);
			});
			
			$(content).html('userId:' + n.annotation.userId + '<br/>'
			+'annotationType: ' + n.annotation.annotationType + '<br/>'
			+'annotationValue: ' + n.annotation.annotationValues + '<br/>'
			+'targetType:' + n.annotationTarget.targetType + '<br/><br/>'
			+'target RA: ' + n.annotationTarget.ra + '<br/>'
			+'target DEC:' + n.annotationTarget.dec + '<br/><br/>');
			content.prepend(gotoButton);

			d.append(content);
		},
		
		hide: function() {
			$(this).hide();
		},
		
		show: function() {
			$(this).show();
		},

		// _setOptions is called with a hash of all options that are changing
		// always refresh when changing options
		_setOptions: function() {
			// in 1.9 would use _superApply
			$.Widget.prototype._setOptions.apply( this, arguments );
			this._refresh();
		},
	
		// _setOption is called for each individual option that is changing
		_setOption: function( key, value ) {
			// prevent invalid color values
			if ( /counter/.test(key) && (value < 0) ) {
				return;
			}
			// in 1.9 would use _super
			$.Widget.prototype._setOption.call( this, key, value );
			
			this._refresh();
		}

	}); //-- end widget
	
});