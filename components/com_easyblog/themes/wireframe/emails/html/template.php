<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<!DOCTYPE html>
<html class="demo-mobile-horizontal" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="background: #f4f3f1;">
	<center style="background: #f4f3f1; padding: 30px 0">
	<table border="0" cellpadding="0" cellspacing="0" style="color: #555; font: 16px/22px Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;" width="600">
	    <tbody>
	    	<?php echo $contents; ?>

	    	<?php if ($unsubscribe) { ?>
	        <tr>
	            <td style="padding: 30px 0; text-align: center; color: #888; font-size: 12px;">
	            	<?php if (!is_array($unsubscribe)) { ?>
						<?php echo JText::_('COM_EASYBLOG_NOTIFICATION_UNSUBSCRIBE'); ?> <a href="<?php echo $unsubscribe;?>" style="color:#00aeef; text-decoration:none;"><?php echo JText::_('COM_EASYBLOG_NOTIFICATION_HERE');?></a>
					<?php } else { ?>
						<?php foreach ($unsubscribe as $type => $link) { ?>
							<?php echo JText::_('COM_EASYBLOG_NOTIFICATION_UNSUBSCRIBE_' . strtoupper($type)); ?> 
							<a href="<?php echo $link;?>" style="color:#00aeef; text-decoration:none;"><?php echo JText::_('COM_EASYBLOG_NOTIFICATION_HERE');?></a>
						<?php } ?>
					<?php } ?>
	            </td>
	        </tr>
	        <?php } ?>

	    </tbody>
	</table>
	</center>
</body>
</html>