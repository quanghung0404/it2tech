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
<form method="post" action="<?php echo JRoute::_('index.php');?>">
	<div class="eb-head">
		<h2 class="eb-head-title reset-heading pull-left">
			<i class="fa fa-file-text-o"></i>&nbsp;

			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_TEMPLATES');?>

			<a href="javascript:void(0);" class="eb-head-popover"
				rel="popover"
				data-placement="bottom"
				data-content="By harnessing the power of collaborative consumption you can get all of the access with none of the overhead."
				>
				<i class="fa fa-info-circle"></i>
			</a>
		</h2>
		<div class="eb-head-form form-inline pull-right">
			<a class="btn btn-default" href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=templateform');?>">
				<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NEW_TEMPLATE');?>
			</a>
		</div>
	</div>

	<div class="eb-box">
		<div class="eb-box-head">
			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_CREATE_NEW_TEMPLATE');?>
		</div>

		<div class="eb-box-body">
			<div class="form">
				<div class="form-group">
				    <label for="template-title">
				    	<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATES_TITLE');?>
				    </label>
				    <input type="text" class="form-control" name="title" id="template-title" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATES_TITLE_PLACEHOLDER');?>" value="<?php echo $this->html('string.escape', $template->title);?>" />
				</div>

				<div class="form-group">
				    <label for="template-content">
				    	<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATES_CONTENT');?>
				    </label>
				    <div>
				    	<?php echo $editor->display('template_content', $template->data->content, '100%', 350, 10, 10, array('image', 'pagebreak', 'ninjazemanta'), null, 'com_easyblog'); ?>
				    </div>
				</div>

				<div class="form-group form-action clearfix">
					<div class="col-md-6">
						<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=templates');?>" class="btn btn-default"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?></a>
					</div>

					<div class="col-md-6">
						<button class="btn btn-primary pull-right"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TEMPLATES_SAVE_TEMPLATE');?></button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $template->id;?>" />

	<?php echo $this->html('form.action', 'templates.save'); ?>
</form>
