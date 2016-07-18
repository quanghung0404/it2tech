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

FD::import('admin:/includes/fields/dependencies');

class SocialFieldsEventMembertransfer extends SocialFieldItem
{
    public function onRegister(&$post, &$session)
    {
        $reg = FD::registry();
        $reg->load($session->values);

        $groupId = $reg->get('group_id');

        if (empty($groupId)) {
            return;
        }

        $value = isset($post['member_transfer']) ? $post['member_transfer'] : $this->params->get('default', 'invite');

        $this->set('value', $value);

        $allowed = $this->params->get('allowed', array());

        $this->set('allowed', $allowed);

        return $this->display();
    }

    public function onEdit(&$post, &$node, $errors)
    {
        // No edit is needed since this field only take effect on registration
        return;
    }

    public function onSample()
    {
        $this->set('value', $this->params->get('default', 'invite'));

        $this->set('allowed', $this->params->get('allowed', array()));

        return $this->display();
    }
}
