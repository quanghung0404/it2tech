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
<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_AUTOPLAY'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field mb-10">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'autoplay', true, 'autoplay', 'data-audio-fieldset-autoplay'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_REPLAY_AUTOMATICALLY'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field mb-10">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'audio_loop', true, 'audio_loop', 'data-audio-fieldset-loop'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_DISPLAY_ARTIST'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field mb-10">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'artist', true, 'artist', 'data-audio-fieldset-artist'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_DISPLAY_TRACK'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field mb-10">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'track', true, 'track', 'data-audio-fieldset-track'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_DISPLAY_DOWNLOAD_LINK'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field mb-10">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'download', true, 'download', 'data-audio-fieldset-download'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

