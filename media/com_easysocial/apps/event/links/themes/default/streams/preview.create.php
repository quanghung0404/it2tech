<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="stream-links">
    <h4 class="es-stream-content-title<?php echo $assets->get('image') || $assets->get('content') ? ' has-info' : ''; ?>">
        <a target="_blank" href="<?php echo $assets->get('link'); ?>"<?php if ($params->get('stream_link_nofollow', false)) { ?> rel="nofollow"<?php } ?>><?php echo $assets->get('title'); ?></a>
    </h4>

    <div class="links-content">
        <div class="es-stream-preview fd-small">
            <?php if ($params->get('stream_thumbnail', true) && $assets->get('image')) { ?>
                <?php if (isset($oembed->html)) { ?>

                    <?php if (!isset($oembed->thumbnail)) { ?>
                        <?php echo $oembed->html; ?>
                    <?php } else { ?>
                        <a href="javascript:void(0);" class="stream-preview-image" data-es-links-embed-item data-es-stream-embed-player="<?php echo $this->html('string.escape', $oembed->html); ?>">
                            <img src="<?php echo $oembed->thumbnail; ?>" />
                            <i class="icon-es-video-play"></i>
                        </a>
                    <?php } ?>

                <?php } else { ?>
                    <a href="<?php echo $assets->get('link'); ?>" class="stream-preview-image" target="_blank"<?php if ($params->get('stream_link_nofollow', false)) { ?> rel="nofollow"<?php } ?>>
                        <img src="<?php echo $image;?>" alt="<?php echo $this->html('string.escape', $assets->get('title'));?>" />
                    </a>
                <?php } ?>
            <?php } ?>

            <?php echo $assets->get('content', ''); ?>
        </div>
    </div>
</div>
