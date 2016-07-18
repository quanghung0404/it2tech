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
<div data-quickpost-extended>
	<div class="eb-quick-text-more hide" data-quickpost-extended-panel>
		<div class="form-group">
			<div class="col-md-6">
				<?php echo $this->html('form.category', 'category_id', 'category_id', '', ' data-quickpost-category'); ?>
			</div>
			<div class="col-md-6">
				<?php if ($this->acl->get('enable_privacy') ) { ?>
					<?php echo JHTML::_('select.genericlist', EB::privacy()->getOptions(), 'access', 'class="form-control" data-quickpost-privacy', 'value', 'text', $this->config->get('main_blogprivacy'));?>
				<?php } ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<textarea class="form-control" rows="1" placeholder="<?php echo JText::_('COM_EASYBLOG_MICROBLOG_TAGS_PLACEHOLDER');?>" data-quickpost-tags></textarea>
			</div>
		</div>
	</div>

	<div class="form-group form-action">
		<div class="col-md-3">
			<button type="button" class="btn btn-default" data-quickpost-extended-toggle>
				<i class="fa fa-ellipsis-h"></i> <?php echo JText::_('COM_EASYBLOG_MICROBLOG_MORE_OPTIONS'); ?>
			</button>
		</div>
		<div class="col-md-9 eb-quick-actions">
			<?php if ($facebook || $twitter || $linkedin) { ?>
			<div class="eb-quick-autopost">
				<label class="text-muted"><?php echo JText::_('COM_EASYBLOG_QUICKPOST_AUTO_POST_TO');?></label>

				<?php if ($facebook) { ?>
				<label data-eb-provide="tooltip" data-placement="bottom" data-original-title="Automatically publishes on Facebook as soon as the post is published.">
					<input name="autoposting[]" value="facebook" type="checkbox" data-autopost-item />
					<i class="fa fa-facebook-square"></i>
				</label>
				<?php } ?>

				<?php if ($twitter) { ?>
				<label data-eb-provide="tooltip" data-placement="bottom" data-original-title="Automatically posts on Twitter as soon as the post is published.">
					<input name="autoposting[]" value="twitter" type="checkbox" data-autopost-item />
					<i class="fa fa-twitter-square"></i>
				</label>
				<?php } ?>

				<?php if ($linkedin) { ?>
				<label data-eb-provide="tooltip" data-placement="bottom" data-original-title="Automatically publishes on LinkedIn as soon as the post is published.">
					<input name="autoposting[]" value="linkedin" type="checkbox" data-autopost-item />
					<i class="fa fa-linkedin-square"></i>
				</label>
				<?php } ?>
			</div>
			<?php } ?>

			<a href="javascript:void(0);" class="btn btn-primary" data-quickpost-publish>
				<?php echo JText::_('COM_EASYBLOG_PUBLISH_STORY_BUTTON');?>
				<i class="eb-loader-font fa fa-refresh fa-spin hide" data-quickpost-loader></i>
			</a>
		</div>
	</div>
</div>

