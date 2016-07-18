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
<div class="modalPrompt">
    <div class="modalPromptDialogs">
        <div class="modalPromptDialog createNewImageVariationPrompt">
            <div class="promptState state-default">
                <div class="promptTitle"><?php echo JText::_( 'COM_EASYBLOG_MM_CREATE_NEW_IMAGE_VARIATION' ); ?></div>
                <span class="promptText"><?php echo JText::_( 'COM_EASYBLOG_MM_CREATE_NEW_IMAGE_VARIATION_DESC' ); ?></span>
                <div class="promptForm imageVariationForm">
                    <div class="formGroup">
                        <label class="formLabel"><?php echo JText::_( 'COM_EASYBLOG_MM_NAME' );?></label>
                        <div class="formControl"><input type="text" class="imageSizeInput newVariationName"></div>
                    </div>
                    <div class="formGroup">
                        <label class="formLabel"><?php echo JText::_( 'COM_EASYBLOG_MM_WIDTH' );?></label>
                        <div class="formControl"><input type="text" class="imageSizeInput newVariationWidth"></div>
                    </div>
                    <div class="formGroup">
                        <label class="formLabel"><?php echo JText::_( 'COM_EASYBLOG_MM_HEIGHT' );?></label>
                        <div class="formControl"><input type="text" class="imageSizeInput newVariationHeight"></div>
                    </div>
                    <div class="newVariationRatio locked"></div>
                    <input class="newVariationLockRatio" type="checkbox" checked="checked" />
                </div>
                <div class="promptActions">
                    <button class="button promptCancelButton cancelVariationButton"><i></i><?php echo JText::_( 'COM_EASYBLOG_MM_CANCEL_BUTTON' ); ?></button>
                    <button class="button green-button createVariationButton"><i></i><?php echo JText::_( 'COM_EASYBLOG_MM_CREATE_BUTTON' ); ?></button>
                </div>
            </div>
            <div class="promptState state-progress">
                <div class="promptTitle"><?php echo JText::_( 'COM_EASYBLOG_MM_CREATING_VARIATION' ); ?></div>
                <span class="promptText"><?php echo JText::sprintf( 'COM_EASYBLOG_MM_WAIT_CREATING_VARIATION', '<span class="variationName">.</span> (<span class="variationWidth">.</span> x <span class="variationHeight">.</span>)' ); ?></span>
                <div class="promptLoader"></div>
            </div>
            <div class="promptState state-fail">
                <div class="promptTitle"><?php echo JText::_( 'COM_EASYBLOG_MM_FAIL_CREATING_VARIATION' ); ?></div>
                <span class="promptText"><?php echo JText::_( 'COM_EASYBLOG_MM_UNABLE_TO_CREATE_VARIATION' ); ?> <span class="variationName">.</span> (<span class="variationWidth">.</span> x <span class="variationHeight">.</span>)</span>
                <div class="promptActions">
                    <button class="button promptCancelButton cancelVariationButton"><i></i><?php echo JText::_( 'COM_EASYBLOG_MM_CANCEL_BUTTON' ); ?></button>
                    <button class="button green-button tryCreateVariationButton"><i></i><?php echo JText::_( 'COM_EASYBLOG_MM_TRY_AGAIN_BUTTON' ); ?></button>
                </div>
            </div>
        </div>
        <div class="overlay"></div>
    </div>
</div>