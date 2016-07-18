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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" data-profile-form>
	<div class="wrapper accordion">
		<div class="tab-box tab-box-alt">
			<div class="tabbable">
				<?php echo $this->loadTemplate('admin/profiles/form.tabs' , array('isNew' => $profile->id == 0 , 'activeTab' => $activeTab)); ?>

				<div class="tab-content">
					<div id="settings" class="tab-pane<?php echo $activeTab == 'settings' ? ' active in' : '';?>">
						<?php echo $this->includeTemplate('admin/profiles/form.settings'); ?>
					</div>

					<div id="registrations" class="tab-pane<?php echo $activeTab == 'registrations' ? ' active in' : '';?>">
						<?php echo $this->includeTemplate('admin/profiles/form.registration'); ?>
					</div>

					<?php if($profile->id ){ ?>
					<div id="avatars" class="tab-pane<?php echo $activeTab == 'avatars' ? ' active in' : '';?>">
						<?php echo $this->includeTemplate('admin/profiles/form.avatars'); ?>
					</div>

					<div id="fields" class="tab-pane<?php echo $activeTab == 'fields' ? ' active in' : '';?>">
						<?php echo $this->includeTemplate('admin/profiles/form.fields'); ?>
					</div>

					<div id="privacy" class="tab-pane<?php echo $activeTab == 'privacy' ? ' active in' : '';?>">
						<?php echo $this->includeTemplate('admin/profiles/form.privacy'); ?>
					</div>

					<div id="access" class="tab-pane<?php echo $activeTab == 'access' ? ' active in' : '';?>">
						<?php echo $this->includeTemplate('admin/profiles/form.access'); ?>
					</div>

					<div id="groups" class="tab-pane<?php echo $activeTab == 'groups' ? ' active in' : '';?>">
						<?php echo $this->includeTemplate('admin/profiles/form.groups'); ?>
					</div>

					<div id="apps" class="tab-pane<?php echo $activeTab == 'apps' ? ' active in' : '';?>">
						<?php echo $this->includeTemplate('admin/profiles/form.apps'); ?>
					</div>
					<?php } ?>

				</div>

			</div>
		</div>
	</div>

	<input type="hidden" name="activeTab" data-tab-active value="<?php echo $activeTab; ?>" />
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="controller" value="profiles" />
	<input type="hidden" name="task" value="store" />
	<input type="hidden" name="id" value="<?php echo $profile->id; ?>" />
	<input type="hidden" name="cid" value="" />
	<?php echo JHTML::_('form.token');?>
</form>
