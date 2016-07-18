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
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_SELECT_GROUPS');?></b>
                <div class="panel-info"><?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_GROUPS_DESC' ); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_SELECT_GROUPS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_SELECT_GROUPS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_SELECT_GROUPS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('tree.groups', 'groups', $groups); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>