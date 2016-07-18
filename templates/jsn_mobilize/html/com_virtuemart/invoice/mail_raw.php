<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>

<?php
// Shop desc for shopper and vendor
// echo $this->loadTemplate('header');
// Message for shopper or vendor
echo $this->loadTemplate('shopper');
// render shipto billto adresses
echo $this->loadTemplate('shopperaddresses');
// render price list
echo  $this->loadTemplate('pricelist');
//dump($salesPriceShipment , 'rawmail');
// more infos
//echo $this->loadTemplate($this->recipient.'_more');
// end of mail
echo $this->loadTemplate('footer');
?>
