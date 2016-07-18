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
<div class="eb-composer-toolbar">
    <div>
        <div class="eb-composer-toolbar-set is-primary row-table" data-name="media-info">

            <div class="col-cell cell-tight toolbar-left">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item col-cell is-button eb-mm-info-back-button" data-eb-mm-info-back-button>
                        <i class="fa fa-chevron-left"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_MM_BACK');?></span>
                    </div>
                </div>
            </div>

            <div class="col-cell toolbar-center">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item col-cell cell-ellipse">
                        <strong>
                            <i class="fa fa-camera"></i>
                            <span><?php echo $file->title; ?></span>
                        </strong>
                        <div class="eb-mm-info-filemeta">
                            <?php if (isset($file->size) && $file->size) { ?>
                            <span class="eb-mm-info-filesize"><?php echo EBMM::formatSize($file->size); ?></span>
                            <?php } ?>

                            <?php if (isset($file->modified) && $file->modified) { ?>
                            <span class="eb-mm-info-filecreation">(<?php echo EB::date($file->modified)->toMySQL(); ?>)</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-cell cell-tight toolbar-right">
                <div class="eb-composer-toolbar-group row-table">

                    <?php if (!EBMM::isExternalPlace($place->id) && ($place->acl->canRenameItem || $place->acl->canMoveItem || $place->acl->canRemoveItem)) { ?>
                    <div class="eb-composer-toolbar-item is-button col-cell dropdown_">
                        <div class="eb-mm-info-menu-button dropdown-toggle_" data-bp-toggle="dropdown" >
                            <span>
                                <i class="fa fa-bars"></i>
                                <span>Actions</span>
                            </span>
                        </div>
                        <ul class="dropdown-menu pull-right">
                            <?php if ($place->acl->canRenameItem) { ?>
                            <li>
                                <a data-eb-mm-file-rename-button><span><?php echo JText::_('COM_EASYBLOG_MM_RENAME');?></span></a>
                            </li>
                            <?php } ?>

                            <?php if ($place->acl->canMoveItem) { ?>
                            <li>
                                <a data-eb-mm-show-move-dialog-button><span><?php echo JText::_('COM_EASYBLOG_MM_MOVE');?></span></a>
                            </li>
                            <?php } ?>

                            <?php if ($place->id != 'flickr' && $place->acl->canRemoveItem) { ?>
                            <li class="divider"></li>
                            <li>
                                <a data-eb-mm-file-remove-button><?php echo JText::_('COM_EASYBLOG_MM_REMOVE');?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>

                    <div class="eb-composer-toolbar-item is-button col-cell eb-mm-info-insert-button" data-eb-mm-file-insert-button>
                        <i class="fa fa-arrow-circle-up"></i>
                        <span class="insert-text"><?php echo JText::_('COM_EASYBLOG_MM_INSERT');?></span>
                        <span class="select-text"><?php echo JText::_('COM_EASYBLOG_MM_SELECT');?></span>
                    </div>

                    <div class="eb-composer-toolbar-item is-button hide-label col-cell eb-mm-close-button" data-eb-mm-close-button>
                        <i class="fa fa-times-circle"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_MM_CLOSE');?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>