/**
 * jQuery infobox Widget v0.1
 * Author: Panickos Neophytou (http://panickos.com)
 *
 * Licensed under the MIT licenses:
 * http://www.opensource.org/licenses/mit-license.php
 *
 **/
 $(function(){
    $.widget( "ui.infobox", {
        // default options
        options: {
            contentObjs: [],
            buttonObjs: [],
            classes: "ui-widget",
            arrowCollapsed: "ui-icon-triangle-1-e",
            arrowExpanded: "ui-icon-triangle-1-s"
        },
                
        _create: function(){
            var me = this;
            var base = this.element;

            base.addClass(this.options.classes).addClass("infobox");

            this._addGroup(base, this.options.contentObjs);
        },

        _addGroup: function(parent, groupObjs){
            var me = this;
            
            for(var i=0; i<groupObjs.length; i++){
                var cur = groupObjs[i];
                if($.isArray(cur)){
                    var internal = $('<div/>').addClass("internal");
                    this._addIcon(parent);
                    this._addGroup(internal, cur);
                    parent.on('click.expand', function(e){
                        e.stopPropagation();
                        $(internal).toggle();
                        $(parent.$icon).toggleClass(me.options.arrowExpanded);
                    });
                    parent.append(internal);
                }else{
                    this._addObject(parent, cur);
                }
            }
        },

        _addIcon: function(parent){
            parent.$icon = $("<span/>").addClass("icon ui-icon "+this.options.arrowCollapsed);
            parent.append(parent.$icon);
        },

        _addObject: function(parent, obj){
            var element = $("<span/>").addClass(obj.classes);
            element.text(obj.label);
            if(obj.onClick != null)
                element.on('click', obj.onClick);
            parent.append(element);
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
    });
});