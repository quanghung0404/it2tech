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

// Include helper file.
FD::import('fields:/user/address/helper');

/**
 * Profile view for Notes app.
 *
 * @since   1.0
 * @access  public
 */
class AddressFieldWidgetsProfile
{
    public function profileHeaderB($key, $user, $field)
    {
        // Get the data
        $data = $field->data;

        if (!$data) {
            return;
        }

        $my = FD::user();
        $privacyLib = FD::privacy($my->id);

        if (!$privacyLib->validate('core.view', $field->id, SOCIAL_TYPE_FIELD, $user->id)) {
            return;
        }

        $obj = FD::makeObject($data);

        $theme = FD::themes();
        $hide = true;

        foreach ($obj as $k => &$v) {
            $v = $theme->html('string.escape', $v);

            if (!empty($v)) {
                $hide = false;
            }
        }

        if ($hide) {
            return true;
        }

        $params = $field->getParams();

        // Convert country to full text
        if (!empty($obj->country)) {
            $obj->country_code = $obj->country;
            $obj->country = SocialFieldsUserAddressHelper::getCountryName($obj->country, $params->get('data_source'));
        }

        $theme->set('value', $obj);
        $theme->set('params', $field->getParams());

        echo $theme->output('fields/user/address/widgets/display');
    }
}
