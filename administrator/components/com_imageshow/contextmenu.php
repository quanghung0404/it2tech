<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: contextmenu.php 13756 2012-07-04 03:12:38Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
?>
<div class="contextMenu" id="sourceimage_contextmenu"
	style="display: none;">
	<ul>
		<span class="gutterLine"></span>
		<li id="selectallimage"><a><?php echo JText::_('CONTEXT_MENU_JSN_IS_SELECT_ALL_IMAGES', true); ?>
		</a></li>
		<li id="deselectall"><a><?php echo JText::_('CONTEXT_MENU_JSN_IS_DESELECT_ALL', true); ?>
		</a></li>
		<li id="revertselection"><a><?php echo JText::_('CONTEXT_MENU_JSN_IS_REVERT_SELECTION', true); ?>
		</a></li>
	</ul>
</div>
<div class="contextMenu" id="showlist_menucontext"
	style="display: none;">
	<ul>
		<div class="gutterLine"></div>
		<li id="selectallimage"><a><?php echo JText::_('CONTEXT_MENU_JSN_IS_SELECT_ALL_IMAGES', true); ?>
		</a></li>
		<li id="deselectall"><a><?php echo JText::_('CONTEXT_MENU_JSN_IS_DESELECT_ALL', true); ?>
		</a></li>
		<li id="revertselection"><a><?php echo JText::_('CONTEXT_MENU_JSN_IS_REVERT_SELECTION', true); ?>
		</a></li>
		<li class="divider"></li>
		<li id="purgeabsoleteimage"><a><?php echo JText::_('CONTEXT_MENU_JSN_IS_PURGE_ABSOLETE_IMAGES', true); ?>
		</a></li>
		<li id="resetselectedimagedetail"><a><?php echo JText::_('CONTEXT_MENU_JSN_IS_RESET_SELECTED_IMAGES_DETAILS', true); ?>
		</a></li>
	</ul>
</div>
