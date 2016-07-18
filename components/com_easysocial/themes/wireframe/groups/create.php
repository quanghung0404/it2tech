<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-container es-groups" data-groups>
    <div class="center mt-20 mb-20">
        <h2 class="h2"><?php echo JText::_('COM_EASYSOCIAL_GROUPS_SELECT_CATEGORY');?></h2>
        <p><?php echo JText::_('COM_EASYSOCIAL_GROUPS_SELECT_CATEGORY_INFO'); ?></p>
    </div>

    <hr />

    <div class="es-create-category-select">
        <div class="btn-group" data-bs-toggle="radio-buttons">
            <?php foreach($categories as $category){ ?>
            <div class="btn-wrap">
                <a href="<?php echo FRoute::groups(array('controller' => 'groups' , 'task' => 'selectCategory' , 'category_id' => $category->id));?>" class="btn btn-es">
                    <img src="<?php echo $category->getAvatar(SOCIAL_AVATAR_SQUARE);?>" class="avatar" />
                    <span><?php echo $category->get('title'); ?></span>
                    <div class="es-description fd-small es-muted mt-5"><?php echo $category->get('description'); ?></div>
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
