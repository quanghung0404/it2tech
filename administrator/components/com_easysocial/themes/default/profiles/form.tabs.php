<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC' ) or die('Unauthorized Access' );
?>
<ul id="userForm" class="nav nav-tabs nav-tabs-icons">
	<li class="tabItem<?php echo $activeTab == 'settings' ? ' active' : '';?>">
		<a data-bs-toggle="tab" href="#settings" data-form-tabs data-item="settings">
			<span class="help-block"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_TAB_PROFILE_GENERAL');?></span>
		</a>
	</li>

	<li class="tabItem<?php echo $activeTab == 'registrations' ? ' active' : '';?>">
		<a href="#registrations" data-bs-toggle="tab" data-form-tabs data-item="registrations">
			<span class="help-block"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_TAB_PROFILE_REGISTRATION' );?></span>
		</a>
	</li>

	<li class="tabItem<?php echo $activeTab == 'avatars' ? ' active' : '';?><?php echo $isNew ? ' inactive' : '';?>">
		<a href="#avatars" <?php echo !$isNew ? 'data-bs-toggle="tab"' : '';?> data-form-tabs data-item="avatars">
			<span class="help-block"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_TAB_DEFAULT_AVATARS');?></span>
		</a>
	</li>

	<li class="tabItem<?php echo $activeTab == 'fields' ? ' active' : '';?><?php echo $isNew ? ' inactive' : '';?>">
		<a href="#fields" <?php echo !$isNew ? 'data-bs-toggle="tab"' : '';?> class="fields" data-form-tabs data-item="fields">
			<span class="help-block"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_TAB_CUSTOM_FIELDS' );?></span>
		</a>
	</li>

	<li class="tabItem<?php echo $activeTab == 'privacy' ? ' active' : '';?><?php echo $isNew ? ' inactive' : '';?>">
		<a href="#privacy" <?php echo !$isNew ? 'data-bs-toggle="tab"' : '';?> data-form-tabs data-item="privacy">
			<span class="help-block"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_TAB_PRIVACY' );?></span>
		</a>
	</li>

	<li class="tabItem<?php echo $activeTab == 'access' ? ' active' : '';?><?php echo $isNew ? ' inactive' : '';?>">
		<a href="#access" <?php echo !$isNew ? 'data-bs-toggle="tab"' : '';?> data-form-tabs data-item="access">
			<span class="help-block"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_TAB_ACCESS' );?></span>
		</a>
	</li>

	<li class="tabItem<?php echo $activeTab == 'groups' ? ' active' : '';?><?php echo $isNew ? ' inactive' : '';?>">
		<a href="#groups" <?php echo !$isNew ? 'data-bs-toggle="tab"' : '';?> data-form-tabs data-item="groups">
			<span class="help-block"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_TAB_GROUPS');?></span>
		</a>
	</li>

	<li class="tabItem<?php echo $activeTab == 'apps' ? ' active' : '';?><?php echo $isNew ? ' inactive' : '';?>">
		<a href="#apps" <?php echo !$isNew ? 'data-bs-toggle="tab"' : '';?> data-form-tabs data-item="apps">
			<span class="help-block"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_TAB_APPS');?></span>
		</a>
	</li>
</ul>
