<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm" action="index.php?option=com_easyblog" method="post" enctype="multipart/form-data">
    <div class="app-tabs">
        <ul class="app-tabs-list list-unstyled">
            <li class="tabItem active">
                <a data-bp-toggle="tab" href="#general" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_DETAILS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#members" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_MEMBERS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#groups" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_GROUPS');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
    	<div id="general" class="tab-pane active in">
    		<?php echo $this->output('admin/teamblogs/form.general'); ?>
    	</div>

    	<div id="members" class="tab-pane">
    		<?php echo $this->output('admin/teamblogs/form.members'); ?>
    	</div>

    	<div id="groups" class="tab-pane">
    		<?php echo $this->output('admin/teamblogs/form.groups'); ?>
    	</div>
    </div>

    <?php echo $this->html('form.action'); ?>
	<input type="hidden" name="id" value="<?php echo $team->id;?>" />
	<input type="hidden" name="deletemembers" id="deletemembers" value="" />
	<input type="hidden" name="deletegroups" id="deletegroups" value="" />
</form>
