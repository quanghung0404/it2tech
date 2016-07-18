<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_TITLE');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_INSTRUCTIONS'); ?></div>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_alpha_userpoint', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_ENABLE_INTEGRATIONS'); ?>
				<?php echo $this->html('settings.toggle', 'main_alpha_userpoint_points', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_SHOW_POINTS'); ?>
				<?php echo $this->html('settings.toggle', 'main_alpha_userpoint_medals', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_SHOW_MEDALS'); ?>
				<?php echo $this->html('settings.toggle', 'main_alpha_userpoint_ranks', 'COM_EASYBLOG_SETTINGS_INTEGRATIONS_AUP_SHOW_RANKS'); ?>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_AUP_FOR_RATINGS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_AUP_FOR_RATINGS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_AUP_FOR_RATINGS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_ratings_aup_rate', $this->config->get('main_ratings_aup_rate'));?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
