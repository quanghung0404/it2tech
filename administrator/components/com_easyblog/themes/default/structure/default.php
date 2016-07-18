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
<a href="<?php echo JURI::root(); ?>administrator/index.php?option=com_easyblog&view=composer&tmpl=component" class="btn-float-new" data-eb-composer>
	<i></i>
</a>
<div id="fd" class="eb eb-admin <?php echo $prefix;?>">
	<?php if ($this->config->get('easyblog_environment') == 'development') { ?>
	<div class="app-devmode is-on alert warning">
		<div class="row-table">
			<div class="col-cell cell-tight">
				<i class="fa fa-warning"></i>
			</div>
			<div class="col-cell pl-10 pr-10">
				<?php echo JText::_('COM_EASYBLOG_CURRENTLY_ON_DEVELOPMENT');?>
			</div>
			<div class="col-cell cell-tight">
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=settings&layout=system');?>" class="btn"><?php echo JText::_('COM_EASYBLOG_CONFIGURE_BUTTON');?></a>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php if ($tmpl != 'component') { ?>
	<div class="app">
		<?php echo $sidebar; ?>

		<div class="app-content">
			<?php if ($heading || $desc) { ?>
			<div class="app-head">
				<h2><?php echo JText::_($heading); ?></h2>
				<p><?php echo JText::_($desc); ?></p>
			</div>
			<?php } ?>

			<?php echo $info->html();?>

			<div class="app-body">
				<?php echo $output; ?>
			</div>
		</div>
	</div>
	<?php } else { ?>
		<?php echo $output; ?>
	<?php } ?>

	<?php if ($jscripts) { ?>
	<div data-eb-scripts>
		<?php echo $jscripts;?>
	</div>
	<?php } ?>

	<?php echo $this->output("site/layout/default"); ?>
</div>
