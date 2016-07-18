<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>400</width>
	<height>250</height>
	<selectors type="json">
    {
        "{submitButton}" : "[data-report-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{submitButton} click": function()
        {
            this.submitButton().attr('disabled', "true");
            this.form().submit();
        }
    }
    </bindings>
	<title><?php echo $title; ?></title>
	<content>
		<?php if ($description) { ?>
		<p class="fd-small"><?php echo $description; ?></p>
		<?php } ?>

		<?php if ($this->my->id && $this->access->exceeded('reports.limit', 1)) { ?>
		<p class="fd-small"><?php echo JText::_('COM_EASYSOCIAL_REPORTS_LIMIT_EXCEEDED'); ?></p>
		<?php } else { ?>
			<textarea data-reports-message class="form-control mt-20" style="width: 100%;height: 100px;" placeholder="<?php echo JText::_('COM_EASYOCIAL_REPORTS_SUBMIT_REPORT_PLACEHOLDER'); ?>"></textarea>

			<div class="mt-20 fd-small">
				<?php echo JText::_( 'COM_EASYSOCIAL_REPORTS_SUBMIT_REPORT_FOOTNOTE' );?>
			</div>
		<?php } ?>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>

		<?php if ($this->my->guest || !$this->access->exceeded('reports.limit', 1)) { ?>
		<button data-report-button type="button" class="btn btn-es-primary btn-sm"><?php echo JText::_('COM_EASYSOCIAL_REPORT_BUTTON'); ?></button>
		<?php } ?>
	</buttons>
</dialog>
