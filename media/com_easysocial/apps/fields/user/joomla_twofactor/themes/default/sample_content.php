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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div data-field-joomla-twofactor>
    <?php echo $this->html('grid.boolean', $inputName, true, $inputName); ?>

    <div class="mb-20 mt-10" data-auth-selection>
        <select name="twofactor_method" class="form-control input-sm" data-auth-selector>
            <option value=""><?php echo JText::_('PLG_EASYSOCIAL_FIELDS_TWOFACTOR_SELECT_AUTHENTICATION_METHOD');?>
        </select>
    </div>

</div>
