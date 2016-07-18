<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm" class="groupsForm" method="post" enctype="multipart/form-data" data-videos-category-form>
    <div class="wrapper accordion">
        <div class="tab-box tab-box-alt">
            <div class="tabbable">

                <?php echo $this->loadTemplate('admin/videocategories/forms/tabs', array('isNew' => $category->id == 0, 'activeTab' => $activeTab)); ?>

                <div class="tab-content">
                    <div id="settings" class="tab-pane<?php echo $activeTab == 'settings' ? ' active in' : '';?>">
                        <?php echo $this->loadTemplate('admin/videocategories/forms/settings', array('category' => $category, 'createAccess' => $createAccess)); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="activeTab" data-tab-active />
    <input type="hidden" name="option" value="com_easysocial" />
    <input type="hidden" name="controller" value="videocategories" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="id" value="<?php echo $category->id; ?>" />
    <input type="hidden" name="cid" value="" />
    <?php echo JHTML::_('form.token');?>
</form>
