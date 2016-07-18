<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: assignpages.php 13334 2012-06-15 13:05:16Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');

class PoweradminModelPositionlisting extends JModelAdmin
{
	public function getForm($data = array(), $loadData = true){}
	
	public function getCurrentTempate($clientID = 0)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from("#__template_styles");
		$query->where("client_id = " . (int) $clientID . ' AND home = 1');
		$db->setQuery($query);
		return $db->loadObject();
	}
	
}
?>