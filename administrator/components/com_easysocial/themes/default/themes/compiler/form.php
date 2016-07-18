<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>

<?php if ($memory_limit < 96) { ?>
<div class="alert alert-warn"><?php echo JText::_( 'COM_EASYSOCIAL_THEMES_COMPILER_MEMORY_LIMIT_BLOCK' ); ?></div>
<?php } ?>

<div class="es-theme-compiler-form"
	 data-compiler="<?php echo $uuid; ?>"
	 data-location="<?php echo $location; ?>"
	 data-name="<?php echo $name; ?>"
	 data-override="<?php echo ($override) ? 1 : 0; ?>">

	<header class="mt-10 mb-20 row">
		<div class="col-md-5">
			<h2 class="mt-5 mb-0 pull-left"><?php echo $element; ?></h2>
			<span class="label label-primary pull-left ml-5"><?php echo $type; ?></span>
			<span class="label label-info pull-left ml-5"><?php echo $location; ?></span>
			<?php if ($override) { ?>
			<span class="label label-danger pull-left ml-5"><?php echo JText::_('COM_EASYSOCIAL_THEMES_OVERRIDE'); ?></span>
			<?php } ?>
		</div>
		<div class="col-md-7">
			<?php if ($memory_limit >= 96) { ?>
			<div class="es-theme-compiler-actions">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-primary" data-build-button><i class="fa fa-flash"></i> <?php echo JText::_('COM_EASYSOCIAL_THEMES_BUILD'); ?></button>
					<button type="button" class="btn btn-primary dropdown-toggle_" data-bs-toggle="dropdown"><i class="caret"></i></button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li data-build-without-minify-button><a href="javascript: void(0);"><i class="fa fa-flash"></i> <?php echo JText::_('COM_EASYSOCIAL_THEMES_BUILD_WITHOUT_MINIFYING'); ?></a></button></li>
						<li data-purge-button><a href="javascript: void(0);"><i class="fa fa-cabinet"></i> <?php echo JText::_('COM_EASYSOCIAL_THEMES_PURGE'); ?></a></li>
					</ul>
				</div>
				<div class="btn-group pull-right mr-5">
					<button type="button" class="btn" data-refresh-button><i class="fa fa-refresh"></i> <?php echo JText::_('COM_EASYSOCIAL_THEMES_REFRESH'); ?></button>
				</div>
			</div>
			<?php } ?>
			<div class="es-theme-compiler-progress">
				<div data-progress-status></div>
				<div class="progress progress-info progress-striped active" data-progress>
					<div class="bar" style="width: 0%" data-progress-bar></div>
				</div>
			</div>
		</div>
	</header>

	<h4><?php echo JText::_('COM_EASYSOCIAL_THEMES_FILES'); ?></h4>
	<?php
		$status = $stylesheet->status();
		echo $this->includeTemplate('admin/themes/compiler/status', array('status' => $status));
	?>

	<h4><?php echo JText::_('COM_EASYSOCIAL_THEMES_LOG'); ?></h4>
	<?php
		$log = $stylesheet->log();
		echo $this->includeTemplate('admin/themes/compiler/log', array('log' => $log));
	?>

	<?php if ($type=='ats') { ?>
	<h4><?php echo JText::_('COM_EASYSOCIAL_THEMES_SECTIONS'); ?></h4>
	<?php
		$sections = $stylesheet->sections();
		echo $this->includeTemplate('admin/themes/compiler/sections', array('sections' => $sections));
	?>
	<?php }; ?>
</div>

