<?php
$output_dir = "uploads/";
$input = JFactory::getApplication()->input;
$postData = $input->getArray($_POST);
if(isset($postData["op"]) && $postData["op"] == "delete" && isset($postData['name']))
{
	$fileName =$postData['name'];
	$filePath = $output_dir. $fileName;
	if (file_exists($filePath)) 
	{
        unlink($filePath);
    }
	echo "Deleted File ".$fileName."<br>";
}

?>