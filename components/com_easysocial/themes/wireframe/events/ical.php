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
?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:<?php echo md5(uniqid(mt_rand(), true));?>

DTSTAMP:<?php echo gmdate('Ymd').'T'. gmdate('His');?>Z

DTSTART:<?php echo $event->getEventStart()->format('Ymd\THis', true);?>

DTEND:<?php echo $event->getEventEnd()->format('Ymd\THis', true);?>

SUMMARY:<?php echo $event->getname();?>

DESCRIPTION:<?php echo $event->description;?>

LOCATION:<?php echo $event->address;?>

END:VEVENT

END:VCALENDAR
