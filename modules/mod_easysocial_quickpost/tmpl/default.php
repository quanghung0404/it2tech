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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="fd" class="es mod-es-quickpost<?php echo $suffix;?>" data-quickpost-module>
    <form action="<?php echo JRoute::_('index.php');?>" method="post">
        <div class="es-story is-expanded es-responsive">
            <div class="es-story-body">
                <div class="es-story-text">
                    <div class="es-story-textbox mentions-textfield" data-story-textbox>
                        <div class="mentions">
                            <div data-mentions-overlay data-default=""></div>
                            <textarea class="es-quickpost-textfield" name="content" id="content" autocomplete="off" data-quickpost-content></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="es-story-footer">
                <div class="es-story-actions btn-group btn-group-sm">
                    <div class="btn btn-es-primary es-story-submit" data-quickpost-userid="<?php echo $my->id; ?>" data-quickpost-submit>
                        <a href="javascript:void(0);">
                            <span><?php echo JText::_('MOD_EASYSOCIAL_QUICKPOST_SHARE');?></span>
                        </a>
                    </div>

                    <div class="es-story-privacy" data-story-privacy>
                        <?php echo FD::privacy()->form(null, SOCIAL_TYPE_STORY, $my->id, 'story.view', true); ?>
                    </div>
                </div>
            </div>
        </div>
    
        <div data-quickpost-message></div>

        <input type="hidden" name="privacy" value="public" />
        <input type="hidden" name="option" value="com_easysocial" />
        <input type="hidden" name="controller" value="story" />
        <input type="hidden" name="task" value="create" />
        <?php echo JHtml::_('form.token');?>
    </form>
</div>