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
defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">
// EasyBlog.ready(function($) {

// 	// @task: Add active item on the first / latest year.
// 	$( '.blog-module-archive > div:first' ).addClass( 'active-year' );
// 	$( '.blog-module-archive .mod-year a' ).bind( 'click' , function(){

// 		$( this ).parent().toggleClass( 'toggle' );
// 		$( this ).parents( '.archive-year-wrapper' ).find( '.mod-months' ).toggle();

// 		return false;
// 	});

// });
</script>
<div id="fd" class="eb eb-mod mod_easyblogarchive <?php echo $params->get('moduleclass_sfx') ?>">
	<?php if (!empty($year)) { ?>
		<?php for ($i = $year['maxyear']; $i >= $year['minyear']; $i--) { ?>
			<?php if (!$showEmptyYear && empty($postCounts->$i)) { ?>
				<?php continue; ?>
			<?php } ?>
			<div class="eb-mod-item">
				<a data-bp-toggle="collapse" data-bp-parent="#accordion" href="#eb-mod-collapse-<?php echo $i; ?>" id="<?php echo $i; ?>" class="<?php echo ( $params->get('collapse', false) ) ? '' : ' collapsed' ?>">
					<i class="eb-mod-media-thumb fa fa-chevron-right mod-muted"></i>
					<b><?php echo $i; ?></b>
				</a>
				<div id="eb-mod-collapse-<?php echo $i; ?>" class="eb-mod-item-submenu collapse<?php echo ( $params->get('collapse', false) ) ? '' : ' in' ?>"  >
				<?php if ($params->get('order') == 'asc') { ?>
					<?php for ($m = 1; $m < 13; $m++) { ?>
						<?php require(JModuleHelper::getLayoutPath('mod_easyblogarchive', 'default_item')); ?>
					<?php } ?>
				<?php } else { ?>
					<?php for ($m = 12; $m > 0; $m--) { ?>
						<?php require(JModuleHelper::getLayoutPath('mod_easyblogarchive', 'default_item')); ?>
					<?php } ?>
				<?php } ?>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-mod-empty">
			<?php echo JText::_('MOD_EASYBLOGARCHIVE_NO_POST'); ?>
		</div>
	<?php } ?>
</div>

