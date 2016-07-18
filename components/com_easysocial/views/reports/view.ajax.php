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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewReports extends EasySocialSiteView
{
	/**
	 * Post processing for storing a report.
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	public function store()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			$message 	= $this->getMessage();

			return $ajax->resolve( '<div class="alert alert-error">' . $message->message . '</div>' );
		}

		$theme = FD::themes();

		$html = $theme->output( 'site/reports/dialog.submitted' );

		return $ajax->resolve( $html );
	}

	/**
	 * Renders a dialog to submit a report against an item on the site.
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function confirmReport()
	{
		if ($this->my->guest && !$this->config->get('reports.guests', false)) {
			return;
		}

		// Check if user is really allowed to submit any reports.
		$access	= FD::access();

		if (!$access->allowed('reports.submit')) {
			$this->setMessage(JText::_('COM_EASYSOCIAL_REPORTS_NOT_ALLOWED_TO_SUBMIT_REPORTS'), SOCIAL_MSG_ERROR);
			return $this->ajax->reject($this->getMessage());
		}

		$title = $this->input->get('title', JText::_('COM_EASYSOCIAL_REPORTS_DIALOG_TITLE'), 'default');
		$description = $this->input->get('description', '', 'default');

		$theme = ES::themes();

		$theme->set('title', $title);
		$theme->set('description', $description);

		$html = $theme->output('site/reports/dialog.form');

		return $this->ajax->resolve($html);
	}
}
