<?php

/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class.
 *
 * @package     JSN_Mobilize
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JSNMobilizeHelper {

    /**
     * Configure the linkbar.
     *
     * @param   string  $vName  The name of the active view
     *
     * @return  void
     */
    public static function addSubmenu($vName = 'profiles') {
        if (JFactory::getApplication()->input->getCmd('tmpl', null) == null) {
            // Get 5 most-recent items
            $items = self::getProfiles(5);
            // Declare 1st-level menu items
            JSNMenuHelper::addEntry('profiles', 'JSN_MOBILIZE_SUB_MENU_MOBILIZATION_TEXT', 'index.php?option=com_mobilize', $vName == '' OR $vName == 'profiles', 'administrator/components/com_mobilize/assets/images/icons-16/icon-mobilize.png', 'sub-menu');

            JSNMenuHelper::addEntry('configuration', 'JSN_MOBILIZE_SUB_MENU_CONFIGURARTION_TEXT', '', false, 'administrator/components/com_mobilize/assets/images/icons-16/icon-cog.png', 'sub-menu');

            JSNMenuHelper::addEntry('about', 'JSN_MOBILIZE_SUB_MENU_ABOUT_TEXT', 'index.php?option=com_mobilize&view=about', $vName == 'about', 'administrator/components/com_mobilize/assets/images/icons-16/icon-about.png', 'sub-menu');

            // Declare 2nd-level menu items	for 'items' entry
            JSNMenuHelper::addEntry('all-profiles', 'All Profiles', 'index.php?option=com_mobilize&view=profiles', false, '', 'sub-menu.profiles');

            if ($items) {
                JSNMenuHelper::addEntry('recent-profiles', 'Recent Profiles', '', false, '', 'sub-menu.profiles');

                foreach ($items AS $item) {
                    JSNMenuHelper::addEntry('item-' . $item->profile_id, $item->profile_title, 'index.php?option=com_mobilize&view=profile&task=profile.edit&layout=edit&profile_id=' . $item->profile_id, false, '', 'sub-menu.profiles.recent-profiles');
                }
            }
            JSNMenuHelper::addSeparator('sub-menu.profiles');
            JSNMenuHelper::addEntry('item-new', 'Create New Profile', 'index.php?option=com_mobilize&view=profile&layout=edit', false, '', 'sub-menu.profiles');

            // Declare 2nd-level menu items	for 'configuration' entry
            JSNMenuHelper::addEntry('global-params', 'Global Parameters', 'index.php?option=com_mobilize&view=configuration&s=configuration&g=configs', false, '', 'sub-menu.configuration');

            JSNMenuHelper::addEntry('messages', 'Messages', 'index.php?option=com_mobilize&view=configuration&s=configuration&g=msgs', false, '', 'sub-menu.configuration');

            JSNMenuHelper::addEntry('languages', 'Languages', 'index.php?option=com_mobilize&view=configuration&s=configuration&g=langs', false, '', 'sub-menu.configuration');

            JSNMenuHelper::addEntry('update', 'Product Update', 'index.php?option=com_mobilize&view=configuration&s=configuration&g=update', false, '', 'sub-menu.configuration');

            JSNMenuHelper::addEntry('maintenance', 'Maintenance', '', false, '', 'sub-menu.configuration');

            // Declare 3rd-level menu items	for 'maintenance' entry
            JSNMenuHelper::addEntry('data', 'Data', 'index.php?option=com_mobilize&view=configuration&s=maintenance&g=data', false, '', 'sub-menu.configuration.maintenance');

            JSNMenuHelper::addEntry('permissions', 'Permissions', 'index.php?option=com_mobilize&view=configuration&s=maintenance&g=permissions', false, '', 'sub-menu.configuration.maintenance');

            // Render the sub-menu
            JSNMenuHelper::render('sub-menu');
        }
    }

    /**
     * Set toolbar title and do some initialization
     *
     * @param   string   $title  Title to set for toolbar.
     * @param   string   $icon   Custom icon for the title.
     * @param   boolean  $help   Whether to show help button or not?
     *
     * @return  void
     */
    public static function initToolbar($title, $icon = '', $help = true) {
        // Set toolbar title
        JToolBarHelper::title(JText::_($title), $icon);
        // Setup custom menu button
        self::addToolbarMenu();

        // Show help button?
        if ($help) {
            $bar = JToolBar::getInstance('toolbar');

            if (JSNVersion::isJoomlaCompatible('2.5')) {
                JToolBarHelper::divider();
                $bar->appendButton('Custom', '<a href="javascript:void(0);" id="jsn-help" class="toolbar"><span class="icon-32-help" title="' . JText::_('JSN_MOBILIZE_HELP') . '" type="Custom"></span>' . JText::_('JSN_MOBILIZE_HELP') . '</a>');
            } else {
                $bar->appendButton('Custom', '<button class="btn btn-small" id="jsn-help" onclick="return false;"><i class="icon-question-sign"></i>' . JText::_('JSN_MOBILIZE_HELP') . '</button>');
            }
        }
    }

    /**
     * Add toolbar button.
     *
     * @return        void
     */
    public static function addToolbarMenu() {
        // Get 5 most-recent items
        $items = self::getProfiles(5);

        // Create a toolbar button that drop-down a sub-menu when clicked
        JSNMenuHelper::addEntry('toolbar-menu', 'Menu', '', false, 'jsn-icon16 jsn-icon-menu', 'toolbar');

        // Declare 1st-level menu items
        JSNMenuHelper::addEntry('profiles', 'JSN_MOBILIZE_SUB_MENU_MOBILIZATION_TEXT', '', false, 'administrator/components/com_mobilize/assets/images/icons-16/icon-mobilize.png', 'toolbar-menu');

        JSNMenuHelper::addEntry('configuration', 'JSN_MOBILIZE_SUB_MENU_CONFIGURARTION_TEXT', 'index.php?option=com_mobilize&view=configuration', false, 'administrator/components/com_mobilize/assets/images/icons-16/icon-cog.png', 'toolbar-menu');

        JSNMenuHelper::addEntry('about', 'JSN_MOBILIZE_SUB_MENU_HELP_TEXT', 'index.php?option=com_mobilize&view=about', false, 'administrator/components/com_mobilize/assets/images/icons-16/icon-about.png', 'toolbar-menu');

        // Declare 2nd-level menu items	for 'items' entry
        JSNMenuHelper::addEntry('all-profiles', 'All Profiles', 'index.php?option=com_mobilize&view=profiles', false, '', 'toolbar-menu.profiles');

        if ($items) {
            JSNMenuHelper::addEntry('recent-profiles', 'Recent Profiles', '', false, '', 'toolbar-menu.profiles');

            foreach ($items AS $item) {
                JSNMenuHelper::addEntry('item-' . $item->profile_id, $item->profile_title, 'index.php?option=com_mobilize&view=profile&task=profile.edit&layout=edit&profile_id=' . $item->profile_id, false, '', 'toolbar-menu.profiles.recent-profiles');
            }
        }
        JSNMenuHelper::addSeparator('toolbar-menu.profiles');
        JSNMenuHelper::addEntry('item-new', 'Create New Profile', 'index.php?option=com_mobilize&view=profile&layout=edit', false, '', 'toolbar-menu.profiles');
    }

    /**
     * Setup menu button for Joomla 3.0.
     *
     * @return  void
     */
    public static function menuToolbar() {
        $subMenuItemLists = JSNMobilizeHelper::getProfiles(5);

        // Build options
        $options[] = array('title' => JText::_('JSN_MOBILIZE_SUBMENU_PROFILES'), 'link' => 'index.php?option=com_mobilize&view=profiles', 'class' => 'parent primary', 'sub_menu_link' => 'index.php?option=com_mobilize&view=profile&task=profile.edit&profile_id={$profile_id}', 'sub_menu_field_title' => 'profile_title', 'sub_menu_link_add_title' => 'Create new profiles', 'sub_menu_link_add' => 'index.php?option=com_mobilize&view=profile&layout=edit', 'data_sub_menu' => $subMenuItemLists, 'icon' => 'jsn-icon-finder',);
        $options[] = array('class' => 'separator');

        $options[] = array('title' => JText::_('JSN_MOBILIZE_SUBMENU_CONFIGURATION'), 'link' => 'index.php?option=com_mobilize&view=configuration');
        $options[] = array('title' => JText::_('JSN_MOBILIZE_SUBMENU_ABOUT'), 'link' => 'index.php?option=com_mobilize&view=about');

        // Generate HTML code for sub-menu
        $html = JSNHtmlGenerate::menuToolbar($options);

        return $html;
    }

    /**
     * Load assets
     *
     * @return  void
     */
    public static function loadAssets($checkVersion = true) {
        // Load common assets
		$stylesheets[] = '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css';
        $stylesheets[] = JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css';
        $stylesheets[] = JURI::root(true) . '/plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css';
        $stylesheets[] = JSN_URL_ASSETS . '/3rd-party/jquery-tipsy/tipsy.css';
        $stylesheets[] = JURI::base(true) . '/components/com_mobilize/assets/css/mobilize.css';
        if ($checkVersion && JSNVersion::isJoomlaCompatible('3.2')) {
            JSNHtmlAsset::addScript(JUri::root(true) . '/media/jui/js/jquery.min.js');
        }
        JSNHtmlAsset::addStyle($stylesheets);
    }

    /**
     * Get data forms
     *
     * @param   integer  $limit  Limit number
     *
     * @return object list
     */
    public static function getProfiles($limit = "") {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__jsn_mobilize_profiles');
        $query->order("profile_id DESC");
        if (empty($limit)) {
            $db->setQuery($query);
        } else {
            $db->setQuery($query, 0, $limit);
        }
        $forms = $db->loadObjectList();
        // Check for a database error.
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }
        return $forms;
    }

    /**
     * Get data Os Support By Profile Id
     *
     * @param   integer  $profileId  Profile Id
     *
     * @return object list
     */
    public static function getOsSupportByProfileId($profileId) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__jsn_mobilize_os_support AS sp');
        $query->join('INNER', '#__jsn_mobilize_os AS s ON s.os_id = sp.os_id');
        $query->where('sp.profile_id = ' . $db->Quote($profileId));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Get data Design By Profile Id
     *
     * @param   integer  $profileId  Profile Id
     *
     * @return object list
     */
    public static function getDesignByProfileId($profileId) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__jsn_mobilize_design');
        $query->where('profile_id = ' . $db->Quote($profileId));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    /**
     * Get data Style Design by class
     *
     * @param   string  $key class name
     * @param   array   $valueStyle
     * @param   array   $backGroundType
     * @return style code
     */
    public function getStyleDesign($key,$valueStyle,$backGroundType){
        $itemMobileToolContainer = array();
        $itemMobileToolSubTitle = array();
        $itemMobileToolSubContent = array();
        $itemMobileToolSubLink = array();
        $boxShadow = array();
        $border = array();
		$borderModule = array();
        foreach ($valueStyle as $val) {
            if (isset($val->key)) {
                if (strpos($val->key, $key . "_container_") !== false) {
                    $keyStyle = explode("_", $val->key);
                    $keyStyle = $keyStyle[count($keyStyle) - 1];
                    if ($keyStyle != "soildColor" && $keyStyle != "gradientColor") {
                        if ($keyStyle == "borderThickness" || $keyStyle == "borderStyle" || $keyStyle == "borderColor") {
                            $itemMobileToolContainer[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $border);
                        } else {
                            $itemMobileToolContainer[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $boxShadow);
                        }
                    } else {
                        $keyBackgroundType = str_replace($keyStyle, "", $val->key);
                        $keyBackgroundType = $keyBackgroundType . "backgroundType";
                        if (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Solid" && $keyStyle == "soildColor") {
                            $itemMobileToolContainer[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $boxShadow);
                        } elseif (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Gradient" && $keyStyle == "gradientColor") {
                            $itemMobileToolContainer[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $boxShadow);
                        }
                    }
                }
				if (strpos($val->key, $key . "_module_tabContainer_") !== false)
				{
					$keyStyle = explode("_", $val->key);
					$keyStyle = $keyStyle[count($keyStyle) - 1];
					if ($keyStyle != "soildColor" && $keyStyle != "gradientColor")
					{
						if ($keyStyle == "borderThickness" || $keyStyle == "borderStyle" || $keyStyle == "borderColor")
						{ 
							$css = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $borderModule);
						}
						else
						{
							$css = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $boxShadow);
						}
					}
					else
					{
						$keyBackgroundType = str_replace($keyStyle, "", $val->key);
						$keyBackgroundType = $keyBackgroundType . "backgroundType";
						if (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Solid" && $keyStyle == "soildColor")
						{
							$css = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
						}
						elseif (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Gradient" && $keyStyle == "gradientColor")
						{
							$css = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
						}
					}
					if ($css)
					{
						$itemModuleSubContainer[$key][] = $css;
					}
				}
                if (strpos($val->key, $key . "_content_title_") !== false) {
                    $itemMobileToolSubTitle[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                }
                if (strpos($val->key, $key . "_content_body_") !== false) {
                    $itemMobileToolSubContent[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                }
                if (strpos($val->key, $key . "_content_link_") !== false) {
                    $itemMobileToolSubLink[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                }
				
				if($val->key == $key . '_container_ba_backgroundType'){
					if($val->value == 'img'){
						$cmd=1;
					}
				}
				if($val->key  == $key.'_container_rtl'){
					$rtl = $val->value;
				}
				if(isset($cmd) && !empty($cmd)){
						if($val->key == $key . '_container_image'){
							$pathImg = $val->value;
						}
						if($val->key == $key.'_container_effectColor'){
							$colorEffect = $val->value;
						}
						if($val->key == $key.'_container_opacity'){
							$colorOpacity = $val->value;
						}
						if($val->key == $key.'_container_imageWidth'){
							$imgW = $val->value;
						}
						if($val->key == $key.'_container_imageHeight'){
							$imgH = $val->value;
						}
						if($pathImg !=''){
							if (strpos($pathImg,'http://') !== false) {
								$pathImg = $pathImg;
							}
							else{
								/* if($_SERVER['SERVER_PORT'] !== ''){
									$pathImg = 'http://' . $_SERVER['SERVER_NAME'] .':'.$_SERVER['SERVER_PORT']. JURI::root(true) .'/'. $pathImg;
								}else{
									$pathImg = 'http://' . $_SERVER['SERVER_NAME'] . JURI::root(true) .'/'. $pathImg;
								} */								 
								
								$app = JFactory::getApplication();
								$preview = $app->input->getInt( 'jsn_mobilize_preview', 0 );
								if ($preview) {
									$pathImg = JURI::root(true) .'/'. $pathImg;
								} else {
									$pathImg = '/'. $pathImg;
								}
							}
							$cssBG = ' position:relative;background:url('.$pathImg.');background-size: '.$imgW .' '. $imgH .';';
							$pathImg='';
						}
				}
            }
        }
        if (!empty($boxShadow)) {
            $itemMobileToolContainer[$key][] = "box-shadow:" . implode(" ", $boxShadow) . ";";
            $itemMobileToolContainer[$key][] = "webkit-box-shadow:" . implode(" ", $boxShadow) . ";";
        }
        if (!empty($border) && !empty($border['border'])) {
            $borderStyle = !empty($border['border-style']) ? $border['border-style'] : "";
            $borderColor = !empty($border['border-color']) ? $border['border-color'] : "";
            $itemMobileToolContainer[$key][] = "border:" . $border['border'] . " " . $borderStyle . " " . $borderColor . ";";
        }
		if (!empty($borderModule) && !empty($borderModule['border'])) {
			$borderStyle = !empty($borderModule['border-style']) ? $borderModule['border-style'] : "";
			$borderColor = !empty($borderModule['border-color']) ? $borderModule['border-color'] : "";
			$itemModuleSubContainer[$key][] = "border:" . $borderModule['border'] . " " . $borderStyle . " " . $borderColor . ";";
		}
		if($key === 'jsn_template'){ 
			if(isset($rtl) && !empty($rtl)){
				$codeCss[] = '#jsn-master .jsn-mobile-layout{text-align:'.$rtl.'}#jsn-master .jsn-mobile-layout input[type="radio"], #jsn-master .jsn-mobile-layout input[type="checkbox"]{float:none;text-align: '.$rtl.' !important}.jsn-master .jsn-narrow .jsn-row-container label{text-align: '.$rtl.' !important}';
				if($rtl === 'right'){
					$app = JFactory::getApplication();
					$preview = $app->input->getInt( 'jsn_mobilize_preview', 0 );
					if (!$preview)
					{
						
						$codeCss[] = '#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li{	margin-right: 15px}
							#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li > a {
								padding-right: 20px;
								background:url(/templates/jsn_mobilize/images/icons/icons-base.png) right -334px no-repeat;}
							.jsn-mobile-layout ul.jsn-menu-mobile span.jsn-menu-toggle{left:0}
							#jsn-menu #jsn-logo{text-align:left !important}';
					}
					else
					{
						
						$codeCss[] = '#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li{	margin-right: 15px}
							#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li > a {
								padding-right: 20px;
								background: url('.JURI::root(true) .'/templates/jsn_mobilize/images/icons/icons-base.png) right -334px no-repeat;}
							.jsn-mobile-layout ul.jsn-menu-mobile span.jsn-menu-toggle{left:0}
							#jsn-menu #jsn-logo{text-align:left !important}';
					}
					
				}
			}
			if(isset($cssBG) && !empty($cssBG)){
				$codeCss[] = "#jsn-master{" .$cssBG."}";
			}
			if (!empty($itemMobileToolContainer[$key])) {
				$codeCss[] = "#jsn-master .jsn-mobile-layout" . "{ position:relative;" . implode("", $itemMobileToolContainer[$key]) . " }";
				$codeCss[] = "#jsn-master .jsn-mobile-layout #jsn-menu .row-fluid {position: relative; width: auto !important}";
			}
			if (!empty($itemMobileToolSubContent[$key])) {
				$codeCss[] = "#jsn-master .jsn-mobile-layout p" . "{ " . implode("", $itemMobileToolSubContent[$key]) . " }";
			}
			if (!empty($itemMobileToolSubTitle[$key])) {
				$codeCss[] = "#jsn-master .jsn-mobile-layout h1, h2, h3, h4, h5, h6" . "{ " . implode("", $itemMobileToolSubTitle[$key]) . " }";
			}
			if (!empty($itemMobileToolSubLink[$key])) {
				$codeCss[] = "#jsn-master .jsn-mobile-layout a" . "{ " . implode("", $itemMobileToolSubLink[$key]) . " }";
			}
		}else{
			if(isset($rtl) && !empty($rtl)){
				if($rtl == 'left' || $rtl == 'right'){
					$codeCss[] = '#jsn-master .jsn-mobile-layout #'.str_replace("_", "-", $key).' {text-align:'.$rtl.' !important}#jsn-master .jsn-mobile-layout #'.str_replace("_", "-", $key).' input[type="radio"], #jsn-master .jsn-mobile-layout #'.str_replace("_", "-", $key).' input[type="checkbox"]{float:none;text-align: '.$rtl.' !important}#jsn-master #'.str_replace("_", "-", $key).' .jsn-narrow .jsn-row-container label{text-align: '.$rtl.' !important}';
				}else{
					$codeCss[] = '';
				}
			}
			if(isset($cssBG) && !empty($cssBG)){
				$codeCss[] = "#jsn-master .jsn-mobile-layout #".str_replace("_", "-", $key)."{".$cssBG ."}";
			}
			if (!empty($itemMobileToolContainer[$key])) {
				$codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " { " . implode("", $itemMobileToolContainer[$key]) . " }";
			}
			if (!empty($itemMobileToolSubContent[$key])) {
				$codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " { " . implode("", $itemMobileToolSubContent[$key]) . " }";
			}
			if (!empty($itemMobileToolSubTitle[$key])) {
				$codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " h1, #jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " h2, #jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " h3, #jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " h4, #jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " h5, #jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " h6{ " . implode("", $itemMobileToolSubTitle[$key]) . "!important}";
			}
			if (!empty($itemMobileToolSubLink[$key])) {
				$codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " a { " . implode("", $itemMobileToolSubLink[$key]) . " }";
			}
			if (!empty($itemModuleSubContainer[$key])) {
				$codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .jsn-modulecontainer{ " . implode("", $itemModuleSubContainer[$key]) . " }";
			}
		}
        return implode("\n", array_merge($codeCss));
    }
    /**
     * generate Style
     *
     * @param   String  $style  Object Style
     *
     * @return style code
     */
    public static function generateStyle($style) {
        if ($style) {
            $codeCss = array();
            $font = array();
            foreach ($style as $key => $value) {
                //	$getValue = json_decode($value);
                if($key == "jsn_typestyle"){
                    if ($value == 'Simple' || $value == 'Retro' || $value == 'Flat' || $value == 'Modern') {
                        $codeCss[] = '#jsn-menu{border-bottom:1px solid #f2f2f2 !important} #jsn-menu ul li a{border-left:1px solid #f2f2f2}';
                    }
                    if ($value == 'Metro' || $value == 'Glass' || $value == 'Solid') {
                        $codeCss[] = '#jsn-menu ul li.dropdown{border-left:1px solid #373737 !important}';
                    }
                }
                if ($value) {
                    $backGroundType = array();
                    $valueDecode = null;
                    $valueStyle = null;
                    
                    if (is_string($value)) {
                        $valueDecode = @json_decode($value);
                    }
                    if (is_null($valueDecode)) {
                        $valueStyle = $value;
                    } else {
                        $valueStyle = $valueDecode;
                    }
                    if (!is_null($valueStyle)) {
                        foreach ($valueStyle as $val) {
                            if (isset($val->key)) {
                                $getKeyStyle = explode("_", $val->key);
                                $getKeyStyle = $getKeyStyle[count($getKeyStyle) - 1];
                                if ($getKeyStyle == "backgroundType") {
                                    $backGroundType[$val->key] = $val->value;
                                }
                            }
                        }
                        
                        $getStyle = new JSNMobilizeHelper();
                        switch ($key) {
                            case "jsn_logo":
                                $logoStyle = array();
                                $border = array();
                                foreach ($valueStyle as $val) {
                                    if (isset($val->key)) {
                                        if (strpos($val->key, $key . "_container_") !== false) {
                                            $keyStyle = explode("_", $val->key);
                                            $keyStyle = $keyStyle[count($keyStyle) - 1];
                                            if ($keyStyle != "soildColor" && $keyStyle != "gradientColor") {
                                                if ($keyStyle == "borderThickness" || $keyStyle == "borderStyle" || $keyStyle == "borderColor") {
                                                    $logoStyle[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $border[$key]);
                                                } else {
                                                    $logoStyle[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                }
                                            } else {
                                                $keyBackgroundType = str_replace($keyStyle, "", $val->key);
                                                $keyBackgroundType = $keyBackgroundType . "backgroundType";
                                                if (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Solid" && $keyStyle == "soildColor") {
                                                    $logoStyle[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                } elseif (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Gradient" && $keyStyle == "gradientColor") {
                                                    $logoStyle[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                }
                                            }
                                        }
                                        if (strpos($val->key, $key . "_content_") !== false) {
                                            $keyStyle = explode("_", $val->key);
                                            $keyStyle = $keyStyle[count($keyStyle) - 1];
                                            if ($keyStyle == "alignment") {
                                                $logoStyle[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value);
                                            }
                                        }
                                    }
                                }
                                if (!empty($border[$key]) && !empty($border[$key]['border'])) {
                                    $borderStyle = !empty($border[$key]['border-style']) ? $border[$key]['border-style'] : "";
                                    $borderColor = !empty($border[$key]['border-color']) ? $border[$key]['border-color'] : "";
                                    $logoStyle[] = "border:" . $border[$key]['border'] . " " . $borderStyle . " " . $borderColor;
                                }
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . "{ " . implode("", $logoStyle) . " }";
                                break;
                            case "jsn_switcher":
                                $itemMenuContainer = array();
                                $border = array();
                                foreach ($valueStyle as $val) {
                                    if (isset($val->key)) {
                                        if (strpos($val->key, $key . "_container_") !== false) {
                                            $keyStyle = explode("_", $val->key);
                                            $keyStyle = $keyStyle[count($keyStyle) - 1];
                                            if ($keyStyle != "soildColor" && $keyStyle != "gradientColor") {
                                                if ($keyStyle == "borderThickness" || $keyStyle == "borderStyle" || $keyStyle == "borderColor") {
                                                    $itemMenuContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $border[$key]);
                                                } else {
                                                    $itemMenuContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                }
                                            } else {
                                                $keyBackgroundType = str_replace($keyStyle, "", $val->key);
                                                $keyBackgroundType = $keyBackgroundType . "backgroundType";
                                                if (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Solid" && $keyStyle == "soildColor") {
                                                    $itemMenuContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                } elseif (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Gradient" && $keyStyle == "gradientColor") {
                                                    $itemMenuContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                }
                                            }
                                        }
                                    }
                                }
                                if (!empty($border[$key]) && !empty($border[$key]['border'])) {
                                    $borderStyle = !empty($border[$key]['border-style']) ? $border[$key]['border-style'] : "";
                                    $borderColor = !empty($border[$key]['border-color']) ? $border[$key]['border-color'] : "";
                                    $itemMenuContainer[] = "border:" . $border[$key]['border'] . " " . $borderStyle . " " . $borderColor;
                                }
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . "{ " . implode("", $itemMenuContainer) . " }";
                                break;
                            case "jsn_menu":
                                $itemMenuContainer = array();
                                $itemMenuSub1 = array();
                                $itemLinkMenuSub1 = array();
                                $itemMenuSub1Active = array();
                                $itemMenuSub2 = array();
                                $itemLinkMenuSub2 = array();
                                $itemMenuIcon = array();
                                $itemMenuContainerActive = array();
                                $border = array();
                                $subMenuBorder = array();
                                foreach ($valueStyle as $val) {
                                    if (isset($val->key)) {
                                        if (strpos($val->key, $key . "_container_") !== false) {
                                            $keyStyle = explode("_", $val->key);
                                            $keyStyle = $keyStyle[count($keyStyle) - 1];
                                            if ($keyStyle == "borderThickness" || $keyStyle == "borderStyle" || $keyStyle == "borderColor") {
                                                $itemMenuContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $border);
                                            } elseif ($keyStyle == "activeColor") {
                                                $itemMenuContainerActive[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                            } elseif ($keyStyle == "iconColor") {
                                                $itemMenuIcon[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                            } elseif ($keyStyle != "soildColor" && $keyStyle != "gradientColor") {
                                                $itemMenuContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                            } else {
                                                $keyBackgroundType = str_replace($keyStyle, "", $val->key);
                                                $keyBackgroundType = $keyBackgroundType . "backgroundType";
                                                if (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Solid" && $keyStyle == "soildColor") {
                                                    $itemMenuContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                } elseif (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Gradient" && $keyStyle == "gradientColor") {
                                                    $itemMenuContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                }
                                            }
                                        }
										if($val->key  == $key.'_container_rtl'){
											$rtl = $val->value;
										}
                                        if (strpos($val->key, $key . "_sublevel1_") !== false) {
                                            $keyStyle = explode("_", $val->key);
                                            $keyStyle = $keyStyle[count($keyStyle) - 1];
                                            if ($keyStyle == "borderThickness" || $keyStyle == "borderStyle" || $keyStyle == "borderColor") {
                                                $itemMenuSub1[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $subMenuBorder);
                                            } elseif ($keyStyle == "activeColor") {
                                                $itemMenuSub1Active[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                            } elseif ($keyStyle == "normalColor") {
                                                $itemMenuSub1[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                            } else {
                                                $itemLinkMenuSub1[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                            }
                                        }
                                        if (strpos($val->key, $key . "_sublevel2_") !== false) {
                                            $keyStyle = explode("_", $val->key);
                                            $keyStyle = $keyStyle[count($keyStyle) - 1];
                                            if ($keyStyle == "normalColor") {
                                                $itemMenuSub2[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                            } elseif ($keyStyle == "activeColor") {
                                                $itemMenuSub2Active[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                            } else {
                                                $itemLinkMenuSub2[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                            }
                                        }
                                    }
                                }
                                if (!empty($border) && !empty($border['border'])) {
                                    $borderStyle = !empty($border['border-style']) ? $border['border-style'] : "";
                                    $borderColor = !empty($border['border-color']) ? $border['border-color'] : "";
                                    $itemMenuContainer[] = "border:" . $border['border'] . " " . $borderStyle . " " . $borderColor . ";";
                                }
                                if (!empty($subMenuBorder) && !empty($subMenuBorder['border'])) {
                                    $borderStyle = !empty($subMenuBorder['border-style']) ? $subMenuBorder['border-style'] : "";
                                    $borderColor = !empty($subMenuBorder['border-color']) ? $subMenuBorder['border-color'] : "";

                                    $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu ul.jsn-menu-mobile > li  { " . implode("", array("border:" . $subMenuBorder['border'] . " " . $borderStyle . " " . $borderColor . ";")) . " }";
                                    $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu  { " . implode("", array("border:" . $subMenuBorder['border'] . " " . $borderStyle . " " . $borderColor . ";")) . " }";
                                }
                                $app = JFactory::getApplication();
                                $preview = $app->input->getInt( 'jsn_mobilize_preview', 0 );
								if($rtl === 'left'){
									if (!$preview)
									{
										$codeCss[] = '#jsn-menu .mobilize-menu{text-align:left}
										#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li{margin-left: 15px !important}
										#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li > a {
											padding-left: 20px !important;
											background:url(/templates/jsn_mobilize/images/icons/icons-base.png) left -334px no-repeat }
										.jsn-mobile-layout ul.jsn-menu-mobile span.jsn-menu-toggle{right:0; left:auto}';
									}
									else
									{
									
										$codeCss[] = '#jsn-menu .mobilize-menu{text-align:left}
										#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li{margin-left: 15px !important}
										#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li > a {
											padding-left: 20px !important;
											background: url('.JURI::root(true) .'/templates/jsn_mobilize/images/icons/icons-base.png) left -334px no-repeat }
										.jsn-mobile-layout ul.jsn-menu-mobile span.jsn-menu-toggle{right:0; left:auto}';
									}								
								}elseif($rtl === 'right'){
									if (!$preview)
									{
										$codeCss[] = '#jsn-menu #jsn-logo{text-align:left !important}
										#jsn-menu .mobilize-menu{text-align:right}
										#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li{margin-right: 15px !important}
										#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li > a {
											padding-right: 20px !important;
											background:url(/templates/jsn_mobilize/images/icons/icons-base.png) right -334px no-repeat }
										.jsn-mobile-layout ul.jsn-menu-mobile span.jsn-menu-toggle{right:auto; left:0}';
									}
									else
									{
										$codeCss[] = '#jsn-menu #jsn-logo{text-align:left !important}
										#jsn-menu .mobilize-menu{text-align:right}
										#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li{margin-right: 15px !important}
										#jsn-menu .mobilize-menu ul.jsn-menu-mobile ul > li > a {
											padding-right: 20px !important;
											background: url('.JURI::root(true) .'/templates/jsn_mobilize/images/icons/icons-base.png) right -334px no-repeat }
										.jsn-mobile-layout ul.jsn-menu-mobile span.jsn-menu-toggle{right:auto; left:0}';
									}
									
								}else{
									$codeCss[] = '';
								}
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . "{ " . implode("", $itemMenuContainer) . " }";

                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu ul.jsn-menu-mobile,#" . $key . " .mobilize-menu div.jsn-menu-mobile{ " . implode("", $itemMenuSub1) . " }";
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu ul.jsn-menu-mobile > li > a{ " . implode("", $itemLinkMenuSub1) . " }";
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu ul.jsn-menu-mobile ul{ " . implode("", $itemMenuSub2) . " }";
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu ul.jsn-menu-mobile ul li a{ " . implode("", $itemLinkMenuSub2) . " }";
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu > li > span.active{ " . implode("", $itemMenuContainerActive) . " }";
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu > li > ul > li.sub-menu-active{ " . implode("", $itemMenuSub1Active) . " }";
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu > li > ul > li.sub-menu-active > ul li.current{ " . implode("", $itemMenuSub2Active) . " }";
                                $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .mobilize-menu > li > span.jsn-menu-toggle i { " . implode("", $itemMenuIcon) . " }";
                                break;
                            case "jsn_mobile_tool":
                               $codeCss[] = JSNMobilizeHelper :: getStyleDesign($key,$valueStyle,$backGroundType);
                                break;
                            case "jsn_content_top":
                               $codeCss[] = JSNMobilizeHelper :: getStyleDesign($key,$valueStyle,$backGroundType);
                            case "jsn_user_top":
                                $codeCss[] = JSNMobilizeHelper :: getStyleDesign($key,$valueStyle,$backGroundType);
                                break;
                            case "jsn_user_bottom":
                                $codeCss[] = JSNMobilizeHelper :: getStyleDesign($key,$valueStyle,$backGroundType);
                                break;
                            case "jsn_content_bottom":
                                $codeCss[] = JSNMobilizeHelper :: getStyleDesign($key,$valueStyle,$backGroundType);
                                break;
                            case "jsn_footer":
                                $codeCss[] = JSNMobilizeHelper :: getStyleDesign($key,$valueStyle,$backGroundType);
                                break;
                            case "jsn_mainbody":
                                $codeCss[] = JSNMobilizeHelper :: getStyleDesign($key,$valueStyle,$backGroundType);
                                break;
							case "jsn_template":
                                $codeCss[] = JSNMobilizeHelper :: getStyleDesign($key,$valueStyle,$backGroundType);
                                break; 
                            
                            default:
                                $itemModuleContainer = array();
                                $itemModuleSubContainer = array();
                                $itemModuleSubTitle = array();
                                $itemModuleSubContent = array();
                                $itemModuleSubLink = array();
                                $boxShadow = array();
                                $borderContainer = array();
                                $borderModule = array();
                                foreach ($valueStyle as $val) {
                                    if (isset($val->key)) {
                                        if (strpos($val->key, $key . "_container_") !== false) {

                                            $keyStyle = explode("_", $val->key);
                                            $keyStyle = $keyStyle[count($keyStyle) - 1];
                                            if ($keyStyle != "soildColor" && $keyStyle != "gradientColor") {
                                                if ($keyStyle == "borderThickness" || $keyStyle == "borderStyle" || $keyStyle == "borderColor") {
                                                    $itemModuleContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $borderContainer);
                                                } else {
                                                    $itemModuleContainer[] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                }
                                            } else {
                                                $keyBackgroundType = str_replace($keyStyle, "", $val->key);
                                                $keyBackgroundType = $keyBackgroundType . "backgroundType";
                                                if (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Solid" && $keyStyle == "soildColor") {
                                                    $itemModuleContainer[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                } elseif (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Gradient" && $keyStyle == "gradientColor") {
                                                    $itemModuleContainer[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                }
                                            }
                                        }
                                        if (strpos($val->key, $key . "_module_tabContainer_") !== false) {
                                            $keyStyle = explode("_", $val->key);
                                            $keyStyle = $keyStyle[count($keyStyle) - 1];
                                            if ($keyStyle != "soildColor" && $keyStyle != "gradientColor") {
                                                if ($keyStyle == "borderThickness" || $keyStyle == "borderStyle" || $keyStyle == "borderColor") {
                                                    $css = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $borderModule);
                                                } else {
                                                    $css = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font, $boxShadow);
                                                }
                                            } else {
                                                $keyBackgroundType = str_replace($keyStyle, "", $val->key);
                                                $keyBackgroundType = $keyBackgroundType . "backgroundType";
                                                if (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Solid" && $keyStyle == "soildColor") {
                                                    $css = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                } elseif (!empty($backGroundType[$keyBackgroundType]) && $backGroundType[$keyBackgroundType] == "Gradient" && $keyStyle == "gradientColor") {
                                                    $css = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                                }
                                            }
                                            if ($css) {
                                                $itemModuleSubContainer[$key][] = $css;
                                            }
                                        }
                                        if (strpos($val->key, $key . "_module_tabContent_title_") !== false) {
                                            $itemModuleSubTitle[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                        }
                                        if (strpos($val->key, $key . "_module_tabContent_body_") !== false) {
                                            $itemModuleSubContent[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                        }
                                        if (strpos($val->key, $key . "_module_tabContent_link_") !== false) {
                                            $itemModuleSubLink[$key][] = JSNMobilizeHelper::getStyleOptions($val->key, $val->value, $font);
                                        }
                                    }
                                }

                                if (!empty($borderContainer) && !empty($borderContainer['border'])) {
                                    $borderStyle = !empty($borderContainer['border-style']) ? $borderContainer['border-style'] : "";
                                    $borderColor = !empty($borderContainer['border-color']) ? $borderContainer['border-color'] : "";
                                    $itemModuleContainer[$key][] = "border:" . $borderContainer['border'] . " " . $borderStyle . " " . $borderColor . ";";
                                }

                                if (!empty($borderModule) && !empty($borderModule['border'])) {
                                    $borderStyle = !empty($borderModule['border-style']) ? $borderModule['border-style'] : "";
                                    $borderColor = !empty($borderModule['border-color']) ? $borderModule['border-color'] : "";
                                    $itemModuleSubContainer[$key][] = "border:" . $borderModule['border'] . " " . $borderStyle . " " . $borderColor . ";";
                                }

                                if (!empty($boxShadow)) {
                                    $itemModuleSubContainer[$key][] = "box-shadow:" . implode(" ", $boxShadow) . ";";
                                    $itemModuleSubContainer[$key][] = "webkit-box-shadow:" . implode(" ", $boxShadow) . ";";
                                }
                                if (!empty($itemModuleContainer[$key])) {
                                    $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . "{ " . implode("", $itemModuleContainer[$key]) . " }";
                                }

                                if (!empty($itemModuleSubTitle[$key])) {
                                    $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .jsn-moduletitle{ " . implode("", $itemModuleSubTitle[$key]) . " }";
                                }

                                if (!empty($itemModuleSubContainer[$key])) {
                                    $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .jsn-modulecontainer{ " . implode("", $itemModuleSubContainer[$key]) . " }";
                                }
                                if (!empty($itemModuleSubContent[$key])) {
                                    $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " .jsn-modulecontainer{ " . implode("", $itemModuleSubContent[$key]) . " }";
                                }
                                if (!empty($itemModuleSubLink[$key])) {
                                    $codeCss[] = "#jsn-master .jsn-mobile-layout #" . str_replace("_", "-", $key) . " a{ " . implode("", $itemModuleSubLink[$key]) . " }";
                                }
                                break;
                        }
                    }
                }
            }
        }
        return implode("\n", array_merge($font, $codeCss));
    }
    /**
     * get Style Options
     *
     * @param   String  $key      Object Style
     *
     * @param   String  $value    Object Style
     *
     * @param   null    &$font    Object Font
     *
     * @param   null    &$option  Object Style
     *
     * @return style code
     */
    public static function getStyleOptions($key, $value, &$font = null, &$option = null) {
        $keyStyle = explode("_", $key);
        $css = array();

        if ($keyStyle && !empty($value)) {
            $keyStyle = $keyStyle[count($keyStyle) - 1];
            switch ($keyStyle) {
                case "normalColor":
                case "soildColor":
                case "gradientColor":
                    if (strpos($value, "(")) {
                        preg_match('/(.*?)\((.*?), (.*?)\s(.*?), (.*?)\s(.*?)\)/', $value, $matches, PREG_OFFSET_CAPTURE);
                        $value1 = !empty($matches[2][0]) ? $matches[2][0] : '';
                        $value2 = !empty($matches[3][0]) ? $matches[3][0] : '';
                        $value3 = !empty($matches[4][0]) ? $matches[4][0] : '';
                        $value4 = !empty($matches[5][0]) ? $matches[5][0] : '';
                        $value5 = !empty($matches[6][0]) ? $matches[6][0] : '';

                        $css[] = "background: " . $value2 . ";";
                        $css[] = "background:linear-gradient(135deg, " . $value2 . " " . $value3 . "," . $value4 . " " . $value5 . ");";
                        $css[] = "background:-moz-linear-gradient(" . $value1 . ", " . $value2 . " " . $value3 . ", " . $value4 . " " . $value5 . ");";
                        $css[] = "background:-webkit-gradient(linear, left top, right bottom, color-stop(" . $value3 . "," . $value2 . "), color-stop(" . $value5 . "," . $value4 . "));";
                        $css[] = "background:-webkit-linear-gradient(" . $value1 . ", " . $value2 . " " . $value3 . "," . $value4 . " " . $value5 . ");";
                        $css[] = "background:-o-linear-gradient(" . $value1 . ", " . $value2 . " " . $value3 . "," . $value4 . " " . $value5 . ");";
                        $css[] = "background:-ms-linear-gradient(" . $value1 . ", " . $value2 . " " . $value3 . "," . $value4 . " " . $value5 . ");";
                        $css[] = "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='" . $value2 . "', endColorstr='" . $value4 . "',GradientType=1 );";
                    } else {
                        $css[] = "background:" . $value . ";";
                    }
                    break;
                case "activeColor":
                    $css[] = "background:" . $value . ";";
                    break;
                case "borderThickness":
                    $option['border'] = $value . "px";
                    break;
                case "borderColor":
                    $option['border-color'] = $value;
                    break;
                case "borderStyle":
                    $option['border-style'] = $value;
                    break;
                case "fontFace":
                    $css[] = "font-family:" . $value . ";";
                    if (!in_array($value, array('Verdana', 'Georgia', 'Courier New', 'Arial', 'Tahoma', 'Trebuchet MS'))) {
                        $font[] = "@import url(http://fonts.googleapis.com/css?family=" . str_replace(" ", "+", $value) . ");";
                    }
                    break;
                case "fontStyle":
                    if ($value == "bold") {
                        $css[] = "font-weight:" . $value . ";";
                    } else {
                        $css[] = "font-style:" . $value . ";";
                    }
                    break;
                case "fontSize":
                    $css[] = "font-size:" . $value . "px;";
                    break;
                case "iconColor":
                    $css[] = "color:" . $value . ";";
                    if ($value != "black") {
                    	$app = JFactory::getApplication();
                    	$preview = $app->input->getInt( 'jsn_mobilize_preview', 0 );
                    	if (!$preview)
                    	{
                    		$css[] = "background-image:url(/media/jui/img/glyphicons-halflings-white.png);";
                    	}
                    	else
                    	{
                    		$css[] = "background-image: url('" . JURI::root(true) . "/media/jui/img/glyphicons-halflings-white.png');";
                    	}
                    }
                    $css[] = "color:" . $value . ";";
                    break;
                case "linkColor":
                case "fontColor":
					$css[] = 'color:' . $value . ';';
                    break;
                case "roundedCornerRadius":
                    $css[] = "border-radius:" . $value . "px;";
                    break;
                case "shadowSpread":
                    $option[] = "0 1px 2px " . $value . "px";
                    break;
                case "shadowColor":
                    $option[] = "rgba(" . implode(",", JSNMobilizeHelper::hex2rgb($value)) . ",0.5)";
                    break;
                case "marginleft":
                    $css[] = "margin-left:" . $value . "px;";
                    break;
                case "marginright":
                    $css[] = "margin-right:" . $value . "px;";
                    break;
                case "margintop":
                    $css[] = "margin-top:" . $value . "px;";
                    break;
                case "marginbottom":
                    $css[] = "margin-bottom:" . $value . "px;";
                    break;
                case "paddingleft":
                    $css[] = "padding-left:" . $value . "px;";
                    break;
                case "paddingright":
                    $css[] = "padding-right:" . $value . "px;";
                    break;
                case "paddingtop":
                    $css[] = "padding-top:" . $value . "px;";
                    break;
                case "paddingbottom":
                    $css[] = "padding-bottom:" . $value . "px;";
                    break;
                case "alignment":
                    $css[] = "text-align:" . $value . ";";
                    break;
            }
        }
        //var_dump($css);
        return implode("\n", $css);
    }

    /**
     * @param   $hex  hexcode
     *
     * @return array
     */
    public static function hex2rgb($hex) {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public  static function checkVMVersion($comparedVersion = '2.9.8')
    {
        if (!class_exists( 'VmConfig' ))
        {
            $vmConfigPath = JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php';
            if (file_exists($vmConfigPath))
            {
                require_once ($vmConfigPath);
            }
            else
            {
                return false;
            }
        }


        $installedVersion = VmConfig::getInstalledVersion();
        if (version_compare($installedVersion, $comparedVersion, '>='))
        {
            return true;
        }

        return false;
    }

}
