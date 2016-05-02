/**
 * jQuery interest Widget v0.1
 * Author: Panickos Neophytou (http://panickos.com)
 *
 * Licensed under the MIT licenses:
 * http://www.opensource.org/licenses/mit-license.php
 *
 **/
 
/*
{
	"msgType":"EXPRESSION_OF_INTEREST",
	"interestId":1,
	"userId":"$userId",
	"alias":"Section 1 SDSS",
	"type":"galaxy",
	"box":{
		"point_bl":{
		"ra":12.0919283,
		"dec":50.3232554
		},
		"point_tr":{
		"ra":12.0919283,
		"dec":50.3232554
		}
	}
}
*/ 
$(function() {
	// the widget definition, where "custom" is the namespace,
	// "interest" the widget name
	$.widget( "ui.interest", {
		// default options
		options: {
			interest: {},
			gotoHandler: function(interestObj){},
			deletefn: function(interestObj){},
			hoverTime: 1000
		},
		
		_create: function() {
			var base = this.element;
			var me = this;
			
			this.element
				// add a class for theming
				.addClass( "interest-list-item" )
				// prevent double click to select text
				.disableSelection();
				
			var offset = $(base).offset();
			
			base.$dialog = $("<div/>").addClass("interest-box").attr("id", 'interest-box-' + this.options.interest.interestId);
			base.append(base.$dialog);
			
			$(base.$dialog).dialog({ autoOpen: false , title: "Interest: "+this.options.interest.alias, position: [offset.left-250, offset.top], width: 250, minHeight: 100, resizable: false});
			
			$(base.$dialog).hover(function() {
					clearTimeout(base.$dialog.t);
				},
				function(){
					clearTimeout(base.$dialog.t);
					base.$dialog.t = setTimeout(function(){
						$(base.$dialog).dialog("close");
			    	}, me.options.hoverTime);
				}
			);
			
			$(base).hover(function(){
				var offset = $(base).offset();
				$(base.$dialog).dialog("option", "position", [offset.left-250, offset.top]);

				clearTimeout(base.$dialog.t);
      			base.$dialog.t = setTimeout(function(){
					$(base.$dialog).dialog("open");
			    }, me.options.hoverTime);
			},
			function(){
				clearTimeout(base.$dialog.t);
				base.$dialog.t = setTimeout(function(){
					$(base.$dialog).dialog("close");
			    }, me.options.hoverTime);
			});
				
			this._refresh();
		},
		
		
		
		_refresh: function() {
			this.element.text(this.options.interest.interestId + ":" + this.options.interest.alias);
			this.refreshDialog();
			
			this._trigger("change");
		},
		
		refreshDialog: function() {
			var base = this;
			var d = this.element.$dialog;
			var interest = this.options.interest;
			var content = $('<div/>').attr("id", 'interest-container-'+interest.interestId);
			var gotoButton = $('<div/>').button().addClass("ui-button-success interest-goto-button").text("GoTo").click(function() {
				var callback = base.options.gotoHandler;
				if ($.isFunction(callback)) callback(n);
			});
			
			var deleteButton = $('<div/>').addClass("interest-delete-button");
			$(deleteButton).button();
			$(deleteButton).text("Delete");
			$(deleteButton).click(function() {
				var callback = base.options.deletefn;
				if ($.isFunction(callback)) callback(n);
			});
			$(deleteButton).addClass("ui-button-error");
			
			$(content).html('Box:<br/>'
			+'RA (bottom-left): ' + interest.box.point_bl.ra + '<br/>'
			+'DEC(bottom-left):' + interest.box.point_bl.dec + '<br/><br/>'
			+'RA (top-right): ' + interest.box.point_tr.ra + '<br/>'
			+'DEC(top-right):' + interest.box.point_tr.dec + '<br/><br/>');
			$(content).prepend(gotoButton);
			$(content).prepend(deleteButton);

			d.append(content);
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
		
	});//-- end widget
});