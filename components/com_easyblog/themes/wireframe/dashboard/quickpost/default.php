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
<div class="eb-head">
	<h2 class="eb-head-title reset-heading pull-left">
		<i class="fa fa-bolt"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_QUICKPOST_HEADING'); ?>
	</h2>
</div>

<div class="eb-quick-post" data-eb-quickpost>
	<div class="alert hide" data-quickpost-alert></div>

	<ul class="eb-quick-tabs reset-list">
		<li <?php echo $active=='standard' ? ' class="active"' : ''; ?>>
			<a href="#standard" data-bp-toggle="tab">
				<i class="fa fa-pencil"></i> <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_STANDARD'); ?>
			</a>
		</li>
		<li <?php echo $active=='photo' ? ' class="active"' : ''; ?>>
			<a href="#photo" data-bp-toggle="tab">
				<i class="fa fa-camera"></i> <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_PHOTO'); ?>
			</a>
		</li>
		<li <?php echo $active=='video' ? ' class="active"' : ''; ?>>
			<a href="#video" data-bp-toggle="tab">
				<i class="fa fa-video-camera"></i> <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_VIDEO'); ?>
			</a>
		</li>
		<li <?php echo $active=='quote' ? ' class="active"' : ''; ?>>
			<a href="#quote" data-bp-toggle="tab">
				<i class="fa fa-quote-left"></i> <?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_QUOTE'); ?>
			</a>
		</li>
		<li <?php echo $active=='link' ? ' class="active"' : ''; ?>>
			<a href="#link" data-bp-toggle="tab">
				<i class="fa fa-link"></i><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_LINK'); ?>
			</a>
		</li>
	</ul>

	<div class="eb-quick-content tab-content">
		<?php echo $this->output('site/dashboard/quickpost/form.standard'); ?>

		<?php echo $this->output('site/dashboard/quickpost/form.photo'); ?>

		<?php echo $this->output('site/dashboard/quickpost/form.video'); ?>

		<?php echo $this->output('site/dashboard/quickpost/form.quote'); ?>

		<?php echo $this->output('site/dashboard/quickpost/form.link'); ?>
	</div>
</div>
