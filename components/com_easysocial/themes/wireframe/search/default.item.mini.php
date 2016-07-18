<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

$maxchar    = 20;

$title 		= ( JString::strlen( $item->title ) > $maxchar ) ? JString::substr( $item->title, 0, $maxchar ) . JText::_( 'COM_EASYSOCIAL_ELLIPSES' ) : $item->title;

$objectImg  = ( $item->image ) ? $item->image : '';
$objectName = $title;
$objectLink = $item->link;
?>

<li class="navSearchItem"
    data-search-item
    data-search-item-id="<?php echo $item->id; ?>"
    data-search-item-type="<?php echo $item->utype; ?>"
    data-search-item-typeid="<?php echo $item->uid; ?>"
    data-search-custom-name="<?php echo $objectName; ?>"
    data-search-custom-avatar="<?php echo $objectImg; ?>"
    >

    <a href="<?php echo $objectLink; ?>">
        <span class="es-avatar pull-left">
            <img class="app-icon-small mr-5" src="<?php echo $objectImg; ?>" />
        </span>
        <span class="search-result-name">
            <i class="fa <?php echo $item->icon; ?>  mr-5"></i>
            <?php echo $objectName; ?>
        </span>
    </a>
</li>
