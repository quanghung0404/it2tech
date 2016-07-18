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
                <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FORGET_PASSWORD_HEADING' ); ?>
            </div>
            <div style="font-size:12px; color: #798796;font-weight:normal">
                <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FORGET_PASSWORD_SUBHEADING' ); ?>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="text-align: center;">

        <div style="margin:30px auto;text-align:center;display:block">
            <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
        </div>

        <table align="center" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;width:100%;">
        <tr>
        <td align="center">
            <table width="540" cellspacing="0" cellpadding="0" border="0" align="center" style="table-layout:fixed;margin: 0 auto;">
                <tr>
                    <td>
                        <p style="text-align:left;">
                            <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_HELLO' ); ?> <?php echo $name; ?>,
                        </p>

                        <p style="text-align:left;">
                            <?php echo JText::sprintf( 'COM_EASYSOCIAL_EMAILS_FORGET_PASSWORD_DESC' , $site ); ?>:
                        </p>

                        <p style="text-align:left;text-align:center;margin-top: 30px;">
                            <?php echo JText::sprintf( 'COM_EASYSOCIAL_EMAILS_FORGET_PASSWORD_DESC_VERIFICATION' , $site ); ?>:
                        </p>
                    </td>
                </tr>
            </table>
        </td>
        </tr>
        </table>

        <div style="border: 1px dashed #ccc;padding: 8px;margin: 0 30px;background: #F8F9FB;font-weight:700;font-size:12px;"><?php echo $token; ?></div>

        <a style="
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
                " href="<?php echo FRoute::account( array( 'layout' => 'confirmReset' , 'external' => true ));?>"><?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_RESET_PASSWORD' );?></a>

        <p style="margin-top: 20px;">
            <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FORGET_USERNAME_FOOTPRINT' ); ?>
        </p>

    </td>
</tr>
