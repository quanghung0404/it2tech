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
<div class="row mt-20 mb-20">
    <div class="col-md-12">
        <div class="es-photos-resize">
            <?php if ($this->config->get('photos.enabled')) { ?>
            <a class="es-photo-small pull-left" data-es-photo="<?php echo $photo->id; ?>" href="<?php echo $photo->getPermalink(); ?>">
            <?php } else { ?>
            <span class="es-photo-small pull-left">
            <?php } ?>

                <img src="<?php echo $photo->getSource('square'); ?>" class="es-stream-content-avatar" />

            <?php if ($this->config->get('photos.enabled')) { ?>
            </a>
            <?php } else { ?>
            </span>
            <?php } ?>
        </div>
    </div>
</div>
