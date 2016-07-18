<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTablePostTemplate extends EasyBlogTable
{
    public $id = null;
    public $user_id = null;
    public $title = null;
    public $data = null;
    public $created = null;
    public $system = null;
    public $screenshot = null;
    public $core = null;
    public $published = null;

    public function __construct(&$db)
    {
        parent::__construct('#__easyblog_post_templates', 'id', $db);
    }

    /**
     * Determines if the post template is a blank templae
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function isBlank()
    {
        return $this->system == 2;
    }

    /**
     * Determines if the post template is a system templae
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function isSystem()
    {
        return $this->system == 1;
    }

    /**
     * Determines if the post template is a core templae
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function isCore()
    {
        return $this->core == 1;
    }

    /**
     * Determines if the post template is a core templae
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function isOwner()
    {
        $my = EB::user();

        if (EB::isSiteAdmin($my->id) || $this->user_id == $my->id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieves the author of the template
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAuthor()
    {
        static $authors = array();

        if (!isset($authors[$this->user_id])) {
            $user = EB::user($this->user_id);
            $authors[$this->user_id] = $user;
        }

        return $authors[$this->user_id];
    }

    /**
     * Loads a blog post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function load($id=null, $reset=true)
    {
        // Load post from post table
        $state = parent::load($id);

        return $state;
    }

    /**
     * Retrieves the creation date in JDate format
     *
     * @since   4.0
     * @access  public
     * @return  JDate
     */
    public function getCreated()
    {
        $date = EB::date($this->created);

        return $date;
    }

    /**
     * Performs check against the properties of the table
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function check()
    {
        if (!$this->title) {
            $this->setError(JText::_('COM_EASYBLOG_TEMPLATES_PLEASE_ENTER_A_TITLE_FOR_YOUR_TEMPLATE'));

            return false;
        }

        return true;
    }

    /**
     * Retrieves the document object
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function getDocument()
    {
        static $documents = array();

        if (!isset($documents[$this->id])) {

            $document = EB::document();
            $document->load($this->data);

            $documents[$this->id] = $document;
        }

        return $documents[$this->id];
    }

    /**
     * An exportable result of this object
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function export()
    {
        $obj = new stdClass();
        $obj->id = $this->id;
        $obj->title = $this->title;
        $obj->document = json_decode($this->data);
        $obj->formattedDate = $this->getCreated()->format(JText::_('DATE_FORMAT_LC2'));

        return $obj;
    }
}
