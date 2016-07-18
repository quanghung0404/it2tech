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

jimport( 'joomla.html.pagination' );

/**
 * Custom pagination class.
 *
 * Usage:
 *
 * <code>
 * <?php
 * $pagination 	= FD::get( 'Pagination' );
 * ?>
 * </code>
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class SocialPagination
{
	/**
	 * Joomla pagination object.
	 * @var	JPagination
	 */
	private $pagination 	= null;

	/**
	 * Class Constructor.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The total number of records.
	 * @param	int		The number of items shown.
	 * @param	int		The number of items to be shown per page.
	 */
	public function __construct( $total , $limitstart , $limit )
	{
		// Initialize the original pagination from Joomla.
		$this->pagination 	= new JPagination( $total, $limitstart ,$limit  );

		if (!FD::isJoomla30()) {
			$this->pagination->pagesCurrent = $this->pagination->get( 'pages.current' );
			$this->pagination->pagesTotal 	= $this->pagination->get( 'pages.total' );
			$this->pagination->pagesStart 	= $this->pagination->get( 'pages.start' );
			$this->pagination->pagesStop 	= $this->pagination->get( 'pages.stop' );
		}
	}

	public static function factory($total, $limitstart, $limit)
	{
		$obj = new self($total, $limitstart, $limit);

		return $obj;
	}

	/**
	 * Allows caller to set additional url parameters
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setVar($key, $value)
	{
		$this->pagination->setAdditionalUrlParam($key, $value);
	}

	/**
	 * Retrieves the html block for pagination codes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool		Determines if we should use the normal post form.
	 * @return	string		The html codes for the pagination.
	 */
	public function getListFooter($path = 'admin', $url = '')
	{
		// Retrieve pages data from Joomla itself.
		$theme 	= FD::themes();

		// If there's nothing here, no point displaying the pagination
		if ($this->pagination->total == 0) {
			return;
		}

		$data = $this->pagination->getData();

		$theme->set('data', $data);
		$theme->set('pagination', $this->pagination);

		$contents = $theme->output($path . '/pagination/default');

		return $contents;
	}

	public function getCounter()
	{
		$start		= $this->limitstart + 1;
		$end		= $this->limitstart + $this->limit < $this->total ? $this->limitstart + $this->limit : $this->total;

 		return FD::get( 'Themes' )
 				->assign( 'start' 	, $start )
 				->assign( 'end'		, $end )
 				->assign( 'total'	, $this->total )
				->output( 'site.pagination.counter' );
	}

	/**
	 * Getter
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __get( $key )
	{
		return $this->pagination->$key;
	}
}
