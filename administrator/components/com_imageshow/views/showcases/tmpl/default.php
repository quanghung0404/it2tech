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

$ordering		= true;
$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
$products 		= $objJSNUtils->getCurrentElementsOfImageShow();
// Display messages
if (JRequest::getInt('ajax') != 1)
{
	echo $this->msgs;
}
?>
<div id="jsn-showcases" class="jsn-page-list">
	<div class="jsn-bootstrap">
		<form action="index.php?option=com_imageshow&controller=showcase"
			method="post" name="adminForm" id="adminForm" class="form-inline">
			<?php
			JRequest::setVar('view', 'showcase');

			$pathRootImage = JURI::root();
			$JSNItemList   = new JSNItemlistGenerator($this->getModel());
			$JSNItemList->addColumn('', 'showcase_id', 'checkbox', array ('checkall' => true, 'name' => 'cid[]', 'class' => 'jsn-column-select', 'classHeader' => 'jsn-column-select', 'onclick'  => 'Joomla.isChecked(this.checked);'));
			$JSNItemList->addColumn('SHOWCASE_TITLE', 'showcase_title', 'link', array ('sortTable' => 'showcase_title', 'class'=>'jsn-column-title', 'classHeader' => 'jsn-column-title', 'link' => 'index.php?option=com_imageshow&controller=showcase&task=edit&cid[]={$showcase_id}'));
			$JSNItemList->addColumn('SHOWCASE_PUBLISHED', 'published', 'published', array ('sortTable' => 'published', 'classHeader' => 'jsn-column-published', 'class' => 'jsn-column-published'));
			$JSNItemList->addColumn('SHOWCASE_ORDER', 'ordering', 'ordering', array ('sortTable' => 'ordering', 'class' => 'jsn-column-ordering', 'classHeader' => 'jsn-column-ordering'));
			$JSNItemList->addColumn('SHOWCASE_THEME', 'theme_title', '', array ('class' => 'jsn-column-theme-title', 'classHeader' => 'header-5percent'));
			$JSNItemList->addColumn('ID', 'showcase_id', '', array ('class' => 'jsn-column-id', 'classHeader' => 'jsn-column-id', 'sortTable' => 'showcase_id'));

			echo $JSNItemList->generateFilter();
			echo $JSNItemList->generate();
			?>
			<?php echo JHTML::_('form.token');?>
		</form>
	</div>
</div>
			<?php JSNHtmlGenerate::footer($products); ?>