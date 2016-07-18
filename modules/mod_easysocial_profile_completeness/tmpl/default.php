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
<div id="fd" class="es mod-es-profile-completeness module-profile-completeness<?php echo $suffix;?>">
    <div class="mod-bd">
        <div class="es-widget">

            <div class="es-widget-title mb-10"><?php echo JText::sprintf('MOD_EASYSOCIAL_PROFILE_COMPLETENESS_PERCENTAGE', $percentage); ?></div>

            <div class="progress mb-10">
              <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage; ?>%;">
              </div>
            </div>
            <a href="<?php echo FRoute::profile(array('layout' => 'edit')); ?>" class="fd-small"><?php echo JText::_('MOD_EASYSOCIAL_PROFILE_COMPLETENESS_COMPLETE_PROFILE_NOW'); ?></a>
        </div>
    </div>
</div>
