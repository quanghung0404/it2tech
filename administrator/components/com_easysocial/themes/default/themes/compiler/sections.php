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
<div data-compile-sections>
	<div class="tab-box tab-box-sidenav">
		<div class="tabbable">
			<ul class="nav nav-tabs nav-tabs-icons">
				<?php foreach ($sections as $i => $section) { ?>
					<?php $sectionId = $stylesheet->sectionId($section); ?>
					<li class="tab-item <?php echo ($i==0) ? 'active' : ''; ?>" data-section-id="<?php echo $sectionId; ?>">
						<a href="#<?php echo $sectionId; ?>" data-bs-toggle="tab"><?php echo ucfirst($section); ?></a>
					</li>
				<?php } ?>
			</ul>
			<div class="tab-content">
				<?php foreach ($sections as $i => $section) { ?>
					<?php $sectionId = $stylesheet->sectionId($section); ?>
					<div class="tab-pane <?php echo ($i==0) ? 'active' : ''; ?>" id="<?php echo $sectionId; ?>">
						<?php echo $this->includeTemplate('admin/themes/compiler/section', array('section' => $section)); ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
