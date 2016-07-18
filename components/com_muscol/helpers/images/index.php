<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php 	error_reporting(E_ALL ^E_NOTICE);
	if (!$_POST){
		$_POST['file'] = "image2.jpg";
		$_POST["width"] = "200";
		$_POST["height"] = "100";
		$_POST["corner_radius"] = "20";
		$_POST["corner_background"] = "#ff9900";
		$_POST["corner_antialias"] = "1";
		$_POST["border_thickness"] = "10";
		$_POST["border_color"] = "#ffffff";
		$_POST["resizing_method"] = "fit";
		$_POST["image_alignment"] = "center";
		$_POST["jpeg_quality"] = "100";
		$_POST["background_color"] = "#000000";
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Testing Area</title>
<style>
	label, input, select, option{
		font-family: Verdana , Arial, Helvetica, sans-serif;
		font-size: 10px;
	}
	
	div{
		float: left;
		margin-right: 20px;
	}
		
	label{
		display: block;
		float: left;
		clear: left;
		width: 200px;
	}
	
	input, select{
		float: left;
		margin: 2px 0;
	}
	
	input[type=submit], hr, img{
		clear: left;
		margin: 5px 0;
	}
</style>
</head>

<body>
<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
<div>
	<label for="file">File:</label>
	<select id="file" name="file">
		<option value="image.jpg"<?=$_POST['file']=='image.jpg'?' selected':'';?>>image.jpg</option>
		<option value="image2.jpg"<?=$_POST['file']=='image2.jpg'?' selected':'';?>>image2.jpg</option>
		<option value="image3.jpg"<?=$_POST['file']=='image3.jpg'?' selected':'';?>>image3.jpg</option>
	</select>
	<label for="width">Width:</label><input type="text" id="width" name="width" value="<?=$_POST['width'];?>" />
	<label for="height">Height:</label><input type="text" id="height" name="height" value="<?=$_POST['height'];?>" />
	<label for="corner_radius">Corner Radius:</label><input type="text" id="corner_radius" name="corner_radius" value="<?=$_POST['corner_radius'];?>" />
	<label for="corner_background">Corner Background:</label><input type="text" id="corner_background" name="corner_background" value="<?=$_POST['corner_background'];?>" />
	<label for="corner_antialias">Corner AntiAlias:</label>
	<select id="corner_antialias" name="corner_antialias">
		<option value="1"<?=$_POST['corner_antialias']=='1'?' selected':'';?>>Yes</option>
		<option value="0"<?=$_POST['corner_antialias']=='0'?' selected':'';?>>No</option>
	</select>
	<label for="corner_position">Corner Position:</label>
	<select id="corner_position" name="corner_position">
		<option value="inner"<?=$_POST['corner_position']=='inner'?' selected':'';?>>Inner</option>
		<option value="outer"<?=$_POST['corner_position']=='outer'?' selected':'';?>>Outer</option>
	</select>
	<label for="border_thickness">Border Thickness:</label><input type="text" id="border_thickness" name="border_thickness" value="<?=$_POST['border_thickness'];?>" />
</div>
<div>
	<label for="border_position">Border Position:</label>
	<select id="border_position" name="border_position">
		<option value="inside"<?=$_POST['border_position']=='inside'?' selected':'';?>>Inside</option>
		<option value="outside"<?=$_POST['border_position']=='outside'?' selected':'';?>>Outside</option>
	</select>
	<label for="border_color">Border Color:</label><input type="text" id="border_color" name="border_color" value="<?=$_POST['border_color'];?>" />
	<label for="resizing_method">Resizing Method:</label>
	<select id="resizing_method" name="resizing_method">
		<option value="fit"<?=$_POST['resizing_method']=='fit'?' selected':'';?>>Fit</option>
		<option value="fill"<?=$_POST['resizing_method']=='fill'?' selected':'';?>>Fill</option>
	</select>
	<label for="image_alignment">Image Alignment:</label>
	<select id="image_alignment" name="image_alignment">
		<option value="left"<?=$_POST['image_alignment']=='left'?' selected':'';?>>Left / Top</option>
		<option value="center"<?=$_POST['image_alignment']=='center'?' selected':'';?>>Center / Middle</option>
		<option value="right"<?=$_POST['image_alignment']=='right'?' selected':'';?>>Right / Bottom</option>
	</select>
	<label for="jpeg_quality">JPEG Quality:</label><input type="text" id="jpeg_quality" name="jpeg_quality" value="<?=$_POST['jpeg_quality'];?>" />
	<label for="background_color">Background Color:</label><input type="text" id="background_color" name="background_color" value="<?=$_POST['background_color'];?>" />
	<input type="submit" value="Submit" />
</div>
</form>
<?php 	$params = "";
	foreach($_POST as $key => $value){
		if (strlen($params) > 0)
			$params .= "&";
		
		$params .= $key ."=".urlencode($value);
	}
	echo "<hr /><img src='image.php?$params' />";
?>
<!--
<img src="image.php?w=200&h=100&m=fill&a=left" />
<img src="image.php?w=200&h=100&m=fill&a=center" />
<img src="image.php?w=200&h=100&m=fill&a=right" />

<img src="image.php?w=200&h=500&m=fill&a=left" />
<img src="image.php?w=200&h=500&m=fill&a=center" />
<img src="image.php?w=200&h=500&m=fill&a=right" />

<img src="image.php?w=200&h=100&m=fit&a=left" />
<img src="image.php?w=200&h=100&m=fit&a=center" />
<img src="image.php?w=200&h=100&m=fit&a=right" />

<img src="image.php?w=200&h=500&m=fit&a=left" />
<img src="image.php?w=200&h=500&m=fit&a=center" />
<img src="image.php?w=200&h=500&m=fit&a=right" />
-->
</body>
</html>