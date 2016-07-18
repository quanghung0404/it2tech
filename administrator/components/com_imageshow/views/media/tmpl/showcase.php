<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: showcase.php 8411 2011-09-22 04:45:10Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
$theme = JRequest::getWord('theme');
$event = JRequest::getWord('event');

if($theme && $event)
{
	JPluginHelper::importPlugin('jsnimageshow', $theme);
	$dispatcher = JDispatcher::getInstance();
	$arg 		= array();
	$plugins 	= $dispatcher->trigger($event, $arg);
}