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
<form id="adminForm" name="adminForm" class="adminForm" action="index.php" method="post">

	<!-- Load the necessary page here -->
	<?php echo $this->includeTemplate( 'admin/settings/forms/' . $page ); ?>

	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="controller" value="settings" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="page" value="<?php echo $page;?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
