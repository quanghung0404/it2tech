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
<?php foreach($blocks as $category => $blockItems) { ?>
    <?php if ((count($blockItems) == 1 && $blockItems[0]->visible == true) || count($blockItems) > 1) { ?>
    <div class="eb-composer-fieldset">
        <div class="eb-composer-fieldset-header">
            <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_CATEGORY_' . strtoupper($category)); ?></strong>
        </div>
        <div class="eb-composer-fieldset-content">
            <div class="eb-composer-block-menu-group" data-eb-composer-block-menu-group>
                <?php foreach ($blockItems as $block) { ?>
                    <?php echo $this->output('site/composer/blocks/menu', array('block' => $block)); ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
<?php } ?>