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
<div class="eb-composer-templates">
	<div class="eb-composer-templates-in">
	<div class="eb-composer-templates-wrap">
		<div class="eb-composer-templates-header">
			<h4><?php echo JText::_('COM_EASYBLOG_COMPOSER_TEMPLATES_HEADING');?></h4>
			<div class="muted">
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_TEMPLATES_HEADING_INFO');?>
			</div>
		</div>
		<div class="eb-composer-templates-content clearfix">
			<div>
				<?php if ($postTemplates) { ?>
					<?php foreach ($postTemplates as $postTemplate) { ?>
					<div class="template text-center"
							data-template-item
							data-uid="<?php echo $postTemplate->id;?>"
							data-blank="<?php echo $postTemplate->isBlank() ? '1' : '0';?>"
						>
						<div class="template-thumb"<?php echo ($postTemplate->screenshot) ? ' style="background-image: url(\'' . rtrim(JURI::root(), '/') . $postTemplate->screenshot . '\');"' : ''; ?>>
							<div class="template-control">
								<a href="javascript:void(0);" class="template-pick" title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_TEMPLATES_SELECT_TEMPLATE');?>">
									<i class="fa fa-check"></i>
								</a>
							</div>

							<a href="#" style="color: #aaa; text-transform: uppercase; font-size: 12px;"></a>

							<?php if (!$postTemplate->isCore() && $postTemplate->isOwner()) { ?>
							<a href="javascript:void(0);" class="template-delete" data-template-delete><?php echo JText::_('COM_EASYBLOG_DELETE_BUTTON');?></a>
							<?php } ?>
						</div>
						<div class="template-name">
							<?php echo JText::_($postTemplate->title);?>
						</div>
						<div class="template-dob text-muted">&nbsp;</div>
					</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>
	</div>
</div>
