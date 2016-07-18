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
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="app-tabs">
        <ul class="app-tabs-list list-unstyled">
            <li class="tabItem active">
                <a data-bp-toggle="tab" href="#general" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_GENERAL');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#listings" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_LIST_OPTION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#category" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_CATEGORY_OPTION');?>
                </a>
            </li>

            <li class="authorItem">
                <a data-bp-toggle="tab" href="#author" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_AUTHOR_OPTION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#tag" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_TAG_OPTION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#posts" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_POST_OPTION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#pagination" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_PAGINATION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#truncation" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_TRUNCATION');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#avatars" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_AVATARS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#layouttoolbar" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_TOOLBAR');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#cover" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_POST_COVER');?>
                </a>
            </li>

             <li class="tabItem">
                <a data-bp-toggle="tab" href="#showcase" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SUBTAB_SHOWCASE');?>
                </a>
            </li>

        </ul>
    </div>

    <div class="tab-content">
        <div id="general" class="tab-pane active in">
            <?php echo $this->output('admin/settings/layout/general'); ?>
        </div>

        <div id="listings" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/listings'); ?>
        </div>

        <div id="category" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/category'); ?>
        </div>

        <div id="author" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/author'); ?>
        </div>

        <div id="tag" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/tag'); ?>
        </div>

        <div id="posts" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/posts'); ?>
        </div>

        <div id="pagination" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/pagination'); ?>
        </div>

        <div id="truncation" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/truncation'); ?>
        </div>

        <div id="avatars" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/avatars'); ?>
        </div>

        <div id="layouttoolbar" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/toolbar'); ?>
        </div>

        <div id="cover" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/cover'); ?>
        </div>

        <div id="showcase" class="tab-pane">
            <?php echo $this->output('admin/settings/layout/showcase'); ?>
        </div>

    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="layout" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />
</form>
