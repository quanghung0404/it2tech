<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<fieldset class="eblog_login" style="border: solid 1px #cccccc; padding: 10px;">
	<h3><?php echo JText::_('COM_EASYBLOG_RESTRICTED_ACCESS_TITLE');?></h3>
	<p><?php echo $message; ?></p>

	<p><a href="javascript:void(0);" onclick="history.back();"><?php echo JText::_('BACK');?></a></p>
</fieldset>

