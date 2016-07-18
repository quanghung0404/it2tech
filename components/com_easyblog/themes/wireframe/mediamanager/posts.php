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
<div class="eb-mm-folder" data-eb-mm-folder data-key="users">

    <div class="eb-composer-toolbar">
        <div>

            <div class="eb-composer-toolbar-set is-primary row-table" data-name="media-folder">
                <div class="col-cell cell-tight toolbar-left">
                    <div class="eb-composer-toolbar-group row-table">
                        <div class="eb-composer-toolbar-item col-cell is-button eb-mm-folder-back-button" data-eb-mm-folder-back-button>
                            <i class="fa fa-chevron-left"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_BACK_BUTTON');?></span>
                        </div>

                        <div class="eb-composer-toolbar-item col-cell">
                            <i class="fa fa-files-o"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_MM_PLACE_POSTS');?></span>
                        </div>
                    </div>
                </div>

                <div class="col-cell toolbar-center">&nbsp;</div>

                <div class="col-cell cell-tight toolbar-right">
                    <div class="eb-composer-toolbar-group row-table">

                        <div class="eb-composer-toolbar-item is-button col-cell">
                            <i class="fa fa-image"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_MM_RENAME');?></span>
                        </div>

                        <div class="eb-composer-toolbar-item is-button col-cell">
                            <i class="fa fa-image"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_MM_MOVE');?></span>
                        </div>

                        <div class="eb-composer-toolbar-item is-button col-cell eb-mm-close-button" data-eb-mm-close-button>
                            <i class="fa fa-close"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_MM_CLOSE_BUTTON'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="eb-composer-viewport" data-scrolly="y">
        <div class="eb-composer-viewport-content" data-scrolly-viewport>

            <div class="eb-mm-folder-content-panel">
                <div class="eb-mm-filegroup type-folder" data-eb-mm-filegroup>
                    <div class="eb-mm-filegroup-body">
                        <div class="eb-mm-filelist view-list">
                            <?php foreach ($posts as $post) { ?>
                            <div data-key="<?php echo EasyBlogMediaManager::getKey('post:' . $post->id);?>" data-eb-mm-file="" class="eb-mm-file type-folder">
                                <i class="fa fa-file"></i>
                                <span>
                                    <?php if ($post->title) { ?>
                                        <?php echo $post->title;?>
                                    <?php } else { ?>
                                        <?php echo JText::sprintf('COM_EASYBLOG_MM_PLACE_POST_UNTITLED', $post->id);?>
                                    <?php } ?>
                                </span>
                                <b class="fa fa-angle-right"></b>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>