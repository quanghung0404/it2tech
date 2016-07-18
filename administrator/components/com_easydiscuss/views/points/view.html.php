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

class EasyDiscussViewPoints extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.points');

		$this->title('COM_EASYDISCUSS_POINTS');

		$state = $this->app->getUserStateFromRequest('com_easydiscuss.points.filter_state', 'filter_state', 	'*', 'word');
		$search = $this->app->getUserStateFromRequest('com_easydiscuss.points.search', 'search', '', 'string' );

		$search = JString::trim(JString::strtolower($search));

		$order = $this->app->getUserStateFromRequest('com_easydiscuss.points.filter_order', 'filter_order', 'a.id', 'cmd');
		$orderDirection = $this->app->getUserStateFromRequest('com_easydiscuss.points.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$model = ED::model('Points', true);
		$points = $model->getPoints();

		foreach ($points as $point) {
			$date = ED::date($point->created);
			$point->created = $date->toMySQL(true);
		}

		$pagination = $model->getPagination();

		$this->set('points', $points );
		$this->set('pagination', $pagination);
		$this->set('state', $state);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('points/default');
	}

	public function form()
	{
		$this->checkAccess('discuss.manage.points');


		$id = $this->input->get('id', 0, 'int');

		$point = ED::table('Points');
		$point->load($id);

		if (!$point->created) {
			$date = ED::date();
			$point->created	= $date->toSql();
		}

		$model = ED::model('Points');
		$rules = $model->getRules();

		$this->set('rules', $rules);
		$this->set('point', $point);

		parent::display('points/form');
	}

	public function registerToolbar()
	{
		$layout = $this->getLayout();

		if ($layout == 'form') {
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_POINTS' ), 'points' );

			JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=points' );
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
			JToolBarHelper::custom( 'saveNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_NEW_BUTTON' ) , false);
			JToolBarHelper::cancel();

			return;
		}

		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_POINTS' ), 'points' );

		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'rules' , 'cog' , '' , JText::_( 'COM_EASYDISCUSS_MANAGE_RULES_BUTTON' ) , false );
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::addNew('add');
		JToolbarHelper::deleteList();
	}
}
