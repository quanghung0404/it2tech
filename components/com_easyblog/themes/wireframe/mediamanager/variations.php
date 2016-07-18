<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-list eb-mm-variation-list" data-eb-mm-variation-list>
    <div class="eb-list-item-group eb-mm-variation-item-group" data-eb-mm-variation-item-group>

        <?php
        // System variations is hardcoded. It will also show missing variations.
        foreach(array('original', 'large', 'medium', 'thumbnail', 'small', 'icon') as $name) {

            $key = 'system/' . $name;
            $missing = !isset($file->variations[$key]);

            if (!$missing) {
                $variation = $file->variations[$key];
            } else {
                $variation = new stdClass();
            }
        ?>
        <div class="eb-list-item eb-mm-variation-item is-system<?php echo $missing ? ' is-missing' : '' ?>"
             data-eb-mm-variation-item
             data-key="<?php echo $key; ?>"
             data-size="<?php if (isset($variation->size)) { echo EasyBlogMediaManager::formatSize($variation->size); } ?>">
            <div>
                <i class="fa <?php echo $missing ? 'fa-warning' : 'fa-lock'; ?>"></i>
                <span><?php echo JText::_('COM_EASYBLOG_MM_IMAGE_SIZE_' . JString::strtoupper($name)); ?></span>
                <?php if (isset($variation->width) && isset($variation->height)) { ?>
                <small><?php echo $variation->width . 'x' . $variation->height; ?></small>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <?php
        // User variations
        foreach($file->variations as $key => $variation) {
            // Skip non-user variations
            if (!preg_match("/^user/ui", $key)) continue;
        ?>
        <div class="eb-list-item eb-mm-variation-item"
             data-eb-mm-variation-item
             data-key="<?php echo $key; ?>"
             data-size="<?php if (isset($variation->size)) { echo EasyBlogMediaManager::formatSize($variation->size); } ?>">
            <div>
                <span><?php echo ucfirst($variation->name); ?></span>
                <?php if (isset($variation->width) && isset($variation->height)) { ?>
                <small><?php echo $variation->width . 'x' . $variation->height; ?></small>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>