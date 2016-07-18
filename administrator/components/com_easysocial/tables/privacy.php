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

FD::import( 'admin:/tables/table' );

/**
 * Object mapping for privacy table.
 *
 * @author	Sam <sam@stackideas.com>
 * @since	1.0
 */

class SocialTablePrivacy extends SocialTable
{
	/**
	 * The table row id
	 * @var int
	 */
	public $id			= null;

	/**
	 * Determines if this is a core privacy
	 * @var boolean
	 */
	public $core		= null;

	/**
	 * Determines the state of the privacy
	 * @var boolean
	 */
	public $state		= null;


	/**
	 * The privacy type
	 * @var string max 64chars
	 */
	public $type = null;

	/**
	 * The privacy type's rule
	 * @var string max 64chars
	 */
	public $rule         = null;

	/**
	 * The privacy value
	 * @var int
	 */
	public $value     = 0;

	/**
	 * The privacy's description
	 * @var int
	 */
	public $description     = null;


	/**
	 * The privacy's options
	 * @var int
	 */
	public $options   = 0;


	public function __construct(& $db )
	{
		parent::__construct( '#__social_privacy' , 'id' , $db );
	}


	public function toJSON()
	{
		$options = FD::json()->decode( $this->options );


		return array('id' 			=> $this->id ,
					 'type' 		=> $this->type,
					 'rule' 		=> $this->rule,
					 'value' 		=> $this->value,
					 'description' 	=> $this->description,
					 'options' 		=> $options,
					 'state' 		=> $state,
					 'core' 		=> $core
		 );
	}

}
