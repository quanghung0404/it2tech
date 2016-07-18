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
<dialog>
    <width>780</width>
    <height>500</height>
    <selectors type="json">
    {
        "{closeButton}"  : "[data-close-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function()
        {
            this.parent.close();
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYSOCIAL_REGISTRATION_JOIN_US'); ?></title>
    <content>
        <script type="text/javascript">
            EasySocial.require().script('site/dashboard/dashboard.guest.login').done(function($) {
                $('[data-registermini-popup]').addController('EasySocial.Controller.Dashboard.Guest.Login');
            });
        </script>
        <div id="fd" class="es mod-es-cta module-cta es-responsive wide w960 w768" data-registermini-popup>
            <?php if ($params->get('splash_image', true)) { ?>
            <div class="es-splash-image" style="background-image:url(<?php echo $params->get('splash_image_url', JURI::root() . 'modules/mod_easysocial_registration_requester/images/splash.jpg'); ?>);"></div>
            <?php } ?>

            <div class="es-cta-info">
                <?php if ($params->get('show_heading_title', true)) { ?>
                <h2 class="es-heading mt-20">
                    <?php echo JText::_($params->get('heading_title')); ?>
                </h2>
                <?php } ?>
                <?php if ($params->get('show_heading_desc', true)) { ?>
                <p class="es-text">
                    <?php echo JText::_($params->get('heading_desc')); ?>
                </p>
                <?php } ?>
            </div>
            <div class="es-cta-actions">

                <div class="es-cta-form">
                    <?php if ($params->get('social') && $config->get('oauth.facebook.registration.enabled') && $config->get('registrations.enabled') && $config->get('oauth.facebook.secret') && $config->get('oauth.facebook.app')) { ?>
                        <?php echo FD::oauth('Facebook')->getLoginButton(FRoute::registration(array('layout' => 'oauthDialog', 'client' => 'facebook', 'external' => true), false), false, 'popup', JText::_('MOD_EASYSOCIAL_REGISTRATION_REQUESTER_REGISTER_WITH_YOUR_FACEBOOK_ACCOUNT')); ?>

                    <hr />
                    <?php } ?>
                    <?php if( !empty( $fields ) ) { ?>
                    <form name="registration" method="post" action="<?php echo JRoute::_( 'index.php' );?>" data-registermini-form>
                        <?php foreach( $fields as $field ) { ?>
                            <div class="register-field" data-registermini-fields-item><?php echo $field->output; ?></div>
                        <?php } ?>

                        <button class="btn btn-es-primary btn-block mb-20" type="button" data-registermini-submit><?php echo JText::_('MOD_EASYSOCIAL_REGISTRATION_REQUESTER_REGISTER_NOW_BUTTON');?> &rarr;</button>

                        <input type="hidden" name="option" value="com_easysocial" />
                        <input type="hidden" name="controller" value="registration" />
                        <input type="hidden" name="task" value="miniRegister" />
                        <?php echo $this->html( 'form.token' );?>
                    </form>
                    <?php } ?>
                </div>

                <div class="es-cta-login">
                    <?php echo JText::_('MOD_EASYSOCIAL_REGISTRATION_REQUESTER_ALREADY_SIGNED_UP'); ?> <a href="<?php echo FRoute::login(); ?>"><?php echo JText::_('MOD_EASYSOCIAL_REGISTRATION_REQUESTER_LOGIN_TO_YOUR_ACCOUNT'); ?></a>
                </div>

            </div>
        </div>
    </content>
</dialog>
