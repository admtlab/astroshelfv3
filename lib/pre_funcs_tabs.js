var gFUNCS = gFUNCS || {};

//For record query history
gFUNCS.insert_query = function(survey, query){
	var user_id = getUserId();
	if(user_id == -1){
		//alert("not login!!!");
	}else{
		$.ajax({type: "POST", url: "./lib/db/local/queryASTRO.php", data: {user_id: user_id, survey: survey, query: query, insert: 1}});
	}				
}

//For generating formatted time
gFUNCS.create_time = function(){
	var curr_time = new Date();
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var year = curr_time.getFullYear();
	var month = months[curr_time.getMonth()];
	var date = curr_time.getDate();
	var hour = curr_time.getHours();
	var min = curr_time.getMinutes();
	var sec = curr_time.getSeconds();
	var formatted_time = date+', '+month+' '+year+' '+hour+':'+min+':'+sec;
	return formatted_time;
}

//For convert UNIX timestamp to formatted time
gFUNCS.convert_time = function(unixed){
	var wrong_ts = unixed + '';
	var ts = (wrong_ts).substring(0, wrong_ts.length-3);
	var time = new Date(ts*1000);
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var year = time.getFullYear();
	var month = months[time.getMonth()];
	var date = time.getDate();
	var hour = time.getHours() > 9 ? time.getHours() : '0' + time.getHours();
	var min = time.getMinutes() > 9 ? time.getMinutes() : '0' + time.getMinutes();
	var sec = time.getSeconds() > 9 ? time.getSeconds() : '0' + time.getSeconds();
	var formatted_time = date+', '+month+' '+year+' '+hour+':'+min+':'+sec;
	return formatted_time;
}		

//For converting RA/Dec
gFUNCS.convert_radec = function(ra, dec, the_survey){
	var tmp1 = (parseFloat(ra) / parseFloat(15));
	if(tmp1 >= 0){
		var hour = Math.floor(tmp1);
	}else{
		var hour = Math.ceil(tmp1);
	}
	var tmp2 = (tmp1 - parseFloat(hour)) * parseFloat(60);
	if(tmp2 >= 0){
		var min = Math.floor(tmp2);
	}else{
		var min = Math.ceil(tmp2);
	}
	var tmp3 = (tmp2 - parseFloat(min)) * parseFloat(60);
	//var sec = Math.round(tmp3, 3);
	var sec = tmp3.toFixed(3);
	//alert('RA: ' + hour + ' ' + min + ' ' + sec);
	
	if(dec >= 0){
		var degree = Math.floor(dec);
	}else{
		var degree = Math.ceil(dec);
	}
	var tmp4 = (parseFloat(dec) - parseFloat(degree)) * parseFloat(60);
	if(tmp4 >= 0){
		var min_d = Math.floor(tmp4);
	}else{
		var min_d = Math.ceil(tmp4);
	}
	var tmp5 = (tmp4 - parseFloat(min_d)) * parseFloat(60);
	//var sec_d = Math.round(tmp5, 3);
	var sec_d = tmp5.toFixed(3);
	//alert('Dec: ' + degree + ' ' + min_d + ' ' + sec_d);
	
	if(dec >= 0){
		var the_ra_dec = hour + ' ' + min + ' ' + sec + ' +' + degree + ' ' + min_d + ' ' + sec_d;
		var the_name = the_survey+' J' + hour + min + sec + '+' + degree + min_d + sec_d;
	}else{
		var the_ra_dec = hour + ' ' + min + ' ' + sec + ' -' + Math.abs(degree) + ' ' + Math.abs(min_d) + ' ' + Math.abs(sec_d);
		var the_name = the_survey+' J' + hour + min + sec + '-' + Math.abs(degree) + Math.abs(min_d) + Math.abs(sec_d);
	}
	
	return {ra_dec: the_ra_dec, name: the_name};
}