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
$session 		= JFactory::getSession();
$identifier		= md5('jsn_imageshow_downloasource_identify_name');
$session->set($identifier, '', 'jsnimageshowsession');
$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
$products 		= $objJSNUtils->getCurrentElementsOfImageShow();
?>
<div class="jsn-master jsn-page-list">
	<div class="jsn-bootstrap">
		<form class="form-inline"
			action="<?php echo JRoute::_('index.php?option=com_imageshow&controller=showlist');?>"
			method="post" name="adminForm" id="adminForm">
			<?php
			$input = JFactory::getApplication()->input;
			$input->set('view', 'showlist');

			$pathRootImage = JURI::root();
			$JSNItemList   = new JSNItemlistGenerator($this->getModel());
			$JSNItemList->addColumn('', 'showlist_id', 'checkbox', array ('checkall' => true, 'name' => 'cid[]', 'class' => 'jsn-column-select', 'classHeader' => 'jsn-column-select', 'onclick'  => 'Joomla.isChecked(this.checked);'));
			$JSNItemList->addColumn('SHOWLIST_TITLE', 'showlist_title', 'link', array ('sortTable' => 'sl.showlist_title','class'=>'jsn-column-title', 'classHeader' => 'jsn-column-title', 'link' => 'index.php?option=com_imageshow&controller=showlist&task=edit&cid[]={$showlist_id}'));
			$JSNItemList->addColumn('SHOWLIST_PUBLISHED', 'published', 'published', array ('sortTable' => 'sl.published', 'class'=>'jsn-column-published', 'classHeader' => 'jsn-column-published'));
			$JSNItemList->addColumn('SHOWLIST_ORDER', 'ordering', 'ordering', array ('sortTable' => 'sl.ordering', 'class' => 'jsn-column-ordering', 'classHeader' => 'jsn-column-ordering'));
			$JSNItemList->addColumn('SHOWLIST_ACCESS_LEVEL', 'access_level', '', array ('class' => 'jsn-column-access', 'classHeader' => 'jsn-column-access'));
			$JSNItemList->addColumn('SHOWLIST_IMAGE_SOURCE', 'image_source_title', '', array ('class' => 'jsn-column-image-source', 'classHeader' => 'header-5percent'));
			$JSNItemList->addColumn('SHOWLIST_IMAGES', 'totalimage', '', array ('class'	=> 'jsn-column-total-image', 'classHeader' => 'header-5percent'));
			$JSNItemList->addColumn('SHOWLIST_HITS', 'hits', '', array ('class' => 'jsn-column-hit', 'classHeader' => 'header-5percent', 'sortTable' => 'sl.hits'));
			$JSNItemList->addColumn('ID', 'showlist_id', '', array ('class' => 'jsn-column-id', 'classHeader' => 'jsn-column-id', 'sortTable' => 'sl.showlist_id'));

			echo $JSNItemList->generateFilter();
			echo $JSNItemList->generate();
			?>
			<?php echo JHTML::_('form.token');?>
		</form>
	</div>
</div>
			<?php JSNHtmlGenerate::footer($products); ?>
