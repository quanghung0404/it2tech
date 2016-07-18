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
<div class="eb-composer-fieldset eb-image-link-fieldset is-disabled" data-eb-image-link-fieldset data-name="image-link">
    <label class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LINK'); ?></strong>
        <?php echo $this->output('site/composer/fields/checkbox', array(
                'classname' => 'eb-composer-fieldset-toggle eb-image-link-toggle',
                'attributes' => 'data-eb-image-link-toggle'
            )); ?>
    </label>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field eb-image-link" data-name="image-link">
            <?php echo $this->output('site/composer/fields/link'); ?>
        </div>
    </div>
</div>