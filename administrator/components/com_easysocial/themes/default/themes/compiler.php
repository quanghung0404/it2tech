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
<div class="es-theme-compiler">
	<div class="tab-box tab-box-sidenav">
		<div class="tabbable">
			<ul class="nav nav-tabs nav-tabs-icons nav-tabs-side">
				<?php
				foreach ($stylesheets as $stylesheet) {
					if (!empty($stylesheet->themes)) {
				?>
						<li class="tab-divider"><?php echo $stylesheet->title; ?></li>
						<?php foreach ($stylesheet->themes as $theme) { ?>
							<li class="tab-item <?php echo ($location==$stylesheet->location && $name==$theme->element) ? 'active' : ''; ?>"><a href="<?php echo FRoute::_('index.php?option=com_easysocial&view=themes&layout=compiler&location=' . $stylesheet->location . '&name=' . $theme->element . (($stylesheet->override) ? '&override=1' : '')); ?>"><?php echo $theme->name; ?></a></li>
						<?php } ?>
					<?php } ?>

				<?php } ?>
			</ul>
			<div class="tab-content tab-content-side">
				<div class="tab-pane active">
					<?php echo $this->includeTemplate("admin/themes/compiler/form"); ?>
				</div>
			</div>
		</div>
	</div>
</div>

