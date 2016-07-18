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
<div class="es-story-link-form"
     data-story-link-form>
	<div class="es-story-link-textbox">
		<input type="text"
		       class="es-story-link-input"
		       data-story-link-input
		       placeholder="<?php echo JText::_('COM_EASYSOCIAL_STORY_LINK_PLACEHOLDER'); ?>"/>
	</div>
	<div class="es-story-link-buttons">
		<i class="loading-indicator fd-small"></i>
		<button type="button"
				class="btn btn-es"
		        data-story-link-attach-button
		        ><?php echo JText::_('COM_EASYSOCIAL_ADD_LINK'); ?></button>
	</div>
</div>
<div class="es-story-link-content"
     data-story-link-content></div>
