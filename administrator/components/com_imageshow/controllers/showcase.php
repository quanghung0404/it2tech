<?php
/**
 * @version    $Id: showcase.php 16516 2012-09-27 11:38:09Z haonv $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class ImageShowControllerShowCase extends JControllerLegacy
{
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('add',  'display');
		$this->registerTask('edit', 'display');
		$this->registerTask('showcaseinstalltheme', 'display');
		$this->registerTask('apply', 'save');
		$this->registerTask('switchTheme', 'switchTheme');
	}

	function display($cachable = false, $urlparams = false)
	{
		switch(strtolower($this->getTask()))
		{
			case 'add':
				{
					JRequest::setVar('layout', 'form');
					JRequest::setVar('view', 'showcase');
					JRequest::setVar('edit', false );
					JRequest::setVar('model', 'showcase');
				}
				break;
			case 'edit':
				{
					JRequest::setVar('layout', 'form');
					JRequest::setVar('view', 'showcase');
					JRequest::setVar('edit', true);
					JRequest::setVar('model', 'showcase');
				}
				break;
			case 'showcaseinstalltheme':
				JRequest::setVar('layout', 'showcaseinstalltheme');
				JRequest::setVar('view', 'showcase');
				JRequest::setVar('model', 'installer');
				break;
			case 'authenticate':
				JRequest::setVar('layout', 'form_login');
				JRequest::setVar('view', 'showcase');
				JRequest::setVar('model', 'showcase');
				break;
			default:
				JRequest::setVar('layout', 'default');
				JRequest::setVar('view', 'showcases');
				JRequest::setVar('model', 'showcases');
		}

		parent::display();
	}

	function save()
	{
		global $objectLog;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$link 				 = 'index.php?option=com_imageshow&controller=showcase';
		$objJSNShowcase 	 = JSNISFactory::getObj('classes.jsn_is_showcase');
		$date 				 = JFactory::getDate();
		$tmpl				 = '';
		$user				 = JFactory::getUser();
		$userID				 = $user->get ('id');
		$db					 = JFactory::getDBO();
		$post				 = JRequest::get('post');
		$cid				 = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['showcase_id'] = (int) $cid[0];

		//$count   = $objJSNShowcase->countShowcase();
		$arrayID = $objJSNShowcase->getShowcaseID();
		$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
		//$limitStatus    = $objJSNUtils->checkLimit();

		/*if($count[0] >= 3 && $limitStatus == true)
		{
			if(!in_array((int)$cid[0], $arrayID))
			{
				$this->setRedirect('index.php?option=com_imageshow&controller=showcase');
				return false;
			}
		}*/

		if($cid[0] == '' or $cid[0] == 0)
		{
			$post['date_created']	= $date->format('Y-m-d H:m:s');
			$post['date_modified']	= $date->format('Y-m-d H:m:s');
		}
		else
		{
			$post['date_modified']	= $date->format('Y-m-d H:m:s');
		}

		if ($post['tmpl'] != '')
		{
			$tmpl=$post['tmpl'];
		}

		$post['general_overall_width'] = $post['general_overall_width'].$post['overall_width_dimension'];

		$showcaseTable = JTable::getInstance('showcase', 'Table');

		if ($post['showcase_id']) {
			$showcaseTable->load($post['showcase_id']);
		}

		$showcaseTable->bind($post);

		if ($showcaseTable->store($post))
		{
			$showcaseTable->reorder();

			if($post['showcase_id'] == 0 || $post['showcase_id'] == '')
			{
				$objectLog->addLog($userID, JRequest::getURI(), JRequest::getVar('showcase_title'),'showcase','add');
			}
			else
			{
				if($this->getTask() == 'save')
				{
					$objectLog->addLog($userID, JRequest::getURI(), JRequest::getVar('showcase_title'),'showcase','modify');
				}
			}

			// only save theme when theme is selected then create link between showcase and theme via theme profile
			if (!empty($post['theme_name']))
			{
				$objJSNShowcaseTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');
				$objJSNShowcaseTheme->importTableByThemeName($post['theme_name']);
				$objJSNShowcaseTheme->importModelByThemeName($post['theme_name']);

				$modelShowcaseTheme = JModelLegacy::getInstance($post['theme_name']);

				if($modelShowcaseTheme) {
					$post = $modelShowcaseTheme->_prepareSaveData($post);
				}

				$showcaseThemeTable = JTable::getInstance($post['theme_name'].@$post['theme_style_name'], 'Table');

				if(!$showcaseThemeTable)
				{
					$msg = JText::_('SHOWCASE_THEME_TABLE_DOES_NOT_EXISTS');
					$link = 'index.php?option=com_imageshow&controller=showcase';
					$this->setRedirect($link, $msg);
					return true;
				}

				$showcaseThemeTable->bind($post);

				if($showcaseThemeTable->store())
				{
					$post['theme_id'] 	= $showcaseThemeTable->theme_id;
					$themeStyleName		= (isset($post['theme_style_name']))?$post['theme_style_name']:'';
					$saveThemeProfile = $objJSNShowcaseTheme->insertThemeProfile($showcaseThemeTable->theme_id, $showcaseTable->showcase_id, $post['theme_name'],$themeStyleName);

					if (!$saveThemeProfile) {
						$msg = JText::_('SHOWCASE_THEME_PROFILE_DOES_NOT_SAVE');
						$link = 'index.php?option=com_imageshow&controller=showcase';
						$this->setRedirect($link, $msg);
						return true;
					}
				}
				else
				{
					$msg = JText::_('SHOWCASE_THEME_DOES_NOT_SAVE');
					$link = 'index.php?option=com_imageshow&controller=showcase';
					$this->setRedirect($link, $msg);
					return true;
				}

			}

			switch ($this->getTask())
			{
				case 'apply':
					$msg  = JText::_('SUCCESSFULLY_SAVED_CHANGES');
					if($post['redirectLink']!='')
					{
						$link = $post['redirectLink'];
					}
					else
					{
						$link = 'index.php?option=com_imageshow&controller=showcase&task=edit&theme='.strtolower(@$post['theme_name']).'&cid[]='. $showcaseTable->showcase_id.$tmpl;
					}
					break;
				default:
					$msg  = JText::_('SUCCESSFULLY_CREATED');
					$link = 'index.php?option=com_imageshow&controller=showcase';
					if (isset($post['jsn-menu-link-redirect']))
					{
						$msg = '';
						$link = ($post['jsn-menu-link-redirect'] != '') ? $post['jsn-menu-link-redirect'] : $link;
					}
					break;
			}
		}
		else
		{
			$msg = JText::_('SHOWCASE_ERROR_SAVING_SHOWCASE');
		}
		if($post['mainSite'] == 'false')
		{
			echo "<script>
				if (typeof window.parent.JoomlaShine != 'undefined' && typeof window.parent.JoomlaShine.jQuery != 'undefined')
				{
					window.parent.location.reload(true);
					window.parent.JoomlaShine.jQuery.closeAllJSNWindow();
				}
				else
				{
					window.parent.location.reload(true);
					window.parent.jQuery.closeAllJSNWindow();
				}
			</script>";
			exit();
		}else
		{
			$this->setRedirect($link, $msg);
		}
	}

	function remove()
	{
		global $mainframe, $objectLog;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$user					=& JFactory::getUser();
		$userID					= $user->get ('id');
		$cid 					= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$showcaseTable 			=& JTable::getInstance('showcase', 'Table');
		$objJSNShowcaseTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');

		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'PLEASE MAKE A SELECTION FROM THE LIST TO' ).' '.JText::_( 'DELETE' ) );
		}

		if (count($cid) == 1)
		{
			$objJSNShowcase = JSNISFactory::getObj('classes.jsn_is_showcase');
			$showcaseInfo 	= $objJSNShowcase->getShowCaseTitle($cid[0]);
		}

		foreach ($cid as $id)
		{
			if ($showcaseTable->load($id))
			{
				$themeProfile = $objJSNShowcaseTheme->getThemeProfile($showcaseTable->showcase_id);


				if ($themeProfile)
				{
					$objJSNShowcaseTheme->importTableByThemeName($themeProfile->theme_name);
					$showcaseThemeTable =& JTable::getInstance($themeProfile->theme_name.@$themeProfile->theme_style_name, 'Table');

					if ($showcaseThemeTable->load((int) $themeProfile->theme_id))
					{
						if ($showcaseThemeTable->delete((int) $themeProfile->theme_id))
						{
							$showcaseTable->delete($id);
							$objJSNShowcaseTheme->deleteThemeProfileShowcaseID($id);
						}
					}
				}
				else
				{
					$showcaseTable->delete($id);
				}
			}
		}

		if (count($cid) == 1)
		{
			$objectLog->addLog($userID, JRequest::getURI(), $showcaseInfo['showcase_title'], 'showcase', 'delete');
		}
		else
		{
			$objectLog->addLog($userID, JRequest::getURI(), count($cid), 'showcase', 'delete');
		}

		$mainframe->redirect('index.php?option=com_imageshow&controller=showcase');
	}

	function publish()
	{
		global $mainframe;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1)
		{
			JError::raiseError(500, JText::_( 'SELECT AN ITEM TO PUBLISH' ));
		}

		$model = $this->getModel('showcases');

		if (!$model->approve($cid, 1))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$mainframe->redirect('index.php?option=com_imageshow&controller=showcase');
	}

	function unpublish()
	{
		global $mainframe;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1)
		{
			JError::raiseError(500, JText::_( 'SELECT AN ITEM TO UNPUBLISH' ) );
		}

		$model = $this->getModel('showcases');

		if (!$model->approve($cid, 0))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$mainframe->redirect('index.php?option=com_imageshow&controller=showcase');
	}

	function cancel()
	{
		global $mainframe;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$mainframe->redirect('index.php?option=com_imageshow&controller=showcase');
	}

	function saveOrder()
	{
		global $mainframe;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$db			= & JFactory::getDBO();
		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
		$total		= count($cid);
		$conditions	= array ();
		$row 		= & JTable::getInstance('showcase','Table');

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		for ($i = 0; $i < $total; $i ++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];

				if (!$row->store())
				{
					JError::raiseError( 500, $db->getErrorMsg() );
					return false;
				}
			}
		}

		$msg = JText::_('New ordering saved');
		$mainframe->redirect('index.php?option=com_imageshow&controller=showcase', $msg);
	}

	function orderup()
	{
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$this->orderCategory($cid[0], -1);
	}

	function orderdown()
	{
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$this->orderCategory($cid[0], 1);
	}

	function orderCategory( $uid, $inc)
	{
		global $mainframe;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$db =& JFactory::getDBO();
		$row = & JTable::getInstance('showcase','Table');
		$row->load( $uid );
		$row->move( $inc);
		$msg = JText::_('New ordering saved');
		$mainframe->redirect('index.php?option=com_imageshow&controller=showcase', $msg);
	}

	function copy()
	{
		global $mainframe;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$objJSNShowcase 	= JSNISFactory::getObj('classes.jsn_is_showcase');
		$objJSNUtils 		= JSNISFactory::getObj('classes.jsn_is_utils');
		$limitStatus 		= $objJSNUtils->checkLimit();
		$totalShowcase 		= $objJSNShowcase->countShowcase();
		$db					=& JFactory::getDBO();
		$date 				=& JFactory::getDate();
		$cid 				= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		$total 				 = count($cid);
		$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');

		for ($i = 0; $i < $total; $i ++)
		{
			if (($totalShowcase[0] + $i >= 3) && $limitStatus == true)
			{
				$this->setRedirect('index.php?option=com_imageshow&controller=showcase');
				return false;
			}

			if($cid[$i])
			{
				$showcaseTable = JTable::getInstance('showcase','Table');
				$showcaseTable->load((int)$cid[$i]);
				$showcaseTable->showcase_title 	= 'Copy of '.$showcaseTable->showcase_title;
				$showcaseTable->ordering 		= 0;
				$showcaseTable->date_created	=  $date->format('%Y-%m-%d %H:%M:%S');
				$showcaseTable->date_modified	=  $date->format('%Y-%m-%d %H:%M:%S');

				$themeProfile = $objJSNShowcaseTheme->getThemeProfile($showcaseTable->showcase_id);

				if ($themeProfile)
				{
					$objJSNShowcaseTheme->importTableByThemeName($themeProfile->theme_name);
					$showcaseThemeTable =& JTable::getInstance($themeProfile->theme_name, 'Table');

					if ($showcaseThemeTable->load((int) $themeProfile->theme_id))
					{
						$showcaseThemeTable->theme_id = null;

						if ($showcaseThemeTable->store())
						{
							$showcaseTable->showcase_id 	= null;

							if (!$showcaseTable->store())
							{
								JError::raiseError( 500, $showcaseTable->getError() );
								return false;
							}
						}
					}

					$showcaseTable->reorder();
				}
			}
		}
		$msg = JText::_('Item(s) successfully copied');
		$mainframe->redirect('index.php?option=com_imageshow&controller=showcase', $msg);
	}

	function switchTheme()
	{
		global $mainframe;

		$session 	=& JFactory::getSession();
		$post 		=	JRequest::get('post');
		$session->set('showcaseThemeSession', $post);
		$currentRequest = str_replace('&task=switchtheme', '', $post['redirectLinkTheme']);
		$currentRequest = str_replace('&subtask', '&task', $currentRequest);

		$mainframe->redirect($currentRequest);
	}

	function refreshListThemes()
	{
		global $mainframe;

		$session 	=& JFactory::getSession();
		$post 		=	JRequest::get('post');
		$session->set('showcaseThemeSession', $post);
		$mainframe->redirect($post['redirectLinkTheme']);
	}

	function installShowcaseTheme()
	{
		$post   = JRequest::get('post');
		$model	= &$this->getModel('installer');
		$link 	= $post['redirect_link'];
		$result = $model->installPlugin();

		if ($result)
		{
			$link .= '&install=true';
		}

		$this->setRedirect($link);
	}

	function changeTheme()
	{
		global $mainframe, $objectLog;

		$cid 					= JRequest::getInt('showcase_id', 0);
		$showcaseTable 			=& JTable::getInstance('showcase', 'Table');
		$objJSNShowcaseTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');


		if (!$cid)
		{
			return false;
		}

		if ($showcaseTable->load($cid))
		{
			$themeProfile = $objJSNShowcaseTheme->getThemeProfile($showcaseTable->showcase_id);
			if ($themeProfile)
			{
				$objJSNShowcaseTheme->importTableByThemeName($themeProfile->theme_name);
				$showcaseThemeTable =& JTable::getInstance($themeProfile->theme_name.@$themeProfile->theme_style_name, 'Table');

				if ($showcaseThemeTable->load((int) $themeProfile->theme_id))
				{
					if ($showcaseThemeTable->delete((int) $themeProfile->theme_id))
					{
						$objJSNShowcaseTheme->deleteThemeProfileShowcaseID($cid);
					}
				}
			}
		}

		return false;
	}
}
?>