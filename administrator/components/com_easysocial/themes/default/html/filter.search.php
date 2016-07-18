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
<div class="input-group input-group-sm">
	<input type="text" class="form-control input-sm" name="search" value="<?php echo $this->html( 'string.escape' , $value );?>" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_SEARCH' , true );?>" data-table-grid-search-input />
	<span class="input-group-btn">
		<button class="btn btn-sm btn-es" data-table-grid-search><i class="fa fa-search "></i></button>
		<button class="btn btn-sm btn-es" data-table-grid-search-reset><i class="fa fa-remove "></i></button>
	</span>
</div>
