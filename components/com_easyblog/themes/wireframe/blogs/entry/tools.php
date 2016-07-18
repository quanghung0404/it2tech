<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-entry-helper">
	<?php echo $this->output('site/blogs/entry/fontsize'); ?>

	<?php if ($preview) { ?>
	<div class="eb-help-subscribe">
		<i class="fa fa-envelope"></i>
	</div>
	<?php } ?>

	<?php if ($this->entryParams->get('post_subscribe_link', true)) { ?>
		<?php if (!$preview && $this->config->get('main_subscription') && $post->subscription) { ?>
			<?php if (!$subscription->id) { ?>
				<div class="eb-help-subscribe">
					<i class="fa fa-envelope"></i>
					<a href="javascript:void(0);" data-blog-subscribe data-type="entry" data-id="<?php echo $post->id;?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_BLOG'); ?></a>
				</div>
			<?php } else { ?>
				<div class="eb-help-unsubscribe">
					<i class="fa fa-envelope"></i>
					<a href="javascript:void(0);" class="link-subscribe" data-return="<?php echo $return; ?>" data-blog-unsubscribe data-subscription-id="<?php echo $subscription->id;?>"><?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE_ENTRY'); ?></a>
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>

	<?php if ($this->config->get('main_reporting') && (!$this->my->guest || $this->my->guest && $this->config->get('main_reporting_guests')) && $this->entryParams->get('post_reporting', true)) { ?>
	<div class="eb-help-report">
		<i class="fa fa-flag"></i>
		<a href="javascript:void(0);" data-blog-report><?php echo JText::_( 'COM_EASYBLOG_REPORT_THIS_POST');?></a>
	</div>
	<?php } ?>

	<?php if ($this->config->get('main_phocapdf_enable') && $this->entryParams->get('post_pdf', true)) { ?>
		<?php echo $this->output('site/blogs/tools/pdf'); ?>
	<?php } ?>

	<?php if ($this->entryParams->get('post_print', true)) { ?>
		<?php echo $this->output('site/blogs/tools/print'); ?>
	<?php } ?>

	<?php echo EB::bookmark()->html($this->entryParams); ?>
</div>
