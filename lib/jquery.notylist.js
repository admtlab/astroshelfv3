/**
 * jQuery notylist Widget v0.1
 * Author: Panickos Neophytou (http://panickos.com)
 *
 * Licensed under the MIT licenses:
 * http://www.opensource.org/licenses/mit-license.php
 *
 **/
$(function(){
	$.widget("ui.notylist", {
		// default options
		options: {
			notifications: {},
			notificationElements: {},
			interestIdToNotificationObjs: {},
			matchedInterestIds: [],
			gotoHandler: function(notificationObj){},
			markAsRead: function(notificationObj){},
			createAddButton: true
		},
		
		_create: function(){
			console.log("notification list widget created");
			var base = this.element;
		
			this.element
				// add a class for theming
				.addClass( "notification-list" )
				// prevent double click to select text
				.disableSelection();
			
			this.getLatestNotifications();
			
			this._refresh();
		},
		
		getLatestNotifications: function(){
			console.log("get latest notes");
			var userId = getUserId();
		  	if(userId >= 0){
			  	//get the active interests from database and add them to the list
				//TODO: no such a script called notification.php
				$.ajax({
					url: RESTbase+'liveinterest',
					type: "GET",
					dataType: "jsonp",
					data: {user_id:userId, n:10},
					success: function (data) {
						console.log(data);
						/*$.each(data.results, function(){
							addNotification(this);
						});*/
					}
				});
			}
		},
		               
//      {
//			msgType:"ANNOTATION_UPDATE",
//			annotation:{
//			 	annotationId:"616AB",
//				timestamp:12345467865,
//				userId:"23",
//				annotationType:"type of annotation", //Not being used but put some text
//				annotationValues:"this is the comment/annotation data", //This is a generic string 
//				sentiment:"POSITIVE"      //{“POSITIVE”/”NEGATIVE”}
//			},
//			annotationTarget:{
//				id:"sasGB1672", //STRING
//				targetType:"object type/paper", //object, set, annotation
//				targetName:"GGB1sss672", //the object’s common name... optional
//				ra:12.34462133,
//				dec:956.3237764
//			},
//			matchedInterestsId: [2]
//		}
                
		addNotification: function(notificationObj){
			var me = this;
			
			$(notificationObj.matchedInterestsId).each(function(index, value){
				if(!$.isArray(me.options.interestIdToNotificationObjs[value]))
					me.options.interestIdToNotificationObjs[value] = new Array();
				me.options.interestIdToNotificationObjs[value].push(notificationObj);
			});
			
			this.options.notifications[notificationObj.data.annoId] = notificationObj;
			
			var ts = new Date(notificationObj.data.tsCreated);
			var timestampStr = ts.getFullYear()+'/'+ts.getMonth()+'/'+ts.getDate()+' '
				+ts.getHours()+':'+ts.getMinutes();
                        
			var notificationElement = $('<div/>').appendTo(me.element);
			$(notificationElement).data('notif_obj', notificationObj);
			
			var infoBox = {
				contentObjs:[
					{label : notificationObj.data.userId.username, classes : "username"},
					{label : notificationObj.data.annoTypeId.annoTypeName, classes : "type"},
					{label : timestampStr, classes : "timestamp"},
					[
						{label : notificationObj.data.objectInfoCollection.name, classes : ""},
						{label : notificationObj.data.targetType, classes : "type"},
						{label : notificationObj.data.objectInfoCollection.surveyObjId, classes : "type", onClick: function(){
								FUNCS4.showSDSSDetails(notificationObj.data.objectInfoCollection.surveyObjId, notificationObj.data.objectInfoCollection.ra, notificationObj.data.objectInfoCollection.dec);
						}},
						{label : notificationObj.data.objectInfoCollection.ra+","+notificationObj.data.objectInfoCollection.dec, classes : "raDec", onClick: function(){
								skyView.jump(notificationObj.data.objectInfoCollection.ra, notificationObj.data.objectInfoCollection.dec);
						}}
					]
				],
				classes: "ui-widget ui-state-highlight ui-corner-all"
			};
                        
			$(notificationElement).infobox(infoBox);
			//$(notificationElement).notification({notification:notificationObj, gotoHandler:me.options.gotoHandler});
			
			this.options.notificationElements[notificationObj.data.annoId] = notificationElement;
		},
		
		removeNotification: function(notificationObj){
			//detach the notification element from this object.
			$.remove(this.options.notificationElements[notificationObj.annotation.annotationId]);
			
			delete this.options.notifications[notificationObj.annotation.annotationId];
			delete this.options.notificationElements[notificationObj.annotation.annotationId];
		},
		
		/*
		 *  Filters the notifications based on the array of interest ids passed as query.
		 **/
		filterNotifications: function(query){
			var base = this;
			base.options.matchedInterestIds = query;
			
			$.each(base.options.notificationElements, function(key, value){
					$(value).hide();
			});
			
			if(query.length < 1){
				$.each(base.options.notificationElements, function(key, value){
						$(value).show();
				});
			}else{
				$.each(query, function(index, value){
					$(base.options.interestIdToNotificationObjs[value]).each(function(i, v){
						$(base.options.notificationElements[v.annotation.annotationId]).show();
					});
				});
			}
		},
		
		_refresh: function(){
			this.filterNotifications(this.options.matchedInterestIds);
			
			this._trigger("change");
		},
		
		// _setOptions is called with a hash of all options that are changing
		// always refresh when changing options
		_setOptions: function(){
			// in 1.9 would use _superApply
			$.Widget.prototype._setOptions.apply( this, arguments );
			this._refresh();
		},
	
		// _setOption is called for each individual option that is changing
		_setOption: function(key, value ){
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
