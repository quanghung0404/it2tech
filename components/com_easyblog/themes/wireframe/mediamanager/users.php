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

                        <div class="eb-composer-toolbar-item col-cell" data-eb-mm-folder-back-button>
                            <i class="fa fa-files-o"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_MM_PLACE_USERS');?></span>
                        </div>
                    </div>
                </div>

                <div class="col-cell toolbar-center">&nbsp;</div>

                <div class="col-cell cell-tight toolbar-right">
                    <div class="eb-composer-toolbar-group row-table">

                        <div class="eb-composer-toolbar-item is-button col-cell" data-eb-mm-search-toggle-button>
                            <i class="fa fa-search"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_MM_SEARCH_BUTTON');?></span>
                        </div>
                        <div class="eb-composer-toolbar-item is-button col-cell eb-mm-close-button" data-eb-mm-close-button>
                            <i class="fa fa-close"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_MM_CLOSE_BUTTON');?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eb-composer-toolbar-set row-table" data-name="users-search">
                <div class="col-cell toolbar-left">
                    <div class="eb-composer-toolbar-group row-table">
                        <div class="eb-composer-toolbar-item col-cell cell-tight join-right">
                            <i class="fa fa-search"></i>
                        </div>
                        <div class="eb-composer-toolbar-item col-cell">
                            <input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_MM_SEARCH_MEDIA_FILES');?>" data-eb-mm-search-input>
                        </div>
                    </div>
                </div>
                <div class="col-cell cell-tight toolbar-right">
                    <div class="eb-composer-toolbar-group row-table">
                        <div class="eb-composer-toolbar-item is-button col-cell eb-mm-search-toggle-button" data-eb-mm-search-toggle-button>
                            <i class="fa fa-close"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_MM_CANCEL_BUTTON');?></span>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="eb-composer-viewport push-bottom" data-scrolly="y">
        <div class="eb-composer-viewport-content" data-scrolly-viewport>

            <div class="eb-mm-folder-content-panel">

                <div class="eb-mm-filegroup type-folder" data-eb-mm-filegroup>
                    <div class="eb-mm-filegroup-body">
                        <div class="eb-mm-filelist view-list">
                            <?php foreach ($authors as $author) { ?>
                            <div data-type="user" data-key="<?php echo EasyBlogMediaManager::getKey('user:' . $author->id);?>" data-eb-mm-file="" class="eb-mm-file type-folder is-search-result">
                                <img src="<?php echo $author->getAvatar();?>" width="16" />&nbsp;
                                <span data-eb-mm-file-title><?php echo $author->getName();?></span>
                                <b class="fa fa-angle-right"></b>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="eb-composer-pagination text-center">
                <a href="javascript:void(0);" class="eb-panel-older pull-left<?php echo $pagination->pagesCurrent <= 1 ? ' inactive' : '';?>"
                    <?php if($pagination->pagesCurrent > 1) { ?>
                    data-eb-mm-pagination-next
                    <?php } ?>
                    data-page="<?php echo $pagination->pagesCurrent - 2;?>"
                >
                    <i class="fa fa-chevron-left"></i>
                </a>

                <a href="javascript:void(0);" class="eb-panel-newer pull-right<?php echo $pagination->pagesCurrent == $pagination->pagesTotal ? ' inactive' : '';?>"
                    <?php if ($pagination->pagesCurrent < $pagination->pagesTotal) { ?>
                    data-eb-mm-pagination-next
                    <?php } ?>

                    data-page="<?php echo $pagination->pagesCurrent;?>"
                >
                    <i class="fa fa-chevron-right"></i>
                </a>

                <div class="eb-panel-pager">
                    <span data-eb-mm-pagination-current><?php echo $pagination->pagesCurrent;?></span> /
                    <span data-eb-mm-pagination-total><?php echo $pagination->pagesTotal;?></span>
                </div>
            </div>

            <?php echo $this->output('site/mediamanager/hints/notfound'); ?>

        </div>
    </div>

</div>
