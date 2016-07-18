<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('');

$li = '<br />';
?>

<html>
    <head>
	<style type="text/css">
            body, td, span, p, th { font-size: 11px; }
	    table.html-email {margin:10px auto;background:#fff;border:solid #dad8d8 1px;}
	    .html-email tr{border-bottom : 1px solid #eee;}
	    span.grey {color:#666;}
	    span.date {color:#666;font-size: 10px;}
	    a.default:link, a.default:hover, a.default:visited {color:#666;line-height:25px;background: #f2f2f2;margin: 10px ;padding: 3px 8px 1px 8px;border: solid #CAC9C9 1px;border-radius: 4px;-webkit-border-radius: 4px;-moz-border-radius: 4px;text-shadow: 1px 1px 1px #f2f2f2;font-size: 12px;background-position: 0px 0px;display: inline-block;text-decoration: none;}
	    a.default:hover {color:#888;background: #f8f8f8;}
	    .cart-summary{ }
	    .html-email th { background: #ccc;margin: 0px;padding: 10px;}
	    .sectiontableentry2, .html-email th, .cart-summary th{ background: #ccc;margin: 0px;padding: 10px;}
	    .sectiontableentry1, .html-email td, .cart-summary td {background: #fff;margin: 0px;padding: 10px;}
	</style>

    </head>

    <body style="background: #F2F2F2;word-wrap: break-word;">
	<div style="background-color: #e6e6e6;" width="100%">
	    <table style="margin: auto;" cellpadding="0" cellspacing="0" width="600" >
		<tr>
		    <td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="html-email">
			    <tr>
				<td >

				    <?php echo JText::sprintf('COM_VIRTUEMART_WELCOME_USER', $this->user->name); ?>
				    <br />
				    <?php
				    if (!empty($this->activationLink)) {
					$activationLink = '<a class="default" href="' . JURI::root() . $this->activationLink . '">' . JText::_('COM_VIRTUEMART_LINK_ACTIVATE_ACCOUNT') . '</a>';
					echo $li;
					echo $activationLink . $li;
				    }
				    ?>
				</td>
			    </tr>
			</table>

			<table class="html-email" cellspacing="0" cellpadding="0" border="0" width="100%">
			    <tr>
				<th width="100%">
				    <?php echo JText::_('COM_VIRTUEMART_SHOPPER_REGISTRATION_DATA') ?>
				</th>

			    </tr>
			    <tr>
				<td valign="top" width="100%">
				    <?php
				    echo JText::_('COM_VIRTUEMART_YOUR_LOGINAME')   . $this->user->username . $li;
				    echo JText::_('COM_VIRTUEMART_YOUR_DISPLAYED_NAME')   . $this->user->name . $li;
				    echo JText::_('COM_VIRTUEMART_YOUR_PASSWORD')  . $this->user->password_clear . $li. $li;
				    echo JText::_('COM_VIRTUEMART_YOUR_ADDRESS')  . $li;

				    foreach ($this->userFields['fields'] as $userField) {
					if (!empty($userField['value']) && $userField['type'] != 'delimiter' && $userField['type'] != 'BT' && $userField['type'] != 'hidden') {
					    echo $userField['title'] . ': ' . $userField['value'] . $li;

					}
				    }
				    ?>
				</td>
			    </tr>
			</table>
		    </td>
		</tr>
	    </table>
	</div>
    </body>
</html>