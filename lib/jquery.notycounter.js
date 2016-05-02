/**
 * jQuery notycounter Widget v0.1
 * Author: Panickos Neophytou (http://panickos.com)
 *
 * Licensed under the MIT licenses:
 * http://www.opensource.org/licenses/mit-license.php
 *
 **/
$(function(){
	$.widget("ui.notycount", {
		// default options
		options: {
			counter: 0,
			onClick: function(counter){counter.resetCounter();}
		},		
		setCounter: function(value){ 
			this._setOption("counter",value);
		},	
		resetCounter: function(){
			this._setOption("counter", 0);
		},
		incrementCounter: function(){ 
			var c = this.options.counter;
			c++;
			this._setOption("counter", c);
		},
		decrementCounter: function(){
			var c = this.options.counter;
			c--;
			this._setOption("counter", c);
		},
		
		_create: function(){
			var base = this;
		
			this.element
				// add a class for theming
				.addClass("notycounter")// ui-button ui-state-default ui-corner-all ui-widget ui-button-text-only" )
				// prevent double click to select text
				.disableSelection();
		
			$(this.element).button({label: "0"});
                        
			$(this.element).bind('click', function(){
				var callback = base.options.onClick;
				if ($.isFunction(callback))	callback(base);
			});
		
			this._refresh();
		},
	
		_refresh: function(){
			$(this.element).button("option", "label", this.options.counter);
			if(this.options.counter > 0){
				this.element.addClass("ui-button-error");
			}else if(this.options.counter == 0){
				this.element.removeClass("ui-button-error");
			}else{
				;
			}
			this._trigger("change");
		},
		
		// always refresh when changing options
		//_setOption is called for each individual option that is changing
		_setOption: function(key, value){
			// prevent invalid color values
			if (/counter/.test(key) && (value < 0)){
				return;
			}
			// in 1.9 would use _super
			$.Widget.prototype._setOption.call(this, key, value);			
			this._refresh();
		},
		
		// _setOptions is called with a hash of all options that are changing	
		_setOptions: function(){
			// in 1.9 would use _superApply
			$.Widget.prototype._setOptions.apply(this, arguments);
			this._refresh();
		}
	});
});


var topics = {};

jQuery.Topic = function(id){
    var callbacks,
        method,
        topic = id && topics[ id ];
    if(!topic){
        callbacks = jQuery.Callbacks();
        topic = {
            publish: callbacks.fire,
            subscribe: callbacks.add,
            unsubscribe: callbacks.remove
        };
        if(id){
            topics[id] = topic;
        }
    }
    return topic;
};