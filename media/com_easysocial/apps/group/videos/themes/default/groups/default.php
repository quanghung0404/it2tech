<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-es-videos class="es-container es-videos" data-videos-listing>
    <div class="es-content">

        <div class="es-filterbar">
            <div class="filterbar-title h5">
                <?php echo JText::_("Videos");?>
                <a href="<?php echo FRoute::videos(array('layout' => 'form', 'uid' => $group->id, 'type' => SOCIAL_TYPE_GROUP));?>" class="pull-right btn btn-es btn-sm"><?php echo JText::_('Add Video');?></a>
            </div>
        </div>

        <div class="es-videos-content-wrapper es-responsive">

            <div class="es-video-content es-video-item-group" data-videos-result>

                <div class="es-video-content es-video-item-group" data-videos-result>
                    <?php echo $this->output('site/videos/default.items'); ?>
                </div>
            </div>

        </div>
    </div>
</div>