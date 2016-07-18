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
<?php if( $toolbar ){ ?>

    <?php if( $this->template->get( 'toolbar_notification' , true ) ){ ?>
    <div class="navbar es-toolbar wide<?php echo ( ( !$this->my->id && ( $login ) ) || ( $this->my->id && ( $profile ) ) ) ? ' has-sides' : '';?>" data-notifications>

        <?php if( ( !$this->my->id && ( $login ) ) || ( $this->my->id && ( $profile ) ) ){ ?>
        <div class="es-toolbar-avatar">
            <ul class="fd-nav">

                <?php if( !$this->my->id && ( $login ) ){ ?>
                <li class="dropdown_">
                    <?php echo $this->includeTemplate( 'site/toolbar/default.login' , array( 'facebook' => $facebook )); ?>
                </li>
                <?php } ?>

                <?php if( $this->my->id && ( $profile ) ){ ?>
                    <?php echo $this->includeTemplate( 'site/toolbar/default.profile' ); ?>
                <?php } ?>

            </ul>
        </div>
        <?php } ?>


        <div class="navbar-inner">
            <div class="es-toolbar-wrap">
                <?php if( $this->my->id ){ ?>
                <ul class="fd-nav">

                    <?php if( $friends ){ ?>
                        <?php echo $this->loadTemplate( 'site/toolbar/default.friends' , array( 'requests' => $newRequests, 'popboxPosition' => $popboxPosition, 'popboxCollision' => $popboxCollision ) ); ?>
                    <?php } ?>

                    <?php if( $conversations ){ ?>
                        <?php echo $this->loadTemplate( 'site/toolbar/default.conversations' , array( 'newConversations' => $newConversations, 'popboxPosition' => $popboxPosition, 'popboxCollision' => $popboxCollision ) ); ?>
                    <?php } ?>

                    <?php if( $notifications ){ ?>
                        <?php echo $this->loadTemplate( 'site/toolbar/default.notifications' , array( 'newNotifications' => $newNotifications, 'popboxPosition' => $popboxPosition, 'popboxCollision' => $popboxCollision ) ); ?>
                    <?php } ?>

                </ul>
                <?php } ?>

                <?php if( $search ){ ?>
                    <form action="<?php echo JRoute::_( 'index.php' );?>" method="post">
                    <div class="fd-navbar-search pull-right" data-nav-search>
                        <i class="fa fa-search"></i>
                        <input type="text" name="q" class="search-query" autocomplete="off" data-nav-search-input placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_SEARCH' , true );?>" />
                    </div>

                    <?php if (isset($filterTypes) && $filterTypes) { ?>
                    <div class="es-navbar-search-filter dropdown pull-right" data-nav-search-filter>
                        <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" data-filter-button>
                            <span class="fa fa-cog"></span>
                        </a>
                        <ul class="es-navbar-dropdown es-dropdown-right fd-reset-list dropdown-menu">
                            <li class="es-navbar-dropdown-head">
                                <div class="es-filter-header">
                                    <div><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_DESC');?></div>
                                </div>

                                <div class="es-filter-helper">
                                    <div class="col-cell">
                                        <div class="select-all">
                                            <a href="javascript:void(0);" data-filter-selectall><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_SELECT_ALL'); ?></a>
                                        </div>
                                    </div>

                                    <div class="col-cell">
                                        <div class="deselect-all">
                                            <a href="javascript:void(0);" data-filter-deselectall><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_DESELECT_ALL'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php
                                $count = 0;
                                foreach($filterTypes as $fType) {
                                    $typeAlias = $fType->id . '-' . $fType->title;
                            ?>
                            <li>
                                <div class="es-checkbox">
                                    <input id="search-type-<?php echo $count;?>"
                                            type="checkbox"
                                            name="filtertypes[]"
                                            value="<?php echo $typeAlias; ?>"
                                            <?php echo (isset($fType->checked) && $fType->checked) ? ' checked="true"' : ''; ?>
                                            data-search-filtertypes />
                                    <label for="search-type-<?php echo $count;?>">
                                        <?php echo $fType->displayTitle;?>
                                    </label>
                                </div>
                            </li>
                        <?php
                                $count++;
                            }
                        ?>
                        </ul>
                    </div>
                    <?php } ?>

                        <?php echo $this->html( 'form.itemid' ); ?>
                        <input type="hidden" name="controller" value="search" />
                        <input type="hidden" name="task" value="query" />
                        <input type="hidden" name="option" value="com_easysocial" />
                        <input type="hidden" name="<?php echo FD::token();?>" value="1" />
                    </form>
                <?php } ?>



            </div>

        </div>
    </div>
    <?php } ?>

    <?php if ($this->my->id) { ?>
        <div
            class="es-mainnav-wrap"
        >
            <a href="javascript:void(0);" class="btn btn-es btn-mainnav-toggle"
                data-popbox=""
                data-popbox-id="fd"
                data-popbox-component="es"
                data-popbox-type="frosty-mainnav"
                data-popbox-toggle="click"
                data-popbox-position="bottom"
                data-popbox-target=".frosty-mainnav"
            >
                <i class="ies-grid-view ies-small mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_TOGGLE_SUBMENU');?>
            </a>

            <div class="frosty-mainnav" data-popbox-content>
            <ul class="fd-nav es-mainnav fd-cf">
                <?php if ($this->my->hasCommunityAccess()) { ?>
                    <?php if( $dashboard ){ ?>
                    <li class="<?php echo $view == 'dashboard' ? 'active' : '';?>">
                        <a data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_DASHBOARD' , true );?>"
                            data-placement="top"
                            data-es-provide="tooltip"
                            href="<?php echo FRoute::dashboard();?>"
                        >
                            <i class="fa fa-home"></i>
                        </a>
                    </li>
                    <?php } ?>

                    <li class="<?php echo $view == 'profile' && !$userId ? 'active' : '';?>">
                        <a href="<?php echo FRoute::profile();?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_VIEW_YOUR_PROFILE' );?>
                        </a>
                    </li>
                    <li class="<?php echo $view == 'friends' && $layout != 'invite' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::friends();?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_FRIENDS' );?>
                        </a>
                    </li>

                    <?php if ($this->config->get('friends.invites.enabled')) { ?>
                    <li class="<?php echo $view == 'friends' && $layout == 'invite' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::friends(array('layout' => 'invite'));?>">
                            <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_INVITE_FRIENDS');?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if( $this->config->get( 'followers.enabled' ) ){ ?>
                    <li class="<?php echo $view == 'followers' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::followers();?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_FOLLOWERS' );?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if( $this->config->get( 'photos.enabled' ) ){ ?>
                    <li class="<?php echo $view == 'albums' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::albums( array( 'uid' => $this->my->getAlias() , 'type' => SOCIAL_TYPE_USER ) );?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_PHOTOS' );?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if ($this->config->get('video.enabled')) { ?>
                    <li>
                        <a href="<?php echo FRoute::videos();?>">
                            <?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_VIDEOS');?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if( $this->config->get( 'groups.enabled' ) ){ ?>
                    <li class="<?php echo $view == 'groups' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::groups();?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_GROUPS' );?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if( $this->config->get( 'events.enabled' ) ){ ?>
                    <li class="<?php echo $view == 'events' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::events();?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_EVENTS' );?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if( $this->config->get( 'badges.enabled' ) ){ ?>
                    <li class="<?php echo $view == 'badges' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::badges( array( 'layout' => 'achievements' ) );?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_ACHIEVEMENTS' );?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if( $this->config->get( 'points.enabled' ) ){ ?>
                    <li class="<?php echo $view == 'points' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::points( array( 'layout' => 'history' , 'userid' => $this->my->getAlias() ) );?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_POINTS_HISTORY' );?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if ($this->config->get('apps.browser')) { ?>
                    <li class="<?php echo $view == 'apps' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::apps();?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_APPS' );?>
                        </a>
                    </li>
                    <?php } ?>

                    <li class="<?php echo $view == 'activities' ? 'active' : '';?>">
                        <a href="<?php echo FRoute::activities();?>">
                            <?php echo JText::_( 'COM_EASYSOCIAL_TOOLBAR_PROFILE_ACTIVITIES' );?>
                        </a>
                    </li>
                <?php } ?>
                </ul>
            </div>
        </div>
    <?php } ?>
<?php } ?>
