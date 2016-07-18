<?php
/**
* @package      EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<tr>
    <td style="text-align: center;padding: 40px 10px 0;">
        <div style="margin-bottom:15px;">
            <div style="font-family:Arial;font-size:32px;font-weight:normal;color:#333;display:block; margin: 4px 0">
                <?php echo JText::_('COM_EASYSOCIAL_EMAILS_REGISTRATION_HEADING'); ?>
            </div>
            <div style="font-size:12px; color: #798796;font-weight:normal">
                <?php echo JText::_('COM_EASYSOCIAL_EMAILS_REGISTRATION_SUBHEADING'); ?>
            </div>
        </div>
    </td>
</tr>


<tr>
    <td style="text-align: center;">

        <div style="margin:30px auto;text-align:center;display:block">
            <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
        </div>

        <p style="margin-bottom: 20px;">
            <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REGISTRATION_THANK_YOU_FOR_REGISTERING' ); ?>
        </p>

        <a href="<?php echo $activation;?>" style="
        display:inline-block;
        text-decoration:none;
        font-weight:bold;
        margin-top: 20px;
        padding:10px 15px;
        line-height:20px;
        color:#fff;font-size: 12px;
        background-color: #83B3DD;
        background-image: linear-gradient(to bottom, #91C2EA, #6D9CCA);
        background-repeat: repeat-x;
        border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
        border-style: solid;
        border-width: 1px;
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
        border-radius:2px; -moz-border-radius:2px; -webkit-border-radius:2px;
        "><?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_ACTIVATE_NOW' ); ?></a>

        <p>
            <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REGISTRATION_ACTIVATION_ALTERNATIVE' ); ?>
            <a href="<?php echo FRoute::registration( array( 'external' => true , 'layout' => 'activation' , 'userid' => $id ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REGISTRATION_ACTIVATION_ALTERNATIVE_THIS_PAGE');?></a>.
        </p>

        <blockquote style="margin-bottom: 50px;">
            <?php echo $token; ?>
        </blockquote>

        <table align="center" width="96px" border="0" cellpadding="0" cellspacing="0">
        <tbody><tr>
        <td width="96" height="96" style="border-collapse:collapse;"><span style="display:block;border:1px solid #f5f5f5;width:96px;height:96px;padding:3px;border-radius:50%;background:#fff;">
            <img src="<?php echo $avatar;?>" alt="" style="width:96px;height: 96px;border-radius:50%; -moz-border-radius:50%; -webkit-border-radius:50%;background:#fff; "/>
        </span></td></tr>
        </tbody></table>

        <table align="center" width="380" style="margin-top:-10px" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="color:#888;border-top: 1px solid #ebebeb;padding: 15px 20px; background-color:#f8f9fb;font-size:13px;text-align:center">
                    <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_USERNAME' ); ?>: <?php echo $username;?>
                </td>
            </tr>
        </table>



        <span style="margin:30px auto;text-align:center;display:block">
            <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
        </span>

        <span style="font-family:Arial;font-size:26px;font-weight:normal;color:#333;display:block; margin: 4px 0 20px;"><?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REGISTRATION_WHATS_NEXT' );?></span>

        <table width="520" align="center" style="border:1px solid #ebebeb" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <table width="250" align="center">
                        <tr>
                            <td valign="top" style="padding: 10px 0;">
                                <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/icon-user.png" alt="" />
                            </td>
                            <td valign="top" style="text-align:left;padding: 10px 0;font-size:12px;">
                                <a href="<?php echo FRoute::profile( array( 'layout' => 'edit' , 'external' => true ) );?>" style="text-decoration:none;color:#00aeef;">
                                    <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_UPDATE_YOUR_PROFILE' ); ?>
                                </a>
                                <p style="margin: 5px 0 0;color:#888;">
                                    <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_UPDATE_YOUR_PROFILE_DESC' ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="background:#ebebeb" width="1">
                    <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/spacer.gif" alt="" width="1" />
                </td>
                <td>
                    <table width="250" align="center">
                        <tr>
                            <td valign="top" style="padding: 10px 0;">
                                <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/icon-invite.png" alt="" />
                            </td>
                            <td valign="top" style="text-align:left;padding: 10px 0;font-size:12px;">
                                <a href="<?php echo FRoute::users( array( 'external' => true ) );?>" style="text-decoration:none;color:#00aeef;">
                                    <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FIND_FRIENDS' ); ?>
                                </a>
                                <p style="margin: 5px 0 0;color:#888;">
                                    <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FIND_FRIENDS_DESC' ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
