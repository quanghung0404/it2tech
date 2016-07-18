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

/**
 * Field application for date time
 *
 * Format to follow for FD::date()
 * Y-M-D H:M:S
 *
 * @since   1.0
 * @author  Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserDatetime extends SocialFieldItem
{

    /**
     * format the value used in data export
     *
     * @since   1.3
     * @access  public
     * @param   array,
     * @param   userid
     */
    public function onExport($data, $user)
    {
        $field = $this->field;

        $formatted = array('date' => '',
                           'timezone' => '');

        if (isset($data[$field->id])) {
            $formatted['date'] = isset($data[$field->id]['date']) ? $data[$field->id]['date'] : '';
            $formatted['timezone'] = isset($data[$field->id]['timezone']) ? $data[$field->id]['timezone'] : '';
        }

        return $formatted;
    }


    public function getValue()
    {
        $container = $this->getValueContainer();

        $container->value = $this->getDatetimeValue($container->raw);

        $container->data = $container->value->toSql();

        return $container;
    }

    public function getDisplayValue()
    {
        $dateObj = $this->getValue();

        return $dateObj->toString();
    }

    /**
     * Displays the field input for user when they register their account.
     *
     * @since   1.0
     * @access  public
     * @param   array
     * @param   SocialTableRegistration
     * @return  string  The html output.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onRegister(&$post, &$registration)
    {
        $postArray = array();

        if (!empty($post[$this->inputName]) && FD::json()->isJsonString($post[$this->inputName])) {
            $postArray = FD::makeArray($post[$this->inputName]);
        }

        $value = !empty($postArray['date']) ? $postArray['date'] : null;

        $date = $this->getDatetimeValue($value);

        $string = '';

        if ($date->isValid()) {
            $string = $date->toSql();
        }

        $this->set('date', $string);

        $this->set('dateObject', $date);

        // Check for errors
        $error = $registration->getErrors($this->inputName);

        // Set errors.
        $this->set('error', $error);

        $this->set('yearPrivacy', false);

        $yearRange = $this->getYearRange();

        $range = array();

        if ($yearRange !== false) {
            $range = range($yearRange->min, $yearRange->max);
        }

        $this->set('yearRange', $yearRange);

        $this->set('range', $range);

        if ($this->params->get('allow_timezone')) {
            $this->set('timezones', $this->getTimezones());
        }

        $timezone = !empty($postArray['timezone']) ? $postArray['timezone'] : $this->getUserTimezone();

        $this->set('timezone', $timezone);

        $calendarDateFormat = $this->getCalendarDateFormat();

        $this->set('calendarDateFormat', $calendarDateFormat);

        $theme = FD::themes();

        $year = $theme->loadTemplate('fields/user/datetime/form.year', array('year' => $date->year, 'yearRange' => $yearRange));
        $month = $theme->loadTemplate('fields/user/datetime/form.month', array('month' => $date->month));
        $day = $theme->loadTemplate('fields/user/datetime/form.day', array('day' => $date->day, 'maxDay' => $date->isValid() ? $date->format('t') : 31));

        $dateHTML = $this->getDateDropdown($year, $month, $day);

        $this->set('dateHTML', $dateHTML);

        // Display the output.
        return $this->display();
    }

    /**
     * Determines whether there's any errors in the submission in the registration form.
     *
     * @since   1.0
     * @access  public
     * @param   array   The posted data.
     * @param   SocialTableRegistration     The registration ORM table.
     * @return  bool    Determines if the system should proceed or throw errors.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onRegisterValidate(&$post)
    {
        return $this->validateDatetime($post);
    }

    /**
     * Executes before a user's registration is saved.
     *
     * @since   1.0
     * @access  public
     * @param   array   The posted data.
     * @return  bool    Determines if the system should proceed or throw errors.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onRegisterBeforeSave(&$post)
    {
        return $this->saveDatetime($post);
    }

    /**
     * Displays the field input for user when they edit their profile.
     *
     * @since   1.0
     * @access  public
     * @param   Array       The post data
     * @param   SocialUser  The user object
     * @param   Array       The error data.
     * @return  string      The html output.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onEdit(&$post, &$user, $errors)
    {
        $postArray = array();

        $app = JFactory::getApplication();

        if (!empty($post[$this->inputName]) && FD::json()->isJsonString($post[$this->inputName])) {
            $postArray = FD::makeArray($post[$this->inputName]);
        }

        $value = !empty($postArray['date']) ? $postArray['date'] : isset($this->value['date']) ? $this->value['date'] : '';

        $string = '';

        $date = $this->getDatetimeValue($value);

        if ($date->isValid()) {
            $string = $date->toSql();
        }

        $this->set('date', $string);

        $this->set('dateObject', $date);

        $error = $this->getError($errors);

        $this->set('error', $error);

        $yearPrivacy = $this->params->get('year_privacy');

        // We do not want to display the year privacy at backend.
        if ($app->isAdmin() && !$user->id) {
            $yearPrivacy = false;
        }

        $this->set('yearPrivacy', $yearPrivacy);

        $yearRange = $this->getYearRange();

        $this->set('yearRange', $yearRange);

        if ($this->params->get('allow_timezone')) {
            $this->set('timezones', $this->getTimezones());
        }

        $timezone = !empty($postArray['timezone']) ? $postArray['timezone'] : !empty($this->value['timezone']) ? $this->value['timezone'] : $this->getUserTimezone($user);

        $this->set('timezone', $timezone);

        $calendarDateFormat = $this->getCalendarDateFormat();

        $this->set('calendarDateFormat', $calendarDateFormat);

        $theme = FD::themes();

        $year = $theme->loadTemplate('fields/user/datetime/form.year', array('year' => $date->year, 'yearRange' => $yearRange));
        $month = $theme->loadTemplate('fields/user/datetime/form.month', array('month' => $date->month));
        $day = $theme->loadTemplate('fields/user/datetime/form.day', array('day' => $date->day, 'maxDay' => $date->isValid() ? $date->format('t') : 31));

        $dateHTML = $this->getDateDropdown($year, $month, $day);

        $this->set('dateHTML', $dateHTML);

        // Display the output.
        return $this->display();
    }

    /**
     * Determines whether there's any errors in the submission in the registration form.
     *
     * @since   1.0
     * @access  public
     * @param   array   The posted data.
     * @param   SocialUser  The user object
     * @return  bool    Determines if the system should proceed or throw errors.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onEditValidate(&$post, &$user)
    {
        return $this->validateDatetime($post);
    }

    /**
     * Executes before a user's registration is saved.
     *
     * @since   1.0
     * @access  public
     * @param   array   The posted data.
     * @param   SocialUser  The user object
     * @return  bool    Determines if the system should proceed or throw errors.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onEditBeforeSave(&$post, &$user)
    {
        return $this->saveDatetime($post, $user);
    }

    /**
     * Responsible to output the html codes that is displayed to
     * a user when their profile is viewed.
     *
     * @since   1.0
     * @access  public
     * @param   SocialUser  The user object
     * @return  string  The html output.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onDisplay($user)
    {
        if (empty($this->value)) {
            return;
        }

        if (!$this->allowedPrivacy($user)) {
            return;
        }

        if (empty($this->value['date'])) {
            return;
        }

        $data = $this->getDatetimeValue($this->value['date']);

        if ($data->isEmpty()) {
            return;
        }

        $allowYear = true;

        if ($this->params->get('year_privacy')) {
            $allowYear = $this->allowedPrivacy($user, 'year');
        }

        $format = $allowYear ? 'd M Y' : 'd M';

        switch ($this->params->get('date_format')) {
            case 2:
            case '2':
                $format = $allowYear ? 'M d Y' : 'M d';
                break;
            case 3:
            case '3':
                $format = $allowYear ? 'Y d M' : 'd M';
                break;
            case 4:
            case '4':
                $format = $allowYear ? 'Y M d' : 'M d';
                break;
        }

        if ($this->params->get('allow_time')) {
            $format .= $this->params->get('time_format') == 1 ? ' g:i:sA' : ' H:i:s';
        }

        // linkage to advanced search page.
        // place the code here so that the timezone wont kick in. we search the date using GMT value.
        $field = $this->field;

        $advGroups = array(SOCIAL_FIELDS_GROUP_GROUP, SOCIAL_FIELDS_GROUP_USER);

        if (in_array($field->type, $advGroups) && $allowYear && $field->searchable) {

            $date = $data->toFormat('Y-m-d');

            $params = array( 'layout' => 'advanced' );
            if ($field->type != SOCIAL_FIELDS_GROUP_USER) {
                $params['type'] = $field->type;
                $params['uid'] = $field->uid;
            }
            $params['criterias[]'] = $field->unique_key . '|' . $field->element;
            $params['operators[]'] = 'between';
            $params['conditions[]'] = $date . ' 00:00:00' . '|' . $date . ' 23:59:59';

            $advsearchLink = FRoute::search($params);
            $this->set( 'advancedsearchlink'    , $advsearchLink );
        }

        $this->set('allowYearSettings', $this->params->get('year_privacy') && FD::user()->id === $user->id);

        if ($this->params->get('allow_timezone')) {
            $timezone = isset($this->value['timezone']) ? $this->value['timezone'] : $this->getUserTimezone($user);

            $this->set('timezone', $timezone);

            $timezones = $this->getTimezones();

            $this->set('timezones', $timezones);

            $data->setTimezone($timezone);
        }

        // Push variables into theme.
        $this->set('date', $data->toFormat($format));

        $this->set('dateObject', $data);

        $this->set('user', $user);

        return $this->display();
    }

    /**
     * Displays the sample html codes when the field is added into the profile.
     *
     * @since   1.0
     * @access  public
     * @return  string  The html output.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function onSample()
    {
        $this->set('yearPrivacy', $this->params->get('year_privacy'));

        $yearRange = $this->getYearRange();

        $range = array();

        if ($yearRange !== false) {
            $range = range($yearRange->min, $yearRange->max);
        }

        $this->set('maxDay' , 31);

        $this->set('yearRange', $yearRange);

        $this->set('range', $range);

        return $this->display();
    }

    public function getDatetimeValue($data = null)
    {
        $dateObj = new SocialFieldsUserDateTimeObject;

        if (empty($data))
        {
            return $dateObj;
        }

        $dateObj->init($data);

        return $dateObj;
    }

    /**
     * Performs php validation on this field
     *
     * @since   1.0
     * @access  public
     * @param   array   The posted data.
     * @return  bool    Determines if the system should proceed or throw errors.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    protected function validateDatetime(&$post)
    {
        $value = isset($post[$this->inputName]) ? $post[$this->inputName] : '';

        $json = FD::json();

        $value = $json->isJsonString($value) ? $json->decode($value) : (object) array();

        $date = isset($value->date) ? $value->date : '';

        // Since the values are stored differently we need to compute the date back.
        $date = $this->getDatetimeValue($date);

        // Determines if this field is required
        if ($date->isEmpty()) {
            if (!$this->isRequired()) {
                return true;
            }

            $this->setError(JText::_('PLG_FIELDS_DATETIME_VALIDATION_PLEASE_SELECT_DATETIME'));

            return false;
        }

        if (!$date->isValid()) {
            // If all date are empty, then just unset it
            $post[$this->inputName] = null;
            unset($post[$this->inputName]);

            $this->setError(JText::_('PLG_FIELDS_DATETIME_VALIDATION_INVALID_DATE_FORMAT'));

            return false;
        }

        // Check for year range
        $range = $this->getYearRange();

        if ($range !== false && !empty($date->year) && ($date->year < $range->min || $date->year > $range->max)) {
            $this->setError(JText::_('PLG_FIELDS_DATETIME_VALIDATION_YEAR_OUT_OF_RANGE'));

            return false;
        }

        return true;
    }

    protected function saveDatetime(&$post, $user = null)
    {
        $value = isset($post[$this->inputName]) ? $post[$this->inputName] : '';

        $json = FD::json();

        $value = $json->isJsonString($value) ? $json->decode($value) : (object) array();

        $date = isset($value->date) ? $value->date : '';
        $timezone = isset($value->timezone) ? $value->timezone : $this->getUserTimezone($user);

        // Since the values are stored differently we need to compute the date back.
        $date = $this->getDatetimeValue($date);

        if ($date->isValid()) {
            // Let's set this value back to the proper element.
            // $post[$this->inputName] = array('date' => $date->toJson(), 'raw' => $date->toSql());

            $post[$this->inputName] = array(
                'date' => $date->toSql(),
                'timezone' => $timezone
            );
        } else {
            //unset($post[$this->inputName]);
            $post[$this->inputName] = array(
                'date' => ''
            );
        }

        return true;
    }

    protected function getYearRange()
    {
        $currentYear = FD::date()->toFormat('Y');

        $minyear = $this->params->get('yearfrom');
        $maxyear = $this->params->get('yearto');

        if (empty($minyear) && empty($maxyear)) {
            return false;
        }

        if (empty($minyear)) {
            $minyear = '1930';
        }

        if (empty($maxyear)) {
            $maxyear = $currentYear + 100;
        }

        if (stristr($minyear, '-') || stristr ($minyear, '+')) {
            $minyear = $currentYear + $minyear;
        }

        if (stristr($maxyear, '-') || stristr ($maxyear, '+')) {
            $maxyear = $currentYear + $maxyear;
        }

        $range = (object) array(
            'min' => $minyear,
            'max' => $maxyear
        );

        return $range;
    }

    protected function getTimezones()
    {
        static $timezones = array();

        if (empty($timezones)) {
            $zones = DateTimeZone::listIdentifiers();

            foreach ($zones as $zone) {
                $key = strstr($zone, '/', true);

                if (!empty($key)) {
                    $timezones[$key][] = $zone;
                }
            }
        }

        return $timezones;
    }

    protected function getUserTimezone($user = null)
    {
        if (empty($user)) {
            $user = FD::user();
        }

        $tz = $user->getParam('timezone');

        if (empty($tz)) {
            $tz = JFactory::getConfig()->get('offset', 'UTC');
        }

        return $tz;
    }

    protected function getDateDropdown($year, $month, $day)
    {
        $dateDropdownHTML = '';

        switch ((int) $this->params->get('date_format')) {
            default:
            case 1:
                return $day . $month . $year;
                break;

            case 2:
                return $month . $day . $year;
                break;

            case 3:
                return $year . $day . $month;
                break;

            case 4:
                return $year . $month . $day;
                break;
        }

    }


    protected function getCalendarDateFormat()
    {
        $format = '';

        switch ((int) $this->params->get('date_format')) {
            default:
            case 1:
                $format = 'DD/MM/YYYY';
                break;

            case 2:
                $format = 'MM/DD/YYYY';
                break;

            case 3:
                $format = 'YYYY/DD/MM';
                break;

            case 4:
                $format = 'YYYY/MM/DD';
                break;
        }

        if (!$this->params->get('allow_time')) {
            return $format;
        }

        switch ((int) $this->params->get('time_format')) {
            case 1:
                $format .= ' hh:mm A';
                break;

            default:
            case 2:
                $format .= ' HH:mm';
                break;
        }

        return $format;
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
        // Determines if this field is required
        $required   = $this->isRequired();

        if ($required && empty($this->value))
        {
            $this->setError(JText::_('PLG_FIELDS_DATETIME_VALIDATION_PLEASE_ENTER_DATE'));
            return false;
        }

        $data   = $this->getDatetimeValue($this->value);

        if ($required && $data->isEmpty())
        {
            $this->setError(JText::_('PLG_FIELDS_DATETIME_VALIDATION_PLEASE_SELECT_DATETIME'));

            return false;
        }

        return true;
    }

    /**
     * Trigger to get this field's value for various purposes.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     * @param  SocialUser    $user The user being checked.
     * @return Mixed               The value data.
     */
    public function onGetValue($user)
    {
        return $this->getValue();
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

        $datetime = $this->getDatetimeValue($this->value);

        if ($datetime->isEmpty()) {
            return false;
        }

        return true;
    }
}

