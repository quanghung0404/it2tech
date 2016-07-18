<?php
/*------------------------------------------------------------------------
 # SEO Boss
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');
if(version_compare(JVERSION, "3.0", "ge")){
  class JBController extends JControllerLegacy{}
}else{
  class JBController extends JController{}
}
class SeobossController extends JBController{
  
  function redirectByURL(){
    $link = JRequest::getVar('url', '', 'get', 'string');
    $mainframe = JFactory::getApplication();
    $mainframe->redirect($link, $msg);
  }
  function getSEFURL(){
    $url = base64_decode(JRequest::getVar('url'));
    echo JRoute::_($url);
  }
}
?>
