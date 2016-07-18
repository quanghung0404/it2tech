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

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerInstallPost extends EasyBlogSetupController
{
	/**
	 * Post installation process
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function execute()
	{
		$results = array();

		// Get the api key so that we can store it
		$key = $this->input->get('apikey', '', 'default');

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_EASYBLOG_INSTALLATION_DEVELOPER_MODE', true));
		}

		// ACL rules needs to be created first before anything else
		$results[] = $this->updateACL();

		// Create site menu
		$results[] = $this->createDefaultMenu('site');

		// Create blog category
		$results[] = $this->createDefaultCategory();

		// Check and assign default blog category.
		$results[] = $this->assignDefaultCategory();

		// Install blocks
		$results[] = $this->installBlocks();

		// Create sample post
		$results[] = $this->createSamplePost();

		// Install post templates
		$results[] = $this->installPostTemplates();

		// Unpublish old Easyblog module
		//$this->unpublishOldModule();

		// unpublish old easyblog plugins
		$this->unpublishOldPlugin();

		$message = '';

		foreach ($results as $obj) {

			if ($obj === false) {
				continue;
			}

			$class = $obj->state ? 'success' : 'error';
			$message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		$this->setInfo($message, true);
		return $this->output();
	}

	/**
	 * Install blocks on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installBlocks()
	{
		$this->engine();

		// Get the path to the package file
		$path = $this->input->get('path', '', 'default');

		// Construct to the place where we store all the blocks
		$path = $path . '/admin/defaults/blocks';

		// @debug
		$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/defaults/blocks';

		// Retrieve the list of files of each blocks
		$files = JFolder::files($path, '.', true, true);

		foreach ($files as $file) {
			$block = json_decode(JFile::read($file));

			$table = EB::table('Block');
			$table->load(array('element' => $block->element));

			$table->bind($block);

			// Save the block
			$table->store();
		}

		return $this->getResultObj(JText::_('COM_EASYBLOG_INSTALLATION_COMPOSER_BLOCKS_INITIALIZED'), true);
	}


	/**
	 * Install blocks on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function installPostTemplates()
	{
		$this->engine();

		// Get the path to the package file
		$path = $this->input->get('path', '', 'default');

		// Construct to the place where we store all the post templates
		$path = $path . '/admin/defaults/post_templates';

		// // @debug
		$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/defaults/post_templates';

		// Retrieve the list of files of each blocks
		$files = JFolder::files($path, '.', true, true);

		$my = JFactory::getUser();
		$db = EB::db();

		foreach ($files as $file) {

			$template = json_decode(JFile::read($file));

			$table = EB::table('PostTemplate');
			$state = $table->load($template->id);

			$table->bind($template);
			$table->data = json_encode($template->data);
			$table->user_id = $my->id;
			$table->created = EB::date()->toSql();
			$table->published = 1;
			$table->core = 1;


			if ($state) {
				// Save the block
				$table->store();

			} else {
				// create new
				$db->insertObject('#__easyblog_post_templates', $table);
			}

		}

		return $this->getResultObj(JText::_('COM_EASYBLOG_INSTALLATION_COMPOSER_POST_TEMPLATES_INITIALIZED'), true);
	}

	/**
	 * Retrieves the main menu item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMainMenuType()
	{
		$this->engine();

		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->quoteName('menutype') . ' FROM ' . $db->quoteName('#__menu');
		$query[] = 'WHERE ' . $db->quoteName('home') . '=' . $db->Quote(1);
		$query = implode(' ', $query);

		$db->setQuery($query);
		$menuType = $db->loadResult();

		return $menuType;
	}


	/**
	 * Create a new default blog menu
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createDefaultMenu()
	{
		// Include foundry framework
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return;
		}

		$db = JFactory::getDBO();

		$query = array();
		$query[] = 'SELECT ' . $db->quoteName('extension_id') . ' FROM ' . $db->quoteName('#__extensions');
		$query[] = 'WHERE ' . $db->quoteName('element') . '=' . $db->Quote('com_easyblog');
		$query = implode(' ', $query);

		$db->setQuery($query);

		// Get the extension id
		$extensionId = $db->loadResult();

		// Get the main menu that is used on the site.
		$menuType = $this->getMainMenuType();

		if (!$menuType) {
			return false;
		}

		// Get any menu items that are already created with com_easyblog
		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__menu');
		$query[] = 'WHERE ' . $db->quoteName('link') . ' LIKE(' . $db->Quote('%index.php?option=com_easyblog%') . ')';
		$query[] = 'AND ' . $db->quoteName('type') . '=' . $db->Quote('component');
		$query[] = 'AND ' . $db->quoteName('client_id') . '=' . $db->Quote(0);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$exists	= $db->loadResult();

		// If menu already exists, we need to ensure that all the existing menu's are now updated with the correct extension id
		if ($exists) {

			$query = array();
			$query[] = 'UPDATE ' . $db->quoteName('#__menu') . ' SET ' . $db->quoteName('component_id') . '=' . $db->Quote($extensionId);
			$query[] = 'WHERE ' . $db->quoteName('link') . ' LIKE (' . $db->Quote('%index.php?option=com_easyblog%') . ')';
			$query[] = 'AND ' . $db->quoteName('type') . '=' . $db->Quote('component');
			$query[] = 'AND ' . $db->quoteName('client_id') . '=' . $db->Quote(0);

			$query = implode(' ', $query);
			$db->setQuery($query);
			$db->Query();

			return $this->getResultObj(JText::_('COM_EASYBLOG_INSTALLATION_SITE_MENU_UPDATED'), true);
		}

		$menu = JTable::getInstance('Menu');
		$menu->menuType = $menuType;
		$menu->title = JText::_('COM_EASYBLOG_INSTALLATION_DEFAULT_MENU_BLOG');
		$menu->alias = 'blog';
		$menu->path = 'easyblog';
		$menu->link = 'index.php?option=com_easyblog&view=latest';
		$menu->type = 'component';
		$menu->published = 1;
		$menu->parent_id = 1;
		$menu->component_id = $extensionId;
		$menu->client_id = 0;
		$menu->language = '*';

		$menu->setLocation('1', 'last-child');

		$state 	= $menu->store();

		return $this->getResultObj(JText::_('COM_EASYBLOG_INSTALLATION_SITE_MENU_CREATED'), true );
	}

	/**
	 * Create a default category for the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createDefaultCategory()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return;
		}

		// Check if there are already categories created
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_category');
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total > 0) {
			return false;
		}

		$my = JFactory::getUser();

		$category = EB::table('Category');
		$category->title = JText::_('COM_EASYBLOG_DEFAULT_CATEGORY_TITLE');
		$category->alias = 'uncategorized';
		$category->created_by = $my->id;
		$category->created = EB::date()->toSql();
		$category->status = true;
		$category->published = 1;
		$category->ordering = 1;
		$category->lft = 1;
		$category->rgt = 2;
		$category->default = 1;

		$category->store();
		return $this->getResultObj(JText::_('COM_EASYBLOG_INSTALLATION_DEFAULT_CATEGORY_CREATED'), true );
	}


	/**
	 * Check and assign a default category for the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function assignDefaultCategory()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return;
		}

		// Check if there are already categories created
        $state = true;
		$db = EB::db();

        // lets check if there is any default category assigned or not.
        $query = "select a.`id` from `#__easyblog_category` as a where a.`published` = 1 and a.`default` = 1";
        $db->setQuery($query);

        $result = $db->loadResult();

        if (! $result) {
            $query = "select a.`id`, count(b.`id`) as `cnt` from `#__easyblog_category` as a";
            $query .= " left join `#__easyblog_post_category` as b on a.`id` = b.`category_id`";
            $query .= " where a.`published` = 1";
            $query .= " group by a.`id`";
            $query .= " order by cnt desc";
            $query .= " limit 1";

            $db->setQuery($query);
            $id = $db->loadResult();

            // now we make sure no other categories which previously marked as default but its unpublished.
            $update = "update `#__easyblog_category` set `default` = 0";
            $db->setQuery($update);

            // now let update this category as default category
            $update = "update `#__easyblog_category` set `default` = 1 where `id` = " . $db->Quote($id);
            $db->setQuery($update);
            $state = $db->query();
        }

        $msg = 'COM_EASYBLOG_INSTALLATION_DEFAULT_CATEGORY_ASSIGNED';
        if (! $state) {
        	$mg = 'COM_EASYBLOG_INSTALLATION_DEFAULT_CATEGORY_ASSIGN_FAILED';
        }

		return $this->getResultObj(JText::_($msg), true );
	}



	/**
	 * Create a sample post on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createSamplePost()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return;
		}

		// Check if there are already categories created
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_post');
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total > 0) {
			return false;
		}

		$my = JFactory::getUser();

		$post = EB::post();

		$data = new stdClass();

		$data->title = JText::_('You have successfully installed EasyBlog');
		$data->permalink = 'easyblog-installed-successfully';
		// $data->content =  '<div class="ebd-block " data-type="html" >'
		// 				. ' <p><img src="http://stackideas.com/images/eblog/install_success5.png"></p>'
		// 				. '</div><div class="ebd-block " data-type="text" >With EasyBlog, you can be assured of quality blogging with the following features:<span class="redactor-invisible-space">​</span></div><div class="ebd-block " data-type="rule" ><hr></div><div class="ebd-block " data-type="columns" ><div class="row" data-responsive="400,300,200,100">'
  //   					. '<div class="col col-md-4" data-size="6">'
  //   					. '	<div class="ebd-block  is-nested" data-type="youtube" ><div class="youtube-embed video-embed-wrapper is-responsive">'
  //   					. ' <iframe src="https://www.youtube.com/embed/f-YEli-NK-w?feature=oembed" width="480" height="270"></iframe>'
		// 				. '</div></div></div>'
  //   					. '<div class="col col-md-8" data-size="6">'
  //   					. '<div class="ebd-block  is-nested" data-type="heading" ><h4>Drag and Drop Blocks<br><br></h4></div><div class="ebd-block  is-nested" data-type="text" >Add elements to your blog with a simple drag and drop element blocks.<span class="redactor-invisible-space">​</span></div></div>'
		// 				. '</div></div><div class="ebd-block " data-type="rule" ><hr></div><div class="ebd-block " data-type="columns" ><div class="row" data-responsive="400,300,200,100">'
  //   					. '<div class="col col-md-1" data-size="6">'
  //   					. '</div>'
  //   					. '<div class="col col-md-8" data-size="6">'
  //   					. '<div class="ebd-block  is-nested" data-type="heading" ><h4>Blog now, post later<br></h4></div><div class="ebd-block  is-nested" data-type="text" >You can compose a blog now, suffer temporal writer\'s block, save and write again, later.<span class="redactor-invisible-space">​</span></div></div>'
		// 				. '</div></div><div class="ebd-block " data-type="columns" ><div class="row" data-responsive="400,300,200,100">'
  //   					. '<div class="col col-md-1" data-size="6">'
  //   					. '</div>'
  //   					. '<div class="col col-md-6" data-size="6">'
  //   					. '<div class="ebd-block  is-nested" data-type="heading" ><h4><br>Social media sharing<br></h4></div><div class="ebd-block  is-nested" data-type="text" >Automatically post into your Twitter, Facebook and LinkedIn whenever you create new blog entries.<span class="redactor-invisible-space">​</span></div></div>'
		// 				. '</div></div><div class="ebd-block " data-type="columns" ><div class="row" data-responsive="400,300,200,100">'
  //   					. '<div class="col col-md-1" data-size="6">'
  //   					. '</div>'
  //   					. '<div class="col col-md-6" data-size="6">'
  //   					. '<div class="ebd-block  is-nested" data-type="heading" ><h4><br>Browse media<br></h4></div><div class="ebd-block  is-nested" data-type="text" >Embedding images and videos is fast and easy.<span class="redactor-invisible-space">​</span></div></div>'
		// 				. '</div></div><div class="ebd-block " data-type="columns" ><div class="row" data-responsive="400,300,200,100">'
  //   					. '<div class="col col-md-1" data-size="6">'
  //   					. '</div>'
  //   					. '<div class="col col-md-6" data-size="6">'
  //   					. '<div class="ebd-block  is-nested" data-type="heading" ><h4><br>More third party integrations<br></h4></div><div class="ebd-block  is-nested" data-type="text" >Having other Joomla! plugins and extensions to work with EasyBlog is just a few clicks away.<span class="redactor-invisible-space">​</span></div></div>'
		// 				. '</div></div><div class="ebd-block " data-type="columns" ><div class="row" data-responsive="400,300,200,100">'
  //   					. '<div class="col col-md-1" data-size="6">'
  //   					. '</div>'
  //   					. '<div class="col col-md-6" data-size="6">'
  //   					. '<div class="ebd-block  is-nested" data-type="heading" ><h4><br>Blog rating<br></h4></div><div class="ebd-block  is-nested" data-type="text" >Users can show intensity of their favorite blog post by rating them with stars.<span class="redactor-invisible-space">​</span></div></div>'
		// 				. '</div></div><div class="ebd-block " data-type="rule" ><hr></div><div class="ebd-block " data-type="text" ><p>And many more powerful features that you can use to make your blog work beautifully and professionally. Need any help? Drop by our <a target="_blank" class="" title="" href="http://stackideas.com/forums/index/easyblog">Official forum</a> or send our support team a ticket via our <a target="_blank" class="" title="" href="https://crm.stackideas.com">CRM</a>. You can even check out <a target="_blank" class="" title="" href="http://stackideas.com/docs/easyblog">EasyBlog\'s Documentation</a>.</p></div>';

		$data->content = '';
		$data->intro = '';

		$data->document = '{"type":"ebd","blocks":[{"uid":"07094988151957076","type":"html","html":"<p><img src=\"http:\/\/stackideas.com\/images\/eblog\/install_success5.png\">\n    \n<\/p>","data":{},"blocks":[],"nested":false,"isolated":false,"text":"\n    \n","editableHtml":"<p><img src=\"http:\/\/stackideas.com\/images\/eblog\/install_success5.png\">\n    \n<\/p>"},{"uid":"03040071225259453","type":"text","html":"With EasyBlog, you can be assured of quality blogging with the following features:<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":false,"isolated":false,"text":"With EasyBlog, you can be assured of quality blogging with the following features:\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">With EasyBlog, you can be assured of quality blogging with the following features:<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"},{"uid":"0346444247988984","type":"rule","html":"\n            <hr>        ","data":{},"blocks":[],"nested":false,"isolated":false,"text":"","editableHtml":"\n            <hr>        "},{"uid":"05725746976677328","type":"columns","html":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-4\" data-size=\"6\">\n        \n    <!--block06016321050556486--><\/div>\n    <div class=\"col col-md-8\" data-size=\"6\">\n        \n    <!--block029067436303012073--><!--block038569004484452307--><\/div>\n<\/div>        ","data":{"columns":[{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"},{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"}]},"blocks":[{"uid":"06016321050556486","type":"youtube","html":"<iframe src=\"https:\/\/www.youtube.com\/embed\/f-YEli-NK-w?feature=oembed\" allowfullscreen=\"\" frameborder=\"0\" height=\"270\" width=\"480\"><\/iframe>","data":{"author":{"name":"StackIdeas","url":"http:\/\/www.youtube.com\/user\/stackideas"},"url":"https:\/\/www.youtube.com\/watch?v=f-YEli-NK-w","width":480,"height":270,"fluid":true,"embed":"<iframe width=\"480\" height=\"270\" src=\"https:\/\/www.youtube.com\/embed\/f-YEli-NK-w?feature=oembed\" frameborder=\"0\" allowfullscreen><\/iframe>","source":"https:\/\/www.youtube.com\/embed\/f-YEli-NK-w?feature=oembed"},"blocks":[],"nested":true,"isolated":false,"text":"https:\/\/www.youtube.com\/embed\/f-YEli-NK-w?feature=oembed","editableHtml":""},{"uid":"029067436303012073","type":"heading","html":"\n            <h4>Drag and Drop Blocks<br><br><\/h4>        ","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        \n            Drag and Drop Blocks        \n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"\n            <h4 contenteditable=\"true\">Drag and Drop Blocks<br><br><\/h4>        "},{"uid":"038569004484452307","type":"text","html":"Add elements to your blog with a simple drag and drop element blocks.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Add elements to your blog with a simple drag and drop element blocks.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Add elements to your blog with a simple drag and drop element blocks.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"},{"uid":"06016321050556486","type":"youtube","html":"<iframe src=\"https:\/\/www.youtube.com\/embed\/f-YEli-NK-w?feature=oembed\" allowfullscreen=\"\" frameborder=\"0\" height=\"270\" width=\"480\"><\/iframe>","data":{"author":{"name":"StackIdeas","url":"http:\/\/www.youtube.com\/user\/stackideas"},"url":"https:\/\/www.youtube.com\/watch?v=f-YEli-NK-w","width":480,"height":270,"fluid":true,"embed":"<iframe width=\"480\" height=\"270\" src=\"https:\/\/www.youtube.com\/embed\/f-YEli-NK-w?feature=oembed\" frameborder=\"0\" allowfullscreen><\/iframe>","source":"https:\/\/www.youtube.com\/embed\/f-YEli-NK-w?feature=oembed"},"blocks":[],"nested":true,"isolated":false,"text":"https:\/\/www.youtube.com\/embed\/f-YEli-NK-w?feature=oembed","editableHtml":""},{"uid":"029067436303012073","type":"heading","html":"\n            <h4>Drag and Drop Blocks<br><br><\/h4>        ","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        \n            Drag and Drop Blocks        \n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"\n            <h4 contenteditable=\"true\">Drag and Drop Blocks<br><br><\/h4>        "},{"uid":"038569004484452307","type":"text","html":"Add elements to your blog with a simple drag and drop element blocks.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Add elements to your blog with a simple drag and drop element blocks.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Add elements to your blog with a simple drag and drop element blocks.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"}],"nested":false,"isolated":false,"text":"\n            \n    \n        \n    \n        \n    \n    \n        \n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n\n\n            \n        \n    \n    \n        \n    \n        \n    \n    \n        \n            Drag and Drop Blocks        \n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n\n\n            \n    \n        \n    \n    \n        Add elements to your blog with a simple drag and drop element blocks.\u200b\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n\n\n\n\n        \n    \n        ","editableHtml":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-4\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\"><!--block06016321050556486-->\n\n\n            \n        <\/div>\n    <\/div>\n    <div class=\"col col-md-8\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\"><!--block029067436303012073-->\n\n\n            <!--block038569004484452307-->\n\n\n\n\n        <\/div>\n    <\/div>\n<\/div>        "},{"uid":"026837448799051344","type":"rule","html":"\n            <hr>        ","data":{},"blocks":[],"nested":false,"isolated":false,"text":"","editableHtml":"\n            <hr>        "},{"uid":"0046830407343804836","type":"columns","html":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        \n    <\/div>\n    <div class=\"col col-md-8\" data-size=\"6\">\n        \n    <!--block08473397800698876--><!--block07279896603431553--><\/div>\n<\/div>        ","data":{"columns":[{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"},{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"}]},"blocks":[{"uid":"08473397800698876","type":"heading","html":"\n            <h4>Blog now, post later<br><\/h4>        ","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        \n            Blog now, post later        \n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"\n            <h4 contenteditable=\"true\">Blog now, post later<br><\/h4>        "},{"uid":"07279896603431553","type":"text","html":"You can compose a blog now, suffer temporal writer\'s block, save and write again, later.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"You can compose a blog now, suffer temporal writer\'s block, save and write again, later.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">You can compose a blog now, suffer temporal writer\'s block, save and write again, later.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"},{"uid":"08473397800698876","type":"heading","html":"\n            <h4>Blog now, post later<br><\/h4>        ","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        \n            Blog now, post later        \n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"\n            <h4 contenteditable=\"true\">Blog now, post later<br><\/h4>        "},{"uid":"07279896603431553","type":"text","html":"You can compose a blog now, suffer temporal writer\'s block, save and write again, later.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"You can compose a blog now, suffer temporal writer\'s block, save and write again, later.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">You can compose a blog now, suffer temporal writer\'s block, save and write again, later.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"}],"nested":false,"isolated":false,"text":"\n            \n    \n        \n\n\n            \n        \n    \n    \n        \n    \n        \n    \n    \n        \n            Blog now, post later        \n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n\n\n            \n    \n        \n    \n    \n        You can compose a blog now, suffer temporal writer\'s block, save and write again, later.\u200b\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n\n\n\n\n        \n    \n        ","editableHtml":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\">\n\n\n            \n        <\/div>\n    <\/div>\n    <div class=\"col col-md-8\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\"><!--block08473397800698876-->\n\n\n            <!--block07279896603431553-->\n\n\n\n\n        <\/div>\n    <\/div>\n<\/div>        "},{"uid":"034305927456222685","type":"columns","html":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        \n    <\/div>\n    <div class=\"col col-md-6\" data-size=\"6\">\n        \n    <!--block09808841066849929--><!--block036551126970772363--><\/div>\n<\/div>        ","data":{"columns":[{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"},{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"}]},"blocks":[{"uid":"09808841066849929","type":"heading","html":"<h4><br>Social media sharing<br><\/h4>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        Social media sharing\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"<h4 contenteditable=\"true\"><br>Social media sharing<br><\/h4>"},{"uid":"036551126970772363","type":"text","html":"Automatically post into your Twitter, Facebook and LinkedIn whenever you create new blog entries.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Automatically post into your Twitter, Facebook and LinkedIn whenever you create new blog entries.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Automatically post into your Twitter, Facebook and LinkedIn whenever you create new blog entries.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"},{"uid":"09808841066849929","type":"heading","html":"<h4><br>Social media sharing<br><\/h4>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        Social media sharing\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"<h4 contenteditable=\"true\"><br>Social media sharing<br><\/h4>"},{"uid":"036551126970772363","type":"text","html":"Automatically post into your Twitter, Facebook and LinkedIn whenever you create new blog entries.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Automatically post into your Twitter, Facebook and LinkedIn whenever you create new blog entries.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Automatically post into your Twitter, Facebook and LinkedIn whenever you create new blog entries.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"}],"nested":false,"isolated":false,"text":"\n            \n    \n        \n            \n        \n    \n    \n        \n    \n        \n    \n    \n        Social media sharing\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n            \n    \n        \n    \n    \n        Automatically post into your Twitter, Facebook and LinkedIn whenever you create new blog entries.\u200b\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n        \n    \n        ","editableHtml":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\">\n            \n        <\/div>\n    <\/div>\n    <div class=\"col col-md-6\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\"><!--block09808841066849929-->\n            <!--block036551126970772363-->\n        <\/div>\n    <\/div>\n<\/div>        "},{"uid":"04238469158738458","type":"columns","html":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        \n    <\/div>\n    <div class=\"col col-md-6\" data-size=\"6\">\n        \n    <!--block039449238301486433--><!--block0558766089683463--><\/div>\n<\/div>        ","data":{"columns":[{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"},{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"}]},"blocks":[{"uid":"039449238301486433","type":"heading","html":"<h4><br>Browse media<br><\/h4>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        Browse media\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"<h4 contenteditable=\"true\"><br>Browse media<br><\/h4>"},{"uid":"0558766089683463","type":"text","html":"Embedding images and videos is fast and easy.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Embedding images and videos is fast and easy.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Embedding images and videos is fast and easy.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"},{"uid":"039449238301486433","type":"heading","html":"<h4><br>Browse media<br><\/h4>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        Browse media\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"<h4 contenteditable=\"true\"><br>Browse media<br><\/h4>"},{"uid":"0558766089683463","type":"text","html":"Embedding images and videos is fast and easy.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Embedding images and videos is fast and easy.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Embedding images and videos is fast and easy.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"}],"nested":false,"isolated":false,"text":"\n            \n    \n        \n            \n        \n    \n    \n        \n    \n        \n    \n    \n        Browse media\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n            \n    \n        \n    \n    \n        Embedding images and videos is fast and easy.\u200b\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n        \n    \n        ","editableHtml":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\">\n            \n        <\/div>\n    <\/div>\n    <div class=\"col col-md-6\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\"><!--block039449238301486433-->\n            <!--block0558766089683463-->\n        <\/div>\n    <\/div>\n<\/div>        "},{"uid":"0048939211996886","type":"columns","html":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        \n    <\/div>\n    <div class=\"col col-md-6\" data-size=\"6\">\n        \n    <!--block03671041092431052--><!--block018677022024891388--><\/div>\n<\/div>        ","data":{"columns":[{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"},{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"}]},"blocks":[{"uid":"03671041092431052","type":"heading","html":"<h4><br>More third party integrations<br><\/h4>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        More third party integrations\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"<h4 contenteditable=\"true\"><br>More third party integrations<br><\/h4>"},{"uid":"018677022024891388","type":"text","html":"Having other Joomla! plugins and extensions to work with EasyBlog is just a few clicks away.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Having other Joomla! plugins and extensions to work with EasyBlog is just a few clicks away.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Having other Joomla! plugins and extensions to work with EasyBlog is just a few clicks away.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"},{"uid":"03671041092431052","type":"heading","html":"<h4><br>More third party integrations<br><\/h4>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        More third party integrations\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"<h4 contenteditable=\"true\"><br>More third party integrations<br><\/h4>"},{"uid":"018677022024891388","type":"text","html":"Having other Joomla! plugins and extensions to work with EasyBlog is just a few clicks away.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Having other Joomla! plugins and extensions to work with EasyBlog is just a few clicks away.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Having other Joomla! plugins and extensions to work with EasyBlog is just a few clicks away.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"}],"nested":false,"isolated":false,"text":"\n            \n    \n        \n            \n        \n    \n    \n        \n    \n        \n    \n    \n        More third party integrations\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n            \n    \n        \n    \n    \n        Having other Joomla! plugins and extensions to work with EasyBlog is just a few clicks away.\u200b\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n        \n    \n        ","editableHtml":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\">\n            \n        <\/div>\n    <\/div>\n    <div class=\"col col-md-6\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\"><!--block03671041092431052-->\n            <!--block018677022024891388-->\n        <\/div>\n    <\/div>\n<\/div>        "},{"uid":"0680012258160072","type":"columns","html":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        \n    <\/div>\n    <div class=\"col col-md-6\" data-size=\"6\">\n        \n    <!--block09070874272077539--><!--block002974416423671611--><\/div>\n<\/div>        ","data":{"columns":[{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"},{"size":6,"content":"COM_EASYBLOG_BLOCK_COLUMN_DEFAULT_TITLE"}]},"blocks":[{"uid":"09070874272077539","type":"heading","html":"<h4><br>Blog rating<br><\/h4>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        Blog rating\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"<h4 contenteditable=\"true\"><br>Blog rating<br><\/h4>"},{"uid":"002974416423671611","type":"text","html":"Users can show intensity of their favorite blog post by rating them with stars.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Users can show intensity of their favorite blog post by rating them with stars.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Users can show intensity of their favorite blog post by rating them with stars.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"},{"uid":"09070874272077539","type":"heading","html":"<h4><br>Blog rating<br><\/h4>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"\n    \n        \n    \n    \n        Blog rating\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n","editableHtml":"<h4 contenteditable=\"true\"><br>Blog rating<br><\/h4>"},{"uid":"002974416423671611","type":"text","html":"Users can show intensity of their favorite blog post by rating them with stars.<span class=\"redactor-invisible-space\">\u200b<\/span>","data":{},"blocks":[],"nested":true,"isolated":false,"text":"Users can show intensity of their favorite blog post by rating them with stars.\u200b","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\">Users can show intensity of their favorite blog post by rating them with stars.<span class=\"redactor-invisible-space\">\u200b<\/span><\/div><\/div>"}],"nested":false,"isolated":false,"text":"\n            \n    \n        \n            \n        \n    \n    \n        \n    \n        \n    \n    \n        Blog rating\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n            \n    \n        \n    \n    \n        Users can show intensity of their favorite blog post by rating them with stars.\u200b\n        \n            \n                \n            \n        \n    \n    \n        \n            \n                Drag to move block.\n            \n        \n    \n\n        \n    \n        ","editableHtml":"\n            <div class=\"row\" data-responsive=\"400,300,200,100\">\n    <div class=\"col col-md-1\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\">\n            \n        <\/div>\n    <\/div>\n    <div class=\"col col-md-6\" data-size=\"6\">\n        <div class=\"ebd-nest\" data-type=\"block\" data-col-wrapper=\"\"><!--block09070874272077539-->\n            <!--block002974416423671611-->\n        <\/div>\n    <\/div>\n<\/div>        "},{"uid":"021494898851960897","type":"rule","html":"\n            <hr>        ","data":{},"blocks":[],"nested":false,"isolated":false,"text":"","editableHtml":"\n            <hr>        "},{"uid":"031212949855866967","type":"text","html":"<p>And many more powerful features that you can use to make your blog work beautifully and professionally. Need any help? Drop by our <a target=\"_blank\" class=\"\" title=\"\" href=\"http:\/\/stackideas.com\/forums\/index\/easyblog\">Official forum<\/a> or send our support team a ticket via our <a target=\"_blank\" class=\"\" title=\"\" href=\"https:\/\/crm.stackideas.com\">CRM<\/a>. You can even check out <a target=\"_blank\" class=\"\" title=\"\" href=\"http:\/\/stackideas.com\/docs\/easyblog\">EasyBlog\'s Documentation<\/a>.<\/p>","data":{},"blocks":[],"nested":false,"isolated":false,"text":"And many more powerful features that you can use to make your blog work beautifully and professionally. Need any help? Drop by our Official forum or send our support team a ticket via our CRM. You can even check out EasyBlog\'s Documentation.","editableHtml":"<div class=\"ebd-nest\" data-type=\"content\" data-eb-text-block-wrapper=\"\"><div data-content-type=\"html\" data-eb-text-content-wrapper=\"\" contenteditable=\"true\"><p>And many more powerful features that you can use to make your blog work beautifully and professionally. Need any help? Drop by our <a target=\"_blank\" class=\"\" title=\"\" href=\"http:\/\/stackideas.com\/forums\/index\/easyblog\">Official forum<\/a> or send our support team a ticket via our <a target=\"_blank\" class=\"\" title=\"\" href=\"https:\/\/crm.stackideas.com\">CRM<\/a>. You can even check out <a target=\"_blank\" class=\"\" title=\"\" href=\"http:\/\/stackideas.com\/docs\/easyblog\">EasyBlog\'s Documentation<\/a>.<\/p><\/div><\/div>"}],"version":"1.0"}';

		$data->created_by = $my->id;
		$data->created = EB::date()->toSql();
		$data->ip = '127.0.0.1';
		$data->frontpage = true;
		$data->published = EASYBLOG_POST_PUBLISHED;
		$data->hits = 1;
		$data->access = 0;
		$data->doctype = 'ebd';
		$data->source_type = 'easyblog.sitewide';

		$post->create();

		$post->bind($data);

		$options = array('validateData' => false,
						 'logUserIpAddress' => false,
						 'applyDateOffset' => false);

		// since this is a sample post, we can safely ignore any warning or strict errors.
		@$post->save($options);

		return $this->getResultObj(JText::_('COM_EASYBLOG_INSTALLATION_SAMPLE_POST_CREATED'), true );
	}

	/**
	 * Update the ACL for EasyBlog
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateACL()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return;
		}

		$db = EB::db();

		// Intelligent fix to delete all records from the #__easyblog_acl_group when it contains ridiculous amount of entries
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__easyblog_acl_group');
		$db->setQuery($query);

		$total = $db->loadResult();

		if ($total > 20000) {
			$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_acl_group');
			$db->setQuery($query);
			$db->Query();
		}

		// First, remove all records from the acl table.
		$query = 'DELETE FROM ' . $db->nameQuote('#__easyblog_acl');
		$db->setQuery($query);
		$db->query();

		// Get the list of acl
		$contents = JFile::read(EBLOG_ADMIN_ROOT . '/defaults/acl.json');
		$acls = json_decode($contents);

		foreach ($acls as $acl) {

			$query = array();
			$query[] = 'INSERT INTO ' . $db->qn('#__easyblog_acl') . '(' . $db->qn('id') . ',' . $db->qn('action') . ',' . $db->qn('group') . ',' . $db->qn('description') . ',' . $db->qn('published') . ')';
			$query[] = 'VALUES(' . $db->Quote($acl->id) . ',' . $db->Quote($acl->action) . ',' . $db->Quote($acl->group) . ',' . $db->Quote($acl->desc) . ',' . $db->Quote($acl->published) . ')';
			$query = implode(' ', $query);

			$db->setQuery($query);
			$db->Query();
		}

		// Once the acl is initialized, we need to create default values for all the existing groups on the site.
		$this->assignACL();

		return $this->getResultObj(JText::_('COM_EASYBLOG_INSTALLATION_ACL_INITIALIZED'), true);
	}

	/**
	 * Assign acl rules to existing Joomla groups
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function assignACL()
	{
		$this->engine();

		// Get the db
		$db = EB::db();

		// Retrieve all user groups from the site
		$query = array();
		$query[] = 'SELECT a.' . $db->qn('id') . ', a.' . $db->qn('title') . ' AS ' . $db->qn('name') . ', COUNT(DISTINCT b.' . $db->qn('id') . ') AS ' . $db->qn('level');
		$query[] = ', GROUP_CONCAT(b.' . $db->qn('id') . ' SEPARATOR \',\') AS ' . $db->qn('parents');
		$query[] = 'FROM ' . $db->qn('#__usergroups') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->qn('#__usergroups') . ' AS b';
		$query[] = 'ON a.' . $db->qn('lft') . ' > b.'  . $db->qn('lft');
		$query[] = 'AND a.' . $db->qn('rgt') . ' < b.' . $db->qn('rgt');
		$query[] = 'GROUP BY a.' . $db->qn('id');
		$query[] = 'ORDER BY a.' . $db->qn('lft') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Default values
		$groups = array();
		$result = $db->loadColumn();

		// Get a list of default acls
		$query = array();
		$query[] = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__easyblog_acl');
		$query[] = 'ORDER BY ' . $db->qn('id') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Get those acls
		$installedAcls = $db->loadColumn();

		// Default admin groups
		$adminGroups = array(7, 8);

		if (!empty($result)) {

			foreach ($result as $id) {

				$id = (int) $id;

				// Every other group except admins and super admins should only have restricted access
				if (in_array($id, $adminGroups)) {
					$groups[$id] = $installedAcls;
				} else {

					$allowedAcl = array();

					// Default guest / public group
					if ($id == 1 || $id == 9) {
						$allowedAcl = array(18, 19, 37, 39);
					} else {
						// other groups
						$allowedAcl = array(1, 3, 4, 6, 8, 10, 11, 12, 13, 14, 15, 16 ,17, 18, 19, 21, 23, 24, 25, 26, 27, 28, 30, 33, 34, 35, 36 , 37, 39, 40, 41, 42);
					}

					$groups[$id] = $allowedAcl;
				}
			}
		}


		// Insert default filter for all groups.
		$tagFilter = 'script,applet,iframe';
		$attrFilter = 'onclick,onblur,onchange,onfocus,onreset,onselect,onsubmit,onabort,onkeydown,onkeypress,onkeyup,onmouseover,onmouseout,ondblclick,onmousemove,onmousedown,onmouseup,onerror,onload,onunload';

		// Go through each groups now
		foreach ($groups as $groupId => $acls) {

			$query = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_acl_filters');
			$query[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($groupId);
			$query = implode(' ', $query);

			$db->setQuery($query);
			$filterExists = $db->loadResult() > 0 ? true : false;

			// If the filters doesn't exist, insert them
			if (!$filterExists) {

				$filter = EB::table('ACLFilter');
				$filter->content_id = $groupId;
				$filter->disallow_tags = in_array($groupId, $adminGroups) ? '' : $tagFilter;
				$filter->disallow_attributes = in_array($groupId, $adminGroups) ? '' : $attrFilter;

				$filter->store();
			}

			// Now we need to insert the acl rules
			$query = array();
			$insertQuery = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_acl_group');
			$query[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($groupId);
			$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('group');

			$query = implode(' ', $query);

			$db->setQuery($query);
			$exists = $db->loadResult() > 0 ? true : false;

			// Reinitialize the query again.
			$query = 'INSERT INTO ' . $db->qn('#__easyblog_acl_group') . ' (' . $db->qn('content_id') . ',' . $db->qn('acl_id') . ',' . $db->qn('status') . ',' . $db->qn('type') . ') VALUES';

			if (!$exists) {

				foreach ($acls as $acl) {
					$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($acl) . ',' . $db->Quote('1') . ',' . $db->Quote('group') . ')';
				}

				//now we need to get the unassigend acl and set it to '0';
				$disabledACLs = array_diff($installedAcls, $acls);

				if ($disabledACLs) {
					foreach ($disabledACLs as $disabledAcl) {
						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($disabledAcl) . ',' . $db->Quote('0') . ',' . $db->Quote('group') . ')';
					}
				}

			} else {

				// Get a list of acl that is already associated with the group
				$sub = array();
				$sub[] = 'SELECT ' . $db->qn('acl_id') . ' FROM ' . $db->qn('#__easyblog_acl_group');
				$sub[] = 'WHERE ' . $db->qn('content_id') . '=' . $db->Quote($groupId);
				$sub[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('group');

				$sub = implode(' ', $sub);
				$db->setQuery($sub);

				$existingGroupAcl = $db->loadColumn();

				// Perform a diff to see which acl rules are missing
				$diff = array_diff($existingGroupAcl, $installedAcls);

				// If there's a difference,
				if ($diff) {
					foreach ($diff as $aclId) {

						$value = 0;

						if (in_array($aclId, $acls)) {
							$value = 1;
						}

						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($aclId) . ',' . $db->Quote($value) . ',' . $db->Quote('group') . ')';
					}
				}
			}

			// Only run this when there is something to insert
			if ($insertQuery) {
				$insertQuery = implode(',', $insertQuery);
				$query .= $insertQuery;

				$db->setQuery($query);
				$db->Query();
			}
		}

		return true;
	}

	/**
	 * Unpublish old module for EasyBlog
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublishOldModule()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return;
		}

		$db = EB::db();

		$moduleName = array('mod_latestblogs', 'mod_topblogs', 'mod_searchblogs', 'mod_imagewall', 'mod_showcase', 'mod_teamblogs', 'mod_subscribers', 'mod_easyblogmostactiveblogger');

		for ($i=0; $i<count($moduleName); $i++) {

		    // jos_modules
		    $query = array();
		    $query[] = 'UPDATE ' . $db->qn('#__modules') . ' SET ' . $db->qn('published') . '=' . $db->Quote('0');
		    $query[] = 'WHERE ' . $db->qn('module') . '=' . $db->Quote($moduleName[$i]);
		    $query[] = 'AND ' . $db->qn('published') . '=' . $db->Quote('1');

		    $query = implode(' ', $query);

		    $db->setQuery($query);
		    $state = $db->query();
		}

		return true;
	}

	public function unpublishOldPlugin()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return;
		}

		$db = EB::db();

		$pluginName = array('pagebreak' => 'easyblog', 'autoarticle' => 'easyblog');

		foreach($pluginName as $element => $folder) {
		    // jos_modules
		    $query = array();
		    $query[] = 'UPDATE ' . $db->qn('#__extensions') . ' SET ' . $db->qn('enabled') . '=' . $db->Quote('0');
		    $query[] = 'WHERE ' . $db->qn('folder') . '=' . $db->Quote($folder);
		    $query[] = 'AND ' . $db->qn('element') . '=' . $db->Quote($element);

		    $query = implode(' ', $query);

		    $db->setQuery($query);
		    $state = $db->query();
		}

		return true;

	}
}
