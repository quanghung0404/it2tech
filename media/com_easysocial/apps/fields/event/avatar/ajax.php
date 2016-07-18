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
defined('_JEXEC') or die('Unauthorized Access');

FD::import('fields:/user/avatar/ajax');

class SocialFieldsEventAvatar extends SocialFieldsUserAvatar
{
    public function upload()
    {
        // Get the ajax library
        $ajax       = FD::ajax();

        // Get the file
        $tmp        = JRequest::getVar($this->inputName, '', 'FILES');

        $file       = array();
        foreach ($tmp as $k => $v) {
            $file[$k] = $v['file'];
        }

        // Check if it is a valid file
        if (empty($file['tmp_name'])) {
            return $ajax->reject(JText::_('PLG_FIELDS_AVATAR_ERROR_INVALID_FILE'));
        }

        // Get user access
        $access = FD::access($this->uid , SOCIAL_TYPE_CLUSTERS);

        // We need to perform sanity checking here
        $options = array('name' => $this->inputName, 'maxsize' => $access->get('photos.maxsize') . 'M', 'multiple' => true);

        $uploader = ES::uploader($options);
        $file = $uploader->getFile(null, 'image');

        // If there was an error getting uploaded file, stop.
        if ($file instanceof SocialException) {
            return $ajax->reject($file->message);
        }

        // Load up the image library so we can get the appropriate extension
        $image  = FD::image();
        $image->load($file['tmp_name']);

        // Copy this to temporary location first
        $tmpPath    = SocialFieldsUserAvatarHelper::getStoragePath($this->inputName);
        $tmpName    = md5($file['name'] . $this->inputName . FD::date()->toMySQL()) . $image->getExtension();

        $source     = $file['tmp_name'];
        $target     = $tmpPath . '/' . $tmpName;
        $state      = JFile::copy($source, $target);

        if (!$state) {
            return $ajax->reject(JText::_('PLG_FIELDS_AVATAR_ERROR_UNABLE_TO_MOVE_FILE'));
        }

        $tmpUri     = SocialFieldsUserAvatarHelper::getStorageURI($this->inputName);
        $uri        = $tmpUri . '/' . $tmpName;

        return $ajax->resolve($file, $uri, $target);
    }
}
