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
<div class="es-stream-login-box">
    <div>
        <?php echo JText::sprintf('COM_EASYSOCIAL_GUEST_STREAM_LOGIN_DESCRIPTION', FRoute::registration() ); ?>
    </div>
    <div class="mt-15">
		<a class="btn btn-es btn-sm" href="<?php echo FRoute::login( array() , false ); ?>"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_BUTTON' ); ?></a>
		<?php echo JText::_('COM_EASYSOCIAL_GUEST_STREAM_OR'); ?>
		<a class="btn btn-es btn-es-primary btn-sm" href="<?php echo FRoute::registration(); ?>"><?php echo JText::_('COM_EASYSOCIAL_REGISTER_NOW_BUTTON'); ?></a>
	</div>
</div>
