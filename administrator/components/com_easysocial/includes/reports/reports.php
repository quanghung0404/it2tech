<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialReports extends EasySocial
{
	public $extension = '';
	public $type = '';
	public $uid = '';

	// The reported item's title
	public $itemTitle = '';

	// The title?
	public $title = '';
	public $text = '';
	public $description = '';
	public $url = '';
	public $icon = false;

	public function __construct($options = array())
	{
		parent::__construct();

		$this->options = $this->normalizeOptions($options);
	}

	public function normalizeOptions($options)
	{
		if ($options) {
			foreach ($options as $key => $value) {
				$this->$key = JText::_($value);
			}
		}

		// Set a default text if API wasn't provided with a custom text.
		if (!isset($options['text'])) {
			$this->text = JText::_('COM_EASYSOCIAL_REPORTS_REPORT_ITEM');
		}

		// Normalize url
		if (!isset($options['url'])) {
			$this->url = JRequest::getURI();
		}

		// Normalize title
		if (!isset($options['title'])) {
			$this->title = JText::_($this->text);
		}
	}

	/**
	 * Factory method to create a new report instance
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function factory($options = array())
	{
		return new self($options);
	}

	/**
	 * Generates the html form for reports
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function html()
	{
		// When guests reporting is disabled, we shouldn't show the form at all
		if (!$this->config->get('reports.guests') && $this->my->guest) {
			return;
		}

		// @access: reports.submit
		// Check if user is allowed to create reports
		if (!$this->access->allowed('reports.submit')) {
			return;
		}

		// Load up the reports model
		$model = ES::model('Reports');

		$options = array('created_by' => $this->my->id);
		$usage = $model->getCount($options);

		// Check if the current user exceeded the reports limit
		if ($this->access->exceeded('reports.limit', $usage)) {
			return;
		}

		// Load up the themes
		$theme = ES::themes();

		$theme->set('url', $this->url);
		$theme->set('extension', $this->extension);
		$theme->set('type', $this->type);
		$theme->set('uid', $this->uid);
		$theme->set('itemTitle', $this->itemTitle);
		$theme->set('title', $this->title);
		$theme->set('text', $this->text);
		$theme->set('description', $this->description);
		$theme->set('icon', $this->icon);

		$contents = $theme->output('site/reports/default.link');

		return $contents;
	}

	/**
	 * Generates the report link to allow users to report on an item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getForm($extension , $type , $uid , $itemTitle , $text , $title = '' , $description = '' ,  $url = '', $icon=false )
	{
		// Determine if the user is a guest
		$my 		= FD::user();
		$config 	= FD::config();

		if( !$my->id )
		{
			if( !$config->get( 'reports.guests' , false ) )
			{
				return;
			}
		}
		else
		{
			// Check if user is allowed to report.
			$access		= FD::access();

			// @access: reports.submit
			// Check if user is allowed to create reports
			if( !$access->allowed( 'reports.submit' ) )
			{
				return;
			}

			$model 	= FD::model( 'Reports' );
			$usage	= $model->getCount( array( 'created_by' => $my->id ) );

			// Check if the current user exceeded the reports limit
			if( $access->exceeded( 'reports.limit' , $usage ) )
			{
				return;
			}
		}

		$theme 	= FD::themes();

		// Set a default text if API wasn't provided with a custom text.
		if( empty( $text ) )
		{
			$text 	= JText::_( 'COM_EASYSOCIAL_REPORTS_REPORT_ITEM' );
		}

		// If url is not provided, use the current URL.
		if( empty( $url ) )
		{
			$url 	= JRequest::getURI();
		}

		// If title is not supplied, we use the text
		if( empty( $title ) )
		{
			$title 	= $text;
		}

		$theme->set( 'url'			, $url );
		$theme->set( 'extension' 	, $extension );
		$theme->set( 'itemTitle' 	, $itemTitle );
		$theme->set( 'title' 		, $title );
		$theme->set( 'text' 		, $text );
		$theme->set( 'type' 		, $type );
		$theme->set( 'uid'			, $uid );
		$theme->set( 'description'	, $description );
		$theme->set( 'icon'         , $icon );

		$contents 	= $theme->output( 'site/reports/default.link' );

		return $contents;
	}
}
