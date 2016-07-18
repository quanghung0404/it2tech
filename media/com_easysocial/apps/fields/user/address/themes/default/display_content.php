<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($params->get('use_maps')) { ?>
<div class="es-locations" data-location-base>
    <div class="es-location-help">
        <a href="http://maps.google.com/?q=<?php echo $value->toString(); ?>" target="_blank"><?php echo JText::_('FIELDS_USER_ADDRESS_VIEW_IN_GOOGLE_MAPS'); ?></a>
    </div>
    <div class="es-location-map" data-location-map data-latitude="<?php echo FD::string()->escape($value->latitude); ?>" data-longitude="<?php echo FD::string()->escape($value->longitude); ?>">

        <div>
            <img class="es-location-map-image" data-location-map-image />
            <div class="es-location-map-actions">
                <button class="btn btn-es es-location-detect-button" type="button" data-location-detect><i class="fa fa-flash"></i> <?php echo JText::_('COM_EASYSOCIAL_DETECT_MY_LOCATION', true); ?></button>
            </div>
        </div>
    </div>

    <div>
        <?php echo (isset($advancedsearchlink) && $advancedsearchlink) ? '<a href="' . $advancedsearchlink . '">' : ''; ?>
        <?php echo FD::string()->escape($value->toString()); ?>
        <?php echo (isset($advancedsearchlink) && $advancedsearchlink) ? '</a>' : ''; ?>
    </div>
</div>
<?php } else { ?>
    <?php echo (isset($advancedsearchlink) && $advancedsearchlink) ? '<a href="' . $advancedsearchlink . '">' : ''; ?>
    <?php if (!empty($value->address1)) { ?>
    <div><?php echo FD::string()->escape($value->address1); ?></div>
    <?php } ?>

    <?php if (!empty($value->address2)) { ?>
    <div><?php echo FD::string()->escape($value->address2); ?></div>
    <?php } ?>

    <?php if (!empty($value->city)) { ?>
    <div><?php echo FD::string()->escape($value->city); ?></div>
    <?php } ?>

    <?php if (!empty($value->state)) { ?>
    <div><?php echo FD::string()->escape($value->state); ?></div>
    <?php } ?>

    <?php if (!empty($value->zip) || !empty($value->country)) { ?>
    <div><?php if (!empty($value->zip)) { echo FD::string()->escape($value->zip); } ?><?php if (!empty($value->zip) && !empty($value->country)) { echo ' '; } ?><?php if (!empty($value->country)) { echo FD::string()->escape($value->country); } ?>
    <?php } ?>

    <?php echo (isset($advancedsearchlink) && $advancedsearchlink) ? '</a>' : ''; ?>
<?php } ?>
