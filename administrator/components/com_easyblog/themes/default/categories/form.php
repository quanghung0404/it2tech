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
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <div class="app-tabs">
        <ul class="app-tabs-list list-unstyled">
            <li class="tabItem active">
                <a data-bp-toggle="tab" href="#general" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_FORM_TITLE');?>
                </a>
            </li>
            <li class="tabItem">
                <a data-bp-toggle="tab" href="#entry" data-form-layouts>
                    <?php echo JText::_('COM_EASYBLOG_CATEGORIES_DEFAULT_POST_OPTIONS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#tags" data-form-layouts>
                    <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_FORM_TAGS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#access" data-form-permissions>
                    <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_FORM_PERMISSIONS');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div id="general" class="tab-pane active in">
            <?php echo $this->output('admin/categories/form.general'); ?>
        </div>

        <div id="entry" class="tab-pane">
            <?php echo $this->output('admin/categories/form.layout.entry'); ?>
        </div>

        <div id="tags" class="tab-pane">
            <?php echo $this->output('admin/categories/form.tags'); ?>
        </div>

        <div id="access" class="tab-pane">
            <?php echo $this->output('admin/categories/form.access'); ?>
        </div>
    </div>

    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="savenew" value="0" id="savenew" />
    <input type="hidden" name="option" value="com_easyblog" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="id" value="<?php echo $category->id;?>" />
</form>
