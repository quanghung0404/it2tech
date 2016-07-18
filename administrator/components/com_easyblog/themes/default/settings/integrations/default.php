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
<form method="post" action="<?php echo JRoute::_('index.php');?>" id="adminForm">
    <div class="app-tabs">
        <ul class="app-tabs-list list-unstyled">
            <li class="tabItem active">
                <a data-bp-toggle="tab" href="#easysocial" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_EASYSOCIAL');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#easydiscuss" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_EASYDISCUSS');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#jfbconnect" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_JFBCONNECT');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#aup" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_AUP');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#jomsocial" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_JOMSOCIAL');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#phocapdf" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_PHOCAPDF');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#adsense" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_ADSENSE');?>
                </a>
            </li>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#pingomatic" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_PINGOMATIC');?>
                </a>
            </li>

        <li class="tabItem">
            <a data-bp-toggle="tab" href="#mailchimp" data-form-tabs>
                <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_MAILCHIMP');?>
            </a>
        </li>

        <li class="tabItem">
            <a data-bp-toggle="tab" href="#sendy" data-form-tabs>
                <?php echo JText::_('COM_EASYBLOG_SETTINGS_INTEGRATIONS_SUBTAB_SENDY');?>
            </a>
        </li>
        </ul>
    </div>

    <div class="tab-content">
        <div id="easysocial" class="tab-pane active in">
            <?php echo $this->output('admin/settings/integrations/easysocial'); ?>
        </div>

        <div id="easydiscuss" class="tab-pane">
            <?php echo $this->output('admin/settings/integrations/easydiscuss'); ?>
        </div>

        <div id="jfbconnect" class="tab-pane">
            <?php echo $this->output('admin/settings/integrations/jfbconnect'); ?>
        </div>

        <div id="jomsocial" class="tab-pane">
            <?php echo $this->output('admin/settings/integrations/jomsocial'); ?>
        </div>

        <div id="aup" class="tab-pane">
            <?php echo $this->output('admin/settings/integrations/aup'); ?>
        </div>

        <div id="phocapdf" class="tab-pane">
            <?php echo $this->output('admin/settings/integrations/phocapdf'); ?>
        </div>

        <div id="adsense" class="tab-pane">
            <?php echo $this->output('admin/settings/integrations/adsense'); ?>
        </div>

        <div id="pingomatic" class="tab-pane">
            <?php echo $this->output('admin/settings/integrations/pingomatic'); ?>
        </div>

        <div id="mailchimp" class="tab-pane">
            <?php echo $this->output('admin/settings/integrations/mailchimp'); ?>
        </div>

        <div id="sendy" class="tab-pane">
            <?php echo $this->output('admin/settings/integrations/sendy'); ?>
        </div>
    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="page" value="integrations" />
    <input type="hidden" name="activeTab" value="<?php echo $activeTab;?>" data-settings-active />    
</form>
