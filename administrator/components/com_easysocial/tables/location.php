<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/tables/table' );

/**
 * Object relation mapping for location.
 *
 * @since   1.0
 * @author  Mark Lee <mark@stackideas.com>
 */
class SocialTableLocation extends SocialTable
{
    /**
     * The unique location id.
     * @var int
     */
    public $id              = null;

    /**
     * The unique type id.
     * @var int
     */
    public $uid             = null;

    /**
     * The unique type item.
     * @var string
     */
    public $type            = null;

    /**
     * The creator or owner of this location item. Foreign key to `#__users`.`id`
     * @var int
     */
    public $user_id         = null;

    /**
     * The creation date time.
     * @var datetime
     */
    public $created         = null;

    /**
     * The short address string
     * @var string
     */
    public $short_address   = null;

    /**
     * The address string
     * @var string
     */
    public $address         = null;

    /**
     * The latitude of the location
     * @var int
     */
    public $latitude        = null;

    /**
     * The longitude of the location
     * @var int
     */
    public $longitude       = null;

    /**
     * The parameters for this location.
     * @var string
     */
    public $params          = null;


    /**
     * Class Constructor.
     *
     * @since   1.0
     * @access  public
     */
    public function __construct( $db )
    {
        parent::__construct('#__social_locations', 'id' , $db);
    }

    /**
     * Loads a location result based on the given uid and type.
     *
     * @since   1.0
     * @access  public
     * @param   int     $uid    The unique item id.
     * @param   string  $type   The type of the item.
     *
     * @return  boolean         True if record exists, false otherwise.
     */
    public function loadByType( $uid , $type )
    {
        $db     = FD::db();
        $query  = 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
                . 'WHERE ' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $uid ) . ' '
                . 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( $type );
        $db->setQuery( $query );

        $result = $db->loadObject();

        if( !$result )
        {
            return false;
        }

        return parent::bind( $result );
    }

    /**
     * Retrieves the intro text portion of a message.
     *
     * @since   1.0
     * @access  public
     * @param   null
     * @return  string      The intro section of the message.
     */
    public function getAddress( $overrideLength = null )
    {
        $config     = FD::config();

        if( !is_null( $overrideLength ) )
        {
            // Get the maximum length.
            $maxLength  = $overrideLength;

            $message    = strip_tags( $this->address );
            $message    = JString::substr( $message , 0 , $maxLength ) . ' ' . JText::_( 'COM_EASYSOCIAL_ELLIPSIS' );

            return $message;
        }

        return $this->address;
    }

    /**
     * Override paren't implementation of store.
     *
     * @since   1.0
     * @access  public
     * @param   null
     * @return  boolean
     */
    public function store( $updateNulls = false )
    {
        $state  = parent::store( $updateNulls );

        return $state;
    }

    /**
     * Retrieves the city value if available.
     *
     * @since   1.2
     * @access  public
     * @return  mixed   String if state is found, false otherwise.
     */
    public function getCity()
    {
        $params     = FD::makeObject($this->params);

        if (isset($params->address_components[2]) && $params->address_components[2]->types[0] == 'locality' ) {
            return $params->address_components[2]->short_name;
        }

        return false;
    }

}
