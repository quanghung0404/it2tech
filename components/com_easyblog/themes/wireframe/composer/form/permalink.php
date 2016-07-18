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
<div class="eb-composer-field eb-composer-field-permalink">
    <div class="permalink-editor" data-permalink-preview>
        <span id="permalink-url" data-permalink-data><?php echo $post->permalink;?></span>
        <a href="javascript:void(0);" class="btn btn-default btn-xs" style="display: inline;" data-permalink-edit><?php echo JText::_('COM_EASYBLOG_EDIT'); ?></a>
    </div>

    <div class="eb-composer-permalink-edit hide" data-permalink-editor>
        <div class="input-group">
            <input type="text" class="form-control input-sm" type="text" name="permalink" value="<?php echo $this->html('string.escape', $post->permalink);?>" data-permalink-input />
            <span class="input-group-btn">
                <a href="javascript:void(0);" class="btn btn-default btn-sm" title="<?php echo JText::_('COM_EASYBLOG_SAVE'); ?>" data-permalink-save>
                    <i class="fa fa-save"></i>
                </a>
                <a href="javascript:void(0);" class="btn btn-danger btn-sm" data-permalink-edit-cancel>
                    <i class="fa fa-close"></i>
                </a>
            </span>
        </div>
    </div>
</div>