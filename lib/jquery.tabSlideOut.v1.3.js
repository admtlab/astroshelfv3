/*
    tabSlideOUt v1.3
    
    By William Paoli: http://wpaoli.building58.com

    To use you must have an image ready to go as your tab
    Make sure to pass in at minimum the path to the image and its dimensions:
    
    example:
    
        $('.slide-out-div').tabSlideOut({
                tabHandle: '.handle',                         //class of the element that will be your tab -doesnt have to be an anchor
                pathToTabImage: 'images/contact_tab.gif',     //relative path to the image for the tab
                imageHeight: '133px',                         //height of tab image
                imageWidth: '44px',                           //width of tab image   
        });

    or you can leave out these options
    and set the image properties using css
    
*/


(function($){
    $.fn.tabSlideOut = function(callerSettings) {
        var settings = $.extend({
            tabHandle: '.handle',
            speed: 300, 
            action: 'click',
            tabLocation: 'left',
            topPos: '200px',
            leftPos: '20px',
            //containerHeight: '400px', optional
            fixedPosition: false,
            positioning: 'absolute',
            pathToTabImage: null,
            imageHeight: null,
            imageWidth: null,
            onLoadSlideOut: false                       
        }, callerSettings||{});

        settings.tabHandle = $(settings.tabHandle);
        var obj = this;
        if (settings.fixedPosition === true) {
            settings.positioning = 'fixed';
        } else {
            settings.positioning = 'absolute';
        }
        
        //ie6 doesn't do well with the fixed option
        if (document.all && !window.opera && !window.XMLHttpRequest) {
            settings.positioning = 'absolute';
        }
        

        
        //set initial tabHandle css
        
        if (settings.pathToTabImage != null) {
            settings.tabHandle.css({
            'background' : 'url('+settings.pathToTabImage+') no-repeat',
            'width' : settings.imageWidth,
            'height': settings.imageHeight
            });
        }
        
        settings.tabHandle.css({ 
            'display': 'block',
            //'textIndent' : '-10px',
            'outline' : 'none',
            'position' : 'absolute'
        });
        
        obj.css({
            'line-height' : '1',
            'position' : settings.positioning
        });

        if(settings.containerHeight == null) {
            settings.containerHeight = obj.outerHeight();
        }
        
        var properties = {
                    containerWidth: parseInt(obj.outerWidth(), 10) + 'px',
                    containerHeight: parseInt(settings.containerHeight, 10) + 'px',
                    tabWidth: parseInt($(settings.tabHandle).textWidth() + 8, 10) + 'px',
                    tabHeight: parseInt(settings.tabHandle.outerHeight(), 10) + 'px'
                };

        settings.tabHandle.css(
            {'height' : properties.tabHeight,
             'border-top-left-radius': '5px 5px',
             '-moz-border-radius-topleft': '5px 5px',
             'border-top-right-radius': '5px 5px',
             '-moz-border-radius-topright': '5px 5px'
            });
            
        var width = $(settings.tabHandle).textWidth() +10;
        settings.tabHandle.css(
            {
             'width' : width + 'px'
            });

        //set calculated css
        if(settings.tabLocation === 'top' || settings.tabLocation === 'bottom') {
            obj.css({'left' : settings.leftPos});
            settings.tabHandle.css(
            {'right' : 0
            });
        }
        
        if(settings.tabLocation === 'top') {
            obj.css({'top' : '-' + properties.containerHeight});
            settings.tabHandle.css({'bottom' : '-' + properties.tabHeight});
        }

        if(settings.tabLocation === 'bottom') {
            obj.css({'bottom' : '-' + properties.containerHeight, 'position' : 'fixed'});
            settings.tabHandle.css({'top' : '-' + properties.tabHeight});
            
        }
        
        if(settings.tabLocation === 'left' || settings.tabLocation === 'right') {
            obj.css({
                'height' : properties.containerHeight,
                'top' : '80px'//settings.topPos
            });
            
            var top = parseInt(properties.tabWidth) / 2 + parseInt(settings.topPos);
            settings.tabHandle.css(
            {'top' : top + 'px'
            });
        }
        
        if(settings.tabLocation === 'left') {
            obj.css({ 'left': '-' + properties.containerWidth});
            var right = (parseInt(properties.tabWidth) / 2) 
                + (parseInt(properties.tabHeight) /2);
            settings.tabHandle.css(
            {'right' : '-' + right + 'px',
             '-webkit-transform': 'rotate(90deg)',
             '-moz-transform': 'rotate(90deg)',
             '-o-transform': 'rotate(90deg)'
            });
        }

        if(settings.tabLocation === 'right') {
            obj.css({ 'right': '-' + properties.containerWidth});
            var left = (parseInt(properties.tabWidth) / 2) 
                + (parseInt(properties.tabHeight) /2);

            settings.tabHandle.css(
            {'left' : '-' + left + 'px',
             '-webkit-transform': 'rotate(270deg)',
             '-moz-transform': 'rotate(270deg)',
             '-o-transform': 'rotate(270deg)'
            });
            
            $('html').css('overflow-x', 'hidden');
        }

        //functions for animation events
        
        settings.tabHandle.click(function(event){
            event.preventDefault();
        });

        var slideIn = function() {
            if (settings.tabLocation === 'top') {
                obj.animate({top:'-' + properties.containerHeight}, settings.speed).removeClass('open'); 
            } else if (settings.tabLocation === 'left') {
                obj.animate({left: '-' + properties.containerWidth}, settings.speed).removeClass('open');
			} else if (settings.tabLocation === 'right' && obj[0].id === "trend_tab") {
 	            
 				var right_dim = $("#trend_tab")[0].offsetWidth;
 				obj.animate({right: '-' + right_dim}, settings.speed).removeClass('open');
 			
 			}else if (settings.tabLocation === 'right') {
				obj.animate({right: '-' + properties.containerWidth}, settings.speed).removeClass('open');
			} else if (settings.tabLocation === 'bottom') {
                obj.animate({bottom: '-' + properties.containerHeight}, settings.speed).removeClass('open');
            }
        };
        
        var slideOut = function() {
            if (settings.tabLocation == 'top') {
                obj.animate({top:'-3px'},  settings.speed).addClass('open');
            } else if (settings.tabLocation == 'left') {
                obj.animate({left:'-3px'},  settings.speed).addClass('open');
            } else if (settings.tabLocation == 'right') {
                obj.animate({right:'-3px'},  settings.speed).addClass('open');
            } else if (settings.tabLocation == 'bottom') {
                obj.animate({bottom:'-3px'},  settings.speed).addClass('open');
            }
        };

        var clickScreenToClose = function() {
            obj.click(function(event){
                event.stopPropagation();
            });
            
            $(document).click(function(){
				if(!(obj.selector == "#thumbnail" && $('#thumbnail.just-closed-context-menu').length > 0)) {
					slideIn();
				} else {
					$('#thumbnail').removeClass('just-closed-context-menu');
				}
            });
        };
        
        var clickAction = function(){
			
			settings.tabHandle.click(function(event){
				
                if (obj.hasClass('open')) {
                    slideIn();

                } else {
                    slideOut();
                }
            });
            
            clickScreenToClose();
        };
        
        var hoverAction = function(){
            obj.hover(
                function(){
                    slideOut();
                },
                
                function(){
                    slideIn();
                });
                
                settings.tabHandle.click(function(event){
                    if (obj.hasClass('open')) {
						
						slideIn();
                    }
                });
                clickScreenToClose();
                
        };
        
        var slideOutOnLoad = function(){
			
			slideIn();
            setTimeout(slideOut, 500);
        };
        
        //choose which type of action to bind
        if (settings.action === 'click') {
            clickAction();
        }
        
        if (settings.action === 'hover') {
            hoverAction();
        }
        
        if (settings.onLoadSlideOut) {
            slideOutOnLoad();
        };
        
    };
})(jQuery);
