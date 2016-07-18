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
<div id="fd" class="es mod-es-followers module-followers<?php echo $suffix;?>">
	<div class="mod-bd">
		<div class="es-widget">
			<?php if ($results) { ?>
			<ul class="widget-list-grid">
				<?php foreach($results as $user) { ?>
				<li>
					<a href="<?php echo $user->getPermalink();?>" class="es-avatar">
						<img src="<?php echo $user->getAvatar();?>" data-popbox="module://easysocial/profile/popbox" data-popbox-position="top-left" data-user-id="<?php echo $user->id; ?>"  />
					</a>
				</li>
				<?php } ?>
			</ul>

			<?php } else { ?>
			<div class=" fd-small">
				<?php echo JText::_('MOD_EASYSOCIAL_FOLLOWERS_NO_FOLLOWERS_CURRENTLY'); ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
