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
<div class="stream-links">
	<h4 class="es-stream-content-title<?php echo $image || $content ? ' has-info' : '';?>">
		<a target="_blank" href="<?php echo $assets->get( 'link' );?>"<?php echo $params->get('stream_link_nofollow', false) ? ' rel="nofollow"' : '';?>>
			<?php echo $assets->get('title'); ?>
		</a>
	</h4>

	<?php if ($image || $content) { ?>
	<div class="links-content">
		<div class="fd-small es-stream-preview">
			<?php if ($params->get('stream_thumbnail', true) && $image) { ?>

				<?php if (isset($oembed->html) && (!$oembed->isArticle)) { ?>

					<?php if (!isset($oembed->thumbnail)) { ?>
						<div class="<?php echo !isset($oembed->thumbnail) || !$oembed->thumbnail ?  '' : 'video-container'; ?>">
						<?php echo $oembed->html; ?>
						</div>
					<?php } else { ?>
						<a href="javascript:void(0);" class="stream-preview-image" data-es-links-embed-item data-es-stream-embed-player="<?php echo $this->html('string.escape', $oembed->html);?>">
							<img src="<?php echo $oembed->thumbnail;?>" />
							<i class="icon-es-video-play"></i>
						</a>
					<?php } ?>

				<?php } else { ?>
					<a href="<?php echo $assets->get('link');?>" class="stream-preview-image" target="_blank"<?php echo $params->get('stream_link_nofollow', false) ? ' rel="nofollow"' : '';?>>
						<img src="<?php echo $image;?>" alt="<?php echo $this->html('string.escape', $assets->get('title'));?>" />
					</a>
				<?php } ?>
			<?php } ?>

			<?php echo $content;?>
		</div>
	</div>
	<?php } ?>
</div>
