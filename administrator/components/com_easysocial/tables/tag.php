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
defined('JPATH_BASE') or die('Unauthorized Access');

FD::import('admin:/tables/table');

class SocialTableTag extends SocialTable
{
    public $id = null;

    /**
     * This stores the type of tag. Example: person / hashtag
     * @var int
     */
    public $type = null;

    /**
     * This stores the item that is being tagged
     * @var string
     */
    public $item_id = null;

    /**
     * This stores the item type that is being tagged
     * @var string
     */
    public $item_type = null;

    /**
     * This stores the location type that the item is being tagged on
     * @var string
     */
    public $target_id = null;

    /**
     * This stores the location type that the item is being tagged on
     * @var string
     */
    public $target_type = null;

    /**
     * This stores the item type that is tagging the target
     * @var string
     */
    public $creator_id = null;

    /**
     * This stores the item type that is tagging the target
     * @var string
     */
    public $creator_type = null;

    /**
     * This stores the offset of the item that needs to be replaced
     * @var int
     */
    public $offset = null;

    /**
     * This stores the length of the string of the item that needs to be replaced
     * @var int
     */
    public $length = null;

    /**
     * This stores the hashtag title if it's a hastag tag.
     * @var string
     */
    public $title = null;

    public function __construct($db)
    {
        parent::__construct('#__social_tags' , 'id' , $db);
    }

    /**
     * Retrieves the tag object
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function getEntity()
    {
        if ($this->item_type == SOCIAL_TYPE_USER) {
            $user = ES::user($this->item_id);

            return $user;
        }
    }
}
