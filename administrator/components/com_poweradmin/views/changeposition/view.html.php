<?php
/**
 * @version     $Id: view.html.php 16024 2012-09-13 11:55:37Z hiepnv $
 * @package     JSN_Poweradmin
 * @subpackage  Config
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

include_once (JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/positions/view.php');

class PoweradminViewChangeposition extends JSNBaseView
{
	/**
	 * Custom sript
	 *
	 * @var array
	 */
	protected $customScripts = array();
	
	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.
	 */
	public function __construct($config = array())
	{
		// Display only the component output
		JFactory::getApplication()->input->def('tmpl', 'component');
	
		parent::__construct($config);
	}
	
	
	public function display($tpl = null)
	{
 		$app = JFactory::getApplication();
 		$document = JFactory::getDocument();
 		// Check if this view is used for module editing page.
 		$moduleEdit = JRequest::getCmd('moduleedit', '');
 		$active_positions = Array();
 		$model = $this->getModel('changeposition');
 		if(!$moduleEdit){
 			$moduleid = $app->getUserState( 'com_poweradmin.changeposition.moduleid' );

 		}else{
 			$moduleid = array(JRequest::getCmd('moduleid', ''));
 		}

 		for( $i = 0; $i < count($moduleid); $i++ ){
 			$active_positions[] = "$('#".$model->getModulePosition(  $moduleid[$i] )."-jsnposition').addClass('active-position').attr('title', 'Active position');";
 		}

 		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.js');
 		JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.jquery.noconflict.js');
 		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.functions.js');
 		//$document->addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.filter.visualmode.js');


 		//Enable position filter.
 		$this->setFilterable(true);

		$customScript = "
			var baseUrl  = '".JURI::root()."';
			var token  = '".JSession::getFormToken()."';
			var moduleid = new Array();
			moduleid = [". @implode(",", $moduleid)."];
			(function ($){
				$(document).ready(function (){
					".implode(PHP_EOL, $active_positions)."
				});
			})(JoomlaShine.jQuery);
 		";

 		$this->addCustomScripts($customScript);


 		//Callback after position clicked.
 		if(!$moduleEdit){
 			$onPostionClick = "
 			if ( !$(this).hasClass('active-position') ){
				JoomlaShine.jQuery.setPosition(moduleid, $(this).attr('id').replace('-jsnposition', ''));
 				parent.JoomlaShine.jQuery('.ui-dialog-content').dialog('close');
 			}
 			";
 		}else{
 			$onPostionClick = "
 			if ( !$(this).hasClass('active-position') ){
 				var posName = $(this).attr('id').replace('-jsnposition', '');
 				parent.JoomlaShine.jQuery('#jform_position').val(posName);
 				parent.modal.close();
 			}
 			";
 		}

 		$this->addPositionClickCallBack($onPostionClick);

 		$template = JSNTemplateHelper::getInstance();
 		$onPositionClick = '';
 		$initFilter = '';
 		$displayNotice = $app->input->getInt('notice');
 		$bypassNotif	= $app->input->getVar('bypassNotif', '');

 		// Get template author.
 		$templateAuthor = $template->getAuthor();
 		
 		JSNPositionsHelper::dispatchTemplateFramework($templateAuthor);
 		
 		$document->addStyleSheet(JSN_URL_ASSETS . '/joomlashine/css/jsn-positions.css');
 		
 		if (JSNVersion::isJoomlaCompatible('3.0'))
 		{
 			$document->addScript(JURI::root(true) . '/media/jui/js/jquery.js');
 		}
 		else
 		{
 			$document->addScript(JSN_URL_ASSETS . '/3rd-party/jquery/jquery-1.8.2.js');
 		}
 		
 		if (isset($this->filterEnabled) AND $this->filterEnabled)
 		{
 			$document->addScript(JSN_URL_ASSETS . '/joomlashine/js/positions.filter.js');
 			$initFilter = 'changeposition = new JoomlaShine.jQuery.visualmodeFilter({});';
 		}
 		
 		if (isset($this->customScripts))
 		{
 			$document->addScriptDeclaration(implode('\n', $this->customScripts));
 		}
 		
 		$onPositionClick = isset($this->onPositionClickCallBack) ? implode('\n', $this->onPositionClickCallBack) : '';
 		
 		// Get JSN Template Framework version
 		$db	= JFactory::getDbo();
 		$q	= $db->getQuery(true);
 		
 		$q->select('manifest_cache');
 		$q->from('#__extensions');
 		$q->where("element = 'jsntplframework'");
 		$q->where("type = 'plugin'", 'AND');
 		$q->where("folder = 'system'", 'AND');
 		
 		$db->setQuery($q);
 		
 		// Load dependency installation status.
 		$res 	= $db->loadObject();
 		$res	= json_decode($res->manifest_cache);
 		$jsnTplFwVersion	=	$res->version;
 		
 		$jsnTemplateCustomJs	= '';
 		if (version_compare($jsnTplFwVersion, '2.0.1', '<=')) {
 			$jsnTemplateCustomJs	= "$('body').addClass('jsn-bootstrap');";
 		}
 		
 		$_customScript = "
			var changeposition;
			(function($){
				$(document).ready(function (){
					var posOutline	= $('.jsn-position');
					var _idAlter	= false;
					if ($('.jsn-position').length == 0) {
						posOutline	= $('.mod-preview');
						_idAlter	= true;
					}else{
						posOutline.css({'z-index':'9999', 'position':'relative'});
					}
					posOutline.each(function(){
						if(_idAlter){
							previewInfo = $(this).children('.mod-preview-info').text();

							_splitted = previewInfo.split('[');
							if(_splitted.length > 1){
								posname	= _splitted[0];
							}
							_splitted = posname.split(': ');
							if(_splitted.length > 1){
								posname	= _splitted[1];
							}

							posname = $.trim(posname);

							$(this).attr('id', posname + '-jsnposition');
						}

						$(this)[0].oncontextmenu = function() {
							return false;
						}
					})
					.click(function () {
						" . $onPositionClick . "
					});
					" . $jsnTemplateCustomJs ."
				});
				" . $initFilter . "
				
			})(jQuery);
		";
 		$document->addScriptDeclaration($_customScript);
 		
 		$previewModulePositionsIsEnabled = JComponentHelper::getParams('com_templates')->get('template_positions_display', 0);

 		if (!$previewModulePositionsIsEnabled)
 		{
 			/**
 			 * Get config class
 			 */
 			JSNFactory::localimport('libraries.joomlashine.config');
 			JSNConfig::extension( 'com_templates', array( 'template_positions_display' => 1 ) );
 		}
 		
 		$config 	= JFactory::getConfig();
 		$secret 	= $config->get('secret');
 		
 		$jsnrender = JSNPositionsRender::getInstance();
 		$jsnrender->renderPage(JURI::root() . 'index.php?poweradmin=1&vsm_changeposition=1&tp=1&jsnpa_key=' . md5($secret), 'changePosition');
 		
 		$this->jsnrender = $jsnrender;
 		
		parent::display($tpl);
	}
	
	
	/**
	 * Method to add customs javacript into page.
	 *
	 * @param   string  $customScript  Custom script
	 *
	 * @return  void
	 */
	
	public function addCustomScripts($customScript = '')
	{
		$this->customScripts[] = $customScript;
	
		return;
	}
	
	/**
	 * Method to add javascript callback functions after a position clicked.
	 *
	 * @param   string  $script  Script code
	 *
	 * @return  void
	 */
	public function addPositionClickCallBack($script = '')
	{
		$this->onPositionClickCallBack[] = $script;
	
		return;
	}
	
	/**
	 * Method to enable/disable position filter.
	 *
	 * @param   boolean  $filterEnabled  Whether to enable filter or not?
	 *
	 * @return  void
	 */
	public function setFilterable($filterEnabled = false)
	{
		$this->filterEnabled = $filterEnabled;
	}
	
	/**
	 * Add assets
	 *
	 * @return  void
	 */
	public function _addAssets()
	{
	}	
}
