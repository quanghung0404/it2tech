<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/views/views');

class EasySocialViewArticles extends EasySocialAdminView
{
	/**
	 * Main method to display the video categories
	 *
	 * @since	1.4
	 * @access	public
	 * @return	null
	 */
    public function display($tpl = null)
    {
        // Get the model
        $model = FD::model('Articles', array('initState' => true));

        // Remember the states
        $search = $model->getState('search');
        $limit = $model->getState('limit');
        $ordering = $model->getState('ordering');
        $direction = $model->getState('direction');

        // Get the categories
        $articles = $model->getItems(array('search' => $search));

        // Get the pagination 
        $pagination = $model->getPagination();

        $jscallback = $this->input->get('jscallback', '');

        $this->set('jscallback', $jscallback);
        $this->set('search', $search);
        $this->set('simple', $this->input->getString('tmpl') == 'component');
        $this->set('articles', $articles);
        $this->set('ordering', $ordering);
        $this->set('direction', $direction);
        $this->set('limit', $limit);
        $this->set('pagination', $pagination);

        return parent::display('admin/articles/default');
    }
}
