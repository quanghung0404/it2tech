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
<tr>
    <td style="text-align: center;padding: 40px 10px 0;">
        <div style="margin-bottom:15px;">
            <div style="font-family:Arial;font-size:32px;font-weight:normal;color:#333;display:block; margin: 4px 0">
                <?php echo JText::sprintf( 'COM_EASYSOCIAL_EMAILS_USER_DELETED_ACCOUNT_BLOCKED' , $name ); ?>
            </div>
            <div style="font-size:12px; color: #798796;font-weight:normal">
                <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_USER_DELETED_ACCOUNT_BLOCKED_DESC' ); ?>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="text-align: center;font-size:12px;color:#888">

        <div style="margin:30px auto;text-align:center;display:block">
            <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
        </div>

        <p style="text-align:left;padding: 0 30px;">
            <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_HELLO' ); ?> <?php echo $adminName; ?>,
        </p>

        <p style="text-align:left;padding: 0 30px;">
            <?php echo JText::sprintf( 'COM_EASYSOCIAL_EMAILS_USER_DELETED_ACCOUNT_BLOCKED_CONTENT' , $name );?>
        </p>

        <table align="center" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;width:100%;">
        <tr>
        <td align="center">
            <table width="540" align="center" style="margin: 20px auto 0;background-color:#f8f9fb;padding:15px 20px;" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="100">
                        <span style="display:block;margin: 0px auto 20px;border:1px solid #f5f5f5;width:64px;padding:3px;border-radius:50%; -moz-border-radius:50%; -webkit-border-radius:50%;background:#fff">
                            <a href="<?php echo $profileLink;?>"><img src="<?php echo $avatar;?>" alt="<?php echo $this->html( 'string.escape' , $name );?>" style="border-radius:50%; -moz-border-radius:50%; -webkit-border-radius:50%;background:#fff" width="64" height="64"/></a>
                        </span>
                    </td>
                    <td>
                        <table style="font-size: 14px;margin: 0 auto 10px 20px; text-align:left;color:#798796" align="">
                            <tr>
                                <td>
                                    <a href="<?php echo $profileLink;?>" style="color: #798796;text-decoration:none;"><strong><?php echo $name;?></strong></a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="margin-bottom: 30px;">
                                        <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FRIENDS' );?>: <?php echo $totalFriends;?>
                                        <span style="font-size:9px;">&bull;</span>
                                        <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FOLLOWERS' );?>: <?php echo $totalFollowers;?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="<?php echo $profileLink;?>" style="
                                    display:inline-block;
                                    text-decoration:none;
                                    font-weight:bold;
                                    margin-top: 5px;
                                    padding:4px 15px;
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
                                    ">
                                        <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_VIEW_PROFILE' );?>
                                    </a>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </td>
        </tr>
        </table>
    </td>
</tr>
