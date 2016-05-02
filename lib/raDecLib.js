function convertRaDec(rah, ram, ras, decd, decm, decs, decsign){
    var raDec = new Array();
    
    rah = parseFloat(rah);
    ram = parseFloat(ram);
    ras = parseFloat(ras);
    decd = parseFloat(decd);
    decm = parseFloat(decm);
    decs = parseFloat(decs);
    
    console.log(rah);
    console.log(ram);
    console.log(ras);
    console.log(decd);
    console.log(decm);
    console.log(decs);
    
    var ra = (rah + (ram/60) + (ras/3600))*15;
    var dec = (Math.abs(decd) + (decm/60) + (decs/3600));
    if(decsign && decsign.indexOf("-") >= 0)
        dec = dec * -1.0;
    
    raDec.push(ra);
    raDec.push(dec);
    
    return raDec;
}