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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <base href="<?php echo JURI::root();?>" target="_blank" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;">

        <table style="border-collapse:collapse;min-height:100% !important;width:100% !important;table-layout:fixed;margin:0 auto;background:#f4f4f4;margin:0;padding:50px 0 80px;color:#798796;font-family:'Lucida Grande',Tahoma,Arial;font-size:11px;">
            <tr>
                <td align="center" style="min-height:100% !important;width:100% !important;">

                        <table cellpadding="0" cellspacing="0" border="0" style="width:600px;table-layout:fixed;margin:0 auto;background:#fff;border:1px solid #ededed;border-top-color:#f4f4f4;border-bottom-color:#f4f4f4;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;">
                            <tbody>
                                <tr>
                                    <td style="padding-top:20px;padding-left:20px;">
                                        <img src="<?php echo $logo;?>" />
                                    </td>
                                </tr>

                                <?php echo $contents; ?>

                                <tr>
                                    <td>
                                    <br /><br />
                                    <div style="margin:30px auto;text-align:center;display:block">
                                        <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
                                    </div>
                                        <table align="center" width="540" style="clear:both;margin:auto 20px">
                                            <tr>
                                                <td style="line-height:1.5;color:#555;font-family:'Lucida Grande',Tahoma,Arial;font-size:12px;text-align:center">
													<?php if ($manageAlerts) { ?>
                                                    <div style="font-size:11px; color:#999; line-height:13px;padding: 20px">
                                                        <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_FOOTER_BECAUSE' ); ?><br /><br />
                                                        <a href="<?php echo FRoute::profile( array( 'layout' => 'editNotifications' , 'external' => true ) );?>" style="color:#00aeef; text-decoration:none;"><?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_MANAGE_ALERTS' );?></a>
                                                    </div>
                                                    <?php } else { ?><br /><br /><?php } ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                </td>
            </tr>
        </table>

</body>
</html>
