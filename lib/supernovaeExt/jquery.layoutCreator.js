////////////////////////////////////////////////////////////////////////////////////////
//
// jQuery LayoutCreator Plugin 
// Author: Nikhil Venkatesh
// Date: November 6, 2013
// Description: This plugin creates a generic, responsive 1-column,
//	two-column, or grid layout. It ships with the features resizable panes, 
//	hiding/showing panels, and specified width/height measurements.
//
// Notes: 1) The panes within the layout must have a class 'layoutPane'.
//
// Parameters:
//	Parameter: layoutType 
//	Type: String
//	Values: one_col (default), two_col, grid
//	Returns: the formatted layout HTML markup
//
//	Parameter: layoutDirection
//	Type: String
//	Values: horizontal (default), vertical
//	Description: used mainly for two column layouts.
//
//	Parameter: layoutWidths
//	Type: Array
//	Values: an array with widths specified in px, %, em. The width is relative
//		to the parent of the div. For one_col, e.g. [ 100% ], two_col e.g. [ 50%, 50% ],
//		for grid, it can be specified per grid unit, per row, or per column.
//		Defaults: one_col [100%], two_col [50%, 50%], grid [parentWidth/numColumns]
//
//	Parameter: layoutHeights
//	Type: Array
//	Values: an array with heights specified in px, %, em. The height is relative
//		to the parent of the div. For one_col, e.g. [ 100% ], two_col e.g. [ 50%, 50% ],
//		for grid, it can be specified per grid unit, per row, or per column.
//		Defaults: one_col [100%], two_col [100%, 100%], grid [parentHeight/numRows]
//	
//	Parameter: bAutoWidth
//	Type: Boolean
//	Values: true (default), false
//	Description: specifies whether or not the layout manager should automatically
//		calculate the layout widths.
//
//	Parameter: bResizable
//	Type: Boolean
//	Values: true (default), false
//	Description: specifies whether or not the individual panes should be resizable.
//
//	Parameter: bHideShow
//	Type: Boolean
//	Values: true (default), false
//	Description: specifies whether or not the hide/show functionality of the panels
//				should be enabled.
//
//	
// Usage: $( '#myLayout' ).layoutCreator()	// initializes the layout with default settings
//		*** This default case counts the number of divs within the layout div. If there is
//		*** one, it defaults to one_col, two it defaults to two_col, and more, defaults to
//		*** grid.  If there is >2 and an odd number, it will leave an empty grid space.
//
////////////////////////////////////////////////////////////////////////////////////////

//////////    BEGIN PLUGIN CODE    ///////////

