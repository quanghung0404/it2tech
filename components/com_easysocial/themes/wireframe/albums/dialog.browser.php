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
<div data-layout="dialog" data-album-browser="<?php echo $uuid; ?>" class="es-container es-media-browser layout-dialog">
	<div data-album-browser-sidebar class="es-sidebar">
		<div class="es-widget es-widget-borderless">
			<!-- <div class="es-widget-body"> -->
				<ul class="es-widget-filter fd-reset-list" data-album-list-item-group>
					<?php foreach($albums as $album) { ?>
					<li data-album-list-item data-album-id="<?php echo $album->id; ?>" class="<?php echo $album->id == $id ? 'active' : '';?>">
						<a href="<?php echo $album->getPermalink(); ?>">
							<i data-album-list-item-cover style="background-image: url(<?php echo $album->getCover( 'thumbnail' ); ?>);"></i>
							<span data-album-list-item-title><?php echo $album->get( 'title' ); ?></span> <b data-album-list-item-count><?php echo $album->getTotalPhotos(); ?></b>
						</a>
					</li>
					<?php } ?>
				</ul>
			<!-- </div> -->
		</div>
	</div>
	<div data-album-browser-content class="es-content"><?php echo $content; ?></div>
</div>
