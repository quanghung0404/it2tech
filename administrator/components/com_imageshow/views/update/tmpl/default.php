<?php
/**
 * @version    $Id: default.php 16584 2012-10-01 11:40:09Z haonv $
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
$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
$products 		= $objJSNUtils->getCurrentElementsOfImageShow();
// Display config form
JSNUpdateHelper::render($this->product, $products);