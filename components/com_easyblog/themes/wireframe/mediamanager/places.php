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
<div class="eb-mm-places" data-eb-mm-places>
    <div class="eb-composer-toolbar">
    <div>

        <div class="eb-composer-toolbar-set row-table" data-name="media-places">

            <div class="col-cell cell-tight toolbar-left">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item col-cell eb-mm-toolbar-title">
                        <strong>
                            <i class="fa fa-camera"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_MEDIA_MANAGER'); ?></span>
                        </strong>
                    </div>
                </div>
            </div>

            <div class="col-cell toolbar-center">&nbsp;</div>

            <div class="col-cell cell-tight toolbar-right">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item is-button hide-label col-cell eb-mm-close-button" data-eb-mm-close-button>
                        <i class="fa fa-times-circle"></i>
                        <span><?php echo JText::_('Close');?></span>
                    </div>
                </div>
            </div>

        </div>

    </div>
    </div>


    <div class="eb-composer-viewport" data-scrolly="y">
        <div class="eb-composer-viewport-content" data-scrolly-viewport>
            <div class="eb-mm-place-list">
                <?php foreach ($places as $place) { ?>
                <div class="eb-mm-place" data-eb-mm-place data-id="<?php echo $place->id; ?>" data-key="<?php echo $place->key; ?>">
                    <i class="<?php echo $place->icon; ?>"></i>
                    <b class="fa fa-chevron-right"></b>
                    <label><?php echo $place->title; ?></label>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>