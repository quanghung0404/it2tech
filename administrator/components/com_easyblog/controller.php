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

class EasyBlogController extends JControllerLegacy
{
	protected $default_view = 'easyblog';

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->app = JFactory::getApplication();
        $this->input = EB::request();
        $this->doc = JFactory::getDocument();
        $this->config = EB::config();
        $this->my = JFactory::getUser();
        $this->info = EB::info();

        if ($this->doc->getType() == 'ajax') {
            $this->ajax = EB::ajax();
        }
    }

    /**
     * Default display method which is invoked by Joomla
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function display($cachable = false, $urlparams = false)
    {
        $type = $this->doc->getType();
        $name = $this->input->get('view', $this->getName(), 'cmd');
        $layout = $this->input->get('layout', 'default', 'cmd');

        // Get the current view object
        $view = $this->getView($name, $type, '');
        $view->setLayout($layout);

        // Attach theme stylesheet
        $stylesheet = EB::stylesheet('admin', 'default');
        $stylesheet->attach();

		parent::display();

		return $this;
	}

    /**
     * ACL checks for admin access
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function checkAccess($acl)
    {
        if (!$this->my->authorise('easyblog.manage.' . $acl , 'com_easyblog')) {
            $this->info->set('JERROR_ALERTNOAUTHOR', 'error');
            return $this->app->redirect('index.php?option=com_easyblog');
        }
    }
}
