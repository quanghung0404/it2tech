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
<div class="eb-composer-placeholder eb-composer-link-placeholder text-center" data-link-form>
    <i class="eb-composer-placeholder-icon fa fa-external-link"></i>
    <b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_ADD_LINK');?></b>
    <p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_ADD_LINK_NOTE');?></p>

    <p class="eb-composer-placeholder-error text-error hide" data-link-error><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_EMPTY'); ?></p>

    <div class="input-group" style="width: 50%; margin: 0 auto;">
        <input type="text" class="form-control input-sm" type="text" value="" data-link-input />
        <span class="input-group-btn">
            <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-link-add><?php echo JText::_('COM_EASYBLOG_BLOCKS_LINKS_ADD_LINK');?></a>
        </span>
    </div>
</div>

<div class="eb-composer-placeholder eb-composer-link-placeholder text-center hidden" data-link-loader>
    <i class="fa fa-refresh fa-spin mr-5"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_CRAWLING_LINK_DATA');?>
</div>


<div class="eb-composer-placeholder eb-composer-link-placeholder-preview hidden eb-blocks-link" data-link-preview>
    <div class="media-table">
        <a class="media-thumb" href="javascript:void(0);" data-preview-image-wrapper>
            <img class="media-object" alt="..." width="150" height="150" data-preview-image />
        </a>
        <div class="media-body">
            <h4 class="media-heading">
                <a href="javascript:void(0);" data-preview-title></a>
            </h4>

            <div class="media-content" data-preview-content></div>

            <div class="media-link">
                <a href="javascript:void(0);" data-preview-link></a>
            </div>
        </div>
    </div>
</div>

