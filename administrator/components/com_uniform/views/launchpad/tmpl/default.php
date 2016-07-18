<?php

/**
 * @version     $Id: default.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  launchpad
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
// Display messages
if (JRequest::getInt('ajax') != 1)
{
	echo $this->msgs;
}
?>
<div id="jsn-launchpad" class="jsn-bootstrap">
    <div class="jsn-launchpad-option jsn-section">
		<div class="jsn-badge-large">1</div>
		<div class="jsn-pane pane-default">
			<h3 class="jsn-section-header"><?php echo JText::_('JSN_UNIFORM_LAUNCHPAD_FORMS'); ?></h3>
			<div class="jsn-section-content">
				<p><?php echo JText::_('JSN_UNIFORM_LAUNCHPAD_FORMS_DES'); ?></p>
				<div class="control-group clearfix">
					<select name="filter_form_id"  class="pull-left" id="filter_form_id">
						<option value="">- <?php echo JText::_('JSN_UNIFORM_SELECT_FORMS'); ?> -</option>
						<?php echo JHtml::_('select.options', JSNUniformHelper::getOptionForms(), 'value', 'text'); ?>
					</select>
					<div class="jsn-iconbar pull-left">
						<a href="javascript:void(0);" id="edit-form" title="<?php echo JText::_('JSN_UNIFORM_EDIT_SELECTED_FORM'); ?>" target="_blank" class="disabled"><i class="jsn-icon24 icon-pencil"></i></a>
						<a href="index.php?option=com_uniform&view=form&layout=edit"  title="<?php echo JText::_('JSN_UNIFORM_CREATE_NEW_FORM'); ?>" id="new-form" target="_blank"><i class="jsn-icon24 icon-plus"></i></a>
						<a href="index.php?option=com_uniform&view=forms" title="<?php echo JText::_('JSN_UNIFORM_SEE_ALL_FORMS'); ?>" target="_blank"><i class="jsn-icon24 icon-folder"></i></a>
					</div>
				</div>
			</div>
		</div>
    </div>
    <div class="jsn-launchpad-action jsn-section">
		<div class="jsn-badge-large">2</div>
		<div class="jsn-pane pane-info">
			<h3 class="jsn-section-header"><?php echo JText::_('JSN_UNIFORM_LAUNCHPAD_PRESENTATION'); ?></h3>
			<div class="jsn-section-content">
				<p><?php echo JText::_('JSN_UNIFORM_LAUNCHPAD_PRESENTATION_DES'); ?></p>
				<div class="control-group">
					<?php echo $this->presentationMethods; ?>
					<select name="menutype"  class="" id="menutype">
						<option value="">- <?php echo JText::_('JSN_UNIFORM_SELECT_MENU'); ?> -</option>
						<?php echo JHtml::_('select.options', JSNUniformHelper::getOptionMenus(), 'value', 'text'); ?>
					</select>
					<a href="javascript:void(0);" class="btn disabled" title="<?php echo htmlspecialchars(JText::_('GO')); ?>" id="jsn-go-link"><?php echo JText::_('GO'); ?></a>
				</div>
			</div>
		</div>
    </div>
    <div id="dialog-plugin">
		<div class="ui-dialog-content-inner jsn-bootstrap">
			<p><?php echo JText::_('JSN_UNIFORM_LAUNCHPAD_PLUGIN_SYNTAX_DES'); ?></p>
			<div id="jsn-clipboard">
				<span class="jsn-clipboard-input">
					<input type="text" value="" name="plugin" class="input-xlarge" id="syntax-plugin">
					<span class="jsn-clipboard-checkicon icon-ok"></span>
				</span>
				<span id="jsn-clipboard-container">
					<button class="btn" id="jsn-clipboard-button">Copy to clipboard</button>
				</span>
			</div>
		</div>
    </div>
</div>
<?php
// Display footer
JSNHtmlGenerate::footer();