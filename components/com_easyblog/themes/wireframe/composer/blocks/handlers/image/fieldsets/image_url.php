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
<div class="eb-composer-fieldset eb-image-url-fieldset" data-eb-image-url-fieldset data-name="image-url">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_URL'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field style-bordered eb-image-url-field" data-eb-image-url-field>
            <div style="margin: 0 auto;" class="input-group">
                <input type="text" value="" class="form-control" data-eb-image-url-field-text />
                <span class="input-group-btn">
                    <a href="javascript:void(0);" class="btn btn-default" data-eb-image-url-field-update-button><?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON'); ?></a>
                </span>
            </div>
        </div>
    </div>
</div>
