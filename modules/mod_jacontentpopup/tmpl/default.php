<?php
/**
 * ------------------------------------------------------------------------
 * JA Content Popup Module for J25 & J34
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="ja-cp-wrap ja-anim-<?php echo substr($anim_type, 0, 5) ?>" id="ja-cp-<?php echo $module->id ?>" style="visibility:hidden;">
	<div class="ja-cp-main-wrap">
		<div class="ja-cp-main">
			<?php 
			require JModuleHelper::getLayoutPath('mod_jacontentpopup', $layout . '_item');
			?>
		</div>
	</div>
	<?php if ($params->get('show_nav_control', 0)) :?>  
	<div class="ja-cp-controls">
		<div class="ja-cp-prev"></div>
		<div class="ja-cp-next"></div>
	</div>
	<?php endif; ?>
<?php if ($pagination) : ?>
	

	<div class="ja-cp-loader">
		<img src="<?php echo $jacpurl ?>/assets/img/ajax-loader.gif" width="32" height="32" alt="" />	
	</div>

	<div class="ja-cp-pagination"<?php echo ($show_paging == 0 ? ' style="display: none"' : '') ?>>
		<?php echo $pagination;?>
	</div>
<?php endif;?>	
</div>