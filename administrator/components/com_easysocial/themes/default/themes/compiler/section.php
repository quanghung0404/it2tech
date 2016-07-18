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

$sectionId = $stylesheet->sectionId($section);
?>

<div class="es-theme-compiler-form" data-section data-section-name="<?php echo $section; ?>">

	<header class="mt-10 mb-20 row">
		<div class="col-md-5">
			<h3 class="mt-5 mb-0 pull-left"><?php echo ucfirst($section); ?></h3>
		</div>

		<div class="col-md-7">
			<?php if ($memory_limit >= 96) { ?>
			<div class="es-theme-compiler-actions">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-primary" data-force-compile-button><i class="fa fa-flash"></i> <?php echo JText::_('COM_EASYSOCIAL_THEMES_COMPILE'); ?></button>
					<button type="button" class="btn btn-primary dropdown-toggle_" data-bs-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu pull-right" role="menu">
						<li data-minify-button><a href="javascript: void(0);"><i class="fa fa-cabinet"></i> <?php echo JText::_('COM_EASYSOCIAL_THEMES_MINIFY'); ?></a></li>
					</ul>
				</div>
				<div class="btn-group pull-right mr-5">
					<button type="button" class="btn" data-refresh-section-button><i class="fa fa-refresh"></i> <?php echo JText::_('COM_EASYSOCIAL_THEMES_REFRESH'); ?></button>
				</div>
			</div>
			<?php } ?>
			<div class="es-theme-compiler-progress">
				<div data-progress-status></div>
				<div class="progress progress-info progress-striped active mb-0" data-progress>
					<div class="bar" style="width: 100%" data-progress-bar></div>
				</div>
			</div>
		</div>
	</header>

	<h4><?php echo JText::_('COM_EASYSOCIAL_THEMES_FILES'); ?></h4>
	<?php
		$status = $stylesheet->status($section);
		echo $this->loadTemplate('admin/themes/compiler/status', array('status' => $status));
	?>

	<h4><?php echo JText::_('COM_EASYSOCIAL_THEMES_LOG'); ?></h4>
	<?php
		$log = $stylesheet->log($section);
		echo $this->loadTemplate('admin/themes/compiler/log', array('log' => $log));
	?>

	<h4><?php echo JText::_('COM_EASYSOCIAL_THEMES_IMPORTS'); ?></h4>
	<?php
		$imports = $stylesheet->imports($section);
		echo $this->loadTemplate('admin/themes/compiler/imports', array('imports' => $imports));
	?>
</div>
