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
FD::import('fields:/user/country/helper');

class SocialFieldsUserCountry extends SocialFieldItem
{

    /**
     * Displays the form during registration
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function onRegister(&$post, &$registration)
    {
        $countries = SocialFieldsUserCountryHelper::getHTMLContentCountries();
        $selected = $this->processSelectedData(!empty($post[$this->inputName]) ? $post[$this->inputName] : '', '');

        $this->set('countries', $countries);
        $this->set('selected', $selected);

        return $this->display();
    }

    /**
     * Processes the post data and validates the country data.
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function onRegisterValidate(&$post)
    {
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        return $this->validateInput($value);
    }

    /**
     * Displays the edit form when a user edits their profile.
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function onEdit(&$post, &$user, $errors)
    {
        $source = $this->params->get('data_source', 'regions');
        $countries = SocialFieldsUserCountryHelper::getHTMLContentCountries($source);
        $selected = $this->processSelectedData(!empty($post[$this->inputName]) ? $post[$this->inputName] : '', $this->value);


        $this->set('countries', $countries);
        $this->set('selected', $selected);
        $this->set('error', $this->getError($errors));

        return $this->display();
    }

    /**
     * Processes the post data and validates the edit
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function onEditValidate(&$post)
    {
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        return $this->validateInput($value);
    }

    /**
     * Displays the sample html output for the admin
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function onSample()
    {
        $countries = SocialFieldsUserCountryHelper::getHTMLContentCountries();

        $this->set('countries', $countries);

        $this->display();
    }

    private function validateInput($value)
    {
        if ($this->isRequired() && empty($value)) {
            $this->setError(JText::_('PLG_FIELDS_COUNTRY_VALIDATION_REQUIRED'));
            return false;
        }

        $value = !empty($value) ? FD::makeArray($value) : array();

        if ($this->isRequired() && empty($value)) {
            $this->setError(JText::_('PLG_FIELDS_COUNTRY_VALIDATION_REQUIRED'));
            return false;
        }

        if (!$this->isRequired() && empty($value)) {
            return true;
        }

        $count = count($value);

        if ($this->params->get('min') > 0 && $count < $this->params->get('min')) {
            $this->setError(JText::_('PLG_FIELDS_COUNTRY_VALIDATION_MINIMUM_ERROR'));
            return false;
        }

        if ($this->params->get('max') > 0 && $count > $this->params->get('max')) {
            $this->setError(JText::_('PLG_FIELDS_COUNTRY_VALIDATION_MAXIMUM_ERROR'));
            return false;
        }

        return true;
    }

    public function onDisplay($user)
    {
        $value = $this->value;

        if (!$value) {
            return;
        }

        $value = FD::makeArray($value);

        if (!$this->allowedPrivacy($user)) {
            return;
        }

        $field = $this->field;

        $countries = array();
        $aslink = array();

        foreach ($value as $v) {
            $country = SocialFieldsUserCountryHelper::getCountryName($v);

            if ($country) {
                if ($field->type == SOCIAL_FIELDS_GROUP_USER && $field->searchable) {
                    $params = array('layout' => 'advanced');
                    $params['criterias[]'] = $field->unique_key . '|' . $field->element;
                    $params['operators[]'] = 'equal';
                    $params['conditions[]'] = $v;

                    $advsearchLink = FRoute::search($params);
                    $aslink[] = $advsearchLink;
                } else {
                    $aslink[] = '';// give empty value so that the array tally with the countries size.
                }

                $countries[] = $country;
            }
        }

        if (count($countries) === 0) {
            return;
        }

        $this->set('advancedsearchlinks', $aslink);
        $this->set('countries', $countries);

        return $this->display();
    }

    /**
     * Checks if this field is complete.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     * @param  SocialUser    $user The user being checked.
     */
    public function onFieldCheck($user)
    {
        return $this->validateInput($this->value);
    }

    /**
     * Checks if this field is filled in.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  array        $data   The post data.
     * @param  SocialUser   $user   The user being checked.
     */
    public function onProfileCompleteCheck($user)
    {
        if (!FD::config()->get('user.completeprofile.strict') && !$this->isRequired()) {
            return true;
        }

        if (empty($this->value)) {
            return false;
        }

        $value = FD::makeArray($this->value);

        if (empty($value)) {
            return false;
        }

        return true;
    }

    public function onRegisterBeforeSave(&$post, &$user)
    {
        $selected = $this->processSelectedData(!empty($post[$this->inputName]) ? $post[$this->inputName] : '', '');

        $values = array();

        foreach ($selected as $s) {
            $values[] = $s->title;
        }

        $post[$this->inputName] = $values;
    }

    /**
     * Processes the posted data
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function onEditBeforeSave(&$post, &$user)
    {
        $selectedCountries = $this->processSelectedData(!empty($post[$this->inputName]) ? $post[$this->inputName] : '', $this->value);
        $values = array();

        if ($selectedCountries) {

            foreach ($selectedCountries as $country) {

                if (is_string($country)) {
                    $values[] = $country;
                } else {
                    $values[] = $country->title;
                }
            }
        }

        $post[$this->inputName] = $values;
    }

    private function processSelectedData($post, $value)
    {
        $selected = array();

        if ($this->params->get('select_type') === 'textboxlist') {
            // If this is textbox list, POST data is code while value is name
            if (!empty($post)) {
                $selected = FD::makeArray($post);

                $tmp = array();

                foreach ($selected as $s) {
                    $name = SocialFieldsUserCountryHelper::getCountryName($s, $this->params->get('data_source', 'regions'));

                    if ($name) {
                        $t = new stdClass();
                        $t->id = $s;
                        $t->title = $name;

                        $tmp[] = $t;
                    }
                }

                $selected = $tmp;
            } else {
                if (!empty($this->value)) {
                    $selected = FD::makeArray($this->value);

                    $tmp = array();

                    foreach ($selected as $s) {
                        $code = SocialFieldsUserCountryHelper::getCountryCode($s, $this->params->get('data_source', 'regions'));

                        if ($code) {
                            $t = new stdClass();
                            $t->id = $code;
                            $t->title = $s;

                            $tmp[] = $t;
                        }
                    }

                    $selected = $tmp;
                }
            }
        } else {
            $value = !empty($post) ? $post : $this->value;

            $selected = FD::makeArray($value);
        }

        return $selected;
    }
}
