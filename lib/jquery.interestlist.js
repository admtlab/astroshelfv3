/**
 * jQuery interestlist Widget v0.1
 * Author: Panickos Neophytou (http://panickos.com)
 *
 * Licensed under the MIT licenses:
 * http://www.opensource.org/licenses/mit-license.php
 *
 **/
 
$(function() {
	// the widget definition, where "custom" is the namespace,
	// "interestlist" the widget name
	$.widget( "ui.interestlist", {
		// default options
		options: {
			interestElements: {},
			interestIdToInterestObj: {},
			interestsSelection: [],
			gotoHandler: function(interestObj){},
			deletefn: function(interestObj){},
			selectionListeners: [],
			interestsUrl: 'db/interest.php',
			userId: -1,
			hoverTime: 1000
		},
		
		
		_create: function() {
			var base = this;
		
			this.element
				// add a class for theming
				.addClass( "interests-list" )
				// prevent double click to select text
				.disableSelection();

			this.element.options = this.options;
                        
                        var offset = $(base.element).offset();
                        
                        base.element.$addNewDialog = $('<div/>').attr('id', 'add-new-interest-dialog');
                        base.element.append(base.element.$addNewDialog);
                        
                        $(base.element.$addNewDialog).dialog({autoOpen: false , 
                            title: "Add new LiveInterest", 
                            position: [offset.left-250, offset.top], 
                            width: 250, 
                            minHeight: 100, 
                            resizable: false});
                        
			$(this.element).selectable({
				selected: function(event, ui){
					var interest = $(ui.selected).data('interest_obj');
			
					base.options.interestsSelection.push(interest.interestId);
					
					$.each(base.options.selectionListeners, function(i, v){
						v(base.options.interestsSelection);
					});
				}, 
				unselected: function(event, ui){
					var interest = $(ui.unselected).data('interest_obj');
			
					$.each(base.options.interestsSelection, function(i, v){
						if(v == interest.interestId)
							base.options.interestsSelection.splice(i,1);
					});
					
					$.each(base.options.selectionListeners, function(i, v){
						v(base.options.interestsSelection);
					});
				}
			});
			
			this.getActiveInterests();
			
			this._refresh();
		},
                
                _initDialog: function() {
                    var base = this;
                    var d = this.element.$dialog;
                    var interest = this.options.interest;
                    var content = $('<div/>').attr("id", 'add-interest-content');
                    
                    var aliasField = $('<input/>').attr("id", "add_sub_alias").attr("type", "text").addClass("alias-field");
                    content.append(aliasField);
                    
                    var addButton = $('<a/>').button().addClass("button positive add").text("Add").click(function() {
                        base.addSubscription(base.options.userID);
                        
                    });
                    content.append(addButton);
                    
                    d.append(content);
                },
		
		_refresh: function() {
			this._trigger("change");
                        //TODO: if the userId option is >0 the add the add button.
                        // else remove it.
		},
                
                addSubscription: function(userId) {
                    var range = getViewBoundingBox(); //TODO: Test this on the astroshelf viewport.
                    var alias = $("#add_sub_alias").val();
                    var interest = sendNewInterestRequest(userId, alias, range.RAMin, range.DecMin, range.RAMax, range.DecMax, function(interest) {
                            add_interest(interest);
                    });
                },
		
		getActiveInterests: function() {
			var base = this;
			
			if(base.options.userId > 0) {
				//get the active interests from database and add them to the list
				$.getJSON(base.options.interestsUrl, {action:"get_active", user_id:base.options.userId},
					function(data) {
						$.each(data.results, function(){
							base.addInterest(this);
						});
					}
				);
			}
			else
				alert("need to login first");
		},
		
		addInterest: function(interestObj) {
			var base = this;
			
			this.options.interestIdToInterestObj[interestObj.interestId] = interestObj;
			
			var interestElement = $('<div/>').appendTo(base.element);
			$(interestElement).data('interest_obj', interestObj);
			$(interestElement).interest({interest:interestObj, gotoHandler:base.options.gotoHandler, deletefn:base.options.deletefn, hoverTime: base.options.hoverTime});
			
			this.options.interestElements[interestObj.interestId] = interestElement;
			
			this._refresh();
		},
		
		removeInterest: function(interestObj) {
			$.remove(this.options.interestElements[interestObj.interestId]);
			
			delete this.options.interestIdToInterestObj[interestObj.interestId];
			delete this.options.interestElements[interestObj.interestId];
			
			this._refresh();
		},
		
		addSelectionListener: function(listener) {
			if($.isFunction(listener)) {
				this.options.selectionListeners.push(listener);
			}
		},
		
		removeSelectionListener: function(listener) {
			$.each(this.options.selectionListeners, function(i, v){
				if(v == listener)
					this.options.selectionListeners.splice(i,1);
			});
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