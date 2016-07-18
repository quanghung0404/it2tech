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
                <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REPORT_HEADING' ); ?>
            </div>
            <div style="font-size:12px; color: #798796;font-weight:normal">
                <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REPORT_SUBHEADING' ); ?>
            </div>
        </div>
    </td>
</tr>

<tr>
    <td style="text-align: center;font-size:12px;color:#888">

        <div style="margin:30px auto;text-align:center;display:block">
            <img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
        </div>

        <table align="center" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;width:100%;">
        <tr>
        <td align="center">
            <table width="540" cellspacing="0" cellpadding="0" border="0" align="center" style="table-layout:fixed;margin: 0 auto;">
                <tr>
                    <td>
                    <div style="font-size:13px;margin: 0 0 20px;text-align:left;display:block;color:#798796">
                        <p>
                            <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_HELLO' ); ?>,<br />
                        </p>

                        <p>
                            <?php echo JText::sprintf( 'COM_EASYSOCIAL_EMAILS_REPORT_NEW_ITEM_REPORTED'  ); ?>
                            <a href="<?php echo $reporterLink;?>"><?php echo $reporter;?></a>. <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REPORT_NEW_ITEM_REPORTED_VIEW_DETAILS' ); ?>
                        </p>

                        <h3 style="margin-top: 30px;"><u><?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REPORT_DETAILS' ); ?></u></h3><br />

                        <div>
                            <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REPORT_REPORT_TITLE' );?>:<br />
                            <a href="<?php echo $url;?>" target="_blank"><?php echo $title; ?></a>
                        </div>

                        <div style="margin-top: 15px;">
                            <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REPORT_REPORT_ON' );?>:<br />
                            <?php echo $this->html( 'string.date' , $created , JText::_( 'DATE_FORMAT_LC2' ) ); ?>
                        </div>


                        <div style="margin-top: 15px;">
                            <?php echo JText::_( 'COM_EASYSOCIAL_EMAILS_REPORT_REPORT_REASON' );?>:

                            <blockquote style="font: 14px/22px normal helvetica, sans-serif;margin-top: 10px;margin-bottom: 10px;margin-left: 0;padding-left: 15px;border-left: 3px solid #ccc;"><?php echo $message; ?></blockquote>
                        </div>

                    </div>
                    </td>
                </tr>
            </table>
        </td>
        </tr>
        </table>

    </td>
</tr>
