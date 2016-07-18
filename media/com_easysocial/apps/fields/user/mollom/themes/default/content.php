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
<div data-field-mollom>
	<?php if( !empty( $captcha ) ) { ?>
	<div class="mollom-wrap">
		<?php echo $captcha->getHTML();?>

		<div class="mt-5">
			<input type="text" name="mollom_<?php echo $inputName;?>" value="" class="full-width" data-check-required />
		</div>
	</div>
	<?php } else {
		echo JText::_( 'PLG_FIELDS_MOLLOM_ERROR_RETRIEVING_CAPTCHA_HTML' );
	} ?>
</div>
