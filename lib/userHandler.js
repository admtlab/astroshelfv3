/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function login_loading() {
	var $parent = $("#login_tab3").parent();
	$("#login_tab3").text('logout');
	var user_name = $("#username").text();
	$parent.append("<b> - "+user_name+"</b>");
	$("#tabs3_middle").show(500);
	$("#tabs3_bottom").show(500);		
	
	//load query history from AstroDB
	$.ajax({type: "GET",
			url: "./lib/db/local/queryASTRO.php",
			data: {userid: getUserId(), select: 1, base: 0, offset: 5},
			dataType: "html"
	}).done(function(html){
		$("#the_table_tabs3").append(html);
		//the_base = 0;
	});
	
	//load bookmarks
	if(bookmarks_fn.setupBookmarkTables) {
		bookmarks_fn.setupBookmarkTables();
	}
	if(annotations_fn.setupAnnotationTables) {
		annotations_fn.setupAnnotationTables();
	}
}

var userData = null;

function initUser(){
    if (readUserFromCookie()) {
        doLogin(userData);
	} else if($("#APIToken").text().length > 5) {
		doLogin($.parseJSON($("#APIToken").text()));
	} else {
		doLogout();
	}
	$("#APIToken").remove();
}

function doLogin(data){
    userData = data.user;
    setUserCookie(data);
    
    $("#loginout").button({label:"Logout", icons: {primary:"ui-icon-extlink"}});
    $('#loginout').off('click.tologin');
    $("#loginout").on("click.tologout", function(){
        doLogout();
    });

    $("#preferences").show();
	$("#supernovae").show();
    $("#username").text(userData.fname + " " + userData.lname);
    //initLiveAnnotations(getUserId());
	
	login_loading();
}

function doLogout(){
	window.location = "/astroshelfv3/splash.html";
}

$(window).unload(function() {
	$.ajax({
		async:false,
		type:"GET",
		url: "https://fake_login:fake_pass@astro.cs.pitt.edu/astroshelfv3/index.php"
	});
	console.log("unloading");
	userData = null;
    deleteUserCookie();
});

function getUserId(){
    if(userData != null){
        return userData.user_id;
    }
    return -1;
}

function isUserLoggedIn(){
    if(userData == null){
        return false;
    }
	
	var cookie = getUserCookie();
	var expires = cookie.expires;
	if(moment().isBefore(expires)) {
		return true;
	} else {
		return false;
	}
}

//////////COOKIES///////////

function readUserFromCookie(){
    var userJSON = readCookie("astroshelfUser");
    if(userJSON)	userData = $.evalJSON(userJSON);
    return isUserLoggedIn();
}

function setUserCookie(data){
    if(data != null){
        var userCookieVal = $.toJSON(data);
        setCookie("astroshelfUser", userCookieVal, 1);
        return true;
    } else {
        console.log("user cookie cannot be set -- User not logged in");
        return true;
    }
}

function getUserCookie() {
	var userJSON = readCookie("astroshelfUser");
	return $.evalJSON(userJSON);
}

function deleteUserCookie() {
    setCookie("astroshelfUser", "", -1);
}

function setCookie(cookieName,cookieValue,nDays){
	var today = new Date();
	var expire = new Date();
	if (nDays==null || nDays==0) nDays=1;
	expire.setTime(today.getTime() + 3600000*24*nDays);
	document.cookie = cookieName+"="+escape(cookieValue)+ ";expires="+expire.toGMTString();
}

function readCookie(cookieName){
	var theCookie = " "+document.cookie;
	var ind = theCookie.indexOf(" "+cookieName+"=");
	if (ind == -1) ind = theCookie.indexOf(";"+cookieName+"=");
	if (ind == -1 || cookieName == "") return "";
	var ind1 = theCookie.indexOf(";",ind+1);
	if (ind1 == -1) ind1 = theCookie.length; 
	return unescape(theCookie.substring(ind+cookieName.length+2,ind1));
}

function setAuthHeaders(xhr) {
	xhr.setRequestHeader("x-access-token", getUserCookie().token);
}
