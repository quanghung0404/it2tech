<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>

<div id="fd" class="eb eb-mod ezb-mod mod_easybloglatestblogger<?php echo $params->get('moduleclass_sfx') ?>">
	<?php if(!empty($bloggers)) { ?>
           <?php foreach($bloggers as $blogger) { ?>
        		<div class="eb-mod-item mod-table cell-top">
                	<?php if ($params->get('showavatar', true)) { ?>
                        <div class="mod-cell cell-tight">
                            <a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $blogger->id); ?>" class="mod-avatar mr-10">
        	                   <img src="<?php echo $blogger->profile->getAvatar();?>" width="50" height="50" alt="<?php echo $blogger->profile->getName(); ?>" />
                            </a>
                        </div>
    	            <?php } ?>

                    <div class="mod-cell">
                        <a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $blogger->id); ?>" class="eb-mod-media-title"><?php echo $blogger->profile->getName(); ?></a>
                        <?php //author's total post ?>
                        <?php if ($params->get('showcount', true)) { ?>
                            <div class="mod-muted">
                                <?php echo $blogger->totalPost.' '.JText::_('MOD_EASYBLOGLATESTBLOGGER_POST_COUNT');  ?>
                            </div>
                        <?php } ?>

                        <?php if ($params->get('showbio', true)) { ?>
                                <?php if ($blogger->biography != '') { ?>
                                    <div class="eb-mod-media-meta"><?php echo strip_tags($blogger->biography); ?></div>
                                <?php } else { ?>
                                    "..."
                                <?php } ?>
                        <?php } ?>

                        <?php if ($params->get('showwebsite', true) && $blogger->profile->getWebsite() != '' && !($blogger->profile->getWebsite() == 'http://')) { ?>
                            <div class="eb-mod-media-meta"><?php echo '<a href="'.$blogger->profile->getWebsite().'" target="_blank">'.$blogger->profile->getWebsite().'</a>'; ?></div>
                        <?php } ?>
                    </div>
        		</div>
    	   <?php } ?>
    <?php } else { ?>
    	<div class="mod-item-nothing">
    		<?php echo JText::_('MOD_EASYBLOGLATESTBLOGGER_NO_BLOGGER'); ?>
    	</div>
    <?php } ?>

</div>
