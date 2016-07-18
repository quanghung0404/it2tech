<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form class="form-horizontal has-privacy" enctype="multipart/form-data" method="post" action="<?php echo JRoute::_('index.php');?>" id="groupCreationForm" data-groups-create-form>
	<div class="es-container es-groups">

		<?php echo $this->includeTemplate('site/groups/create.progress'); ?>

		<!-- Custom fields -->
		<?php if ($fields) { ?>
			<?php foreach ($fields as $field){ ?>
				<?php echo $this->loadTemplate('site/groups/create.steps.field' , array('field' => $field , 'errors' => $errors)); ?>
			<?php } ?>
		<?php } ?>

		<div class="form-group">
			<div class="col-sm-8 col-sm-offset-3 fd-small mt-20">
				<?php echo JText::_('COM_EASYSOCIAL_REGISTRATIONS_REQUIRED');?>
			</div>
		</div>

		<!-- Actions -->
		<div class="form-actions">
			<?php if ($currentStep != 1){ ?>
			<button type="button" class="btn btn-es btn-medium pull-left" data-groups-create-previous><?php echo JText::_('COM_EASYSOCIAL_PREVIOUS_BUTTON'); ?></button>
			<?php } ?>
			<button type="button" class="btn btn-es-primary btn-medium pull-right" data-groups-create-submit><?php echo $currentIndex === $totalSteps || $totalSteps < 2 ? JText::_('COM_EASYSOCIAL_SUBMIT_BUTTON') : JText::_('COM_EASYSOCIAL_CONTINUE_BUTTON');?></button>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="currentStep" value="<?php echo $currentIndex; ?>" />
	<input type="hidden" name="controller" value="groups" />
	<input type="hidden" name="task" value="store" />
	<input type="hidden" name="option" value="com_easysocial" />
</form>

