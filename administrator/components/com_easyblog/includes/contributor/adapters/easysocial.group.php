<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogContributorEasySocialGroup extends EasyBlogContributorAbstract
{
    public $group = null;

    public function __construct($id)
    {
        parent::__construct($id, 'group');

        if (!EB::easysocial()->exists()) {
            return;
        }

        $this->group = FD::group($id);
    }

    public function getHeader()
    {
        $output = EB::easysocial()->renderMiniHeader($this->group);
        echo $output;
        return $output;
    }

    public function getAvatar()
    {
        if (!EB::easysocial()->exists()) {
            return;
        }

        return $this->group->getAvatar();
    }

    public function getTitle()
    {
        if (!EB::easysocial()->exists()) {
            return;
        }

        return $this->group->getName();
    }

    public function getPermalink()
    {
        if (!EB::easysocial()->exists()) {
            return;
        }

        return $this->group->getPermalink();
    }

    public function canDelete()
    {
        $allowed = $this->group->isAdmin();

        return $allowed;
    }
}
