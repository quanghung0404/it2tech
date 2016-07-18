<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$defaultName = isset($user->name) ? $user->name : '';
?>

<div class="o-avatar-status<?php echo ($user->isOnline()) ? ' is-online': ' is-offline'; ?>">
    <?php if ($status && $this->config->get('layout_user_online')) { ?>
    	<div class="o-avatar-status__indicator"></div>
    <?php } ?>

	<a href="<?php echo $user->getPermalink();?>"
	    class="o-avatar o-avatar--<?php echo $size; ?>"

        <?php if (!$popbox && !$this->config->get('integration_easysocial_popbox')) { ?>
        data-ed-provide="tooltip"
        data-placement="top"
        title="<?php echo $user->getName($defaultName);?>"
        <?php } ?>

        <?php if ($popbox && !$this->config->get('integration_easysocial_popbox')) { ?>
        data-ed-popbox="ajax://site/views/profile/popbox"
        data-ed-popbox-position="top-left"
        data-ed-popbox-toggle="hover"
        data-ed-popbox-offset="4"
        data-ed-popbox-type="avatar"
        data-ed-popbox-component="popbox--avatar"
        data-ed-popbox-cache="1"
        data-args-id="<?php echo $user->id; ?>"
        <?php } ?>
	>
	    <?php if ($this->config->get('layout_avatar')) { ?>
	        <img src="<?php echo $user->getAvatar();?>" alt="<?php echo $this->escape($user->getName($defaultName));?>"<?php echo ED::easysocial()->getPopbox($user->id);?>/>
	    <?php } else { ?>
	        <span class="o-avatar o-avatar--<?php echo $size; ?> o-avatar--text o-avatar--bg-<?php echo $user->getNameInitial()->code;?>"><?php echo $user->getNameInitial()->text;?></span>
	    <?php } ?>
	</a>
</div>

<?php if( $this->config->get('main_ranking') && $rank){ ?>
    <div class="ed-rank-bar ed-rank-bar--max-width-no t-lg-mt--sm" data-original-title="<?php echo $this->escape(ED::getUserRanks($user->id)); ?>">
        <div class="ed-rank-bar__progress" style="width: <?php echo $this->escape(ED::getUserRankScore($user->id)); ?>%"></div>
    </div>
<?php } ?>
