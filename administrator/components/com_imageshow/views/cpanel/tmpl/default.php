<?php
/**
 * @version    $Id: default.php 17065 2012-10-16 04:06:37Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$session 		= JFactory::getSession();
$identifier		= md5('jsn_imageshow_downloasource_identify_name');
$session->set($identifier, '', 'jsnimageshowsession');
// Display messages
if (JRequest::getInt('ajax') != 1)
{
	echo $this->msgs;
}
$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
$products 		= $objJSNUtils->getCurrentElementsOfImageShow();
?>
<!--[if IE 7]>
        <link rel="stylesheet" type="text/css" href="components/com_imageshow/assets/css/fixie7.css">
<![endif]-->
<script type="text/javascript">
	var baseUrl = '<?php echo JURI::root(); ?>';
</script>
<div id="jsn-launchpad" class="jsn-cpanel-container jsn-bootstrap">
	<div class="jsn-launchpad-option jsn-section">
		<div class="jsn-badge-large">1</div>
		<div class="jsn-pane pane-default">
			<h3 class="jsn-section-header">
			<?php echo JText::_('CPANEL_CPANEL_SHOWLIST'); ?>
			</h3>
			<div class="jsn-section-content">
				<p>
				<?php echo JText::_('CPANEL_SETUP_WHAT_IMAGES_TO_BE_SHOWN_IN_THE_GALLERY'); ?>
				</p>
				<div class="control-group clearfix">
				<?php echo $this->lists['showlist']; ?>
					<div class="jsn-iconbar pull-left">
						<a href="javascript:void(0);" id="edit-showlist" target=""
							rel='{"title": "<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_SHOWLIST_SETTINGS')); ?>"}'
							class="disabled"
							title="<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_YOU_MUST_SELECT_SOME_SHOWLIST_TO_EDIT')); ?>">
							<i class="jsn-icon24 jsn-icon-pencil"></i> </a>
							<?php
							if ($this->totalShowlist >= 3 && strtolower(JSN_IMAGESHOW_EDITION) == 'free')
							{
								?>
						<a href="javascript: void(0);" id="cannot-add-showlist"
							rel='{"title": "<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_UPGRADE_TO_PRO_EDITION_FOR_MORE')); ?>", "content": "<?php echo $this->objJSNUtils->escapeSpecialString(JText::sprintf('SHOWLIST_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_SHOWLISTS_IN_FREE_EDITION', '<a href="' . JSN_IMAGESHOW_UPGRADE_LINK . '" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">' .  JText::_('UPGRADE_TO_PRO_EDITION') . '</a>')); ?>"}'
							title="<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_CREATE_NEW_SHOWCASE')); ?>">
							<i class="jsn-icon24 jsn-icon-plus"></i> </a>
							<?php
							}
							else
							{
								?>
						<a
							href="index.php?option=com_imageshow&controller=showlist&task=add&tmpl=component"
							id="add-showlist"
							rel='{"title": "<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_SHOWLIST_SETTINGS')); ?>"}'
							title="<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_CREATE_NEW_SHOWLIST')); ?>">
							<i class="jsn-icon24 jsn-icon-plus"></i> </a>
							<?php
							}
							?>
						<a href="index.php?option=com_imageshow&controller=showlist"
							target="_blank"
							title="<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_SEE_ALL_SHOWLISTS')); ?>">
							<i class="jsn-icon24 jsn-icon-folder"></i> </a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="jsn-launchpad-option jsn-section">
		<div class="jsn-badge-large">2</div>
		<div class="jsn-pane pane-default">
			<h3 class="jsn-section-header">
			<?php echo JText::_('CPANEL_CPANEL_SHOWCASE'); ?>
			</h3>
			<div class="jsn-section-content">
				<p>
				<?php echo JText::_('CPANEL_SETUP_HOW_TO_PRESENT_IMAGES_IN_THE_GALLERY'); ?>
				</p>
				<div class="control-group clearfix">
				<?php echo $this->lists['showcase']; ?>
					<div class="jsn-iconbar pull-left">
						<a href="javascript:void(0);" id="edit-showcase" target=""
							rel='{"title": "<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_SHOWCASE_SETTINGS')); ?>"}'
							class="disabled"
							title="<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_YOU_MUST_SELECT_SOME_SHOWCASE_TO_EDIT')); ?>">
							<i class="jsn-icon24 jsn-icon-pencil"></i> </a>

						<a
							href="index.php?option=com_imageshow&controller=showcase&task=add&tmpl=component"
							id="add-showcase"
							rel='{"title": "<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_SHOWCASE_SETTINGS')); ?>"}'
							title="<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_CREATE_NEW_SHOWCASE')); ?>">
							<i class="jsn-icon24 jsn-icon-plus"></i> </a>
						<a href="index.php?option=com_imageshow&controller=showcase"
							target="_blank"
							title="<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_SEE_ALL_SHOWCASES')); ?>">
							<i class="jsn-icon24 jsn-icon-folder"></i> </a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="jsn-launchpad-action jsn-section">
		<div class="jsn-badge-large">3</div>
		<div class="jsn-pane pane-info">
			<h3 class="jsn-section-header">
			<?php echo JText::_('CPANEL_PRESENTATION'); ?>
			</h3>
			<div class="jsn-section-content">
				<p>
				<?php echo JText::_('CPANEL_CONFIGURE_HOW_TO_PRESENT_THE_GALLERY'); ?>
				</p>
				<div class="control-group">
				<?php echo $this->lists['presentationMethods']; ?>
				<?php echo $this->lists['menu']; ?>
					<a href="javascript:void(0);" class="btn disabled"
						title="<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_GO')); ?>"
						id="jsn-go-link"><?php echo JText::_('CPANEL_GO'); ?> </a> <a
						rel='{"size": {"x": 500, "y": 450}, "buttons": {"ok": false, "close": true}}'
						class="btn disabled jsn-is-modal" id="jsn-go-link-modal"
						title="<?php echo $this->objJSNUtils->escapeSpecialString(JText::_('CPANEL_GO')); ?>"><?php echo JText::_('CPANEL_GO'); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php JSNHtmlGenerate::footer($products); ?>

