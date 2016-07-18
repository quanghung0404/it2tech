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
<div class="eb-composer-panels" data-eb-composer-panels>
    <div>
        <div class="eb-composer-panel-tabs">
            <div class="eb-composer-panel-tab mobile-show col-cell" style="width: 1%;" data-eb-composer-panel-show-drawer>
                <div style="text-align: right; padding: 0 15px;">
                    <i class="fa fa-chevron-left" style="font-size: 18px;"></i>
                </div>
            </div>

            <div class="eb-composer-panel-tab active" data-eb-composer-panel-tab data-id="post-options">
                <div>
                    <i class="fa fa-pencil"></i>
                    <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_PANEL_POST');?></span>
                </div>
            </div>

            <div class="eb-composer-panel-tab" data-eb-composer-panel-tab data-id="blocks">
                <div>
                    <i class="fa fa-cube"></i>
                    <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS');?></span>
                </div>
            </div>

            <?php if ($this->config->get('layout_composer_fields')) { ?>
            <div class="eb-composer-panel-tab<?php echo !$displayFieldsTab ? ' hide' : '';?>" data-eb-composer-panel-tab data-id="fields">
                <div>
                    <i class="fa fa-th-large"></i>
                    <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOM_FIELDS');?></span>
                </div>
            </div>
            <?php } ?>

            <?php if ($this->config->get('layout_dashboardseo')) { ?>
            <div class="eb-composer-panel-tab" data-eb-composer-panel-tab data-id="seo">
                <div>
                    <i class="fa fa-globe"></i>
                    <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_SEO');?></span>
                </div>
            </div>
            <?php } ?>

            <?php if ($this->config->get('layout_composer_history')) { ?>
            <div class="eb-composer-panel-tab" data-eb-composer-panel-tab data-id="revisions">
                <div>
                    <i class="fa fa-code-fork"></i>
                    <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_HISTORY');?></span>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="eb-composer-panel-group">
            <?php echo $this->output('site/composer/panel/post'); ?>
            <?php echo $this->output('site/composer/panel/fields'); ?>
            <?php echo $this->output('site/composer/panel/blocks'); ?>


            <?php if ($this->config->get('layout_dashboardseo')) { ?>
                <?php echo $this->output('site/composer/panel/seo'); ?>
            <?php } ?>

            <?php if ($this->config->get('layout_composer_history')) { ?>
                <?php echo $this->output('site/composer/panel/revisions'); ?>
            <?php } ?>
        </div>
    </div>
</div>
