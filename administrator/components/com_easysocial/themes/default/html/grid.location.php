<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-locations <?php echo $classname; ?>" data-language="<?php echo FD::config()->get('general.location.language'); ?>">
	<div class="es-location-textbox">
		<input type="text"
			   value="<?php echo $location->address; ?>"
			   placeholder="<?php echo JText::_('COM_EASYSOCIAL_WHERE_ARE_YOU_NOW'); ?>"
			   autocomplete="off" data-location-textField disabled />
	</div>
	<div class="es-location-buttons">
		<button type="button" class="es-location-button btn btn-es" data-detect-location-button><?php echo JText::_('COM_EASYSOCIAL_DETECT_MY_LOCATION'); ?></button>
	</div>
	<div class="es-location-suggestions" data-location-suggestions></div>
	<div class="es-location-map" data-location-map>
	    <div>
	        <img class="es-location-map-image" data-location-map-image />
	    </div>
	</div>
	<input type="hidden" name="lat" data-location-lat value="<?php echo $location->latitude; ?>" />
	<input type="hidden" name="lng" data-location-lng value="<?php echo $location->longitude; ?>" />
</div>
