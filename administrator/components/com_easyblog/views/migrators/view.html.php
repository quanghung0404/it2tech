<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewMigrators extends EasyBlogAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		$this->checkAccess('easyblog.manage.migrator');

		$this->setHeading('COM_EASYBLOG_TITLE_MIGRATORS', '', 'fa-laptop');

		$layout 	= $this->getLayout();

		$this->set('config', $this->config);

		$htmlcontent = '';

		if (method_exists($this, $layout)) {
			$htmlcontent = $this->$layout();
		}

		$this->set('htmlcontent', $htmlcontent);

		parent::display('migrators/default');
	}

	public function blogger()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_BLOGGERXML', '', 'fa-laptop');

		$bloggerxmlfiles  = $this->getXMLFiles( 'blogger' );
		$lists	= JHTML::_('select.genericlist',  $bloggerxmlfiles, 'bloggerxmlfiles', 'class="form-control" data-xml-blogger', 'value', 'state', '');

		$theme = EB::template();

		$ebCategories = $this->getEasyBlogCategories();

		$theme->set('categories', $ebCategories);
		$theme->set('lists', $lists);
		$output = $theme->output('admin/migrators/adapters/blogger');

		return $output;
		// parent::display('migrators/adapters/blogger');
	}

	public function k2()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_K2', '', 'fa-retweet');
		$k2Installed		= $this->k2Exists();
		$lists		= $this->getK2Categories();

		$theme = EB::template();

		$theme->set('lists', $lists);
		$theme->set('k2Installed', $k2Installed);

		$output = $theme->output('admin/migrators/adapters/k2');
		return $output;

		// parent::display('migrators/adapters/k2');
	}

	/**
	 * Generates the wordpress migrator form.
	 *
	 * @since	5.0.28
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function wordpress()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_WORDPRESS_IMPORTXML', '', 'fa-wordpress');

		// get wp xml files
		$wpxmlfiles = $this->getXMLFiles('wordpress');
		$lists = JHTML::_('select.genericlist',  $wpxmlfiles, 'wpxmlfiles', 'class="form-control" data-xml-wordpress', 'value', 'state', '');

		$theme = EB::template();
		$theme->set('lists', $lists);

		$output = $theme->output('admin/migrators/adapters/wordpress');
		
		return $output;
	}

	public function wordpressjoomla()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_WORDPRESSJOOMLA', '', 'fa-file-word-o');

		//check if wordpress installed or not.
		$lists	= array();
		$wpInstalled		= $this->wpExists();
		$wpBlogsList		= '';

		if ($wpInstalled) {
			$wpBlogsList		= $this->getWPBlogs();
			$lists	= JHTML::_('select.genericlist',  $wpBlogsList, 'wpBlogId', 'class="form-control" data-blogid-wordpress', 'value', 'state', '');
		}

		$theme = EB::template();

		$theme->set('wpInstalled', $wpInstalled);
		$theme->set('wpBlogsList', $wpBlogsList);
		$theme->set('lists', $lists);

		$output = $theme->output('admin/migrators/adapters/wordpressjoomla');
		return $output;

		// parent::display('migrators/adapters/wordpressjoomla');
	}

	public function zoo()
	{
		$db	= EB::db();
		$path	= JPATH_ROOT . '/administrator/components/com_zoo';

		$this->setHeading('COM_EASYBLOG_MIGRATOR_ZOO', '', 'fa-retweet');

		jimport('joomla.filesystem.folder');
		$zooInstalled	= true;
		$htmlList	= array();

		$theme = EB::template();

		if (!JFolder::exists($path)) {
			$zooInstalled	= false;
		}

		if ($zooInstalled) {
			$query	= 'SELECT * FROM `#__zoo_application`';
			$query	.= ' WHERE `application_group` = '.$db->quote( 'blog' );

			$db->setQuery($query);
			$items	= $db->loadObjectList();

			$htmlList[]		= JHTML::_( 'select.option' , '0' , ' -- Please select Application -- ' , 'value' , 'state' );

			if (count($items) > 0) {
				foreach( $items as $item)
				{
					$htmlList[]	= JHTML::_('select.option', $item->id, $item->name, 'value', 'state');
				}
			}

		}

		$lists	= JHTML::_('select.genericlist',  $htmlList, 'zooAppId', 'class="form-control" data-applicationid-zoo', 'value', 'state', '');

		$theme = EB::template();

		$theme->set('zooInstalled', $zooInstalled);
		$theme->set('lists', $lists);

		$output = $theme->output('admin/migrators/adapters/zoo');
		return $output;

		// parent::display('migrators/adapters/zoo');
	}

	public function joomla()
	{
		$this->setHeading('COM_EASYBLOG_MIGRATOR_JOOMLA', '', 'fa-joomla');

		$categories[]	= JHTML::_('select.option', '0', '- '.JText::_('COM_EASYBLOG_MIGRATORS_SELECT_CATEGORY').' -');
		$authors[]		= JHTML::_('select.option', '0', '- '.JText::_('COM_EASYBLOG_MIGRATORS_SELECT_AUTHOR').' -', 'created_by', 'name');

		$lists['sectionid'] = array();

		$articleCat		= JHtml::_('category.options', 'com_content');

		$articleAuthors	= $this->get( 'ArticleAuthors16' );

		$categories		= array_merge($categories, $articleCat);
		$lists['catid'] = JHTML::_('select.genericlist',  $categories, 'catId', 'class="form-control" data-migrate-article-category', 'value', 'text', '');

		$authors 	= array_merge($authors, $articleAuthors);
		$lists['authorid'] = JHTML::_('select.genericlist',  $authors, 'authorId', 'class="form-control" data-migrate-article-author', 'created_by', 'name', 0);

		// state filter
		$state			= $this->getDefaultState();
		$articleState	= array();
		foreach($state as $key => $val)
		{
			$obj		= new stdClass();
			$obj->state	= $val;
			$obj->value	= $key;

			$articleState[]	= $obj;
		}

		$stateList		= array();
		$stateList[]	= JHTML::_('select.option', '*', '- '.JText::_('COM_EASYBLOG_MIGRATORS_SELECT_STATE').' -', 'value', 'state');

		$stateList		= array_merge($stateList, $articleState);
		$lists['state']	= JHTML::_('select.genericlist',  $stateList, 'stateId', 'class="form-control" data-migrate-article-state', 'value', 'state', '*');

		$jomcommentInstalled	= $this->jomcommentExists();

		//check if myblog installed or not.
		$myblogInstalled	= $this->myBlogExists();
		$myBlogSection		= '';

		if ($myblogInstalled) {
			require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_myblog' . DIRECTORY_SEPARATOR . 'config.myblog.php');
			$myblogConfig	= new MYBLOG_Config();
			$myBlogSection	= $myblogConfig->get('postSection');
		}

		$ebCategories		= $this->getEasyBlogCategories();

		$theme = EB::template();

		$theme->set( 'myBlogSection' , $myBlogSection );
		$theme->set('jomcommentInstalled', $jomcommentInstalled);
		$theme->set('lists', $lists);
		$theme->set('ebCategories', $ebCategories);

		$output = $theme->output('admin/migrators/adapters/article');

		return $output;
		// parent::display('migrators/adapters/article');
	}

	public function section($excludeSection='', $name, $active = NULL, $javascript = NULL, $order = 'ordering', $uncategorized = true, $scope = 'content' )
	{
		$db = EasyBlogHelper::db();

		$categories[] = JHTML::_('select.option',  '-1', '- '. JText::_( 'COM_EASYBLOG_MIGRATORS_SELECT_SECTION' ) .' -' );

		if ($uncategorized) {
			$categories[] = JHTML::_('select.option',  '0', JText::_( 'Uncategorized' ) );
		}

		$excludeSQL = '';
		if (!empty($excludeSection)) {
			$excludeSQL = ' AND id != ' . $db->Quote($excludeSection);
		}

		$query = 'SELECT id AS value, title AS text'
		. ' FROM #__sections'
		. ' WHERE published = 1'
		. ' AND scope = ' . $db->Quote($scope)
		. $excludeSQL
		. ' ORDER BY ' . $order;

		$db->setQuery($query);
		$sections = array_merge($categories, $db->loadObjectList());

		$category = JHTML::_('select.genericlist',   $sections, $name, 'class="form-control" '. $javascript, 'value', 'text', $active);

		return $category;
	}

	public function getDefaultState()
	{
		$state			= null;
		if (EasyBlogHelper::getJoomlaVersion() >= '1.6') {
			$state = array('1' => 'Published', '0' => 'Unpublished', '2' => 'Archived', '-2' => 'Trash');
		}
		else {
			$state = array('P' => 'Published', 'U' => 'Unpublished', 'A' => 'Archived');
		}
		return $state;
	}

	public function k2Exists()
	{
		$path		= JPATH_ROOT . '/administrator/components/com_k2';
		return JFolder::exists($path);
	}

	public function getWPBlogs()
	{
		$db = EasyBlogHelper::db();

		$query = 'select * from `#__wp_posts` where `post_type` = ' . $db->Quote('post');
		$db->setQuery($query);

		$result = $db->loadObjectList();

		$htmlList = array();
		if (count($result) > 0) {
			$htmlList[]	= JHTML::_('select.option', '0', 'All', 'value', 'state');
			foreach ($result as $item) {
				$htmlList[]	= JHTML::_('select.option', $item->ID, $item->post_title, 'value', 'state');
			}
		}

		if (count($htmlList) <= 0) {
			//this could be single site wordpress.
			$query  = 'SHOW TABLES LIKE ' . $db->Quote( '%wp_posts%' );
			$db->setQuery( $query );

			$result = $db->loadObjectList();
			if (count( $result ) > 0) {
				$htmlList[]	= JHTML::_('select.option', '1', 'Single site WordPress', 'value', 'state');
			}
		}

		return $htmlList;
	}

	public function getEasyBlogCategories()
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT id, title FROM `#__easyblog_category` where `published` = ' . $db->Quote('1');
		$db->setQuery( $query );
		$items	= $db->loadObjectList();

		$lists	= array();

		//default list
		$lists[]	= JHTML::_( 'select.option' , '' , ' -- Please select category -- ' , 'value' , 'state' );

		foreach ($items as $item) {
			$lists[]	= JHTML::_( 'select.option' , $item->id , $item->title , 'value' , 'state' );
		}

		return JHTML::_('select.genericlist',  $lists , 'categoryid', 'class="form-control" data-easyblog-category ', 'value', 'state', '');
	}

	public function jomcommentExists()
	{
		$path		= JPATH_ROOT . '/administrator/components/com_jomcomment';
		return JFolder::exists($path);
	}

	public function myBlogExists()
	{
		$file	= JPATH_ROOT . 'administrator/components/com_myblog/config.myblog.php';
		if (!JFile::exists($file)) {
			return false;
		}
		return true;
	}



	public function getK2Categories()
	{
		$db	= EasyBlogHelper::db();

		jimport( 'joomla.filesystem.folder' );
		$path		= JPATH_ROOT . '/administrator/components/com_k2';

		if (!JFolder::exists($path)) {
			return false;
		}

		$query	= 'SELECT * FROM `#__k2_categories`';
		$db->setQuery( $query );
		$items	= $db->loadObjectList();

		if (!$items) {
			return false;
		}

		$lists	= array();

		foreach ($items as $item) {
			$lists[]	= JHTML::_( 'select.option' , $item->id , $item->name , 'value' , 'state' );
		}

		return JHTML::_('select.genericlist',  $lists , 'k2category', 'class="form-control" data-migrate-k2-category', 'value', 'state', '');
	}

	public function getXMLFiles( $type = 'wordpress' )
	{
		$fixedLocation	= JPATH_ROOT . '/administrator/components/com_easyblog/xmlfiles';

		if ($type == 'blogger')
			$fixedLocation .= '/blogger';

		$htmlList		= array();

		if (JFolder::exists($fixedLocation)) {
			$files	= JFolder::files( $fixedLocation, '.xml');

			if(count($files) > 0) {
				foreach( $files as $file)
				{
					$htmlList[]	= JHTML::_('select.option', $file, $file , 'value', 'state');
				}
			}
		}
		return $htmlList;
	}

	public function wpExists()
	{
		$file	= JPATH_ROOT . '/administrator/components/com_wordpress/admin.wordpress.php';
		if (!JFile::exists($file)) {
			return false;
		}
		return true;
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_MIGRATORS' ), 'migrators' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolBarHelper::custom( 'migrators.purge', 'delete.png', 'delete_f2.png', JText::_( 'COM_EASYBLOG_PURGE_HISTORY') , false );
	}

	public function registerSubmenu()
	{
		return 'submenu.php';
	}
}
