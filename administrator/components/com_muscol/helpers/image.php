<?php
error_reporting(0);

function hexToImageColor($im, $hexColor){
	$hexColor = str_replace("#","",$hexColor);
	$red = hexdec(substr($hexColor, 0, 2));
	$green = hexdec(substr($hexColor, 2, 2));
	$blue = hexdec(substr($hexColor, 4, 2));
	return imagecolorallocate($im, $red, $green, $blue);
}

$imageFile = $_GET['file'];
$width = isset($_GET['width']) && is_numeric($_GET['width']) && $_GET['width'] > 0 ? $_GET['width'] : false; // false => auto
$height = isset($_GET['height']) && is_numeric($_GET['height']) && $_GET['height'] > 0 ? $_GET['height'] : false; // false => auto
$cornerRadius = isset($_GET['corner_radius']) && !is_numeric($_GET['corner_radius']) && $_GET['corner_radius'] > -1 ? $_GET['corner_radius'] : 0;
$cornerBackground = isset($_GET['corner_background']) && eregi("^(#{0,1}[a-fA-F0-9]{6}|transparent)$",$_GET['corner_background']) ? $_GET['corner_background'] : "transparent";
$cornerAntiAlias = isset($_GET['corner_antialias']) && $_GET['corner_antialias'] == 0 ? false : true;
$cornerPosition = isset($_GET['corner_position']) && in_array($_GET['corner_position'],array('inner','outer')) ? $_GET['corner_position'] : "outer";
$borderThickness = isset($_GET['border_thickness']) && is_numeric($_GET['border_thickness']) ? $_GET['border_thickness'] : 0; // Negative for inside border, positive for outside border
$borderPosition = isset($_GET['border_position']) && in_array($_GET['border_position'],array('inside','outside')) ? $_GET['border_position'] : "outside";
$borderColor = isset($_GET['border_color']) && eregi("^(#{0,1}[a-fA-F0-9]{6}|transparent)$",$_GET['border_color']) ? $_GET['border_color'] : "#000000";
$resizingMethod = isset($_GET['resizing_method']) && in_array($_GET['resizing_method'],array('fit','fill')) ? $_GET['resizing_method'] : "fill";
$imageAlignment = isset($_GET['image_alignment']) && in_array($_GET['image_alignment'],array('left','center','right','top','middle','bottom')) ? $_GET['image_alignment'] : "center";
$jpegQuality = isset($_GET['jpeg_quality']) && is_numeric($_GET['jpeg_quality']) && $_GET['jpeg_quality'] >= 0 && $_GET['jpeg_quality'] <= 100 ? $_GET['jpeg_quality'] : "100";
$backgroundColor = isset($_GET['background_color']) && eregi("^(#{0,1}[a-fA-F0-9]{6}|transparent)$",$_GET['background_color']) ? $_GET['background_color'] : "#000000";
$rotation = 0;
$rotationMethod; // resizing or not
$rotationReferencePoint; // top-left, top-middle, top-right, middle-left, center (middle-center), middle-right, bottom-left, bottom-middle, bottom-right
$allowExpanding = false;

//germi
//$imageFile = .$imageFile

if (empty($imageFile) || !file_exists($imageFile)){
	$text = "File not found";
	$size = 10;
	$font = 'verdana.ttf';
	
	$im = imagecreate(100,30);
	imagefill($im, 0, 0, hexToImageColor($im, "#ffffff"));
	imagettftext($im, $size, 0, 5, 20, hexToImageColor($im, "#000000"),$font,$text);
	imagejpeg($im,null,100);
	
	exit();
}

$fileType = strtolower(substr($imageFile, strrpos($imageFile,".")+1));
$fileType = $fileType == "jpg" ? "jpeg" : $fileType;

eval('$src_im = imagecreatefrom'.$fileType.'($imageFile);');
$src_size = getimagesize($imageFile);

if ($width && !$height){
	$height = $src_size[1] * $width / $src_size[0];
} else if ($height && !$width){
	$width = $src_size[0] * $height / $src_size[1];
} else if (!$width && !$height){
	$width = $src_size[0];
	$height = $src_size[1];
}

$im = imagecreatetruecolor($width, $height);
imagealphablending($im,true);
//germi
//imageantialias($im, $cornerAntiAlias);
if ($backgroundColor == "transparent"){
	$transparentColor = hexToImageColor($im, "#000000");
	imagecolortransparent($im,$transparentColor);
} else {
	imagefill($im, 0, 0, hexToImageColor($im, $backgroundColor));
}


$sx = 0;
$sy = 0;
$sw = $src_size[0];
$sh = $src_size[1];

$dw = $width;
$dh = $src_size[1] * $width / $src_size[0];

if (($resizingMethod == "fill" && $dh < $height) || ($resizingMethod == "fit" && $dh > $height)){
	$dw = $src_size[0] * $height / $src_size[1];
	$dh = $height;
}

switch ($imageAlignment){
	case "left":
	case "top":
		$dx = 0;
		$dy = 0;
	break;
	
	case "center":
	case "middle":
		$dx = ($width-$dw)/2;
		$dy = ($height-$dh)/2;
	break;
	
	case "right":
	case "bottom":
		$dx = $width-$dw;
		$dy = $height-$dh;
	break;
}

imagecopyresampled($im, $src_im, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh);

if ($backgroundColor == "transparent"){
	header("Content-type: image/png");
	imagepng($im);
} elseif ($fileType == "jpeg") {
	header("Content-type: image/jpeg");
	imagejpeg($im,null,$jpegQuality);
} else {
	header("Content-type: image/".$fileType);
	eval('image'.$fileType.'($im);');
}


?>