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
<?php if ($tags) { ?>
    <?php foreach ($tags as $tag) { ?>
    <li data-tags-item data-id="<?php echo $tag->id;?>">
        <div class="es-avatar-wrap">
            <div class="es-avatar-remove-tag">
                <a href="javascript:void(0);" data-placement="top" data-es-provide="tooltip" data-original-title="<?php echo JText::_('Remove Tag');?>" data-remove-tag>
                    <i class="fa fa-remove"></i>
                </a>
            </div>
            <a class="es-avatar es-avatar-sm" href="<?php echo $tag->getEntity()->getPermalink();?>" data-placement="top" data-es-provide="tooltip" data-original-title="<?php echo $tag->getEntity()->getName();?>">
                <img src="<?php echo $tag->getEntity()->getAvatar();?>" /> <?php echo $tag->getEntity()->getName();?>
            </a>
        </div>
    </li>
    <?php } ?>
<?php } ?>