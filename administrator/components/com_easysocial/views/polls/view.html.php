<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include main views file.
FD::import('admin:/views/views');

class EasySocialViewPolls extends EasySocialAdminView
{
	/**
	 * Default poll listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display($tpl = null)
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_POLLS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_POLLS');

		// Get the model
		$model = FD::model('Polls', array('initState' => true));

		$search = $model->getState( 'search' );
		$ordering 	= $model->getState( 'ordering' );
		$direction 	= $model->getState( 'direction' );

		// Add Joomla buttons
		JToolbarHelper::deleteList();

		// Get polls
		$polls = $model->getAllPolls();

		foreach ($polls as $poll) {
			$poll->creator = FD::user($poll->created_by);
		}

		// Get pagination
		$pagination	= $model->getPagination();

		$this->set('pagination', $pagination);
		$this->set('search', $search);
		$this->set('polls', $polls);
		$this->set('ordering', $ordering);
		$this->set('direction', $direction);

		echo parent::display('admin/polls/default');
	}

}
