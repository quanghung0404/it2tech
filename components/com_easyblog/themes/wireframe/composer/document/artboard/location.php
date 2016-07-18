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
<div class="eb-composer-art eb-composer-location <?php echo $post->hasLocation() ? ' has-location' : '';?>"
    data-eb-composer-location
    data-eb-composer-art
    data-id="location"
>
    <div class="eb-composer-art-workarea">
        <div class="eb-composer-art-placeholder eb-composer-location-form dropdown_ open" data-eb-composer-location-form>
            <i class="fa fa-map-marker"></i>
            <i class="fa fa-compass fa-spin"></i>

            <div class="row-table">
                <div class="col-cell">
                    <input type="text" class="form-control input-lg eb-composer-location-textfield"
                        tabindex="-1"
                        data-eb-composer-location-textfield placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ENTER_PLACE_TOWN_OR_CITY', true);?>"
                        data-placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_ENTER_PLACE_TOWN_OR_CITY', true);?>"
                        data-placeholder-searching="<?php echo JText::_('COM_EASYBLOG_COMPOSER_DETECTING_CURRENT_LOCATION', true);?>"
                    />
                </div>
                <div class="col-cell cell-tight pl-10">
                    <a class="btn btn-lg btn-primary" data-eb-composer-location-detect-button>
                        <i class="fa fa-bolt"></i>
                        <?php echo JText::_('COM_EASYBLOG_COMPOSER_LOCATION_DETECT'); ?>
                    </a>
                </div>
            </div>

            <div class="eb-composer-location-message" data-eb-composer-location-form-message></div>

            <div class="eb-composer-location-autocomplete" data-eb-composer-location-autocomplete>
                <s><s></s></s>
                <ul class="dropdown-menu eb-composer-location-places" data-eb-composer-location-places></ul>
            </div>
        </div>



        <div class="eb-composer-art-remove-button" data-eb-composer-location-remove-button>
            <i class="fa fa-close"></i><span>&nbsp;<?php echo JText::_('COM_EASYBLOG_COMPOSER_REMOVE');?></span>
        </div>

        <i class="fa fa-compass fa-spin eb-composer-location-loading"></i>
    </div>

    <div class="eb-composer-location-map <?php echo $post->hasLocation() ? ' is-ready' : '';?>" data-eb-composer-location-map
        <?php echo $post->hasLocation() ? ' style="background-image: url(\'' . $post->getLocationImage() . '\');"' : '';?>></div>

</div>
