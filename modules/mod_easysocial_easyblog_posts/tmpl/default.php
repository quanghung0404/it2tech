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
<div id="fd" class="es mod-es-recent-blogs module-social<?php echo $suffix;?>">
	<ul class="widget-list es-nav es-nav-stacked es-nav-blogs">
		<?php foreach( $posts as $post ){ ?>
		<li>
			<div class="media widget-main-link pl-15 pt-5 pr-15">
				<?php if( $params->get( 'show_image' , true ) ){ ?>
				<div class="media-object pull-left">
					<span class="es-avatar es-avatar-sm">
						<a href="<?php echo $post->getPermalink();?>" title="<?php echo $modules->html( 'string.escape' , $post->title );?>">
						<?php if ($post->hasImage()) { ?>
								<img src="<?php echo $post->getImage('medium');?>" />
						<?php } else { ?>
							<img src="<?php echo rtrim( JURI::root() , '/' );?>/modules/mod_easysocial_easyblog_posts/styles/default.png" />
						<?php } ?>
						</a>
					</span>
				</div>
				<?php } ?>

				<div class="media-body">
					<div class="es-mod-title">
						<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
					</div>

					<?php if ($params->get('show_category', true)) { ?>
					<div class="es-mod-desp">
						<a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo JText::_($post->getPrimaryCategory()->title); ?></a>
					</div>
					<?php } ?>

					<?php if( $params->get( 'show_author' , true ) ){ ?>
					<div class="es-mod-author">
						<?php echo JText::_( 'MOD_EASYSOCIAL_EASYBLOG_BY' );?> <a href="<?php echo $post->user->getPermalink();?>"<?php echo $params->get( 'popover' , true ) ? ' data-popbox="module://easysocial/profile/popbox" data-user-id="' . $post->user->id . '"' : '';?>><?php echo $post->user->getName();?></a>
					</div>
					<?php } ?>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>
