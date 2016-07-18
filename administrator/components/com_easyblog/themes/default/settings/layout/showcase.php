<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_COVER_FEATURED_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_COVER_FEATURED_INFO');?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_SIZE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_SIZE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_SIZE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<select name="cover_featured_size" class="form-control">
							<option value="small" <?php echo $this->config->get('cover_featured_size') == 'small' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_SMALL');?></option>
							<option value="thumbnail" <?php echo $this->config->get('cover_featured_size') == 'thumbnail' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_THUMBNAIL');?></option>
							<option value="medium" <?php echo $this->config->get('cover_featured_size') == 'medium' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_MEDIUM');?></option>
							<option value="large" <?php echo $this->config->get('cover_featured_size') == 'large' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_LARGE');?></option>
							<option value="original" <?php echo $this->config->get('cover_featured_size') == 'original' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ORIGINAL');?></option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_CROP_COVER'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_CROP_COVER'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_CROP_COVER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'cover_featured_crop', $this->config->get('cover_featured_crop'), 'cover_featured_crop', 'data-cover-featured-crop'); ?>
					</div>
				</div>
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_WIDTH'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_WIDTH'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_WIDTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row-table">
							<div class="col-cell">
								<input type="text" class="form-control" name="cover_featured_width" value="<?php echo $this->config->get('cover_featured_width', 200);?>" data-cover-featured-width />
							</div>
							<div class="col-cell pl-10" style="width: 60%">pixels</div>
						</div>
					</div>
				</div>

				<div class="form-group<?php echo !$this->config->get('cover_featured_crop') ? ' hide' : '';?>" data-cover-featured-height>
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_HEIGHT'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_HEIGHT'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_HEIGHT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row-table">
							<div class="col-cell">
								<input type="text" class="form-control" name="cover_featured_height" value="<?php echo $this->config->get('cover_featured_height', 200);?>" />
							</div>
							<div class="col-cell pl-10" style="width: 60%">pixels</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGNMENT'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGNMENT'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGNMENT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<select name="cover_featured_alignment" class="form-control" style="width: 50%;">
							<option value="left"<?php echo $this->config->get('cover_featured_alignment') == 'left' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_LEFT');?></option>
							<option value="right"<?php echo $this->config->get('cover_featured_alignment') == 'right' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SETTINGS_POST_COVER_ALIGN_RIGHT');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
	</div>
</div>
