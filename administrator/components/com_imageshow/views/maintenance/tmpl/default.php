<?php
/**
 * @version    $Id: default.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Display messages
if (JRequest::getInt('ajax') != 1)
{
	echo $this->msgs;
}

// Display config form
JSNConfigHelper::render($this->config);
$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
$products 		= $objJSNUtils->getCurrentElementsOfImageShow();
// Display footer
JSNHtmlGenerate::footer($products);
