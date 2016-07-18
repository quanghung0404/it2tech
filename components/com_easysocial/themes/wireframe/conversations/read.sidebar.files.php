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
<hr class="es-hr mt-20 mb-10" />
<div class="conversation-files-title">
	<i class="icon-es-attachment mr-5"></i> 
	<b><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILES' );?></b>
</div>

<?php if( $files ){ ?>
	<div class="conversation-files-list mt-15">
		<?php foreach( $files as $attachment ){ ?>
			<div class="fd-cf mt-5">
				<a href="<?php echo FRoute::conversations( array( 'layout' => 'download' , 'fileid' => $attachment->id ) );?>"
					data-es-provide="tooltip"
					data-original-title="<b><?php echo $this->html( 'string.escape' , $attachment->name );?></b><br /><br /><?php echo JText::sprintf( 'COM_EASYSOCIAL_CONVERSATIONS_FILE_UPLOADED_ON' , $attachment->getUploadedDate()->toLapsed() );?>"
					data-html="true"
					data-placement="bottom"
				>

					<i class="icon-es-<?php echo $attachment->getIconClass();?> mr-5"></i>

					<?php if( JString::strlen( $attachment->name ) > 15 ){ ?>
						<?php echo JString::substr( $attachment->name , 0 , 15 ); ?><?php echo JText::_( 'COM_EASYSOCIAL_ELLIPSES' ); ?>
					<?php } else { ?>
						<?php echo $attachment->name;?>
					<?php } ?>
				</a>
				<div class="pull-right mr-10">
					<span ><?php echo $attachment->getSize();?> <?php echo JText::_( 'COM_EASYSOCIAL_UNIT_KILOBYTES' );?></span>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } else { ?>
	<div class="conversation-files-empty fd-small mt-10">
		<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_NO_FILES_FOUND' ); ?>
	</div>
<?php } ?>
