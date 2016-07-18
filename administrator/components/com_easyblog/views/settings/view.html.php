<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewSettings extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.setting');

		$layout = $this->getLayout();
		$activeTab = $this->input->get('active', '', 'default');

		$this->set('activeTab', $activeTab);
		$this->set('config', $this->config);

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Someone is trying to access index.php?option=com_easyblog&view=settings
		$this->general();
	}

	public function general()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_GENERAL', '', 'fa-wrench');

		$this->set('namespace', 'settings/general/default');

		parent::display('settings/form');
	}

	public function media()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_MEDIA', '', 'fa-picture-o');

		$this->set('namespace', 'settings/media/default');
		parent::display('settings/form');
	}

	public function seo()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_SEO', '', 'fa-cloud');

		$this->set('namespace', 'settings/seo/default');
		parent::display('settings/form');
	}

	public function comments()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_COMMENTS', '', 'fa-comments-o');

		//check if jomcomment installed.
		$jcInstalled = false;
		if(file_exists(JPATH_ROOT . '/administrator/components/com_jomcomment/config.jomcomment.php' ) )
		{
			$jcInstalled = true;
		}

		//check if jcomments installed.
		$jComment 		= false;
		$jCommentFile 	= JPATH_ROOT . '/components/com_jcomments/jcomments.php';

		if (JFile::exists($jCommentFile)) {
			$jComment = true;
		}

		//check if rscomments installed.
		$rsComment 		= false;
		$rsCommentFile 	= JPATH_ROOT . '/components/com_rscomments/rscomments.php';

		if (JFile::exists($rsCommentFile)) {
			$rsComment = true;
		}

		// @task: Check if easydiscuss plugin is installed and enabled.
		$easydiscuss	= JPluginHelper::isEnabled( 'content' , 'easydiscuss' );

		$komento		= JPluginHelper::isEnabled( 'content' , 'komento' );

		$this->set('easydiscuss', $easydiscuss);
		$this->set('komento', $komento);
		$this->set('jcInstalled', $jcInstalled);
		$this->set('jComment', $jComment);
		$this->set('rsComment', $rsComment);

		$this->set('namespace', 'settings/comments/default');
		parent::display('settings/form');
	}

	public function storage()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_REMOTE_STORAGE', '', 'fa-cloud-download');

		$buckets = array();

		if ($this->config->get('amazon_enable') && $this->config->get('amazon_key') && $this->config->get('amazon_secret')) {

			$amazon = EB::amazon();

			$buckets = $amazon->getBuckets();
		}

		$this->set('buckets', $buckets);
		$this->set('namespace', 'settings/storage/default');
		parent::display('settings/form');
	}

	public function dropbox()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_DROPBOX', '', 'fa-dropbox');

		$this->set('namespace', 'settings/dropbox/default');
		parent::display('settings/form');
	}

	public function layout()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_LAYOUT', '', 'fa-desktop');

		// Get the category params
		$params = $this->config;

		// Get the param forms from the view manifest file
		$manifest = JPATH_ROOT . '/components/com_easyblog/views/entry/tmpl/default.xml';
		$postform = EB::form()->render($manifest, $params, true, 'layout_', false);

		// Render the form for listing options
		$manifest = JPATH_ROOT . '/components/com_easyblog/views/latest/tmpl/default.xml';
		$listing = EB::form()->render($manifest, $params, true, 'listing_', false);

		// Render the form for category options
		$manifest = JPATH_ROOT . '/components/com_easyblog/views/categories/tmpl/listings.xml';
		$categoryForm = EB::form()->render($manifest, $params, true, 'category_', false);

		// Render the form for tag options
		$manifest = JPATH_ROOT . '/components/com_easyblog/views/tags/tmpl/tag.xml';
		$tagForm = EB::form()->render($manifest, $params, true, 'tag_', false);

		// Render the form for author options
		$manifest = JPATH_ROOT . '/components/com_easyblog/views/blogger/tmpl/listings.xml';
		$authorForm = EB::form()->render($manifest, $params, true, 'blogger_', false);

		$this->set('listing', $listing);
		$this->set('categoryForm', $categoryForm);
		$this->set('tagForm', $tagForm);
		$this->set('authorForm', $authorForm);
		$this->set('postform', $postform);
		$this->set('namespace', 'settings/layout/default');

		parent::display('settings/form');
	}

	public function notifications()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_NOTIFICATIONS', '', 'fa-bell-o');

		$this->set('namespace', 'settings/notifications/default');
		parent::display('settings/form');
	}

	public function integrations()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_INTEGRATIONS', '', 'fa-sitemap');
		$this->set('namespace', 'settings/integrations/default');
		parent::display('settings/form');
	}

	public function social()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_SOCIAL_INTEGRATIONS', '', 'fa-share-square');

		$this->set('namespace', 'settings/social/default');
		parent::display('settings/form');
	}

	public function mailchimp()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_MAILCHIMP', '', 'fa-share-square');

		$this->set('namespace', 'settings/mailchimp/default');
		parent::display('settings/form');
	}

	public function sendy()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_SENDY', '', 'fa-paper-plane-o');

		$this->set('namespace', 'settings/sendy/default');
		parent::display('settings/form');
	}

	public function system()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_SYSTEM', '', 'fa-flask');

		$ownerIds = EB::getDefaultSAIds();

		$this->set('ownerIds', $ownerIds);
		$this->set('namespace', 'settings/system/default');

		parent::display('settings/form');
	}

	public function reporting()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_REPORTING', '', 'fa-exclamation-triangle');


		$this->set('namespace', 'settings/reporting/default');
		parent::display('settings/form');
	}

	public function remote()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_REMOTE_PUBLISHING', '', 'fa-rocket');


		$this->set('namespace', 'settings/remote/default');
		parent::display('settings/form');
	}

	public function mailbox()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_MAILBOX_PUBLISHING', '', 'fa-envelope');


		$this->set('namespace', 'settings/mailbox/default');
		parent::display('settings/form');
	}

	public function antispam()
	{
		$this->setHeading('COM_EASYBLOG_TITLE_SETTINGS_ANTISPAM', '', 'fa-ambulance');

		$this->set('namespace', 'settings/antispam/default');
		parent::display('settings/form');
	}

	public function users()
	{
		$this->setHeading('COM_EASYBLOG_SETTINGS_TAB_AUTHORS');

		$this->set('namespace', 'settings/users/default');
		parent::display('settings/form');
	}

	public function teamblogs()
	{
		$this->setHeading('COM_EASYBLOG_SETTINGS_TAB_TEAMBLOGS');

		$this->set('namespace', 'settings/teamblogs/default');
		parent::display('settings/form');
	}

	public function getSocialButtonOrder()
	{
		$config = EasyBlogHelper::getConfig();

		$socialButtons  = explode( ',', EBLOG_SOCIAL_BUTTONS );

		$socialButtonOrders = array();

		foreach($socialButtons as $key)
		{
			$config_key = 'integrations_order_' . $key;
			$socialButtonOrders[$key]   = $config->get( $config_key , '0');
		}

		return $socialButtonOrders;
	}

	public function getThemes( $selectedTheme = 'default' )
	{
		$html	= '<select name="layout_theme" class="inputbox">';

		$themes	= $this->get( 'Themes' );

		for( $i = 0; $i < count( $themes ); $i++ )
		{
			$theme		= JString::strtolower( $themes[ $i ] );

			if ( $theme != 'dashboard' ) {
				$selected	= ( $selectedTheme == $theme ) ? ' selected="selected"' : '';
				$html		.= '<option' . $selected . '>' . $theme . '</option>';
			}
		}

		$html	.= '</select>';

		return $html;
	}

	public function getDashboardThemes( $selectedTheme = 'system' )
	{
		$html	= '<select name="layout_dashboard_theme" class="inputbox">';

		$model	= $EB::model( 'Settings' );
		$themes	= $model->getThemes( true );

		for( $i = 0; $i < count( $themes ); $i++ )
		{
			$theme		= JString::strtolower( $themes[ $i ] );

			$selected	= ( $selectedTheme == $theme ) ? ' selected="selected"' : '';
			$html		.= '<option' . $selected . '>' . $theme . '</option>';
		}

		$html	.= '</select>';

		return $html;
	}

	/**
	 * Retrieves a list of themes available for bloggers
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBloggerThemes()
	{
		$config = EasyBlogHelper::getConfig();

		$themes	= $this->get( 'Themes' );

		$options = array ();

		foreach ($themes as $theme)
		{
			$options[] = JHTML::_('select.option', $theme, $theme);
		}

		$previouslyAvailable = $config->get('layout_availablebloggertheme');

		return JHTML::_('select.genericlist', $options, 'layout_availablebloggertheme[]', 'multiple="multiple" class="form-control" style="height: 200px;"', 'value', 'text', explode('|', $previouslyAvailable) );
	}

	public function getEmailsTemplate()
	{
		JHTML::_('behavior.modal' , 'a.modal' );
		$html	= '';

		$files	= JFolder::files( EBLOG_THEMES . DIRECTORY_SEPARATOR . 'default' );
		$emails	= array();

		foreach( $files as $file )
		{
			if( JString::substr( $file , 0 , 5 ) == 'email' )
			{
				$emails[] 	= $file;
			}
		}


		ob_start();

		foreach($emails as $email)
		{
		?>
			<div>
				<div style="float:left; margin-right:5px; clear:none">
				<?php echo JText::_($email); ?>
				</div>
				<div style="margin-top:5px; clear:none">
				[
				<?php
				if(is_writable(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $email))
				{
				?>
					<a class="modal" rel="{handler: 'iframe', size: {x: 700, y: 500}}" href="index.php?option=com_easyblog&view=settings&layout=editEmailTemplate&file=<?php echo $email; ?>&tmpl=component&browse=1"><?php echo JText::_('COM_EASYBLOG_EDIT');?></a>
				<?php
				}
				else
				{
				?>
					<span style="color:red; font-weight:bold;"><?php echo JText::_('COM_EASYBLOG_UNWRITABLE');?></span>
				<?php
				}
				?>
				]
				</div>
			</div>
		<?php
		}
		$html   = ob_get_contents();
		@ob_end_clean();

		return $html;
	}

	public function editEmailTemplate()
	{
		$file		= JRequest::getVar('file', '', 'GET');
		$filepath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $file;
		$content	= '';
		$html		= '';
		$msg		= JRequest::getVar('msg', '', 'GET');
		$msgType	= JRequest::getVar('msgtype', '', 'GET');

		ob_start();

		if(!empty($msg))
		{
			EasyBlogHelper::addStyleSheet('/components/com_easyblog/assets/css/common.css');
		?>
			<div id="eblog-message" class="<?php echo $msgType; ?>"><?php echo $msg; ?></div>
		<?php
		}

		if(is_writable($filepath))
		{
			$content = JFile::read($filepath);
		?>
			<form name="emailTemplate" id="emailTemplate" method="POST">
				<textarea rows="28" cols="93" name="content"><?php echo $content; ?></textarea>
				<input type="hidden" name="option" value="com_easyblog">
				<input type="hidden" name="c" value="settings">
				<input type="hidden" name="task" value="saveEmailTemplate">
				<input type="hidden" name="file" value="<?php echo $file; ?>">
				<input type="hidden" name="tmpl" value="component">
				<input type="hidden" name="browse" value="1">
				<input type="submit" name="save" value="<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE' );?>">
				<?php if(EasyBlogHelper::getJoomlaVersion() <= '1.5') : ?>
				<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_CLOSE' );?>" onclick="window.parent.document.getElementById('sbox-window').close();">
				<?php endif; ?>
			</form>
		<?php
		}
		else
		{
		?>
			<div><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_UNWRITABLE'); ?></div>
		<?php
		}

		$html = ob_get_contents();
		@ob_end_clean();

		echo $html;
	}

	public function getPaginationSettings( $key , $selected )
	{
		$listLength = array();
		$listLength[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_USE_JOOMLA_LIST_LENGTH' ) );

		for( $i = 1; $i <= 10; $i++ )
		{
			$listLength[] = JHTML::_('select.option', $i , JText::_( $i ) );
		}

		$listLength[] = JHTML::_('select.option', '15', JText::_( '15' ) );
		$listLength[] = JHTML::_('select.option', '20', JText::_( '20' ) );
		$listLength[] = JHTML::_('select.option', '25', JText::_( '25' ) );
		$listLength[] = JHTML::_('select.option', '30', JText::_( '30' ) );
		$listLength[] = JHTML::_('select.option', '50', JText::_( '50' ) );
		$listLength[] = JHTML::_('select.option', '100', JText::_( '100' ) );
		return JHTML::_('select.genericlist', $listLength, $key , ' class="inputbox"', 'value', 'text', $selected );
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_HOME_SETTINGS' ), 'settings' );

		JToolBarHelper::apply('settings.save');
		JToolbarHelper::divider();
		JToolbarHelper::custom('export', 'download', '', JText::_('COM_EASYBLOG_EXPORT_SETTINGS'), false);
		JToolbarHelper::custom('import', 'upload', '', JText::_('COM_EASYBLOG_IMPORT_SETTINGS'), false);
	}
}
