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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="fd" class="es mod-es-groups-categories module-group-categories<?php echo $suffix;?> es-responsive">

	<ul class="es-categories-list fd-reset-list">
		<?php foreach ($categories as $category) { ?>
		<li>
			<?php if ($params->get('display_avatar', true)) { ?>
				<div class="es-category-avatar  es-avatar es-avatar-sm es-avatar-border-sm">
					<img class="" src="<?php echo $category->getAvatar();?>" alt="<?php echo $modules->html( 'string.escape' , $category->get( 'title' ) );?>" />
				</div>
			<?php } ?>
			<div class="es-category-object">

				<a href="<?php echo FRoute::groups(array('layout' => 'category', 'id' => $category->getAlias()));?>" class="category-title"><?php echo $category->get('title');?></a>

				<?php if ($params->get('display_desc', false)) { ?>
				<p class="category-desc">
					<?php echo $modules->html( 'string.truncater' , $category->get( 'description' ), $params->get( 'desc_max' , 250 ) ) ;?>
				</p>
				<?php } ?>
			</div>

			<?php if ($params->get('display_counter', true)) { ?>
			<div class="es-category-meta">
				<span class="hit-counter">
					<i class="fa fa-users"></i> <?php echo JText::sprintf( FD::string()->computeNoun( 'MOD_EASYSOCIAL_GROUPS_CATEGORY_GROUPS_COUNT' , $category->getTotalGroups() ) , $category->getTotalGroups() ); ?>
				</span>
			</div>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>

</div>
