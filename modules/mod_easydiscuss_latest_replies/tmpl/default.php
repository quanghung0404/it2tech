<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="ed" class="ed-mod m-leaderboard <?php echo $params->get('moduleclass_sfx');?>">
    <div class="ed-list--vertical has-dividers--bottom-space">

    <?php foreach($replies as $reply) { ?>
        <div class="ed-list__item">

            <div class="o-flag">
            <?php if ($params->get('show_replies_avatar')) { ?>
                <div class="o-flag__img t-lg-mr--md">
                    <a class="o-avatar" href="<?php echo $reply->profile->getPermalink(); ?>">
                        <img src="<?php echo $reply->profile->getAvatar(); ?>">
                    </a>
                </div>
            <?php } ?>
                <div class="o-flag__body">
                    <a href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=post&id='.$reply->parent_id); ?>" class="m-post-title t-lg-mb--sm">
                        <?php echo ED::string()->escape($reply->question->title); ?>
                    </a>
                    <div class="m-list--inline t-lg-mb-sm">
                        <div class="m-list__item">
                             <div class="m-post-meta t-fs--sm"> <?php echo JText::sprintf( 'MOD_LATESTREPLIES_REPLIED_BY' , '<a href="' . $reply->profile->getLink() . '">' . $reply->profile->getName() . '</a>' );?></div>
                        </div>                        
                    </div>   
                </div>
            </div>

            <div class="m-list__item">
            	<?php echo $reply->content;?>
            </div>

            <div class="m-list__item">
                 <div class="m-post-meta t-fs--sm"><?php echo ED::date()->toLapsed($reply->created); ?></div>
            </div>
        </div>
    <?php } ?>
    </div>
</div>