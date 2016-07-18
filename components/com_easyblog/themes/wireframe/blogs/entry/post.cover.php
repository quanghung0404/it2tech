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
<?php if ($post->image && $this->entryParams->get('post_image', true) || (!$post->image && $this->entryParams->get('post_image_placeholder', false) && $this->entryParams->get('post_image', true))) { ?>
    <div class="eb-image eb-post-thumb<?php echo $this->config->get('cover_width_entry_full') ? " is-full" : " is-" . $this->config->get('cover_alignment_entry')?>">
        <?php if (!$this->config->get('cover_crop_entry', false)) { ?>
            <a class="eb-post-image eb-image-popup-button"
                href="<?php echo $post->getImage('original');?>"
                title="<?php echo $this->escape($post->title);?>"
                target="_blank"
                style="
                    <?php if ($this->config->get('cover_width_entry_full')) { ?>
                    width: 100%;
                    <?php } else { ?>
                    width: <?php echo $this->config->get('cover_width_entry');?>px;
                    <?php } ?>"
            >
                <img itemprop="image" src="<?php echo $post->getImage($this->config->get('cover_size_entry', 'large'));?>" alt="<?php echo $this->escape($post->title);?>" />
            </a>
        <?php } ?>

        <?php if ($this->config->get('cover_crop_entry', false)) { ?>
            <a class="eb-post-image-cover eb-image-popup-button"
                href="<?php echo $post->getImage('original');?>"
                title="<?php echo $this->escape($post->title);?>"
                target="_blank"
                style="
                    background-image: url('<?php echo $post->getImage($this->config->get('cover_size_entry', 'large'));?>'); 
                    <?php if ($this->config->get('cover_width_entry_full')) { ?>
                    width: 100%;
                    <?php } else { ?>
                    width: <?php echo $this->config->get('cover_width_entry');?>px;
                    <?php } ?>
                    height: <?php echo $this->config->get('cover_height_entry');?>px;"
            ></a>
        <?php } ?>
    </div>
<?php } ?>