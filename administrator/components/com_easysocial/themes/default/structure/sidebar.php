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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-sidebar app-sidebar-collapse" data-sidebar>

	<ul class="app-sidebar-nav list-unstyled">
		<?php foreach ($menus as $menu) { ?>

			<?php if ((isset($menu->access) && $this->my->authorise($menu->access, 'com_easysocial')) || !isset($menu->access)) { ?>
			<li class="sidebar-item 
				menu-<?php echo $menu->class;?> 
				menuItem<?php echo !empty( $menu->childs ) ? ' dropdown' : '';?>
				<?php echo in_array($view, $menu->views) ? ' active' : '';?>" 
				data-sidebar-item
			>

				<?php if ($menu->link == 'null') { ?>
					<a href="javascript:void(0);" class="dropdown-toggle_" data-sidebar-parent>
				<?php } else { ?>
					<a href="<?php echo $menu->link;?>">
				<?php } ?>

					<i class="fa <?php echo $menu->class;?>"></i><span><?php echo JText::_( $menu->title ); ?></span>
					<span class="badge"><?php echo $menu->count > 0 ? $menu->count : ''; ?></span>
				</a>


				<?php if (isset($menu->childs) && $menu->childs) { ?>
				<ul role="menu" class="dropdown-menu<?php echo $menu->view == $view ? ' in' : '';?>" id="menu-<?php echo $menu->uid;?>" data-sidebar-child>
					<?php foreach ($menu->childs as $child) { ?>

						<?php $active = JRequest::getVar((string) $menu->active , '' ); ?>

						<li class="menu-<?php echo isset($child->class) && $child->class ? $child->class : '';?> childItem<?php echo $active == $child->url->{$menu->active} && $view == $child->url->view ? ' active' : '';?>">
							<a href="<?php echo $child->link;?>">
								<span><?php echo JText::_($child->title); ?></span>
								<i class="icon-caret-right"></i>
							</a>
							<span class="badge"><?php echo $child->count > 0 ? $child->count : ''; ?></span>
						</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</li>
			<?php } ?>

		<?php } ?>
	</ul>
</div>
