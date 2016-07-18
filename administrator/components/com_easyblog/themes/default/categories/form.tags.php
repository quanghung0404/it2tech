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
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_DEFAULT_TAGS'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_DEFAULT_TAGS_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="catname" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_PARAM_ENTER_TAGS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_PARAM_ENTER_TAGS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_PARAM_ENTER_TAGS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <textarea name="params[tags]" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_PARAM_TAGS_PLACEHOLDER');?>"><?php echo $params->get('tags');?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>