<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewSettings extends EasyDiscussAdminView
{
	/**
	 * Renders the settings form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.settings');

		// Determines which layout that we should show currently.
		$layout = $this->getLayout();

		if ($layout == 'default') {
			$layout = 'general';
		}

		// Set the title and the description of the page
		$title = JText::_('COM_EASYDISCUSS_SETTINGS_' . strtoupper($layout) . '_TITLE');
		$desc = JText::_('COM_EASYDISCUSS_SETTINGS_' . strtoupper($layout) . '_DESC');

		$this->title($title);
		$this->desc($desc);

		JToolBarHelper::apply();

		// Get the tabs
		$contents = $this->getContents($layout);

		$this->set('contents', $contents);

		parent::display('settings/wrapper');
	}

	/**
	 * Renders the tabs
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getContents($layout)
	{
		$path = DISCUSS_ADMIN_ROOT . '/themes/default/settings/' . $layout;

		$files = JFolder::files($path, '.php');
		$tabs = array();

		foreach ($files as $file) {

			if ($file == 'default.php') {
				continue;
			}

			if ($layout == 'social' && $file == 'social.buttons.php') {
				continue;
			}

			$tab = str_ireplace('.php', '', $file);
			$tabs[$file] = $tab;
		}

		// We need to sort the tabs to ensure that general.php should always be the first item.
		usort($tabs, array($this, "resortTabs"));

		$defaultSAId = ED::getDefaultSAIds();
		$joomlaVersion = ED::getJoomlaVersion();
		$joomlaGroups = ED::getJoomlaUserGroups();

		// Get the active tab
		$active = $this->input->get('active', '', 'string');
		$active = str_ireplace('ed-', '', $active);

		$theme = ED::themes();

		$theme->set('active', $active);
		$theme->set('layout', $layout);
		$theme->set('tabs', $tabs);
		$theme->set('defaultSAId', $defaultSAId);
		$theme->set('joomlaVersion', $joomlaVersion);
		$theme->set('joomlaGroups', $joomlaGroups);
		$theme->set('layout', $layout);

		if ($layout == 'email') {
			$categories = $this->getCategories();
			$theme->set('categories', $categories);
		}

		$output = $theme->output('admin/settings/contents');

		return $output;
	}

	/**
	 * Resort the tabs
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function resortTabs($a, $b)
	{
		if ($a == 'general') {
			return 0;
		}

		return 1;
	}

	public function getCategories()
	{
		$db = ED::db();
		$query = 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
			 . 'WHERE ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

		$db->setQuery( $query );
		$categories	= $db->loadObjectList();

		return $categories;
	}

	public function editEmailTemplate()
	{
		$file		= JRequest::getVar('file', '', 'GET');
		$filepath	= DISCUSS_THEMES . '/wireframe/emails/' . $file;
		$content	= '';
		$html		= '';
		$msg		= JRequest::getVar('msg', '', 'GET');
		$msgType	= JRequest::getVar('msgtype', '', 'GET');

		ob_start();

		if(!empty($msg))
		{
		?>
			<div id="discuss-message" class="<?php echo $msgType; ?>"><?php echo $msg; ?></div>
		<?php
		}

		if(is_writable($filepath))
		{
			$content = JFile::read($filepath);
		?>
			<form name="emailTemplate" id="emailTemplate" method="POST">
				<div>
				<?php if(DiscussHelper::getJoomlaVersion() <= '1.5') : ?>
				<input type="button" value="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_CLOSE' );?>" onclick="window.parent.document.getElementById('sbox-window').close();">
				<?php endif; ?>
				<input type="submit" name="save" value="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE' );?>">
				</div>
				<textarea rows="28" cols="93" name="content"><?php echo $content; ?></textarea>
				<input type="hidden" name="option" value="com_easydiscuss">
				<input type="hidden" name="controller" value="settings">
				<input type="hidden" name="task" value="saveEmailTemplate">
				<input type="hidden" name="file" value="<?php echo $file; ?>">
				<input type="hidden" name="tmpl" value="component">
				<input type="hidden" name="browse" value="1">


			</form>
		<?php
		}
		else
		{
		?>
			<div><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_UNWRITABLE'); ?></div>
		<?php
		}

		$html = ob_get_contents();
		ob_end_clean();

		echo $html;
	}
}
