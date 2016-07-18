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
<div class="eb-mm-hint hint-upload">
    <div class="eb-hint layout-overlay style-gray">
        <div>
            <i class="eb-hint-icon fa fa-upload"></i>
            <span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_MM_UPLOAD_DROP_MESSAGE');?><br/>
                <span class="eb-plupload-btn">
                    <button class="btn btn-primary" data-plupload-browse-button>
                        <i class="fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MM_UPLOAD');?>
                    </button>
                </span>
            </span>
        </div>
    </div>
</div>
