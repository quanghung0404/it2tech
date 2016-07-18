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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SENDYAPP');?></b>
                <div class="panel-info">
                    <?php echo JText::_('COM_EASYBLOG_SENDY_INFO'); ?><br><br>
                    <a class="btn btn-primary btn-sm" target="_blank" href="http://sendy.co/?ref=WDAF8"><?php echo JText::_('COM_EASYBLOG_GET_SENDY_APP');?></a>
                </div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'subscription_sendy', 'COM_EASYBLOG_SENDY_ENABLE'); ?>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SENDY_URL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SENDY_URL'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SENDY_URL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" name="subscription_sendy_url" class="form-control " value="<?php echo $this->config->get('subscription_sendy_url');?>" size="5" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SENDY_LISTID'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SENDY_LISTID'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SENDY_LISTID_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" name="subscription_sendy_listid" class="form-control " value="<?php echo $this->config->get('subscription_sendy_listid');?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>