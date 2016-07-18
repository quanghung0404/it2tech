<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h4>
	<i class="fa fa-support muted mr-5"></i> <span><?php echo JText::_('COM_EASYBLOG_UPDATE_REQUIRED');?></span>
</h4>
<hr />
<p class="small">
	<?php echo JText::_('COM_EASYBLOG_OUTDATED_VERSION_DETAILS');?>
</p>

<table class="table table-striped">
	<tr>
		<td>
			<div class="fd-small"><?php echo JText::_('COM_EASYBLOG_INSTALLED_VERSION');?></div>
		</td>
		<td>
			<div class="fd-small"><?php echo $localVersion;?></div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="fd-small"><?php echo JText::_('COM_EASYBLOG_LATEST_VERSION');?></div>
		</td>
		<td>
			<div class="fd-small"><?php echo $onlineVersion;?></div>
		</td>
	</tr>
</table>

<div class="mt-20 center">
	<a href="index.php?option=com_easysocial&update=true" class="btn btn-primary btn-sm">
		<i class="fa fa-cloud-download"></i> <?php echo JText::_('COM_EASYBLOG_GET_UPDATES');?>
	</a>
</div>
