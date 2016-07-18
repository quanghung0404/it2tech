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
<div class="eb-composer-field eb-font" data-type="font">

    <div class="eb-tabs pill-style" data-eb-tabs-mode="toggle">
        <div class="eb-tabs-menu eb-pill">
            <div class="eb-tabs-menu-item eb-pill-item eb-font-color-menu" data-id="font-color" data-eb-font-color-menu>
                <span style="background-color: #000;" data-eb-font-color-caption></span>
            </div>
            <div class="eb-tabs-menu-item eb-pill-item eb-font-family-menu" data-id="font-family" data-eb-font-family-menu>
                <div class="row-table layout-fixed">
                    <div class="col-cell">
                        <b><?php echo JText::_('COM_EASYBLOG_COMPOSER_FONT');?></b>
                    </div>
                    <div class="col-cell eb-tabs-menu-item-value">
                        <span data-eb-font-family-caption><?php echo JText::_('COM_EASYBLOG_COMPOSER_FONT_DEFAULT');?></span>
                        <i class="fa fa-caret-down"></i>
                    </div>
                </div>
            </div>
            <div class="eb-tabs-menu-item eb-pill-item eb-font-size-menu" data-id="font-size" data-eb-font-size-menu>
                <div class="row-table layout-fixed">
                    <div class="col-cell">
                        <b><?php echo JText::_('COM_EASYBLOG_COMPOSER_FONT_SIZE');?></b>
                    </div>
                    <div class="col-cell eb-tabs-menu-item-value">
                        <span data-eb-font-size-caption></span>
                        <i class="fa fa-caret-down"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="eb-tabs-content">
            <div class="eb-tabs-content-item eb-font-color-content" data-id="font-color" data-eb-font-color-content>
                <?php echo $this->output('site/composer/fields/colorpicker', array(
                    'attributes' => 'data-eb-font-color-picker'
                )); ?>
            </div>
            <div class="eb-tabs-content-item eb-font-family-content" data-id="font-family" data-eb-font-family-content>
                <div class="eb-composer-field eb-list" data-type="list">
                    <div class="eb-list-item-group">
                        <div class="eb-list-item active" data-eb-font-family-option data-value=""><?php echo JText::_('COM_EASYBLOG_COMPOSER_FONT_DEFAULT');?></div>
                        <div class="eb-list-item" data-eb-font-family-option style="font-family: Arial, sans-serif;" data-value="Arial">Arial</div>
                        <div class="eb-list-item" data-eb-font-family-option style="font-family: Comic Sans MS, cursive;" data-value="Comic Sans MS">Comic Sans MS</div>
                        <div class="eb-list-item" data-eb-font-family-option style="font-family: Courier, monospace;" data-value="Courier">Courier</div>
                        <div class="eb-list-item" data-eb-font-family-option style="font-family: Georgia, serif;" data-value="Georgia">Georgia</div>
                        <div class="eb-list-item" data-eb-font-family-option style="font-family: Tahoma, sans-serif;" data-value="Tahoma">Tahoma</div>
                        <div class="eb-list-item" data-eb-font-family-option style="font-family: Trebuchet MS, sans-serif;" data-value="Trebuchet MS">Trebuchet MS</div>
                        <div class="eb-list-item" data-eb-font-family-option style="font-family: Verdana, sans-serif;" data-value="Verdana">Verdana</div>
                    </div>
                </div>
            </div>
            <div class="eb-tabs-content-item eb-font-size-content" data-id="font-size" data-eb-font-size-content>
                <?php
                    echo $this->output('site/composer/fields/numslider', array(
                        'name' => 'fontsize'
                    ));
                ?>
            </div>
        </div>
    </div>

</div>