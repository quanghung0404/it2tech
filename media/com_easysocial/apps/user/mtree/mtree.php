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

FD::import('admin:/includes/apps/apps');

class SocialuserAppMtree extends SocialAppItem
{
    /**
     * Load dependencies
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function exists()
    {
        $file = JPATH_ROOT . '/administrator/components/com_mtree/admin.mtree.class.php';

        if (!JFile::exists($file)) {
            return false;
        }

        require_once($file);

        return true;
    }

    /**
     * Prepares the stream items for mosets tree
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function onPrepareStream(SocialStreamItem &$stream, $includePrivacy = true)
    {
        if (!$this->exists() || $stream->context != 'mtree') {
            return;
        }

        // Decorate the stream
        $stream->display = SOCIAL_STREAM_DISPLAY_FULL;
        $stream->color = '#6f90b5';
        $stream->fonticon = 'ies-comments-2';
        $stream->label = JText::_('APP_USER_MTREE_STREAM_LABEL');


        // Get the link object
        $db = JFactory::getDbo();
        $link = new mtLinks($db);
        $link->load($stream->contextId);

        $this->decorate($link);

        $this->set('actor', $stream->actor);
        $this->set('link', $link);

        $stream->title = parent::display('streams/title');
        $stream->content = parent::display('streams/content');
    }

    /**
     * Decorates the link object
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function decorate(&$link)
    {
        $db = JFactory::getDBO();
        $image = new mtImages($db);
        $image->load(array('link_id' => $link->link_id, 'ordering' => 1));

        require_once(__DIR__ . '/models/listing.php');

        $model = new ListingModel('Listing');

        $link->link_image = $image->filename;

        $links = array(&$link);
        
        $model->decorate($links);
    }
}
