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
<dialog>
	<width>450</width>
	<height>150</height>
	<selectors type="json">
	{
		"{saveButton}"    : "[data-save-button]",
		"{cancelButton}"  : "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{

		init: function() {

			EasySocial.require()
				.library("textboxlist")
				.done(function($){

					$('[data-textfield]').textboxlist(
						{
							unique: true,

							plugin: {
								autocomplete: {
									exclusive: true,
									minLength: 2,
									query: function( keyword ) {

										var users = getTaggedUsers();

										var ajax = EasySocial.ajax("site/views/privacy/getfriends", {
											q: keyword,
											exclude: users
										});
										return ajax;
									}
								}
							},


							getTaggedUsers: function()
							{
								var users = [];
								var items = $( "[data-textboxlist-item]" );
								if( items.length > 0 )
								{
									$.each( items, function( idx, element ) {
										users.push( $( element ).data('id') );
									});
								}

								return users;
							}

						}
					);

				});


		},

		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_TITLE'); ?></title>
	<content>
		<div>
			<form class="form-horizontal">
			<div class="control-group">
				<label class="control-label"for="inputPassword"><?php echo JText::_('COM_EASYSOCIAL_PRIVACY_CUSTOM_DIALOG_NAME'); ?></label>
				<div class="controls">
					<div class="textboxlist" data-textfield >

						<?php
							if( count( $friends ) )
							{
								foreach( $friends as $friend )
								{
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
			</div>
			</form>
		</div>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
		<button data-save-button type="button" class="btn btn-es-primary"><?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON'); ?></button>
	</buttons>
</dialog>
