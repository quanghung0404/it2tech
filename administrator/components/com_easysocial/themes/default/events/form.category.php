<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm" class="groupsForm" method="post" enctype="multipart/form-data">
    <div class="wrapper accordion">
        <div class="tab-box tab-box-alt">
            <div class="tabbable">

                <?php echo $this->loadTemplate('admin/groups/form.category.tabs' , array('isNew' => $category->id == 0 , 'activeTab' => $activeTab)); ?>

                <div class="tab-content">
                    <div id="settings" class="tab-pane<?php echo $activeTab == 'settings' ? ' active in' : '';?>">
                        <?php echo $this->includeTemplate('admin/events/form.category.settings'); ?>
                    </div>

                    <?php if($category->id){ ?>
                    <div id="fields" class="tab-pane<?php echo $activeTab == 'fields' ? ' active in' : '';?>">
                        <?php echo $this->includeTemplate('admin/profiles/form.fields', array('id' => $category->id, 'formType' => 'CLUSTER')); ?>
                    </div>

                    <div id="access" class="tab-pane<?php echo $activeTab == 'access' ? ' active in' : '';?>">
                        <?php echo $accessForm; ?>
                    </div>
                    <?php } ?>

                </div>

            </div>
        </div>
    </div>

    <input type="hidden" name="activeTab" data-tab-active value="<?php echo $activeTab; ?>" />
    <input type="hidden" name="option" value="com_easysocial" />
    <input type="hidden" name="controller" value="events" />
    <input type="hidden" name="task" value="saveCategory" />
    <input type="hidden" name="id" value="<?php echo $category->id; ?>" />
    <input type="hidden" name="cid" value="" />
    <?php echo JHTML::_('form.token');?>
</form>
