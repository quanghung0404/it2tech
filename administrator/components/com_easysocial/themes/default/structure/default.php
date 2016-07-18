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
<div id="fd" class="es <?php echo $class;?>">
	<?php if ($tmpl != 'component' && $this->config->get('general.environment') == 'development') { ?>
	<div class="app-devmode is-on alert warning">
	    <div class="row-table">
	        <div class="col-cell cell-tight">
	            <i class="fa fa-info-circle"></i>
	        </div>
	        <div class="col-cell pl-10 pr-10">
	            You are currently running on <b>Development environment</b> and your <b>javascript files are not compressed</b>. This will cause performance downgrade while using EasySocial.
	        </div>
	        <div class="col-cell cell-tight">
	            <a href="<?php echo JRoute::_('index.php?option=com_easysocial&view=settings&layout=form&page=general');?>" class="btn">Configure</a>
	        </div>
	    </div>
	</div>
	<?php } ?>
	
	<?php if ($tmpl != 'component') { ?>
		<div class="app">
			<div id="fd" class="es container-nav hidden">
				<a class="nav-sidebar-toggle" data-bp-toggle="collapse" data-target=".app-sidebar-collapse">
					<i class="fa fa-bars"></i>
					<span><?php echo JText::_('COM_EASYBLOG_MOBILE_MENU');?></span>
				</a>
				<a class="nav-subhead-toggle" data-bp-toggle="collapse" data-target=".subhead-collapse">
					<i class="fa fa-cog"></i>
					<span><?php echo JText::_('COM_EASYBLOG_MOBILE_OPTIONS');?></span>
				</a>
			</div>
			
			<?php echo $sidebar; ?>

			<div class="app-content">
				<?php echo ES::info()->toHTML(); ?>
				
				<div class="app-head">
					<?php echo $this->includeTemplate('admin/structure/control'); ?>
				</div>

				<div class="app-body accordion">
					<?php echo ES::profiler()->toHTML(); ?>

					<?php echo $html; ?>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<?php echo ES::info()->toHTML(); ?>

		<?php echo $html; ?>

	<?php } ?>
</div>
