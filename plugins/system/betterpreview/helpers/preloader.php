<?php
/**
 * Preload Form
 *
 * @package         Better Preview
 * @version         4.1.2PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$fid = JFactory::getApplication()->input->get('fid');
?>
	<body style="text-align:center;padding-top:5%;font-size:2em;">
	<?php echo JText::_('BP_LOADING'); ?>
	<script type="text/javascript">
		if (parent == undefined || parent.<?php echo $fid; ?> == undefined) {
			window.location = "../index.php";
		}

		var data = parent.<?php echo $fid; ?>();
		document.write(
			'<form action="' + data.url + '" name="preview_form" method="post" style="display:none;">' +
			'<input type="hidden" name="bp_preview" value="1" />' +
			'<input type="hidden" name="session_id" value="' + data.session_id + '" />' +
			'<input type="hidden" name="user" value="' + data.user + '" />'
		);

		previewdata = [];
		els = data.form.elements;
		for (i = 0; i < els.length; i++) {
			el = els[i];
			if (!el || el.name == undefined || !el.name || (data.isjform && el.name.substr(0, 6) != "jform[")) {
				continue;
			}
			if (el.type == "radio" || el.type == "checkbox") {
				if (!el.seleceted && !el.checked) {
					continue;
				}
			}

			key = el.name;
			if (data.isjform) {
				key = key.replace(/^jform\[(.*)\]/, "$1");
			}
			key = key.replace(/(\[.*)\]/, "]$1");

			if (el.type == "select-multiple") {
				var vals = [];
				for (j = 0; j < el.options.length; j++) {
					if (el.options[j].selected) {
						vals[vals.length] = el.options[j].value;
					}
				}
				previewdata[key] = vals.join(',');
			}

			previewdata[key] = el.value;
		}
		for (key in data.overrides) {
			previewdata[key] = data.overrides[key];
		}
		for (key in previewdata) {
			document.write('<textarea name="previewdata[' + key + ']">' + encodeURIComponent(previewdata[key]) + '</textarea>');
		}
		document.write('</form>');
		document.forms['preview_form'].submit();
	</script>
	</body>
<?php
die;
