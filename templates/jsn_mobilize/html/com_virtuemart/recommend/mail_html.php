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
	<table style="margin: auto;" cellpadding="0" cellspacing="0" width="600" ><tr><td>

		<table width="600" border="0" cellpadding="0" cellspacing="0" class="html-email">
			<tr>
				<td valign="top">
					<img src="<?php  echo JURI::root () . $this->vendor->images[0]->file_url ?>" />
				</td>
				<td>
					<?php echo $this->vendorAddress; ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<strong><?php echo JText::sprintf ('COM_VIRTUEMART_MAIL_SHOPPER_NAME', $this->user['email']); ?></strong><br/>
				</td>
			</tr>
		</table>

		<table style="margin: auto;" cellpadding="0" cellspacing="0" width="600" >
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="html-email">
						<tr>
							<td >
								<?php echo JText::sprintf('COM_VIRTUEMART_RECOMMEND_MAIL_MSG', $this->product->product_name,   $this->comment); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php

		$link = JURI::root().'index.php?option=com_virtuemart';

		echo '<br/><br/>';

		/* GENERAL FOOTER FOR ALL MAILS */
		echo JText::_('COM_VIRTUEMART_MAIL_FOOTER' ) . '<a href="'.$link.'">'.$this->vendor->vendor_name.'</a>';
		echo '<br/>';
		echo $this->vendor->vendor_name .'<br />'.$this->vendor->vendor_phone .' '.$this->vendor->vendor_store_name .'<br /> '.$this->vendor->vendor_store_desc.'<br />'.$this->vendor->vendor_legal_info;
		?>
	</td></tr>
	</table>
</div>
</body>
</html>



