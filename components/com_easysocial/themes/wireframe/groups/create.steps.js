<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

EasySocial.require()
.script('site/groups/groups')
.done(function($){

	$('[data-groups-create-form]').implement(
		EasySocial.Controller.Groups.Create ,
		{
			"previousLink"	: "<?php echo FRoute::groups(array('layout' => 'steps' , 'step' => ($currentStep - 1)) , false);?>"
		}
	);
});
