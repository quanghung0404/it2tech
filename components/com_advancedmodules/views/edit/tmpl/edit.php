<?php
/**
 * @package         Advanced Module Manager
 * @version         5.3.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

JHtml::_('bootstrap.framework');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.combobox');
JHtml::_('formbehavior.chosen', 'select');

$hasContent          = empty($this->item->module) || isset($this->item->xml->customContent);
$hasContentFieldName = 'content';

// For a later improvement
if ($hasContent)
{
	$hasContentFieldName = 'content';
}

// Get Params Fieldsets
$this->fieldsets     = $this->form->getFieldsets('params');
$this->hidden_fields = '';

$uri = JUri::getInstance();

$script = "
Joomla.submitbutton = function(task)
{
	if (task == 'module.cancel' || document.formvalidator.isValid(document.id('module-form'))) {";
if ($hasContent)
{
	$script .= $this->form->getField($hasContentFieldName)->save();
}
$script .= "	Joomla.submitform(task, document.getElementById('module-form'));
				if (self != top)
				{
					window.top.setTimeout('window.parent.SqueezeBox.close();', 1000);
				}
			}
	};";
if (JFactory::getUser()->authorise('core.admin'))
{
	$script .= "
	jQuery(document).ready(function()
	{
		// add alert on remove assignment buttons
		jQuery('button.nn_remove_assignment').click(function()
		{
			if(confirm('" . str_replace('<br />', '\n', JText::_('AMM_DISABLE_ASSIGNMENT', true)) . "')) {
				jQuery('div#toolbar-options button').click();
			}
		});
	});";
}

JFactory::getDocument()->addScriptDeclaration($script);
NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');
NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/toggler.min.js');

JFactory::getDocument()->addStyleSheetVersion(JUri::root(true) . '/media/nnframework/css/frontend.min.css');
?>

<form action="<?php echo JRoute::_('index.php?option=com_advancedmodules&view=edit&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm"
      id="module-form" class="form-validate">

	<div class="row-fluid">

		<!-- Begin Content -->
		<div class="span12">

			<h1 class="page_title">
				<?php echo JText::sprintf('AMM_MODULE_EDIT', $this->item->title . ' <span class="label label-default">' . $this->item->module . '</span>'); ?>
			</h1>

			<div class="btn-toolbar">
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-primary"
					        onclick="Joomla.submitbutton('module.apply')">
						<i class="icon-apply"></i>
						<?php echo JText::_('JAPPLY') ?>
					</button>
				</div>
				<div class="btn-group">
					<button type="button" class="btn btn-default"
					        onclick="Joomla.submitbutton('module.save')">
						<i class="icon-save"></i>
						<?php echo JText::_('JSAVE') ?>
					</button>
				</div>
				<div class="btn-group">
					<button type="button" class="btn btn-default"
					        onclick="Joomla.submitbutton('module.cancel')">
						<i class="icon-cancel"></i>
						<?php echo JText::_('JCANCEL') ?>
					</button>
				</div>
			</div>

			<hr class="hr-condensed" />

			<div class="row-fluid">
				<div class="span12">
					<fieldset class="form-horizontal">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('title'); ?>
							</div>
							<div class="controls">
								<?php echo str_replace('input-xxlarge', '', $this->form->getInput('title')); ?>
							</div>
						</div>

						<?php
						if ($hasContent)
						{
							echo '<hr />' . $this->form->getInput($hasContentFieldName) . '<hr />';
						}
						$this->fieldset = 'basic';
						$html           = JLayoutHelper::render('joomla.edit.fieldset', $this);
						echo $html ? $html . '<hr />' : '';
						?>

						<?php echo $this->form->getControlGroup('showtitle'); ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('position'); ?>
							</div>
							<div class="controls">
								<?php echo $this->loadTemplate('positions'); ?>
							</div>
						</div>
					</fieldset>
					<?php
					// Set main fields.
					$this->fields = array(
						'published',
						'access',
						'ordering',
						'note',
					);
					?>
					<?php echo str_replace('form-vertical', 'form-horizontal', JLayoutHelper::render('joomla.edit.global', $this)); ?>
					<fieldset class="form-horizontal">
						<?php if ($this->item->client_id == 0) : ?>
							<?php echo $this->render($this->assignments, 'pre_post_html'); ?>
						<?php endif; ?>
						<?php if ($this->item->client_id == 0 && $this->config->show_hideempty) : ?>
							<?php echo $this->render($this->assignments, 'hideempty'); ?>
						<?php endif; ?>
						<?php if ($this->config->show_extra) : ?>
							<?php for ($i = 1; $i <= 5; $i++) : ?>
								<?php if (isset($this->config->{'extra' . $i}) && $this->config->{'extra' . $i} != '') : ?>
									<?php
									$label   = explode('|', $this->config->{'extra' . $i}, 2);
									$tooltip = isset($label['1']) ? JText::_($label['1']) : '';
									$label   = JText::_($label['0']);
									?>
									<div class="control-group">
										<div class="control-label">
											<label id="advancedparams_extra<?php echo $i; ?>-lbl" for="advancedparams_extra<?php echo $i; ?>"
												<?php echo $tooltip ? 'class="tooltip" title="<strong>' . $label . '</strong><br />' . $tooltip . '"' : ''; ?>>
												<?php echo $label; ?>
											</label>
										</div>
										<div class="controls">
											<?php echo $this->assignments->getInput('extra' . $i); ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endfor; ?>
						<?php endif; ?>
					</fieldset>

					<?php echo JHtml::_('bootstrap.startAccordion', 'moduleSlide'); ?>

					<?php if ($this->item->client_id == 0) : ?>
						<?php echo JHtml::_('bootstrap.addSlide', 'moduleSlide', JText::_('AMM_ASSIGNMENTS'), 'assignment'); ?>
						<?php echo $this->loadTemplate('assignment'); ?>
						<?php echo JHtml::_('bootstrap.endSlide'); ?>
					<?php endif; ?>

					<div class="form-horizontal">
						<?php
						$this->fieldsets        = array();
						$this->ignore_fieldsets = array('basic', 'description');
						echo JLayoutHelper::render('joomla.edit.params', $this);
						?>
					</div>

					<?php echo JHtml::_('bootstrap.endAccordion'); ?>

				</div>
			</div>

			<div class="btn-toolbar">
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-primary"
					        onclick="Joomla.submitbutton('module.apply')">
						<i class="icon-apply"></i>
						<?php echo JText::_('JAPPLY') ?>
					</button>
				</div>
				<div class="btn-group">
					<button type="button" class="btn btn-default"
					        onclick="Joomla.submitbutton('module.save')">
						<i class="icon-save"></i>
						<?php echo JText::_('JSAVE') ?>
					</button>
				</div>
				<div class="btn-group">
					<button type="button" class="btn btn-default"
					        onclick="Joomla.submitbutton('module.cancel')">
						<i class="icon-cancel"></i>
						<?php echo JText::_('JCANCEL') ?>
					</button>
				</div>
			</div>

		</div>
		<!-- End Content -->
	</div>

	<?php echo $this->hidden_fields; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="current" value="<?php echo base64_encode($uri->toString()); ?>" />
	<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get('return', null, 'base64'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $this->form->getInput('module'); ?>
	<?php echo $this->form->getInput('client_id'); ?>
</form>
