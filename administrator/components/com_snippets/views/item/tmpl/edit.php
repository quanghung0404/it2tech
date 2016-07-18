<?php
/**
 * Item View Template: Edit
 *
 * @package         Snippets
 * @version         4.1.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('com_content', JPATH_ADMINISTRATOR);

NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');
JHtml::stylesheet('nnframework/style.min.css', false, true);
?>

<form action="<?php echo JRoute::_('index.php?option=com_snippets&id=' . ( int ) $this->item->id); ?>" method="post"
      name="adminForm" id="item-form" class="form-validate form-horizontal">

	<div class="row-fluid">
		<div class="span12">
			<?php echo $this->render($this->item->form, 'details', JText::_('JDETAILS')); ?>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12">
			<?php echo $this->render($this->item->form, '-content', JText::_('NN_CONTENT')); ?>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(task) {
		var f = document.getElementById('item-form');
		if (task == 'item.cancel') {
			Joomla.submitform(task, f);
			return;
		}

		// do field validation
		if (f['jform[name]'].value.trim() == "") {
			alert("<?php echo JText::_('SNP_THE_ITEM_MUST_HAVE_A_NAME', true); ?>");
		} else if (f['jform[alias]'].value.trim() == "") {
			alert("<?php echo JText::_('SNP_THE_ITEM_MUST_HAVE_AN_ID', true); ?>");
		} else {
			Joomla.submitform(task, f);
		}
	}
</script>
