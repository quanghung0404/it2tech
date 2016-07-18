<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');


class AlbumsController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}

	function update_tables(){
		//echo "test";die;
		$model = $this->getModel('albums');
		$model->update_tables();
		
		$this->setRedirect( 'index.php?option=com_muscol' );
	}
}
class ArtistsController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}
class FormatsController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}
class TagsController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}
class TypesController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}
class GenresController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}
class SongsController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}
class CommentsController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}
class RatingsController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}
class ElementController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}
class PlaylistsController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}
}