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
<a href="<?php echo $user->getPermalink();?>" alt="<?php echo $this->html('string.escape', $user->getName());?>" class="<?php echo $avatar ? ' es-avatar' : '';?>"
    <?php if ($popbox){ ?>
    data-popbox="module://easysocial/profile/popbox"
    data-popbox-position="<?php echo $position;?>"
    data-user-id="<?php echo $user->id;?>"
    <?php } ?>
    >
    <?php if ($avatar) { ?>
        <img src="<?php echo $user->getAvatar();?>" title="<?php echo $this->html('string.escape', $user->getName());?>" alt="<?php echo $this->html('string.escape', $user->getName());?>" />
    <?php } else { ?>
        <?php echo $user->getName();?>
    <?php } ?>
</a>
