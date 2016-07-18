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


$buttonLabel = 'COM_EASYBLOG_COMPOSER_SAVE_TO_DASHBOARD';
$buttonURL = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries');

if ($post->isPending()) {
    $buttonLabel = 'COM_EASYBLOG_COMPOSER_SAVE_TO_DASHBOARD_PENDING';
    $buttonURL = EBR::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');
}

if ($this->app->isAdmin()) {
    $buttonLabel = 'COM_EASYBLOG_COMPOSER_SAVE_TO_BACKEND_POSTS';
    $buttonURL = EBR::_('index.php?option=com_easyblog&view=blogs');
    if ($post->isPending()) {
        $buttonLabel = 'COM_EASYBLOG_COMPOSER_SAVE_TO_BACKEND_POSTS_PENDING';
        $buttonURL = EBR::_('index.php?option=com_easyblog&view=blogs&layout=pending');
    }
}

?>
<div class="eb-composer-saving text-center">
    <div class="col-cell">
        <h2 data-eb-composer-saving-message><?php echo JText::_('COM_EASYBLOG_COMPOSER_SAVING_PLEASE_WAIT'); ?></h2>

        <h2 data-eb-composer-saving-info-message></h2>

        <div class="hide" data-eb-composer-saving-redirect-message>
            <?php echo JText::_('COM_EASYBLOG_COMPOSER_SAVE_REDIRECTING_DASHBOARD_WAIT'); ?>
        </div>

        <div class="progress mt-20 mb-20" data-eb-composer-saving-progress-bar>
            <div class="progress-bar progress-bar-info active" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100" style="width: 5%"></div>
        </div>

        <div class="pt-20">
            <a href="javascript:void(0);" class="btn btn-lg btn-default mr-5" data-eb-composer-saving-close-button><?php echo JText::_('COM_EASYBLOG_CLOSE'); ?></a>
            <a href="<?php echo $buttonURL; ?>" class="btn btn-lg btn-primary" data-eb-composer-saving-entry-button>
                <?php echo JText::_($buttonLabel);?>
            </a>
        </div>
    </div>
</div>
