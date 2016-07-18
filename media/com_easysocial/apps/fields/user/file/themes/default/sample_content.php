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
<div data-field-file>
	<div data-field-file-list>
		<input type="file" class="input" />
	</div>

	<div>
		<a href="javascript:void(0);" class="btn btn-small" <?php if( $limit == 1 ) { ?>style="display: none;"<?php } ?> data-field-file-add><?php echo JText::_( 'PLG_FIELDS_FILE_ADD_ANOTHER_FILE' ); ?></a>
	</div>

	<div class="small mb-0" data-field-file-size-text <?php if( !$params->get( 'show_size_limit' ) ) { ?>style="display: none;"<?php } ?>>
		<?php echo JText::_( 'PLG_FIELDS_FILE_MAXIMUM_FILE_SIZE' ); ?> <span data-field-file-size><?php echo $params->get( 'size_limit' ); ?></span> <strong><?php echo JText::_( 'MB' ); ?></strong>
	</div>
</div>
