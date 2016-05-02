var notificationsSocket;
var notificationsSocketOpen = false;
var socketType;

function getSocketType(){
    var type;
    if(!("WebSocket" in window)){
        return "not_supported";
    }else if("MozWebSocket" in window){
        type = "MozSoc";
    }else{
        type = "RegSoc";
    }
    return type;
}

function initLiveAnnotations(userId){
    if(userId > 0){
        socketType = getSocketType();
        if(socketType == 'not_supported')	alert("Sockets not supported");

        $("#counter").show();
        $("#notylist").notylist();

        $.Topic("newNotification").subscribe(incrementCounter);
        $.Topic("newNotification").subscribe(addNotification);

        initNotificationsConnection(confluenceHostname, notificationsSocketPort, userId);
        //getActiveInterests(userId);
    }
}

function incrementCounter(){
    $("#counter").notycount("incrementCounter");
}

function addNotification(notificationObj){
    $("#notylist").notylist("addNotification", notificationObj);
}

function initNotificationsConnection(hostname, port, userId){
    var socketType = getSocketType();
    var host = 'ws://'+hostname+':'+port;
	
    if(!notificationsSocketOpen || !window.annotationsSocket || notificationsSocket.readyState == notificationsSocket.CLOSED || notificationsSocket.readyState == notificationsSocket.CLOSING){
        try{
            notificationsSocketOpen = true;
            if(socketType == "MozSoc")
                notificationsSocket = new MozWebSocket(host);
            else if	(socketType == "RegSoc")
                notificationsSocket = new WebSocket(host);
			else	;
	
            notificationsSocket.onopen = function(){
                var userInfo = {
                    "interestedUserId": userId
                };
                userInfoJSON = $.toJSON(userInfo);
                notificationsSocket.send(userInfoJSON);
                console.log(host+':opened');
            };
			
            notificationsSocket.onmessage = function(msg){
                // this should be implemented by the UI module to update the right elements.
                console.log(msg);
                var notification = $.evalJSON(msg.data);
                console.log(notification);
                $.Topic("newNotification").publish(notification);
            };
			
            notificationsSocket.onclose = function(){
                if(notificationsSocketOpen){
                    //try to reopen it after a timeout.
                    console.log(host+':closed');
                    //initNotificationsConnection(hostname, port, userId);
                }
            };
        }catch(exception){
			alert(exception);
        }
    }else{
        console.log(host+':was already open');
    }
}

function clearLiveAnnotations(){
    if(window.notificationsSocket && notificationsSocket.readyState == notificationsSocket.OPEN){
        console.log("about to close socket");
        notificationsSocketOpen = false;
        notificationsSocket.close();
    }   
    $("#counter").hide();
    //TODO: clear the list of notifications
}

function getLatestNotifications(userId){
    //get the active interests from database and add them to the list
    $.getJSON(RESTbase+"user/"+userId+"/notifications", {limit:10},
		function(data){
			$.each(data.results, function(){
				add_notification(this, 1);
			});
		}
    );
}

function registerNewLiveInterest(raMin, raMax, decMin, decMax, keyword, label){
    var liveinterest = {
        "label": label,
        "keyword": keyword,
        "box":{
            "bottomLeft": {
                "ra":raMin,
                "dec":decMin
            },
            "topRight": {
                "ra":raMax,
                "dec":decMax
            }
        },
        "active":1,
        "userId": {
            "userId": getUserId()
        }
    }
    
    console.log(liveinterest);
    var liveinterestJSON = $.toJSON(liveinterest);
    
    var url = RESTbase+"liveinterest/add";
    
    $.ajax({
        url: "lib/jsonHandler.php",
        type: "POST",
        data: {
            url: url,
            data: liveinterestJSON
        },
        success: function(data){
            console.log(data);
        },
        dataType: "json"
    });
}

function getActiveInterests(userId){
    //get the active interests from database and add them to the list
    $.getJSON(RESTbase + "user/"+userId+"/liveinterests", {
        //TODO: check the getJSON() jQuery
    },
    function(data){
        $.each(data.results, function(){
            add_interest(this);
        });
    });
}

function sendRemoveInterestRequest(interestsSocket, interest_obj, onSuccess){
    try{
        $.ajax({
            url: RESTbase + interest_obj.interestId+ '/deactivate',
            type: "POST",
            success: function(data){
                if(data.msg != "SUCCESS") {
                    console.log("Not deactivated");
                }
                else {
                    console.log("Deactivated");
                }
            }
        });
    }catch(exception){
        alert(exception);
    }
}
//TODO: register the event of adding a number to the notifications button.
//TODO: register the event of decreasing the notifications...