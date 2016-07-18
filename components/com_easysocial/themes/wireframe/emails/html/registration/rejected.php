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
<tr>
	<td style="text-align: center;padding: 40px 10px 0;">
		<div style="margin-bottom:15px;">
			<div style="font-family:Arial;font-size:32px;font-weight:normal;color:#333;display:block; margin: 4px 0">
				<?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REGISTRATION_APPLICATION_REJECTED_HEADING' ); ?>
			</div>
			<div style="font-size:12px; color: #798796;font-weight:normal">
				<?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REGISTRATION_APPLICATION_REJECTED_SUBHEADING' ); ?>
			</div>
		</div>
	</td>
</tr>

<tr>
	<td style="font-size:12px;color:#888;padding: 0 30px;">
		<div style="margin:30px auto;text-align:center;display:block">
			<img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
		</div>

		<p>
			<?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_HELLO' ); ?> <?php echo $name; ?>,<br />
		</p>

		<p>
			<?php echo JText::sprintf( 'COM_EASYSOCIAL_EMAILS_REGISTRATION_REJECTED' , $site ); ?>
		</p>

		<?php if( isset( $reason ) && !empty( $reason ) ){ ?>
		<p style="margin-top: 20px;">
			<?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REGISTRATION_REJECTED_REASON' ); ?>
		</p>
		<blockquote style="font: 14px/22px normal helvetica, sans-serif;margin-top: 10px;margin-bottom: 10px;margin-left: 0;padding-left: 15px;border-left: 3px solid #ccc;"><?php echo $reason; ?></blockquote>
		<?php } ?>
	</td>
</tr>
