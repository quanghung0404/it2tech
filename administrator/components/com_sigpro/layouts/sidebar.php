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

<div id="sigSideBar" class="sigTransition">
	<div class="sigSideBarWrapper sigTransition">
		<div class="sigFloatLeft sigBoxSizing sideBarTextArea">
			<div class="sideBarHeader sigTextCenter">
				<a href="index.php?option=com_sigpro&amp;view=info" title="<?php echo JText::_('COM_SIGPRO_INFO'); ?>" class="sig-icon-info-circled">
					<i class="hidden"><?php echo JText::_('COM_SIGPRO_INFO'); ?></i>
				</a>
				<?php if ($this->tmpl != 'component' && (version_compare(JVERSION, '1.6.0', 'lt') || JFactory::getUser()->authorise('core.admin', 'com_sigpro'))): ?>
				<a href="index.php?option=com_sigpro&amp;view=settings" title="<?php echo JText::_('COM_SIGPRO_SETTINGS'); ?>" class="sig-icon-cog">
					<i class="hidden"><?php echo JText::_('COM_SIGPRO_SETTINGS'); ?></i>
				</a>
				<?php endif; ?>
				<a href="http://www.joomlaworks.net/support/docs/simple-image-gallery-pro" target="_blank" title="<?php echo JText::_('COM_SIGPRO_DOCUMENTATION'); ?>" class="sig-icon-help-circled">
					<i class="hidden"><?php echo JText::_('COM_SIGPRO_DOCUMENTATION'); ?></i>
				</a>
			</div>
			<div class="sideBarBody">
				<h3 class="sigPurple"><?php echo JText::_('COM_SIGPRO_QUICK_HOWTO'); ?></h3>
				<div class="sideBarDesc"><?php echo JText::_('COM_SIGPRO_QUICK_HOWTO_TEXT'); ?></div>
			</div>
			<?php echo SigProHelper::copyrights(); ?> </div>
		<div class="sigFloatRight sigBoxSizing sidebarHandle"> <a class="sideToggler sig-icon-menu" href="#" ></a>
			<div class="sigProThumbnailsToolbar">
				<?php if($this->view == 'galleries' || $this->view == 'gallery'): ?>
				<a href="#landscape" class="sigProRatioButton sigViewLandscape sigHighlighted" title="<?php echo JText::_('COM_SIGPRO_LANDSCAPE'); ?>"></a> <a href="#portrait" class="sigProRatioButton sigViewPortrait" title="<?php echo JText::_('COM_SIGPRO_PORTRAIT'); ?>"></a>
				<?php else: ?>
				<div class="sigBrdrFix"></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
