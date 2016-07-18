<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

?>
<div class="sigProMainMenu sigFloatRight">
	<ul class="sigThin sigLightMenu">
		<li>
			<a href="index.php?option=com_sigpro&amp;view=galleries&amp;type=site" class="sigProMenuItems"><i class="sig-icon sig-icon-picture"></i><?php echo JText::_('COM_SIGPRO_SITE_GALLERIES'); ?></a>
		</li>
		<?php if (JFile::exists(JPATH_SITE.'/components/com_k2/k2.php')): ?>
		<li>
			<a href="index.php?option=com_sigpro&amp;view=galleries&amp;type=k2" class="sigProMenuItems"><i class="sig-icon sig-icon-picture"></i><?php echo JText::_('COM_SIGPRO_K2_GALLERIES'); ?></a>
		</li>
		<?php endif; ?>
		<?php if($isSuperUser): ?>
		<li>
			<a href="index.php?option=com_sigpro&amp;view=galleries&amp;type=users" class="sigProMenuItems"><i class="sig-icon sig-icon-picture"></i><?php echo JText::_('COM_SIGPRO_USER_GALLERIES'); ?></a>
		</li>
		<?php endif; ?>
		<li>
			<a href="index.php?option=com_sigpro&amp;view=media" class="sigProMenuItems"><i class="sig-icon sig-icon-archive"></i><?php echo JText::_('COM_SIGPRO_MEDIA_MANAGER'); ?></a>
		</li>
	</ul>
</div>