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
<div class="eb-composer-fieldset eb-note-style-field">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_TYPE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field">

            <div class="eb-swatch swatch-grid">
                <div class="row" data-eb-composer-block-alert-type>
                    <div class="col-xs-6">
                        <div class="eb-swatch-item eb-swatch-alert" data-type="success">
                            <div class="eb-swatch-preview">
                                <div>
                                    <div class="alert alert-success" role="alert">
                                        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_PREVIEW_TITLE');?></strong>
                                        <br />
                                        <?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_PREVIEW_CONTENT');?>
                                    </div>
                                </div>
                            </div>
                            <div class="eb-swatch-label">
                                <span><?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_SUCCESS');?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="eb-swatch-item eb-swatch-alert" data-type="info">
                            <div class="eb-swatch-preview">
                                <div>
                                    <div class="alert alert-info" role="alert">
                                        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_PREVIEW_TITLE');?></strong>
                                        <br />
                                        <?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_PREVIEW_CONTENT');?>
                                    </div>
                                </div>
                            </div>
                            <div class="eb-swatch-label">
                                <span><?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_INFO');?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="eb-swatch-item eb-swatch-alert selected" data-type="warning">
                            <div class="eb-swatch-preview">
                                <div>
                                    <div class="alert alert-warning" role="alert">
                                        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_PREVIEW_TITLE');?></strong>
                                        <br />
                                        <?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_PREVIEW_CONTENT');?>
                                    </div>
                                </div>
                            </div>
                            <div class="eb-swatch-label">
                                <span><?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_WARNING');?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="eb-swatch-item eb-swatch-alert" data-type="danger">
                            <div class="eb-swatch-preview">
                                <div>
                                    <div class="alert alert-danger" role="alert">
                                        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_PREVIEW_TITLE');?></strong>
                                        <br />
                                        <?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_PREVIEW_CONTENT');?>
                                    </div>
                                </div>
                            </div>
                            <div class="eb-swatch-label">
                                <span><?php echo JText::_('COM_EASYBLOG_BLOCKS_NOTE_DANGER');?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
