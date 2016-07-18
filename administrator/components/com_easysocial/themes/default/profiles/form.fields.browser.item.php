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
<li <?php if ($app->hidden) { ?>style="display: none;"<?php } ?> data-fields-browser-item data-id="<?php echo $app->id; ?>" data-element="<?php echo $app->element; ?>" data-core="<?php echo $app->core; ?>" data-title="<?php echo $app->title; ?>" data-unique="<?php echo $app->unique; ?>">
    <a href="javascript:void(0);" class="btn btn-es btn-block">
        <i class="<?php echo $app->getParams()->get('icon' , '') ? $app->getParams()->get('icon') : 'icon-field-' . $app->element;?>"></i>
        <?php echo JText::_($app->title); ?>
    </a>
</li>
