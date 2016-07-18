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
<p><?php echo JText::sprintf( 'COM_EASYSOCIAL_VERSION_OUTDATED_POPBOX' , $local , $online ); ?></p>
<a href="index.php?option=com_easysocial&update=true" class="btn btn-es-success btn-sm"><?php echo JText::_( 'COM_EASYSOCIAL_GET_UPDATES_BUTTON' ); ?> &rarr;</a>
