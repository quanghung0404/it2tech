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

class EasyDiscussViewRanks extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.ranks');


		$model = ED::model('Ranks', true);
		$ranks = $model->getRanks();

		$this->title('COM_EASYDISCUSS_SETTINGS_RANKS_TITLE');
		$this->desc('COM_EASYDISCUSS_SETTINGS_RANKS_DESC');

		$rankingType = $this->config->get('main_ranking_calc_type') == 'points' ? JText::_('COM_EASYDISCUSS_RANKING_POINTS') : JText::_('COM_EASYDISCUSS_RANKING_POSTS');

		$this->set('rankingType', $rankingType);
		$this->set('ranks', $ranks);

		parent::display('ranks/default');
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_RANKING' ), 'ranks' );

		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
	}
}
