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
                <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_GROUP_INVITED_HEADING' ); ?>
            </div>
        </div>
    </td>
</tr>


<tr>
    <td style="text-align: center;">

        <div style="margin:30px auto;text-align:center;display:block">
            <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
        </div>

        <p style="margin-bottom: 50px;">
            <?php echo JText::sprintf( 'COM_EASYSOCIAL_EMAILS_GROUP_INVITED_CONTENT' , '<a href="' . $invitorLink . '">' . $invitorName . '</a>' , '<a href="' . $groupLink . '">' . $groupName . '</a>' ); ?>
        </p>

        <h1 style="font-size: 24px;">
            <a href="<?php echo $groupLink;?>" style="text-decoration:none;"><?php echo $groupName;?></a>
        </h1>

        <table align="center" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;width:100%;">
        <tr>
        <td align="center">
            <table align="center" width="96px" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr>
            <td width="96" height="96" style="border-collapse:collapse;"><span style="display:block;border:1px solid #f5f5f5;width:96px;height:96px;padding:3px;border-radius:50%;background:#fff;">
                <img src="<?php echo $groupAvatar;?>" alt="" style="width:96px;height: 96px;border-radius:50%; -moz-border-radius:50%; -webkit-border-radius:50%;background:#fff; "/>
            </span></td></tr>
            </tbody>
            </table>
        </td>
        </tr>
        </table>

        <a href="<?php echo $acceptLink;?>" style="
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
        "><?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_GROUP_ACCEPT_INVITATION' ); ?></a>
    </td>
</tr>
