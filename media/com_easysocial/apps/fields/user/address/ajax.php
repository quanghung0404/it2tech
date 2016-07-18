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

// Include the fields library
FD::import('admin:/includes/fields/dependencies');

// Include helper file.
FD::import('fields:/user/address/helper');

/**
 * Processes ajax calls for the Address field.
 *
 * @since   1.3
 * @author  Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserAddress extends SocialFieldItem
{
    public function getStates()
    {
        $country = FD::input()->getString('country');

        $region = FD::table('Region');
        $region->load(array('type' => SOCIAL_REGION_TYPE_COUNTRY, 'name' => $country, 'state' => SOCIAL_STATE_PUBLISHED));

        $states = $region->getChildren(array('ordering' => $this->params->get('sort')));

        $data = new stdClass();

        foreach ($states as $state) {
            $data->{$state->code} = $state->name;
        }

        FD::ajax()->resolve($data);
    }
}
