<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewTags extends EasyBlogAdminView
{
	/**
	 * Displays a list of tags on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.tag');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		$this->setHeading('COM_EASYBLOG_TITLE_TAGS', '', 'fa-tags');

		$filter_state	= $this->app->getUserStateFromRequest( 'com_easyblog.tags.filter_state', 'filter_state', '*', 'word' );
		$search			= $this->app->getUserStateFromRequest( 'com_easyblog.tags.search', 'search', '', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $this->app->getUserStateFromRequest( 'com_easyblog.tags.filter_order', 'filter_order', 'ordering', 'cmd' );
		$orderDirection	= $this->app->getUserStateFromRequest( 'com_easyblog.tags.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		//Get data from the model
		$model				= EB::model('Tags');
		$tags				= $model->getData();

		for( $i = 0 ; $i < count( $tags ); $i++ )
		{
			$tag		= $tags[ $i ];
			$tag->count	= $model->getUsedCount( $tag->id );
			$tag->title	= JString::trim($tag->title);
			$tag->alias	= JString::trim($tag->alias);
		}
		$pagination		= $this->get( 'Pagination' );

		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction	= JRequest::getVar('browsefunction', 'insertTag');

		$this->set( 'browse' , $browse );
		$this->set( 'browsefunction' , $browsefunction );
		$this->set( 'tags' 		, $tags );
		$this->set( 'pagination'	, $pagination );
		$this->set( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->set( 'search'			, $search );
		$this->set( 'order'			, $order );
		$this->set( 'orderDirection'	, $orderDirection );

		parent::display('tags/default');
	}

	/**
	 * Displays the tag form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function form()
	{
		// Get the tag id
		$id = $this->input->get('id', 0, 'int');

		// Load the tag
		$tag = EB::table('Tag');
		$tag->load($id);

		$title = 'COM_EASYBLOG_TAGS_NEW_TAG_TITLE';

		if ($tag->id) {
			$title = 'COM_EASYBLOG_TAGS_EDIT_TAG_TITLE';
		}

		JToolBarHelper::title(JText::_($title), 'tags');

		$this->setHeading($title, '', 'fa-tags');

		$tag->title = JString::trim($tag->title);
		$tag->alias = JString::trim($tag->alias);

		// Set default values for new entries.
		if (!$tag->created) {
			$tag->published	= true;
		}

		$this->set('tag', $tag);

		parent::display('tags/form');
	}

	/**
	 * Displays the Joomla toolbar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function registerToolbar()
	{
		$layout = $this->getLayout();

		if ($layout == 'form') {
			JToolBarHelper::apply('tags.save');
			JToolBarHelper::save('tags.saveclose');
			JToolBarHelper::custom('tags.savenew', 'save.png', 'save_f2.png', JText::_('COM_EASYBLOG_SAVE_NEW_BUTTON'), false);
			JToolBarHelper::divider();
			JToolBarHelper::cancel('tags.cancel');

			return;
		}

		JToolBarHelper::title(JText::_('COM_EASYBLOG_TAGS'), 'tags');

		JToolbarHelper::addNew('tags.new');
		JToolBarHelper::divider();
		JToolbarHelper::publishList('tags.publish');
		JToolbarHelper::unpublishList('tags.unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'tags.setDefault' , 'star' , '' , JText::_( 'COM_EASYBLOG_MAKE_DEFAULT' ) );
		JToolBarHelper::custom( 'tags.removeDefault' , 'star-empty' , '' , JText::_( 'COM_EASYBLOG_REMOVE_DEFAULT' ) );
		JToolBarHelper::divider();
		JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_DELETE_TAGS_CONFIRMATION'), 'tags.delete');

	}
}
