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
<div class="eb-composer-toolbar-set row-table" data-name="media-folder">

    <div class="col-cell cell-tight toolbar-left">
        <div class="eb-composer-toolbar-group row-table">
            <div class="eb-composer-toolbar-item col-cell is-button eb-mm-folder-back-button" data-eb-mm-folder-back-button>
                <i class="fa fa-chevron-left"></i>
                <span><?php echo JText::_('COM_EASYBLOG_MM_BACK');?></span>
            </div>
            <div class="eb-composer-toolbar-item col-cell eb-mm-folder-header">

                <?php if ($place->acl->canUploadItem) { ?>
                <div class="dropdown" data-scantime="<?php echo $folder->scantime; ?>">
                    <span class="dropdown-toggle_" data-bp-toggle="dropdown">
                        <i class="<?php echo $place->icon;?>"></i>
                        <strong data-eb-mm-folder-title><?php echo $folder->title; ?></strong>
                        <span class="eb-mm-folder-title-filter">
                            <span class="filter-all">
                                <?php echo JText::_('COM_EASYBLOG_MM_ALL_ITEMS');?>
                            </span>
                            <span class="filter-new">
                                <?php echo JText::_('COM_EASYBLOG_MM_RECENTLY_UPLOADED');?>
                            </span>
                        </span>
                    </span>
                    <ul class="dropdown-menu">
                        <li class="active" data-eb-mm-show-all-files-button>
                            <a>
                                <?php echo JText::_('COM_EASYBLOG_MM_ALL_ITEMS');?>
                            </a>
                        </li>
                        <li data-eb-mm-show-new-files-button>
                            <a>
                                <?php echo JText::_('COM_EASYBLOG_MM_RECENTLY_UPLOADED');?> <b data-eb-mm-new-file-count></b>
                            </a>
                        </li>
                    </ul>
                </div>
                <?php } else { ?>
                <div data-scantime="<?php echo $folder->scantime; ?>">
                    <strong data-eb-mm-folder-title><?php echo $folder->title; ?></strong>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-cell toolbar-center">&nbsp;</div>

    <div class="col-cell cell-tight toolbar-right">
        <div class="eb-composer-toolbar-group row-table">

            <?php if ($place->acl->canUploadItem) { ?>
            <div class="eb-composer-toolbar-item is-button col-cell eb-mm-folder-upload-button" id="<?php echo $browseButtonId; ?>" data-eb-mm-folder-upload-button>
                <i class="fa fa-upload"></i>
                <span><?php echo JText::_('COM_EASYBLOG_MM_UPLOAD');?></span>
            </div>
            <?php } ?>

            <div class="eb-composer-toolbar-item is-button col-cell eb-mm-search-toggle-button" data-eb-mm-search-toggle-button>
                <i class="fa fa-search"></i>
                <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_MM_SEARCH');?></span>
            </div>

            <?php if ($place->acl->canCreateFolder || (($place->acl->canRenameItem || $place->acl->canRemoveItem) && !$folder->root)) { ?>
            <div class="eb-composer-toolbar-item is-button col-cell eb-mm-folder-menu-button dropdown_" data-eb-mm-folder-menu-button>
                <div class="dropdown-toggle_" data-bp-toggle="dropdown">
                    <span>
                        <i class="fa fa-bars"></i>
                        <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_MM_ACTIONS');?></span>
                    </span>
                </div>
                <ul class="dropdown-menu pull-right">

                    <?php if ($place->acl->canCreateFolder) { ?>
                    <li data-eb-mm-create-folder>
                        <a>
                            <i class="fa fa-folder-open-o"></i> <?php echo JText::_('COM_EASYBLOG_MM_CREATE_FOLDER');?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if ($currentEditor != 'composer') { ?>
                    <li data-eb-mm-insert-gallery>
                        <a>
                            <i class="fa fa-photo"></i> <?php echo JText::_('COM_EASYBLOG_MM_INSERT_AS_GALLERY');?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if (!$folder->root) { ?>
                        <?php if ($place->acl->canRenameItem) { ?>
                        <li data-eb-mm-folder-rename-button>
                            <a><i class="fa fa-edit"></i> <?php echo JText::_('COM_EASYBLOG_MM_RENAME_FOLDER');?></a>
                        </li>
                        <?php } ?>

                        <li data-eb-mm-folder-move-button>
                            <a><i class="fa fa-exchange"></i> <?php echo JText::_('COM_EASYBLOG_MM_MOVE_FOLDER');?></a>
                        </li>

                        <?php if ($place->acl->canRemoveItem) { ?>
                        <li data-eb-mm-folder-remove-button>
                            <a><i class="fa fa-trash-o"></i> <?php echo JText::_('COM_EASYBLOG_MM_REMOVE_FOLDER');?></a>
                        </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>

            <div class="eb-composer-toolbar-item is-button hide-label col-cell eb-mm-close-button" data-eb-mm-close-button>
                <i class="fa fa-times-circle"></i>
                <span><?php echo JText::_('COM_EASYBLOG_MM_CLOSE');?></span>
            </div>
        </div>
    </div>

</div>
