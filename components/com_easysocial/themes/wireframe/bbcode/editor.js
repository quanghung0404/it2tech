EasySocial.require()
.script(
	"site/explorer",
	"site/explorer/popup"
)
.library(
	"markitup",
	"expanding"
)
.done(function($){

	var settings = {

		onTab: {
			keepDefault: false,
			replaceWith: '    '
		},

		previewParserVar: 'data',

		markupSet: [
			{
				name: "<?php echo JText::_('COM_EASYSOCIAL_BBCODE_BOLD'); ?>",
				key: 'B',
				openWith: '[b]',
				closeWith: '[/b]',
				className: 'markitup-bold'
			},
			{
				name: "<?php echo JText::_('COM_EASYSOCIAL_BBCODE_ITALIC'); ?>",
				key: 'I',
				openWith: '[i]',
				closeWith: '[/i]',
				className: 'markitup-italic'
			},
			{
				name: "<?php echo JText::_('COM_EASYSOCIAL_BBCODE_UNDERLINE'); ?>",
				key: 'U',
				openWith: '[u]',
				closeWith: '[/u]',
				className: 'markitup-underline'
			},
			{
				name: "<?php echo JText::_('COM_EASYSOCIAL_BBCODE_CODE'); ?>",
				openWith: '[code type="markup"]',
				closeWith: '[/code]',
				className: 'markitup-code'
			}
			<?php if( $files ){ ?>
			,{
				className: 'markitup-files',
				name: "<?php echo JText::_('COM_EASYSOCIAL_BBCODE_INSERT_FILE');?>",
				replaceWith: function(h) {
					// Get the current position of the caret
					var caretPosition = h.caretPosition.toString();

					EasySocial.explorer
						.open({
							<?php if (isset($controllerName)) { ?>
							controllerName: '<?php echo $controllerName; ?>',
							<?php } ?>
							type: "<?php echo $type;?>",
							uid: <?php echo $uid; ?>,
							url: "site/controllers/explorer/hook"
						})
						.done(function(explorer, popup){

							explorer.element
								.off("fileUse.discussion")
								.on("fileUse.discussion", function(event, id, file, data){

									if (!data) return false;

									// Get the current textarea
									var textarea = $('[data-<?php echo $uniqueId;?>]'),
										text = '[file id="' + data.id + '"]' + data.name + '[/file]';

									// If the caret is at the first position just update the value.
									if (caretPosition==0) {
										textarea.val(text);
										popup.hide();
										return;
									}

									var contents = textarea.val();

									textarea.val( contents.substring( 0 , caretPosition ) + text + contents.substring(caretPosition, contents.length) );

									popup.hide();
								});
							});
				},
				beforeInsert: function(h ) {
				},
				afterInsert: function(h ) {
				},
			}
			<?php } ?>
		]
	};

	$('[data-<?php echo $uniqueId;?>]').markItUp(settings).expandingTextarea();
});
