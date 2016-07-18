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
<?php if( $attachments && $this->config->get( 'conversations.attachments.enabled' ) ){ ?>
<div class="conversation-attachments">

	<div data-conversation-attachment-wrapper>
		<h6><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ATTACHMENTS' ); ?>:</h6>

		<ul class="fd-reset-list">
			<?php foreach( $attachments as $attachment ){ ?>
				<li class="attach-item uploadItem<?php echo $attachment->hasPreview() ? ' preview' : ''; ?>" data-conversation-attachment>
					<div class="fd-cf">
					    <div class="media">
					        <div class="pull-left">
					            <?php if( $attachment->hasPreview() ){ ?>
					            <div class="attachment-preview">
					            	<a href="<?php echo $attachment->getPreviewURI();?>" target="_blank"><img src="<?php echo $attachment->getPreviewURI();?>" /></a>
					            </div>
					            <?php } ?>
					        </div>
					        <div class="media-body">
					            <div class="">
					                <?php echo $attachment->name; ?>
					                <span class="attach-size muted fd-small">- <?php echo $attachment->getSize( 'kb' );?> <?php echo JText::_( 'COM_EASYSOCIAL_UNIT_KILOBYTES' );?></span>
					            </div>
					            <div class="btn-group btn-group-xs" role="group" aria-label="...">
					                <a class="btn btn-default" href="<?php echo $attachment->getPermalink();?>"><i class="fa fa-download"></i></a>
					                <?php if( $attachment->isOwner( $this->my->id ) ){ ?>
					                	<a href="javascript:void(0);" class="btn btn-default delete-attachment" data-attachment-delete data-id="<?php echo $attachment->id;?>"><i class="fa fa-remove"></i></a>
					                <?php } ?>
					            </div>
					            
					        </div>
					    </div>
					    
					    
					</div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php } ?>
