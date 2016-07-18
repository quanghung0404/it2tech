<?php
/**
 * @version     $Id: default.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Forms
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
<div class="jsn-page-list">
    <div class="jsn-bootstrap">
	<form action="<?php echo JRoute::_('index.php?option=com_mobilize&view=profiles'); ?>" method="post" class="form-inline" id="adminForm" name="adminForm">
		<?php
		$JSNItemList = new JSNItemlistGenerator($this->getModel());
		$JSNItemList->addColumn('', 'profile_id', 'checkbox', array('checkall' => true, 'name' => 'cid[]', 'class' => 'jsn-column-select', 'onclick' => 'Joomla.isChecked(this.checked);'));
		$JSNItemList->addColumn('JGLOBAL_TITLE', 'profile_title', 'link', array('class' => 'jsn-column-title', 'sortTable' => 'p.profile_title', 'link' => 'index.php?option=com_mobilize&view=profile&task=profile.edit&profile_id={$profile_id}'));
		$JSNItemList->addColumn('JSN_MOBILIZE_PROFILE_OS_SUPPORT', '', 'custom', array('classHeader' => 'header-5percent', 'obj' => $this, 'method' => 'renderOSSupport'));
		//$JSNItemList->addColumn('JSN_MOBILIZE_PROFILE_THEME', '', 'custom', array('classHeader' => 'header-10percent', 'obj' => $this, 'method' => 'renderTheme'));
		$JSNItemList->addColumn('JSTATUS', 'profile_state', 'published', array('class' => 'jsn-column-published', 'sortTable' => 'p.profile_state'));
		$JSNItemList->addColumn('JFIELD_ORDERING_LABEL', 'ordering', 'ordering', array('sortTable' => 'p.ordering', 'class' => 'jsn-column-ordering', 'classHeader' => 'header-5percent'));
		$JSNItemList->addColumn('JGRID_HEADING_ID', 'profile_id', '', array('class' => 'jsn-column-id', 'sortTable' => 'p.profile_id'));
		echo $JSNItemList->generateFilter();
		echo $JSNItemList->generate();
		?>
		<?php echo JHtml::_('form.token'); ?>
	</form>
	<script>
		var self =this;
		$('.form-inline').find('.jsn-table-centered tbody').each(function(){
			if(!$.trim($(this).html())){
				$('.jsn-table-centered tfoot tr td').html('No Data');
			}
		})
	</script>
    </div>
</div>
<?php
// Display footer
JSNHtmlGenerate::footer();