class SocialFieldsUserDateTimeObject
{
    public $year;
    public $month;
    public $day;
    public $hour = '00';
    public $minute = '00';
    public $second = '00';

    private $date;

    public function __construct()
    {
        $args = func_get_args();

        if (empty($args))
        {
            return true;
        }

        return call_user_func_array(array($this, 'init'), $args);
    }

    public function init()
    {
        $json = FD::json();

        $args = func_get_args();

        $count = func_num_args();

        if ($count === 1 && is_string($args[0]) && !empty($args[0])) {
            if ($json->isJsonString($args[0])) {
                $args[0] = $json->decode($args[0]);
            } else {
                if (strtotime($args[0])) {
                    $args[0] = FD::date($args[0], false);
                }
            }
        }

        $keys = array('year', 'month', 'day', 'hour', 'minute', 'second');

        if ($count === 1 && (is_object($args[0]) || is_array($args[0]))) {
            $data = (object) $args[0];

            // Exception if date or timezone key exist
            if (isset($data->date)) {
                $date = null;
                if ($json->isJsonString($data->date)) {
                    $tmp = $json->decode($data->date);
                    $tmpDateString = $tmp->year . '-' . $tmp->month . '-' . $tmp->day;

                    $date = FD::date($tmpDateString, false);

                } else {
                    $date = FD::date($data->date, false);

                }

                if (isset($data->timezone)) {
                    $date->setTimezone(new DateTimeZone($data->timezone));
                }

                $this->year = $date->format('Y');
                $this->month = $date->format('m');
                $this->day = $date->format('d');
                $this->hour = $date->format('H');
                $this->minute = $date->format('i');
                $this->second = $date->format('s');

                unset($data->date);
                unset($data->timezone);
            }

            foreach ($keys as $key) {
                if (isset($data->$key)) {
                    $this->$key = $data->$key;
                }
            }
        }

        if ($count === 1 && $args[0] instanceof SocialDate) {
            $date = $args[0];

            $this->year = $date->toFormat('Y');
            $this->month = $date->toFormat('m');
            $this->day = $date->toFormat('d');
            $this->hour = $date->toFormat('H');
            $this->minute = $date->toFormat('i');
            $this->second = $date->toFormat('s');
        }

        if ($count > 1) {
            foreach ($args as $i => $arg) {
                $this->{$keys[$i]} = $arg;
            }
        }

        $this->date = $this->toDate();

        return true;
    }

