<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row mb-10 mt-10">
    <div class="col-md-12">
        <blockquote>
            <?php echo strip_tags($content); ?>
        </blockquote>

        <div>
            <a href="<?php echo $permalink; ?>#answer" class="mt-5"><?php echo JText::_('APP_EVENT_DISCUSSIONS_VIEW_ANSWER'); ?></a>
            &nbsp;&middot;&nbsp;
            <a href="<?php echo $permalink; ?>" class="mt-5"><?php echo JText::_('APP_EVENT_DISCUSSIONS_VIEW_DISCUSSION'); ?> &rarr;</a>
        </div>
    </div>
</div>
