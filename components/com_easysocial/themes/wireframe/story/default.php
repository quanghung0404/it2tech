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
<div class="es-story es-responsive is-expanded" data-story="<?php echo $story->id;?>" data-story-form data-story-hashtags="<?php echo implode(',', $story->hashtags); ?>">

    <div class="es-story-avatar es-avatar es-avatar-sm">
        <img alt="<?php echo $this->html( 'string.escape' , $this->my->getName() );?>" src="<?php echo $this->my->getAvatar();?>" />
    </div>

    <div class="es-story-header" data-story-header>
        <div class="es-story-panel-buttons" data-story-panel-buttons>
            <div class="es-story-panel-button active" data-story-panel-button data-story-plugin-name="text">
                <i class="fa fa-pencil" data-story-attachment-icon data-es-provide="tooltip" data-placement="top" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_STORY_POST_UPDATES', true);?>"></i>
                <span><?php echo JText::_('COM_EASYSOCIAL_STORY_STATUS', true);?></span>
            </div>
            <?php if ($story->panels) { ?>
                <?php foreach ($story->panels as $panel) { ?>
                    <div class="es-story-panel-button" data-story-panel-button data-story-plugin-name="<?php echo $panel->name;?>"><?php echo $panel->button->html;?></div>
                <?php } ?>
            <?php } ?>
            <!-- <div class="es-story-panel-button es-story-reset-button" data-story-reset><i class="fa fa-remove"></i></div> -->

            <div class="dropdown es-story-panel-dropdown">
                <a data-bs-toggle="dropdown" class="es-story-panel-dropdown-toggle dropdown-toggle_" href="#">
                    <i class="fa fa-chevron-down"></i>
                </a>
                <div class="dropdown-menu es-story-panel-dropdown-menu">
                    <?php if ($story->panels) { ?>
                        <?php foreach( $story->panels as $panel ){ ?>
                            <div class="es-story-panel-button" data-story-panel-button data-story-plugin-name="<?php echo $panel->name;?>"><?php echo $panel->button->html;?></div>
                        <?php } ?>
                    <?php } ?>                    
                </div>
            </div>
        </div>

    </div>

    <div class="es-story-body" data-story-body>
        <div class="es-story-text-placeholder-ie9"><?php echo JText::_('COM_EASYSOCIAL_STORY_PLACEHOLDER'); ?></div>
        <div class="es-story-text">
            <div class="es-story-textbox mentions-textfield" data-story-textbox>
                <div class="mentions">
                    <div data-mentions-overlay data-default="<?php echo $this->html( 'string.escape' , $story->overlay ); ?>"><?php echo $story->overlay; ?></div>
                    <textarea class="es-story-textfield" name="content" 
                        data-story-textField
                        data-mentions-textarea
                        data-default="<?php echo $this->html( 'string.escape' , $story->content ); ?>"
                        data-initial="0"
                        placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_STORY_PLACEHOLDER' ); ?>"><?php echo $story->content; ?></textarea>
                </div>
            </div>
        </div>
        <div class="es-story-panel-content">
            <div class="es-story-panel-contents" data-story-panel-contents>
                <?php foreach ($story->panels as $panel) { ?>
                    <div class="es-story-panel-content <?php echo $panel->content->classname; ?> for-<?php echo $panel->name; ?>" data-story-panel-content data-story-plugin-name="<?php echo $panel->name; ?>">
                        <?php echo $panel->content->html; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="es-story-footer" data-story-footer>

        <div class="es-story-meta-contents" data-story-meta-contents>

            <?php if ($this->config->get('stream.story.mentions')) { ?>
            <div class="es-story-meta-content" data-story-meta-content="friends">
                <div class="es-story-friends" data-story-friends>
                    <div class="es-story-friends-textbox textboxlist">
                        <input type="text" class="textboxlist-textField" autocomplete="off" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_WHO_ARE_YOU_WITH', true ); ?>" data-textboxlist-textField />
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($this->config->get('stream.story.location')) { ?>
            <div class="es-story-meta-content es-locations" data-story-location data-story-meta-content="location">
                <div class="es-location-map" data-story-location-map>
                    <div>
                        <img class="es-location-map-image" data-story-location-map-image />
                        <div class="es-location-map-actions">
                            <button class="btn btn-es es-location-detect-button btn-sm" type="button" data-story-location-detect-button>
                                <i class="fa fa-map-marker mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_DETECT_MY_LOCATION', true); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="es-location-form" data-story-location-form>
                    <div class="es-location-textbox" data-story-location-textbox data-language="<?php echo FD::config()->get('general.location.language'); ?>">
                        <input type="text" class="input-sm form-control" placeholder="<?php echo JText::_('COM_EASYSOCIAL_WHERE_ARE_YOU_NOW'); ?>" autocomplete="off" data-story-location-textField disabled/>
                        <div class="es-location-autocomplete has-shadow is-sticky" data-story-location-autocomplete>
                            <b><b></b></b>
                            <div class="es-location-suggestions" data-story-location-suggestions>
                            </div>
                        </div>
                    </div>
                    <div class="es-location-buttons">
                        <i class="fd-loading"></i>
                        <a class="es-location-remove-button" href="javascript: void(0);" data-story-location-remove-button><i class="fa fa-remove"></i></a>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($this->config->get('stream.story.moods')) { ?>
            <div class="es-story-meta-content es-story-mood is-empty" data-story-mood data-story-meta-content="mood">
                <div class="es-story-mood-form">
                    <table class="es-story-mood-textbox" data-story-mood-textbox>
                        <tr><td>
                            <div class="es-story-mood-verb" data-story-mood-verb>
                                <?php foreach ($moods as $mood) { ?>
                                    <span<?php echo ($mood->key == 'feeling') ? ' class="active"' : ''; ?> data-story-mood-verb-type="<?php echo $mood->key; ?>"><?php echo JText::_($mood->verb); ?></span>
                                <?php } ?>
                            </div>
                        </td>
                        <td width="100%">
                            <input type="text" class="input-sm form-control" placeholder="<?php echo JText::_('COM_EASYSOCIAL_HOW_ARE_YOU_FEELING'); ?>" autocomplete="off" data-story-mood-textfield />
                        </td>
                        </tr>
                    </table>
                    <div class="es-story-mood-buttons">
                        <a class="es-story-mood-remove-button" href="javascript: void(0);" data-story-mood-remove-button><i class="fa fa-remove"></i></a>
                    </div>
                </div>
                <div class="es-story-mood-presets" data-story-mood-presets>
                    <ul class="list-unstyled">
                    <?php foreach ($moods as $mood) { ?>
                        <?php foreach ($mood->moods as $preset) { ?>
                        <li class="es-story-mood-preset"
                            data-story-mood-preset
                            data-story-mood-icon="<?php echo $preset->icon ?>"
                            data-story-mood-verb="<?php echo $mood->key; ?>"
                            data-story-mood-subject="<?php echo $preset->key; ?>"
                            data-story-mood-text="<?php echo JText::_($preset->text); ?>"
                            data-story-mood-subject-text="<?php echo JText::_($preset->subject); ?>"><i class="es-emoticon <?php echo $preset->icon; ?>"></i> <?php echo JText::_($preset->subject); ?></li>
                        <?php } ?>
                    <?php } ?>
                    </ul>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="es-story-meta-buttons">
            <?php if ($this->config->get('stream.story.mentions')) { ?>
            <div class="es-story-meta-button" data-story-meta-button="friends" data-es-provide="tooltip" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_STORY_FRIENDS_TOOLTIP', true);?>" data-placement="bottom">
                <i class="fa fa-user-plus"></i>
            </div>
            <?php } ?>

            <?php if ($this->config->get('stream.story.location')) { ?>
            <div class="es-story-meta-button" data-story-meta-button="location" data-es-provide="tooltip" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_STORY_LOCATION_TOOLTIP', true);?>" data-placement="bottom">
                <i class="fa fa-map-marker"></i>
            </div>
            <?php } ?>

            <?php if ($this->config->get('stream.story.moods')) { ?>
            <div class="es-story-meta-button" data-story-meta-button="mood" data-es-provide="tooltip" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_STORY_MOOD_TOOLTIP', true);?>" data-placement="bottom">
                <i class="fa fa-smile-o"></i>
            </div>
            <?php } ?>
        </div>

        <div class="es-story-actions btn-group btn-group-sm<?php echo $story->requirePrivacy() ? '' : ' no-privacy'; ?>">
            <button class="btn btn-es-primary es-story-submit" data-story-submit type="button"><?php echo JText::_("COM_EASYSOCIAL_STORY_SHARE"); ?></button>
            <?php if ($story->requirePrivacy()) { ?>
            <div class="es-story-privacy" data-story-privacy><?php echo FD::privacy()->form(null, SOCIAL_TYPE_STORY, $this->my->id, 'story.view', true); ?></div>
            <?php } ?>
        </div>
    </div>

    <input type="hidden" name="target" data-story-target value="<?php echo $story->getTarget(); ?>" />
    <input type="hidden" name="cluster" data-story-cluster value="<?php echo $story->getClusterId(); ?>" />
    <input type="hidden" name="clustertype" data-story-clustertype value="<?php echo $story->getClusterType(); ?>" />

    <i class="loading-indicator"></i>
</div>
