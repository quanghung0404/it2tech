<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>

<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_LAYOUT_PROGRESS' ); ?></legend>
	<div class="migrator-progress clearfix">
		<div class="progressValue-box">
			<span class="progressPercentage">0</span> %
		</div>
		<div class="progressBar-box">
			<div class="progressBar"></div>
		</div>
	</div>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_LAYOUT_LOG' ); ?></legend>
	<div class="log-wrap">
		<ul class="logList log-list reset-ul">
			<li>
				<span class="small">Nothing started yet</span>
			</li>
		</ul>
	</div>
	<a class="clearLog button" href="javascript:void(0);"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_LOG_CLEAR' ); ?></a>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_LAYOUT_STATISTIC' ); ?></legend>
	<table cellspacing="1" class="migrator-statistic admintable">
		<tr>
			<td class="key"><span><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_STATISTIC_TOTAL_ARTICLES' ); ?></span></td>
			<td style="padding-top: 9px;"><span class="totalPosts"></span></td>
		</tr>
		<tr>
			<td class="key"><span><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_STATISTIC_TOTAL_COMMENTS' ); ?></span></td>
			<td style="padding-top: 9px;"><span class="totalComments"></span></td>
		</tr>
		<tr>
			<td class="key"><span><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_STATISTIC_MIGRATED_COMMENTS' ); ?></span></td>
			<td style="padding-top: 9px;"><span class="migratedComments">0</span> / <span class="totalComments"></span></td>
		</tr>
	</table>
</fieldset>
