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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div id="fd" class="es mod-es-friends module-friends<?php echo $suffix;?>">
	<div class="mod-bd">
		<div class="es-widget">
			<?php if ($friends) { ?>
			<ul class="widget-list-grid">
				<?php foreach ($friends as $user) { ?>
				<li>
					<a href="<?php echo $user->getPermalink();?>" 
						class="es-avatar"
						<?php if ($params->get('popover', true)) { ?>
						data-popbox="module://easysocial/profile/popbox"
						<?php } ?>
						data-popbox-position="<?php echo $params->get('popover_position', 'top-left');?>"
						data-user-id="<?php echo $user->id;?>"
					>
						<img src="<?php echo $user->getAvatar();?>" />
					</a>
				</li>
				<?php } ?>
			</ul>
			<?php } ?>

			<?php if ($params->get('showall_link', true)) { ?>
			<div class="mod-small fd-small">
				<a href="<?php echo FRoute::friends();?>"><?php echo JText::_('MOD_EASYSOCIAL_FRIENDS_VIEW_ALL'); ?></a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
