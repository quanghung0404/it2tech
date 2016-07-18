<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<tr data-field="<?php echo $field->id; ?>"
	data-display-field="<?php echo $field->id; ?>"
>
	<td class="profile-data-label">
		<?php if($params->get('display_title')) {
			echo JText::_($params->get('title')) . ':';
		} ?>
	</td>
	<td class="profile-data-info">
		<?php if($params->get('privacy') && $user->id === $this->my->id) {
			echo $this->includeTemplate('site/fields/privacy');
		} ?>

		<?php echo $this->includeTemplate($subNamespace); ?>
	</td>
</tr>
