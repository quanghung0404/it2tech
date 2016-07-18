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

FD::import('site:/views/views');

class EasySocialViewProfile extends EasySocialSiteView
{
    public function display($tpl = null)
    {
        $auth   = JRequest::getString('auth');

        // Get the current logged in user's information
        $model  = FD::model('Users');
        $id     = $model->getUserIdFromAuth($auth);

        $userId = JRequest::getInt('userid');

        // If user id is not passed in, return logged in user
        if (!$userId) {
            $userId = $id;
        }

        // If we still can't find user's details, throw an error
        if (!$userId) {

            $this->set('code', 403);
            $this->set('message', JText::_('Invalid user id provided.'));

            return parent::display();
        }

        $me = FD::user($id);
        $user = FD::user($userId);

        $this->set('id', $userId);
        $this->set('isself', $id == $userId);
        $this->set('isfriend', $user->isFriends($id));
        $this->set('isfollower', $user->isFollowed($id));
        $this->set('username', $user->username);
        $this->set('friend_count', $user->getTotalFriends());
        $this->set('follower_count', $user->getTotalFollowing());
        $this->set('badges', $user->getTotalBadges());
        $this->set('points', $user->getPoints());
        $this->set('avatar_thumb', $user->getAvatar());

        $birthday = $user->getFieldValue('BIRTHDAY');

        if (!empty($birthday)) {
            $this->set('age', $birthday->value->toAge());
        }

        $gender = $user->getFieldValue('GENDER');

        $this->set('gender', !empty($gender) ? $gender->data : 0);

        // Prepare DISPLAY custom fields
        FD::language()->loadAdmin();
        // FD::apps()->loadAllLanguages();

        $steps = FD::model('steps')->getSteps($user->profile_id, SOCIAL_TYPE_PROFILES, SOCIAL_PROFILES_VIEW_DISPLAY);
        $fields = FD::model('fields')->getCustomFields(array('profile_id' => $user->profile_id, 'data' => true, 'dataId' => $user->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => SOCIAL_PROFILES_VIEW_DISPLAY));

        $library = FD::fields();
        $args = array(&$user);
        $library->trigger('onGetValue', SOCIAL_FIELDS_GROUP_USER, $fields, $args);

        // Get the step mapping first
        $profileSteps = array();
        foreach ($steps as $step) {
            $profileSteps[$step->id] = JText::_($step->title);
        }

        $profileFields = array();
        foreach ($fields as $field) {
            $value = (string) $field->value;

            if (!empty($value)) {
                $data = array(
                    'group_id' => $field->step_id,
                    'group_name' => $profileSteps[$field->step_id],
                    'field_id' => $field->id,
                    'field_name' => JText::_($field->title),
                    'field_value' => (string) $field->value
                );

                $profileFields[] = $data;
            }
        }

        $this->set('more_info', $profileFields);

        $this->set('code', 200);
        parent::display();
    }
}
