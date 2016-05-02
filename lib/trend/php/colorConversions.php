<?php

# function to convert HSV to RGB
function &HSVtoRGB($H, $S, $V) {

    $RGB = array();
		
	//1
    $H /= 60;

    //2
    $I = floor($H);
    $F = $H - $I;
	
    //3
    $M = $V * (1.0 - $S);
    $N = $V * (1.0 - $S * $F);
    $K = $V * (1.0 - $S * (1.0 - $F));
	
    //4
    switch ($I) {
        case 0:
            $RGB = array($V,$K,$M);
            break;
        case 1:
            $RGB = array($N,$V,$M);
            break;
        case 2:
            $RGB = array($M,$V,$K);
            break;
        case 3:
            $RGB = array($M,$N,$V);
            break;
        case 4:
            $RGB = array($K,$M,$V);
            break;
        case 5:
        case 6: //for when $H=1 is given
            $RGB = array($V,$M,$N);
            break;
    }
    return $RGB;
}

function &RGBtoHSV($R, $G, $B)    // RGB values:    0-255, 0-255, 0-255
{                                // HSV values:    0-360, 0-100, 0-100
    // Convert the RGB byte-values to percentages
    $R = ($R / 255);
    $G = ($G / 255);
    $B = ($B / 255);

    // Calculate a few basic values, the maximum value of R,G,B, the
    //   minimum value, and the difference of the two (chroma).
    $maxRGB = max($R, $G, $B);
    $minRGB = min($R, $G, $B);
    $chroma = $maxRGB - $minRGB;

    // Value (also called Brightness) is the easiest component to calculate,
    //   and is simply the highest value among the R,G,B components.
    // We multiply by 100 to turn the decimal into a readable percent value.
    $computedV = 100 * $maxRGB;

    // Special case if hueless (equal parts RGB make black, white, or grays)
    // Note that Hue is technically undefined when chroma is zero, as
    //   attempting to calculate it would cause division by zero (see
    //   below), so most applications simply substitute a Hue of zero.
    // Saturation will always be zero in this case, see below for details.
    if ($chroma == 0)
        return array("h"=>0, "s"=>0, "v"=>$computedV);

    // Saturation is also simple to compute, and is simply the chroma
    //   over the Value (or Brightness)
    // Again, multiplied by 100 to get a percentage.
    $computedS = 100 * ($chroma / $maxRGB);

    // Calculate Hue component
    // Hue is calculated on the "chromacity plane", which is represented
    //   as a 2D hexagon, divided into six 60 degree sectors. We calculate
    //   the bisecting angle as a value 0 <= x < 6, that represents which
    //   portion of which sector the line falls on.
    if ($R == $minRGB)
        $h = 3 - (($G - $B) / $chroma);
    elseif ($B == $minRGB)
        $h = 1 - (($R - $G) / $chroma);
    else
        $h = 5 - (($B - $R) / $chroma);

    // After we have the sector position, we multiply it by the size of
    //   each sector's arc (60 degrees) to obtain the angle in degrees.
    $computedH = 60 * $h;
	$ret = array("h"=>$computedH, 
		"s"=>$computedS, "v"=>$computedV);
    
		return $ret;
}

function &HEXtoRGB($hex) {

	$color = array();

	if(strlen($hex) == 3) {
		$color['r'] = hexdec(substr($hex, 0, 1) . $r);
		$color['g'] = hexdec(substr($hex, 1, 1) . $g);
		$color['b'] = hexdec(substr($hex, 2, 1) . $b);
	}
	else if(strlen($hex) == 6) {
		$color['r'] = hexdec(substr($hex, 0, 2));
		$color['g'] = hexdec(substr($hex, 2, 2));
		$color['b'] = hexdec(substr($hex, 4, 2));
	}

	return $color;
}

function RGBtoHEX($r, $g, $b) {
	//String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
	$hex = "#";
	$hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);

	return $hex;
}

?>