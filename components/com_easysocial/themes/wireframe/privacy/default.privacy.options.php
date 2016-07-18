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

// $identifier = 'data-privacy-item';
// if( $isHtml )
// 	$identifier = 'data-privacy-item-html';

$defaultKey = '';

$defaultCustom = '';
if( count( $item->custom ) > 0 )
{
	$cIds = array();
	foreach( $item->custom as $custom )
	{
		$cIds[] = $custom->user_id;
	}

	$defaultCustom = implode( ',', $cIds );
}
?>
<div class="es-privacy"
	 data-es-provide="tooltip"
	 data-placement="top"
	 data-original-title="<?php echo $tooltipText; ?>"
	 data-privacy-mode="<?php echo ($isHtml) ? 'html' : 'ajax'; ?>"
     <?php echo ( $item->editable ) ? ' data-es-privacy-container' : ''?>
>

    <?php if ($item->editable) { ?>
		<a class="es-privacy-toggle btn btn-es btn-notext" href="javascript:void(0);" data-privacy-toggle>
			<i class="<?php echo $icon; ?>" data-privacy-icon ></i>
			<span class="caret"></span>
		</a>
	<?php } else { ?>
		<span class="es-privacy-toggle-label">
			<i class="<?php echo $icon; ?>" data-privacy-icon ></i>
		</span>
	<?php } ?>

	<?php if( $item->editable ) { ?>

	<ul class="es-privacy-menu dropdown-menu" data-privacy-menu>
		<?php foreach( $item->option as $opKey => $opVal ) {

			if( $opVal ) { $defaultKey = $opKey; }

			if( $this->config->get( 'general.site.lockdown.enabled' ) && $opKey == SOCIAL_PRIVACY_0 )
			{
				continue;
			}

		?>
			<li data-privacy-item
				data-value="<?php echo $opKey; ?>"
				data-utype="<?php echo $item->type; ?>"
				data-uid="<?php echo $item->uid; ?>"
				data-pid="<?php echo $item->id; ?>"
				<?php echo ($item->override) ? ' data-userid="'.$item->user_id.'"' : ''; ?>
				data-pitemid="<?php echo $item->pid; ?>"
				data-streamid="<?php echo $streamid; ?>"
				class="privacyItem <?php echo ( $opVal ) ? 'active':''; ?>"
				data-privacyicon="<?php echo FD::privacy()->getIconClass( $opKey ); ?>"
			>
				<a href="javascript:void(0);">
					<i class="<?php echo FD::privacy()->getIconClass( $opKey ); ?>"></i>
					<?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_OPTION_' . strtoupper( $opKey ) ); ?>
				</a>
			</li>

		<?php } ?>
	</ul>

	<div class="es-privacy-menu es-privacy-custom-form dropdown-menu dropdown-arrow-topright" data-privacy-custom-form>
		<div class="pb-5"><?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_NAME'); ?></div>
		<div>
			<div class="textboxlist" data-textfield >

				<?php
					if( count( $item->custom ) > 0 )
					{
						foreach( $item->custom as $friend )
						{
							$friend = FD::user( $friend->user_id );
				?>
					<div class="textboxlist-item" data-id="<?php echo $friend->id; ?>" data-title="<?php echo $friend->getName(); ?>" data-textboxlist-item>
						<span class="textboxlist-itemContent" data-textboxlist-itemContent><?php echo $friend->getName(); ?><input type="hidden" name="items" value="<?php echo $friend->id; ?>" /></span>
						<a class="textboxlist-itemRemoveButton" href="javascript: void(0);" data-textboxlist-itemRemoveButton></a>
					</div>
				<?php
						}

					}
				?>

				<input type="text" class="textboxlist-textField" data-textboxlist-textField placeholder="<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_ENTER_NAME'); ?>" autocomplete="off" />
			</div>
		</div>
		<div class="pt-10 pb-5">
			<button data-cancel-button type="button" class="btn btn-es"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
			<button data-save-button type="button" class="btn btn-es-primary"><?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON'); ?></button>
		</div>
	</div>


	<?php } ?>

	<input type="hidden" name="privacy" value="<?php echo $defaultKey; ?>" data-privacy-hidden />
	<input type="hidden" name="privacyCustom" value="<?php echo $defaultCustom; ?>" data-privacy-custom-hidden />
</div>
