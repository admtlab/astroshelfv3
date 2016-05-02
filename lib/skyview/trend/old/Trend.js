function genTrendImg(){
	$('#genTrendButton').button("disable");
	$('#loadImage').removeClass('hidden');
	$('#loadImage').loading(true);	
	$('#trendImage').removeClass('hidden');
	$('#trendImage').empty().append('<br/>');
	$('#plotDiv').remove();
	
	var num = $('#numObjects').val();
	var type = $('#typeObjects').val();
	var waveMin = 3800; //document.getElementById('minWave').value;
	var waveMax = 9200; //document.getElementById('maxWave').value;
	var waveNum = 500; //document.getElementById('numWave').value;
	reqTrendImg(num, type, waveMin, waveMax, waveNum);
}

function contextGenTrendImg(numObjects, type){
	$('#genTrendButton').button("disable");
	$('#loadImage').removeClass('hidden');
	$('#loadImage').loading(true);	
	$('#trendImage').removeClass('hidden');
	$('#trendImage').empty().append('<br/>');
	$('#plotDiv').remove();
    var waveMin = 3800; //document.getElementById('minWave').value;
    var waveMax = 9200; //document.getElementById('maxWave').value;
    var waveNum = 500; //document.getElementById('numWave').value;
    reqTrendImg(numObjects, type, waveMin, waveMax, waveNum);
}

function reqTrendImg(numObj, typeObj, minWave, maxWave, numWave){
	// Construct the request
	var raDecStr = $("#RA-Dec").text().split(",");
	var ra = parseFloat(raDecStr[0]);
	var dec = parseFloat(raDecStr[1]);

	var url = "lib/trend/querySpecSDSS.php"
	var data = "ra=" + ra + "&dec=" + dec + "&numObj=" + numObj + 
	"&typeObj=" + typeObj + "&minWave=" + minWave + "&maxWave=" + maxWave + "&numWave=" + numWave;
	var d = new Date();
	d = d.getTime();
	var createTrend = "lib/trend/generateTrendImage.php?d=" + d;
  
	var request = $.ajax({
		url: url,
		type: "GET",
		data: data,
		dataType: "html"
	});
    console.log("processing trend");

	request.done(function(msg){
		$('#trendImage').append(msg + "<img src='" + createTrend + "' usemap='#trendMap'>");
		$('#genTrendButton').button("enable");
		$('#loadImage').addClass('hidden');
		$('#loadImage').loading(false);
		console.log("done trend");
	});

    request.fail(function(jqXHR, textStatus){
        console.log( "Request failed: " + textStatus );
    });
  
	// Do the request
	//sendRequest(request);
}

function showPlot(plot, ra, dec){
	$('#trendImage').addClass('hidden');
	var imWidth = top.document.getElementById('trend_image_content').offsetWidth;
	$('#trendImage').after("<div id='plotDiv' style='text-align:center'><br/><img width='" + imWidth +"' height='" + imWidth + "' src='" + plot + "'><br/><br/><input type='button' id='plotJump' value='Show in SkyView' /></div>");
	
	$('#plotDiv img').on('click', function(){
		$(this).parent().remove();
		$('#trendImage').removeClass('hidden');
	});
	
	$('#plotDiv #plotJump').button();
	$('#plotDiv #plotJump').on('click', function(){ skyView.jump(ra, dec); });
}

/* 
function showTrendImgInterface(){
    document.getElementById('trendImageDiv').removeAttribute('class');
    document.getElementById('catalogSearch').setAttribute('class', 'hidden');
}
    
function showSearchMain(){
    document.getElementById('catalogSearch').removeAttribute('class');
    document.getElementById('trendImageDiv').setAttribute('class', 'hidden');
}
	
function returnSearch(){	
	top.frames[1].document.getElementById('trendPanel').setAttribute('class', 'hidden');
	top.frames[1].document.getElementById('searchPanel').setAttribute('class','navButtonCurrent');
}
*/