// Create an immediately invoked anonymous function to encapsulate plugin data
// Pass in the jQuery object itself to avoid conflicts with other libraries
// Use the $ alias - all plugin info will be contained inside a private scope
// Use the semi-colon at the beginning just in case other files aren't closed correctly
// Pass in window and document as local variables rather than global variables - slight
// increase in performance and can be more efficiently minified
;(function ( $, window, document, undefined ){
	// Extend the jQuery prototype to add the layoutCreator plugin
	// $.fn or jQuery.fn is essentially a shortcut for jQuery.prototype 
	// pass in a custom 'options' object that will override default options, if specified
	$.fn.layoutCreator = function( options ){
		// "this" is equal to $( '#myLayout' )
		// Create new option, extend object with defaultOptions and passed in options
		// Use empty object as first parameter to avoid overwriting default options object
		var settings = $.extend({}, $.fn.layoutCreator.defaultOptions, options);
		// cache the jQuery object passed into the plugin
		var $this = $( this );

		// Private flags to signal which type of layout it is
		var one_col = false, two_col = false, grid = false;
		// If the user specifies the layoutType in the options
		//	1) Check to see if they entered any specific widths, else apply default
		//	2) Check to see if they entered any layout direction, else apply default

		// Handle responsive web design
		handleWindowResize($this);
		$(window).trigger('resize');

		if ( settings['layoutType'] ) {
			var _layoutType = settings['layoutType'];
			console.log("Specified layout type: " + _layoutType);
		} 
		// If user did not specify any layout type, automatically generate the correct layout
		// If there is one layoutPane: one_col, if there are two layoutPanes: two_col, more than
		// two layoutPanes: grid
		else {
			console.log("Did not specify layout type.");
			var $layoutPanes = $this.children('.layoutPane');
			var _numPanes = $layoutPanes.length;
			if ( _numPanes == 1 ) {	// one-column layout
				// Set the one_col flag
				one_col = true;
				console.log("Generating a one column layout.");
				if (settings['bAutoWidth'] == true) {
					// Automatically generate the widths of the children (for a 1 column layout, it will
					// just be 100%)
					console.log("Auto width set to true. Column will be fully sized in parent.");
					// Loop through each of the panes, and set the CSS properties for alignment, width, and height
					// I'm automatically setting height to the parent's height, and this will adjust itself based on
					// window resize.
					$.each($layoutPanes, function(index, elem){
						$(this).css({
							'position': 'relative',
							'width': '100%',
							'height': '$(this).parent().height()'
						});

						if (settings['bHideShow'] == true)	// check to make sure the flag is set
							addHideShow($(this), index);
					});
				}
			} else if ( _numPanes == 2) {	// two-column layout
				// Set the two_col flag
				two_col = true;
				console.log("Generating a two column layout.");
				// If the bAutoWidth is set to true (default value)
				if (settings['bAutoWidth'] == true) {
					// Automatically generate the widths of the children (for a 2 column layout, it will
					// be 50% each)
					console.log("Auto width set to true. Columns will be sized evenly in parent.");
					// Loop through each of the panes, and set the CSS properties for alignment, width, and height
					// I'm automatically setting height to the parent's height, and this will adjust itself based on
					// window resize.
					$.each($layoutPanes, function(index, elem){
						$(this).css({
							'position': 'relative',
							'width': '49.5%', 
							'float': 'left', 
							'height': '$(this).parent().height()'
						});
						if(index == 0)
							$(this).css('border-right', '2px solid black');
						else
							$(this).css('border-left', '2px solid black');
						if (settings['bHideShow'] == true)	// check to make sure the flag is set
							addHideShow($(this), index);
					});
				}
			} else if ( _numPanes > 2 ) {	// grid-layout
				// Set the grid flag
				grid = true;
				console.log("Generating a grid layout.");
			}
		} 
		return this;	//return the jQuery object for chaining
	};

	// Private function that sets the window resize handler
	function handleWindowResize(baseObj) {
		var $this = baseObj;
		var $orig_width = $this.width(), 
			$orig_height = $this.height();

		$(window).resize( function(){
			// Cache the widths of the original layout and the window
			var $window = $( window );
			var $window_width = $window.width(), 
				$window_height = $window.height();

			// If the browser's width becomes less than the layout's width, alter the layout's width
			if ($window_width < $orig_width + 50) {
				console.log("Altering layout width.");
				$this.css('width', "95%");
			} else if ($window_width >= $orig_width) {
				$this.css('width', $orig_width);
			}
			if ($window_height < $orig_height + 50) {
				console.log("Altering layout height.");
				$this.css('height', $window_height - 20);
				$this.children().css('height', $this.height());
			} else if ($window_height >= $orig_height) {
				$this.css('height', $orig_height);
				$this.children().css('height', $this.height());
			}
		});
	}

	// Private function to add the hide/show functionality to each pane
	// Params: the pane to add the hide/show button
	//			the index of the pane to add the button on
	function addHideShow(pane, index) {
		pane.append('<button id="pane' + index + '_hs' + '">Hide</button>');
		var $curPaneButton = $('#pane' + index + '_hs');
		$curPaneButton
			.css({'position': 'absolute', 'top':'5%', 'right':'3%', 'float':'right'})
			.clicktoggle(function(event){
				event.preventDefault();
				console.log('#pane' + index + '_hs clicked hide.');
				$curPaneButton.text('Show');
				pane.animate({
					width: ['6%', 'linear']
				}, { duration: 500, easing:'linear', queue: false, complete: function(){
					console.log("Animation complete.");
				}});

				var $paneTitle = pane.find('.lcPaneTitle');
				$paneTitle.css('display', 'inline-block');		//used to set the width only as big as the text
				var $origTitleWidth = $paneTitle.width();	//cache the original width before any transformations/animations
				$paneTitle.css({
						'transform': 'rotate(-90deg)',	//transform the text to be shown vertically
						'position': 'absolute',	// set the position to absolute so we can position it regardless of flow
						'left': '0',	// make sure to set the left and right to 0 to allow for the child div to have a 
						'right': '0',	// 		greater width than the parent div
						'top': '50%',	// Set the top position to halfway in the parent div to center the text
						'color': '#000099',	// set an arbitrary color - this should be customizable
						'width': $origTitleWidth	// set the title width to the original one just in case the width is altered
					});
				// Set the button to be centered within the shortened pane
				$curPaneButton.css('right', '0');
				// Set the pane background (should be customizable) and set the opacity of everything but the show/hide button and
				// the pane title to 0.2
				pane.css('background', '#009933').find(':not("#pane' + index + '_hs"):not(".lcPaneTitle")')
					.animate({'opacity': '0.2'}, { duration: 500, easing:'swing', queue: true });


				// Get the left position after the transform so we can move the text to the correct location
				// Set the margin-left to be negative to push it in the correct direction
				$paneTitle.css('margin-left', (-1 * $('.lcPaneTitle').eq(index).position().left + 5) + 'px');

				// Cache the next and previous panes (if they exist)
				var $nextPane = pane.next();
				var $prevPane = pane.prev();
				// If there is a next pane, animate that pane and make it wider to fill up the remaining space
				if($nextPane) {
					$nextPane.animate({
						width: '93.5%'
					}, { duration: 500, easing:'linear', queue: false, complete: function(){
						console.log("Next pane animation complete.");
					}});
				}
				// If there is a previous pane, alter that pane's width to fill up the remaining space
				if($prevPane) {
					$prevPane.animate({
						width: '93.5%'
					}, { duration: 500, easing:'linear', queue: false, complete: function(){
						console.log(" Prev pane animation complete.");
					}});
				}

			},
			// for clicking show
			function(event){
				event.preventDefault();
				console.log('#pane' + index + '_hs clicked show.');
				$curPaneButton.text('Hide');
				pane.animate({
					width: ['49.5%', 'linear']
				}, { duration: 500, easing:'linear', queue: false, complete: function(){
					console.log("Animation complete.");
				}});
				// We need to actually retrieve the background of the pane and reset it to that
				// Right now, we are just hardcoding the background
				$curPaneButton.css('right', '3%');
				pane.css('background', '#FFFFFF')
					.find('.lcPaneTitle')
					// Undo the transformations and positioning from hiding the content
					.css({
						'transform': 'rotate(0deg)',
						'position': 'relative',
						'top': '0%',
						'color': '#000000',
						'margin-left': '0'
					})
					.end()
					// Set the opacity of everything back to 1
					.find('*')
					.css('opacity', '1');

				var $nextPane = pane.next();
				var $prevPane = pane.prev();
				if($nextPane) {
					$nextPane.animate({
						width: '49.5%'
					}, { duration: 500, easing:'linear', queue: false, complete: function(){
						console.log("Animation complete.");
					}});
				}
				if($prevPane) {
					$prevPane.animate({
						width: '49.5%'
					}, { duration: 500, easing:'linear', queue: false, complete: function(){
						console.log("Animation complete.");
					}});
				}
			});
	}

	// Toggle function replacement plugin
	$.fn.clicktoggle = function(a, b) {
	    return this.each(function() {
	        var clicked = false;
	        $(this).click(function() {
	            if (clicked) {
	                clicked = false;
	                return b.apply(this, arguments);
	            }
	            clicked = true;
	            return a.apply(this, arguments);
	        });
	    });
	};

	// Default options for plugin - publicly exposed so it can be altered/modified by users
	$.fn.layoutCreator.defaultOptions = { 
		'bAutoWidth' : true,
		'bResizable' : true,
		'bHideShow' : true
	};

	// End of closure
})( jQuery, window, document );

//////////    END PLUGIN CODE    ///////////