    public function isEmpty()
    {
        foreach ($this->toArray() as $k => $v) {
            // we do not want to test against the private 'date'
            if ($k == 'date') {
                continue;
            }
            if (empty($v)) {
                return true;
            }
        }

        return false;
    }

    public function isValid()
    {
        return !$this->isEmpty() && strtotime($this->day . '-' . $this->month . '-' . $this->year . ' ' . $this->hour . ':' . $this->minute . ':' . $this->second);
    }

    public function toJSON()
    {
        return FD::json()->encode($this->toArray());
    }

    public function toDate()
    {
        if (empty($this->date)) {
            if ($this->isEmpty()) {
                $this->date = FD::date();
            } else {
                $this->date = FD::date($this->year . '-' . $this->month . '-' . $this->day . ' ' . $this->hour . ':' . $this->minute . ':' . $this->second, false);
            }
        }

        return $this->date;
    }

    public function toArray($publicOnly = true)
    {
        if ($publicOnly) {
            return call_user_func('get_object_vars', $this);
        }

        return get_object_vars($this);
    }

    public function toFormat($format, $local = true)
    {
        return $this->toDate()->toFormat($format, $local);
    }

    public function format($format, $local = true)
    {
        return $this->toDate()->toFormat($format, $local);
    }

    public function toSql()
    {
        return $this->toDate()->toSql();
    }

    public function toString()
    {
        return $this->day . ' ' . JText::_($this->toFormat('F')) . ' ' . $this->year;
    }

    public function toUnix()
    {
        return $this->toDate()->toUnix();
    }


    public function toAge()
    {
        $now = FD::date();

        $years = floor(($now->toFormat('U') - $this->toFormat('U')) / (60*60*24*365));

        return $years;
    }

    public function setTimezone($dtz)
    {
        if (empty($dtz)) {
            return $this;
        }

        if (is_string($dtz)) {
            $dtz = new DateTimeZone($dtz);
        }

        $this->date->setTimezone($dtz);
    }

    public function __toString()
    {
        return $this->isValid() ? $this->toString() : '';
    }
}
