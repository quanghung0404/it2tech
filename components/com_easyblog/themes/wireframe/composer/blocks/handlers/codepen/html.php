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
<div class="eb-composer-placeholder eb-composer-link-placeholder text-center" data-codepen-form>
    <i class="eb-composer-placeholder-icon fa fa-codepen"></i>
    <b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODEPEN');?></b>
    <p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODEPEN_DESC');?></p>
    <p class="eb-composer-placeholder-error text-error hide" data-codepen-error><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODEPEN_EMPTY'); ?></p>
    <div class="input-group" style="width: 70%; margin: 0 auto;">
        <input type="text" class="form-control input-sm" type="text" value="" data-codepen-source placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODEPEN_PLACEHOLDER', true);?>" />
        <span class="input-group-btn">
            <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-codepen-insert><?php echo JText::_('COM_EASYBLOG_BLOCKS_SLIDESHARE_EMBED_CODEPEN_BUTTON');?></a>
        </span>
    </div>
</div>

<div class="eb-composer-placeholder eb-composer-video-placeholder text-center hidden" data-codepen-loader>
    <i class="fa fa-refresh fa-spin mr-5"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_SLIDESHARE_EMBED_SLIDES_LOADING');?>
</div>

<div class="eb-composer-placeholder eb-composer-link-placeholder-preview hidden" data-codepen-preview>
</div>

