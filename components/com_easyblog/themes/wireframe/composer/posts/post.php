<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-posts-list">
    <?php foreach ($posts as $post) { ?>
    <div class="eb-composer-posts-item">
        <a href="javascript:void(0);"><?php echo $post->title; ?></a>

        <div class="eb-composer-post-publish">
            <time><?php echo $post->formattedDate; ?></time>
        </div>

        <div class="btn-group btn-group-sm">
            <a href="javascript:void(0);" class="btn btn-default"
                data-eb-composer-insert-link 
                data-title="<?php echo htmlspecialchars($post->title, ENT_QUOTES); ?>" 
                data-permalink="<?php echo $post->permalink; ?>"
                data-image="<?php echo $post->getImage();?>"
                data-content="<?php echo strip_tags($post->getIntro());?>"
            ><?php echo JText::_('COM_EASYBLOG_COMPOSER_INSERT_TO_POST');?></a>
        </div>
    </div>
    <?php } ?>
</div>