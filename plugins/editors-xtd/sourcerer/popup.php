<?php
/**
 * @package         Sourcerer
 * @version         5.2.2PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
if ($user->get('guest')
	|| (
		!$user->authorise('core.edit', 'com_content')
		&& !$user->authorise('core.create', 'com_content')
	)
)
{
	JError::raiseError(403, JText::_("ALERTNOTAUTH"));
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
$parameters = NNParameters::getInstance();
$params = $parameters->getPluginParams('sourcerer');

if (JFactory::getApplication()->isSite())
{
	if (!$params->enable_frontend)
	{
		JError::raiseError(403, JText::_("ALERTNOTAUTH"));
	}
}

$class = new PlgButtonSourcererPopup($params);
$class->render();

class PlgButtonSourcererPopup
{
	var $params = null;

	function __construct(&$params)
	{
		$this->params = $params;
	}

	function render()
	{
		jimport('joomla.filesystem.file');

		// Load plugin language
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
		NNFrameworkFunctions::loadLanguage('plg_system_nnframework');
		NNFrameworkFunctions::loadLanguage('plg_editors-xtd_sourcerer');
		NNFrameworkFunctions::loadLanguage('plg_system_sourcerer');

		JHtml::stylesheet('nnframework/style.min.css', false, true);
		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/script.min.js');

		JFactory::getDocument()->addStyleSheet('//code.jquery.com/ui/1.9.2/themes/smoothness/jquery-ui.css');
		JFactory::getDocument()->addScript('//code.jquery.com/ui/1.9.2/jquery-ui.js');

		// Tag character start and end
		list($tag_start, $tag_end) = explode('.', $this->params->tag_characters);

		$script = "
			var sourcerer_syntax_word = '" . $this->params->syntax_word . "';
			var sourcerer_tag_characters = ['" . $tag_start . "', '".$tag_end."'];
			var sourcerer_editorname = '" . JFactory::getApplication()->input->getString('name', 'text') . "';
			var sourcerer_default_addsourcetags = " . (int) $this->params->addsourcetags . ";
			var sourcerer_root = '" . JUri::root(true) . "';
		";
		JFactory::getDocument()->addScriptDeclaration($script);
		JHtml::stylesheet('sourcerer/popup.min.css', false, true);

		JHtml::script('sourcerer/script.min.js', false, true);

		$this->params->code = '';
		if ($this->params->use_example_code == 1 || (JFactory::getApplication()->isAdmin() && $this->params->use_example_code == 2))
		{
			$this->params->code = $this->params->example_code;
		}
		$this->params->code = str_replace('<br />', "\n", $this->params->code);

		echo $this->getHTML();
	}

	function getHTML()
	{
		JFactory::getApplication()->setUserState('editor.source.syntax', 'php');

		$editor = JEditor::getInstance('codemirror');

		ob_start();
		?>
		<div class="header">
			<h1 class="page-title">
				<span class="icon-nonumber icon-sourcerer"></span>
				<?php echo JText::_('INSERT_CODE'); ?>
			</h1>
		</div>

		<div class="subhead">
			<div class="container-fluid">
					<div class="btn-toolbar" id="toolbar">
						<div class="btn-group" id="toolbar-apply">
							<button href="#" onclick="nnSourcererPopup.insertText();window.parent.SqueezeBox.close();" class="btn btn-small btn-success">
								<span class="icon-apply icon-white"></span> <?php echo JText::_('SRC_INSERT') ?>
							</button>
						</div>

						<div class="btn-group">
							<button class="btn btn-small hasTip" id="btn-sourcetags" onclick="nnSourcererPopup.toggleSourceTags();return false;" title="<?php echo JText::_('SRC_TOGGLE_SOURCE_TAGS_DESC'); ?>">
								<span class="icon-nonumber icon-src-sourcetags"></span> <?php echo JText::_('SRC_TOGGLE_SOURCE_TAGS') ?>
							</button>
						</div>

						<div class="btn-group">
							<button class="btn btn-small hasTip" id="btn-tagstyle" onclick="nnSourcererPopup.toggleTagStyle();return false;" title="<?php echo JText::_('SRC_TOGGLE_TAG_STYLE_DESC'); ?>">
								<span class="icon-nonumber icon-src-tagstyle"></span> <?php echo JText::_('SRC_TOGGLE_TAG_STYLE') ?>
							</button>
						</div>

						<div class="btn-group" id="toolbar-cancel">
							<button href="#" onclick="if(confirm('<?php echo JText::_('NN_ARE_YOU_SURE'); ?>')){window.parent.SqueezeBox.close();}" class="btn btn-small">
								<span class="icon-cancel "></span> <?php echo JText::_('JCANCEL') ?>
							</button>
						</div>

						<?php if (JFactory::getApplication()->isAdmin() && JFactory::getUser()->authorise('core.admin', 1)) : ?>
							<div class="btn-wrapper" id="toolbar-options">
								<button onclick="window.open('index.php?option=com_plugins&filter_folder=system&filter_search=sourcerer');" class="btn btn-small">
									<span class="icon-options"></span> <?php echo JText::_('JOPTIONS') ?>
								</button>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid container-main">
			<form action="index.php" id="sourceForm" method="post">

				<div class="control-group form-inline">
				</div>

				<div class="well well-small src_editor">
					<?php echo $editor->display('source', $this->params->code, '100%', '100%', 10, 10, 0, null, null, null, array('linenumbers' => 1, 'tabmode' => 'shift')); ?>
				</div>

				<script type="text/javascript">
					nnSourcererPopup.init();
				</script>
			</form>
		</div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
