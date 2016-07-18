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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="wrapper accordion">
	<div class="tab-box tab-box-alt">
		<div class="tabbable">
			<ul id="userForm" class="nav nav-tabs nav-tabs-icons">
				<li class="tabItem active" data-tabnav data-for="profile">
					<a href="#profile" data-bs-toggle="tab">
						<?php echo JText::_( 'COM_EASYSOCIAL_USERS_PROFILE' );?>
					</a>
				</li>
				<?php if( isset( $user ) ){ ?>
				<li class="tabItem" data-tabnav data-for="badges">
					<a href="#badges" data-bs-toggle="tab">
						<?php echo JText::_( 'COM_EASYSOCIAL_USERS_ACHIEVEMENTS' );?>
					</a>
				</li>
				<li class="tabItem" data-tabnav data-for="points">
					<a href="#points" data-bs-toggle="tab">
						<?php echo JText::_( 'COM_EASYSOCIAL_USERS_POINTS' );?>
					</a>
				</li>
				<li class="tabItem" data-tabnav data-for="notifications">
					<a href="#notifications" data-bs-toggle="tab">
						<?php echo JText::_( 'COM_EASYSOCIAL_USERS_NOTIFICATIONS' );?>
					</a>
				</li>
				<li class="tabItem" data-tabnav data-for="privacy">
					<a href="#privacy" data-bs-toggle="tab">
						<?php echo JText::_( 'COM_EASYSOCIAL_USERS_PRIVACY' );?>
					</a>
				</li>
				<li class="tabItem" data-tabnav data-for="usergroup">
					<a href="#usergroup" data-bs-toggle="tab">
						<?php echo JText::_( 'COM_EASYSOCIAL_USERS_USERGROUP' );?>
					</a>
				</li>
				<li class="tabItem" data-tabnav data-for="gravity">
					<a href="#gravity" data-bs-toggle="tab">
						<?php echo JText::_('COM_EASYSOCIAL_USERS_SITE_GRAVITY');?>
					</a>
				</li>
				<li class="tabItem" data-tabnav data-for="activities">
					<a href="#activities" data-bs-toggle="tab">
						<?php echo JText::_('COM_EASYSOCIAL_USERS_SITE_ACTIVITIES');?>
					</a>
				</li>
				<?php } ?>
			</ul>

			<div class="tab-content tab-content-side">

				<div id="profile" class="tab-pane active" data-tabcontent data-for="profile">
					<?php echo $this->includeTemplate('admin/users/form.profile'); ?>
				</div>

				<?php if( isset( $user ) ){ ?>
				<div id="badges" class="tab-pane" data-tabcontent data-for="badges">
					<?php echo $this->includeTemplate('admin/users/form.badges'); ?>
				</div>

				<div id="points" class="tab-pane" data-tabcontent data-for="points">
					<?php echo $this->includeTemplate('admin/users/form.points'); ?>
				</div>

				<div id="notifications" class="tab-pane" data-tabcontent data-for="notifications">
					<?php echo $this->includeTemplate('admin/users/form.notifications'); ?>
				</div>

				<div id="privacy" class="tab-pane" data-tabcontent data-for="privacy">
					<?php echo $this->includeTemplate('admin/users/form.privacy'); ?>
				</div>

				<div id="usergroup" class="tab-pane" data-tabcontent data-for="usergroup">
					<?php echo $this->includeTemplate('admin/users/form.usergroups'); ?>
				</div>

				<div id="gravity" class="tab-pane" data-tabcontent data-for="gravity">
					<div class="panel">
						<div class="panel-head">
							<b><?php echo JText::_('COM_EASYSOCIAL_USERS_INTERACTION_GRAPH');?></b>
						</div>

						<div class="panel-body">
							<?php echo $this->loadTemplate( 'admin/users/form.chart' , array( 'stats' => $stats ) ); ?>
						</div>
					</div>
				</div>

				<div id="activities" class="tab-pane" data-tabcontent data-for="activities">
					<div class="panel">
						<div class="panel-head">
							<b><?php echo JText::_( 'COM_EASYSOCIAL_USERS_RECENT_ACTIVITY' );?></b>
						</div>

						<div class="panel-body">
							<div data-form-activity>
								<span data-form-activity-loader><?php echo JText::_( 'COM_EASYSOCIAL_USERS_RETRIEVING_USERS_ACTIVITY' ); ?></span>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>

</div>
