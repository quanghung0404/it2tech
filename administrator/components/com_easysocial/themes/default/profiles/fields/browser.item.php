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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<li style="<?php echo $defaultField->hidden ? 'display: none;' : '';?>" 
    data-fields-browser-item data-id="<?php echo $defaultField->id; ?>" 
    data-element="<?php echo $defaultField->element; ?>" 
    data-core="<?php echo $defaultField->core; ?>" 
    data-title="<?php echo $defaultField->title; ?>" 
    data-unique="<?php echo $defaultField->unique; ?>"
>
    <a href="javascript:void(0);" class="btn btn-es btn-block">
        <i class="<?php echo $defaultField->getParams()->get('icon' , '') ? $defaultField->getParams()->get('icon') : 'icon-field-' . $defaultField->element;?>"></i>
        <?php echo JText::_($defaultField->title); ?>
    </a>
</li>
