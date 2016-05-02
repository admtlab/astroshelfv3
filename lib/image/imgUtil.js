function get_image(skycanvas, ra, dec) {
	
	var req = null;
	var dataURL = skycanvas;
	
	console.log(dataURL.value);
		
    var url = "js/saveImage.php";

    var req = null;
    try{
        req = new XMLHttpRequest();
    } catch (ms){
        try{
            req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (nonms){
            try{
                req = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (failed){
                req = null;
            }
        }
    }
		
    req.open("POST", url, true);   
    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    req.send("img_data=" + encodeURIComponent(dataURL) + "&RA=" + ra + "&Dec=" + dec + "&bool=" + bool);
	req.close();
	
}
