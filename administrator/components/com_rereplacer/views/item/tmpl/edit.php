<?php
/**
 * @package         ReReplacer
 * @version         6.2.0PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

$user    = JFactory::getUser();
$contact = new stdClass;

$db = JFactory::getDbo();

$query = 'SHOW TABLES LIKE ' . $db->quote($db->getPrefix() . $this->config->contact_table);
$db->setQuery($query);

$has_contact_table = $db->loadResult();
if ($has_contact_table)
{
	$query = $db->getQuery(true)
		->select('c.misc')
		->from('#__' . $this->config->contact_table . ' as c')
		->where('c.user_id = ' . (int) $user->id);
	$db->setQuery($query);
	$contact = $db->loadObject();
}

JHtml::stylesheet('nnframework/style.min.css', false, true);
NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');
?>

<form action="<?php echo JRoute::_('index.php?option=com_rereplacer&id=' . ( int ) $this->item->id); ?>" method="post"
      name="adminForm" id="item-form" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span9 span-md-8">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS'); ?></a>
				</li>
				<li>
					<a href="#areas" data-toggle="tab"><?php echo JText::_('RR_SEARCH_AREAS'); ?></a>
				</li>
				<li>
					<a href="#assignments" data-toggle="tab"><?php echo JText::_('NN_PUBLISHING_ASSIGNMENTS'); ?></a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="details">
					<div class="row-fluid">
						<div class="span8 span-md-12 span-lg-7">
							<fieldset>
								<?php echo $this->render($this->item->form, 'search'); ?>
								<?php echo $this->render($this->item->form, 'replace'); ?>
								<?php echo $this->render($this->item->form, 'xml'); ?>
							</fieldset>
						</div>
						<div class="span4 span-md-12 span-lg-5">
							<fieldset>
								<?php echo $this->render($this->item->form, 'options'); ?>
							</fieldset>
						</div>
					</div>

					<legend><?php echo JText::_('RR_DYNAMIC_TAGS'); ?></legend>
					<?php
					$yes = '<span class="icon-save"></span> ' . JText::_('JYES');
					$no  = '<span class="icon-cancel"></span> ' . JText::_('JNO');
					?>
					<p><?php echo JText::_('RR_DYNAMIC_TAGS_DESC'); ?></p>

					<table class="table table-striped">
						<thead>
						<tr>
							<th><?php echo JText::_('RR_SYNTAX'); ?></th>
							<th class="left">
								<span><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></span></th>
							<th class="left">
								<span><?php echo JText::_('RR_INPUT_EXAMPLE'); ?></span></th>
							<th class="left">
								<span><?php echo JText::_('RR_OUTPUT_EXAMPLE'); ?></span></th>
							<th>
								<span rel="tooltip" title="<?php echo JText::_('RR_USE_IN_NORMAL'); ?>"><?php echo JText::_('NN_NORMAL'); ?></span>
							</th>
							<th>
								<span rel="tooltip" title="<?php echo JText::_('RR_USE_IN_REGEX'); ?>"><?php echo JText::_('RR_REGEX'); ?></span>
							</th>
							<th>
								<span rel="tooltip" title="<?php echo JText::_('RR_USE_IN_SEARCH'); ?>"><?php echo JText::_('RR_SEARCH'); ?></span>
							</th>
							<th>
								<span rel="tooltip" title="<?php echo JText::_('RR_USE_IN_REPLACE'); ?>"><?php echo JText::_('RR_REPLACE'); ?></span>
							</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td style="font-family:monospace">[[comma]]</td>
							<td><?php echo JText::_('RR_USE_INSTEAD_OF_A_COMMA'); ?></td>
							<td style="font-family:monospace">[[comma]]</td>
							<td style="font-family:monospace">,</td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[space]]</td>
							<td><?php echo JText::_('RR_USE_FOR_LEADING_OR_TRAILING_SPACES'); ?></td>
							<td style="font-family:monospace">[[space]]</td>
							<td></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[user:id]]</td>
							<td><?php echo JText::_('RR_DYNAMIC_TAG_USER_ID'); ?></td>
							<td style="font-family:monospace">[[user:id]]</td>
							<td><?php echo $user->id; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[user:username]]</td>
							<td><?php echo JText::_('RR_DYNAMIC_TAG_USER_USERNAME'); ?></td>
							<td style="font-family:monospace">[[user:username]]</td>
							<td><?php echo $user->username; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[user:name]]</td>
							<td><?php echo JText::_('RR_DYNAMIC_TAG_USER_NAME'); ?></td>
							<td style="font-family:monospace">[[user:name]]</td>
							<td><?php echo $user->name; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[user:&#8230;]]</td>
							<td><?php echo JText::_('RR_DYNAMIC_TAG_USER_OTHER'); ?></td>
							<td style="font-family:monospace">[[user:misc]]</td>
							<td><?php echo isset($contact->misc) ? $contact->misc : ''; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[date:&#8230;]]</td>
							<td><?php echo JText::sprintf('RR_DYNAMIC_TAG_DATE', '<a rel="{handler: \'iframe\', size:{x:window.getSize().x-100, y: window.getSize().y-100}}" href="http://www.php.net/manual/function.strftime.php" class="modal">', '</a>'); ?></td>
							<td nowrap="nowrap" style="font-family:monospace">[[date:%A, %d %B
								%Y]]
							</td>
							<td><?php echo strftime('%A, %d %B %Y'); ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[random:&#8230;-&#8230;]]</td>
							<td><?php echo JText::_('RR_DYNAMIC_TAG_RANDOM'); ?></td>
							<td style="font-family:monospace">[[random:0-100]]</td>
							<td><?php echo rand(0, 100); ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[counter]]</td>
							<td><?php echo JText::_('RR_DYNAMIC_TAG_COUNTER'); ?></td>
							<td style="font-family:monospace">[[counter]]</td>
							<td>1</td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[escape]]&#8230;[[/escape]]</td>
							<td><?php echo JText::_('RR_DYNAMIC_TAG_ESCAPE'); ?></td>
							<td style="font-family:monospace">[[escape]]\1[[/escape]]</td>
							<td><?php echo addslashes(JText::_('RR_ITS_A_STRING')); ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[uppercase]]&#8230;[[/uppercase]]</td>
							<td><?php echo JText::_('RR_DYNAMIC_TAG_UPPERCASE'); ?></td>
							<td style="font-family:monospace">[[uppercase]]\1[[/uppercase]]</td>
							<td><?php echo strtoupper(JText::_('RR_ITS_A_STRING')); ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						<tr>
							<td style="font-family:monospace">[[lowercase]]&#8230;[[/lowercase]]</td>
							<td><?php echo JText::_('RR_DYNAMIC_TAG_LOWERCASE'); ?></td>
							<td style="font-family:monospace">[[lowercase]]\1[[/lowercase]]</td>
							<td><?php echo strtolower(JText::_('RR_ITS_A_STRING')); ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
							<td align="center"><?php echo $no; ?></td>
							<td align="center"><?php echo $yes; ?></td>
						</tr>
						</tbody>
					</table>

					<p><?php echo JText::sprintf('RR_HELP_ON_REGULAR_EXPRESSIONS', '<a rel="{handler: \'iframe\', size: {x: 800, y: window.getSize().y-100}}" href="index.php?nn_qp=1&folder=media.rereplacer.images&file=popup.php" class="modal">', '</a>'); ?></p>
				</div>

				<div class="tab-pane" id="areas">
					<fieldset>
						<?php echo $this->render($this->item->form, 'areas'); ?>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_('NN_TAGS'); ?></legend>
						<?php echo $this->render($this->item->form, 'tags'); ?>
					</fieldset>
				</div>

				<div class="tab-pane" id="assignments">
					<fieldset>
						<?php echo $this->render($this->item->form, 'assignments'); ?>
					</fieldset>
				</div>
			</div>
		</div>
		<div class="span3 span-md-4 form-vertical">
			<h4><?php echo JText::_('JDETAILS'); ?></h4>
			<hr />
			<fieldset>
				<?php echo $this->render($this->item->form, 'details'); ?>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="has_easyblog" value="<?php echo NNFrameworkFunctions::extensionInstalled('easyblog'); ?>" />
	<input type="hidden" name="has_flexicontent" value="<?php echo NNFrameworkFunctions::extensionInstalled('flexicontent'); ?>" />
	<input type="hidden" name="has_form2content" value="<?php echo NNFrameworkFunctions::extensionInstalled('form2content'); ?>" />
	<input type="hidden" name="has_k2" value="<?php echo NNFrameworkFunctions::extensionInstalled('k2'); ?>" />
	<input type="hidden" name="has_zoo" value="<?php echo NNFrameworkFunctions::extensionInstalled('zoo'); ?>" />
	<input type="hidden" name="has_akeebasubs" value="<?php echo NNFrameworkFunctions::extensionInstalled('akeebasubs'); ?>" />
	<input type="hidden" name="has_hikashop" value="<?php echo NNFrameworkFunctions::extensionInstalled('hikashop'); ?>" />
	<input type="hidden" name="has_mijoshop" value="<?php echo NNFrameworkFunctions::extensionInstalled('mijoshop'); ?>" />
	<input type="hidden" name="has_redshop" value="<?php echo NNFrameworkFunctions::extensionInstalled('redshop'); ?>" />
	<input type="hidden" name="has_virtuemart" value="<?php echo NNFrameworkFunctions::extensionInstalled('virtuemart'); ?>" />
	<input type="hidden" name="has_cookieconfirm" value="<?php echo NNFrameworkFunctions::extensionInstalled('cookieconfirm'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(task) {
		passCheck = 1;
		if (task != 'item.cancel') {
			passCheck = checkFields();
		}

		if (passCheck) {
			var f = document.getElementById('item-form');
			if (self != top) {
				if (task == 'item.cancel' || task == 'item.save') {
					f.target = '_top';
				} else {
					f.action += '&tmpl=component';
				}
			}
			Joomla.submitform(task, f);
		}
	}

	function checkFields() {
		var f = document.getElementById('item-form');

		if (f['jform[name]'].value == '') {
			alert('<?php echo JText::_('RR_THE_ITEM_MUST_HAVE_A_NAME', true); ?>');
			return false;
		}

		if (f['jform[use_xml]'][1].checked) {
			if (f['jform[xml]'].value == '') {
				alert('<?php echo JText::_('RR_THE_ITEM_MUST_HAVE_AN_XML_FILE', true); ?>');
				return false;
			}
		} else {
			if (f['jform[search]'].value == '') {
				alert('<?php echo JText::_('RR_THE_ITEM_MUST_HAVE_SOMETHING_TO_SEARCH_FOR', true); ?>');
				return false;
			}
		}

		if (
			( f['jform[between_start]'].value.trim() != '' && f['jform[between_start]'].value.trim().length < 3 ) ||
			( f['jform[between_end]'].value.trim() != '' && f['jform[between_end]'].value.trim().length < 3 )
		) {
			alert('<?php echo sprintf(JText::_('RR_THE_SEARCH_BETWEEN_STRINGS_SHOULD_BE_LONGER', true), 2); ?>');
			return false;
		}

		return true;
	}
</script>
