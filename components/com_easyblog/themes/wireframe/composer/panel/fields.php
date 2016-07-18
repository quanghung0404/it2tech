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
<div class="eb-composer-panel" data-eb-composer-panel data-id="fields">

    <div class="eb-composer-panel-content" data-scrolly="y">
        <div data-scrolly-viewport>
            <form name="fields" class="eb-composer-fields" method="post" data-eb-composer-form></form>
            <div class="eb-hint style-gray layout-overlay eb-composer-fields-empty" data-eb-composer-fields-empty>
                <div>
                    <i class="eb-hint-icon fa fa-th-large"></i>
                    <span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_PLEASE_SELECT_CATEGORY_FIRST');?></span>
                </div>
            </div>
        </div>
    </div>

</div>
