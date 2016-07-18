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
defined('_JEXEC') or die('Unauthorized Access');

FD::import('site:/views/views');

class EasySocialViewRegistration extends EasySocialSiteView
{
    /**
     * Determines if the view should be visible on lockdown mode
     *
     * @since   1.0
     * @access  public
     * @return  bool
     */
    public function isLockDown()
    {
        $config = FD::config();

        if($config->get('general.site.lockdown.registration')) {
            return false;
        }

        return true;
    }

    /**
     * Displays the registration request
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function request()
    {
        $id = $this->input->getInt('id');

        $table = JTable::getInstance('Module');

        $table->load($id);

        FD::language()->load('mod_easysocial_registration_requester', JPATH_SITE);

        $params = FD::registry($table->params);

        $profileId = $params->get('profile_id');

        // If there's no profile id, then we automatically assign the default profile id
        if (empty($profileId)) {
            $profileModel = FD::model('profiles');
            $defaultProfile = $profileModel->getDefaultProfile();
            $profileId = $defaultProfile->id;
        }

        $fieldsModel = FD::model('fields');

        $options = array(
            'visible' => SOCIAL_PROFILES_VIEW_MINI_REGISTRATION,
            'profile_id' => $profileId
        );

        $fields = $fieldsModel->getCustomFields($options);

        if (!empty($fields)) {
            FD::language()->loadAdmin();

            $fieldsLib = FD::fields();

            $session = JFactory::getSession();
            $registration = FD::table('Registration');
            $registration->load($session->getId());

            $data = $registration->getValues();

            $args = array(&$data, &$registration);

            $fieldsLib->trigger('onRegisterMini', SOCIAL_FIELDS_GROUP_USER, $fields, $args);
        }

        $theme  = FD::themes();

        $theme->set('params', $params);
        $theme->set('config', FD::config());
        $theme->set('fields', $fields);

        $output = $theme->output('site/registration/dialog.request');

        return $this->ajax->resolve($output);
    }
}
