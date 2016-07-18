<?php
/**
 * @version    $Id: modal.php 16077 2012-09-17 02:30:25Z giangnd $
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

include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'elements' . DS . 'jsnshowlist.php';
include_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_imageshow' . DS . 'elements' . DS . 'jsnshowcase.php';
$objJFormShowlist = new JFormFieldJsnshowlist;
$objJFormShowcase = new JFormFieldJsnshowcase;
$dimension = array(
	'0' => array('value' => 'px',
	'text' => JText::_('px')),
	'1' => array('value' => '%',
	'text' => JText::_('%'))
);
$dropboxDimension = JHTML::_('select.genericList', $dimension, 'dimension', 'class="inputbox dimension"' . '', 'value', 'text', '');
?>
<div class="jsn-imageshow-plg-editor-container jsn-bootstrap">
	<div class="jsn-imageshow-plg-editor-wrapper">
		<h3 class="jsn-section-header">
		<?php echo JText::_('PLG_EDITOR_GALLERY_SETTINGS');?>
		</h3>
		<div class="setting">
			<ul>
				<li><label style="float: left;"><?php echo JText::_('PLG_EDITOR_SHOWLIST');?>
				</label> <?php echo $objJFormShowlist->showlistDropDownList('showlist_id', 'showlist_id');?>
				</li>
				<li><label><?php echo JText::_('PLG_EDITOR_SHOWCASE');?> </label> <?php echo $objJFormShowcase->showcaseDropDownList('showcase_id', 'showcase_id');?>
				</li>
			</ul>
		</div>
		<div class="parameter">
			<hr class="jsn-horizontal-line" />
			<ul>
				<li><label style="float: left;"><?php echo JText::_('PLG_EDITOR_WIDTH');?>
				</label> <input type="text" name="width" id="width" /> <?php echo $dropboxDimension; ?>
				</li>
				<li><label><?php echo JText::_('PLG_EDITOR_HEIGHT');?> </label> <input
					type="text" name="height" id="height" />
				</li>
			</ul>
		</div>
		<div class="insert">
			<div class="form-actions">
				<button disabled="disabled" id="btn_insert_button"
					onclick="window.parent.jSelectSyntax($('showlist_id'), $('showcase_id'), $('width'), $('height'), $('dimension'))"
					name="button_installation_data" type="button" class="btn">
					<?php echo JText::_('PLG_EDITOR_INSERT');?>
				</button>
			</div>
		</div>
	</div>
</div>
