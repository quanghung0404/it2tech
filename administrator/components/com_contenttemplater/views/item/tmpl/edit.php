<?php
/**
 * Item View Template: Edit
 *
 * @package         Content Templater
 * @version         5.1.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('com_content', JPATH_ADMINISTRATOR);

$user  = JFactory::getUser();
$db    = JFactory::getDbo();
$query = $db->getQuery(true)
	->select('c.misc')
	->from('#__' . $this->config->contact_table . ' as c')
	->where('c.user_id = ' . (int) $user->id);
$db->setQuery($query);
$contact = $db->loadObject();

NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');
JHtml::stylesheet('nnframework/style.min.css', false, true);
?>

<form action="<?php echo JRoute::_('index.php?option=com_contenttemplater&id=' . ( int ) $this->item->id); ?>" method="post"
      name="adminForm" id="item-form" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span10">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#editor" data-toggle="tab"><?php echo JText::_('NN_CONTENT'); ?></a>
				</li>
				<li>
					<a href="#contentsettings" data-toggle="tab"><?php echo JText::_('CT_CONTENT_SETTINGS'); ?></a>
				</li>
				<li>
					<a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></a>
				</li>
				<li>
					<a href="#assignments" data-toggle="tab"><?php echo JText::_('NN_PUBLISHING_ASSIGNMENTS'); ?></a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="editor">
					<?php echo $this->render($this->item->form, '-content'); ?>
					<div class="row-fluid">
						<fieldset>
							<legend><?php echo JText::_('CT_DYNAMIC_TAGS'); ?></legend>
							<p><?php echo JText::_('CT_DYNAMIC_TAGS_DESC'); ?></p>

							<table class="table table-striped">
								<thead>
								<tr>
									<th><?php echo JText::_('CT_SYNTAX'); ?></th>
									<th class="left">
										<span><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></span></th>
									<th class="left">
										<span><?php echo JText::_('CT_OUTPUT_EXAMPLE'); ?></span></th>
									<th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td style="font-family:monospace">[[user:id]]</td>
									<td><?php echo JText::_('CT_DYNAMIC_TAG_USER_ID'); ?></td>
									<td><?php echo $user->id; ?></td>
								</tr>
								<tr>
									<td style="font-family:monospace">[[user:username]]</td>
									<td><?php echo JText::_('CT_DYNAMIC_TAG_USER_USERNAME'); ?></td>
									<td><?php echo $user->username; ?></td>
								</tr>
								<tr>
									<td style="font-family:monospace">[[user:name]]</td>
									<td><?php echo JText::_('CT_DYNAMIC_TAG_USER_NAME'); ?></td>
									<td><?php echo $user->name; ?></td>
								</tr>
								<tr>
									<td style="font-family:monospace">[[user:...]]</td>
									<td><?php echo JText::_('CT_DYNAMIC_TAG_USER_OTHER'); ?></td>
									<td><?php echo isset($contact->misc) ? $contact->misc : ''; ?></td>
								</tr>
								<tr>
									<td style="font-family:monospace">[[date:...]]</td>
									<td><?php echo JText::sprintf('CT_DYNAMIC_TAG_DATE', '<a rel="{handler: \'iframe\', size:{x:window.getSize().x-100, y: window.getSize().y-100}}" href="http://www.php.net/manual/function.strftime.php" class="modal">', '</a>', '<span style="font-family:monospace">[[date: %A, %d %B %Y]]</span>'); ?></td>

									<td><?php echo strftime('%A, %d %B %Y'); ?></td>
								</tr>
								<tr>
									<td style="font-family:monospace">[[random:...-...]]</td>
									<td><?php echo JText::_('CT_DYNAMIC_TAG_RANDOM'); ?></td>
									<td><?php echo rand(0, 100); ?></td>
								</tr>
								<tr>
									<td style="font-family:monospace">[[text:MY_STRING]]</td>
									<td><?php echo JText::_('CT_DYNAMIC_TAG_TEXT'); ?></td>
									<td><?php echo JText::_('CT_MY_STRING'); ?></td>
								</tr>
								<tr>
									<td style="font-family:monospace">[[template:...]]</td>
									<td><?php echo JText::_('CT_DYNAMIC_TAG_TEMPLATE'); ?></td>
									<td>
										<?php echo JText::_('CT_DYNAMIC_TAG_TEMPLATE_OUTPUT'); ?>
									</td>
								</tr>
								</tbody>
							</table>
						</fieldset>
					</div>
				</div>

				<div class="tab-pane" id="contentsettings">
					<?php echo $this->render($this->item->form, '-content-settings'); ?>
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#content-general" data-toggle="tab"><?php echo JText::_('COM_CONTENT_ARTICLE_DETAILS'); ?></a>
						</li>
						<li>
							<a href="#content-publishing" data-toggle="tab"><?php echo JText::_('COM_CONTENT_FIELDSET_PUBLISHING'); ?></a>
						</li>
						<li>
							<a href="#content-images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_FIELD_IMAGE_OPTIONS'); ?></a>
						</li>
						<li>
							<a href="#content-basic" data-toggle="tab"><?php echo JText::_('COM_CONTENT_ATTRIBS_FIELDSET_LABEL'); ?></a>
						</li>
						<li>
							<a href="#content-editorconfig" data-toggle="tab"><?php echo JText::_('COM_CONTENT_SLIDER_EDITOR_CONFIG'); ?></a>
						</li>
						<li>
							<a href="#content-customfields" data-toggle="tab"><?php echo JText::_('CT_CUSTOM_FIELDS'); ?></a>
						</li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane active" id="content-general">
							<div class="row-fluid">
								<div class="span8">
									<fieldset>
										<?php echo $this->render($this->item->form, '-content-general-left'); ?>
									</fieldset>
								</div>
								<div class="span4">
									<fieldset class="form-vertical">
										<?php echo $this->render($this->item->form, '-content-general-right'); ?>
									</fieldset>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="content-publishing">
							<div class="row-fluid">
								<div class="span6">
									<fieldset>
										<?php echo $this->render($this->item->form, '-content-publishing-left'); ?>
									</fieldset>
								</div>
								<div class="span6">
									<fieldset>
										<?php echo $this->render($this->item->form, '-content-publishing-right'); ?>
									</fieldset>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="content-images">
							<div class="row-fluid">
								<div class="span6">
									<fieldset>
										<?php echo $this->render($this->item->form, '-content-images'); ?>
									</fieldset>
								</div>
								<div class="span6">
									<fieldset>
										<?php echo $this->render($this->item->form, '-content-urls'); ?>
									</fieldset>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="content-basic">
							<fieldset>
								<?php echo $this->render($this->item->form, '-content-basic'); ?>
							</fieldset>
						</div>
						<div class="tab-pane" id="content-editorconfig">
							<fieldset>
								<?php echo $this->render($this->item->form, '-content-editorconfig'); ?>
							</fieldset>
						</div>
						<div class="tab-pane" id="content-customfields">
							<fieldset>
								<?php echo $this->render($this->item->form, '-content-customfields'); ?>
							</fieldset>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="publishing">
					<div class="row-fluid">
						<div class="span6">
							<fieldset>
								<legend><?php echo JText::_('CT_EDITOR_BUTTON_LIST'); ?></legend>
								<?php echo $this->render($this->item->form, 'publishing-button'); ?>
							</fieldset>
						</div>
						<div class="span6">
							<fieldset>
								<legend><?php echo JText::_('CT_LOAD_BY_DEFAULT'); ?></legend>
								<?php echo $this->render($this->item->form, 'publishing-load'); ?>
							</fieldset>
						</div>
						<div class="span6">
							<fieldset>
								<legend><?php echo JText::_('CT_LOAD_BY_URL'); ?></legend>
								<?php echo $this->render($this->item->form, 'publishing-url'); ?>
							</fieldset>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="assignments">
					<fieldset>
						<?php echo $this->render($this->item->form, 'assignments'); ?>
					</fieldset>
				</div>
			</div>
		</div>
		<div class="span2 form-vertical">
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
		var f = document.getElementById('item-form');
		if (task == 'item.cancel') {
			Joomla.submitform(task, f);
			return;
		}

		// do field validation
		if (f['jform[name]'].value.trim() == "") {
			alert("<?php echo JText::_('CT_THE_ITEM_MUST_HAVE_A_NAME', true); ?>");
		} else {
			Joomla.submitform(task, f);
		}
	}
</script>
