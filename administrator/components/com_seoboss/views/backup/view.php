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

jimport('joomla.application.component.view');

/**
 * Updates Manager Default View
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */

class SeobossViewBackup extends JBView{
    function __construct($config = null)
    {
        parent::__construct($config);
        $this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
    }
    
    function display($tpl=null)
    {
      JToolBarHelper::title( JText::_( 'SEO_BACKUP_AND_RESTORE' ),'joomboss_backup.png' );
      JToolbarHelper::help(null, false, 'http://joomboss.com/products/seoboss/documentation/53-seo-boss-settings?tmpl=component');
      parent::display($tpl);
    }
    
}