<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Finder.Content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.helper');
jimport('joomla.filesystem.file');

// Load the base adapter.
$finderLibFile = JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';
if (!JFile::exists($finderLibFile)) {
	return;
}
require_once $finderLibFile;


/**
 * Finder adapter for com_content.
 *
 * @package     Joomla.Plugin
 * @subpackage  Finder.Content
 * @since       2.5
 */
class plgFinderEasySocialUsers extends FinderIndexerAdapter
{
	/**
	 * The plugin identifier.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $context = 'EasySocial.Users';

	/**
	 * The extension name.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $extension = 'com_easysocial';

	/**
	 * The sublayout to use when rendering the results.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $layout = 'item';

	/**
	 * The type of content that the adapter indexes.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $type_title = 'EasySocial.Users';

	/**
	 * The table name.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $table = '#__users';

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since   2.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Delete a url from the cache
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFromCache($id)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('link_id') . ' FROM ' . $db->qn('#__finder_links');
		$query[] = 'WHERE ' . $db->qn('url') . ' LIKE ' . $db->Quote('%option=com_easysocial&view=profile&id=' . $id . '%');

		$query = implode(' ', $query);
		$db->setQuery($query);

		$item = $db->loadResult();

		if (FD::isJoomla30()) {
			$state = $this->indexer->remove($item);
		} else {
			$state = FinderIndexer::remove($item);
		}

		return $state;
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ($context == 'easysocial.users') {
			return $this->deleteFromCache($table->id);
		} elseif ($context == 'com_finder.index') {
			$id = $table->link_id;
		} else {
			return true;
		}

		// Remove the items.
		return $this->remove($id);
	}

	/**
	 * Method to determine if the access level of an item changed.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row      A JTable object
	 * @param   boolean  $isNew    If the content has just been created
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterSave($context, $row, $isNew)
	{
		// We only want to handle easysocial users
		if ($context == 'easysocial.users' && $row && $row->id && $row->state == 1 && $row->block == 0) {

			// Reindex the item
			$this->reindex($row->id);
		}

		// If user is blocked, we want to remove them from the cache.
		if ($context == 'easysocial.users' && $row && $row->id && $row->block) {
			return $this->deleteFromCache($row->id);
		}

		return true;
	}



	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param   FinderIndexerResult  $item    The item to index as an FinderIndexerResult object.
	 * @param   string               $format  The item format
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	protected function index(FinderIndexerResult $item, $format = 'html')
	{

		// Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}

		// load foundry config
		$config = FD::config();


		$privacy = FD::privacy( $item->user_id );

		//default access
		$access = 1;

		if ($config->get('users.indexer.privacy', 1)) {
			$privacyValue	= $privacy->getValue( 'profiles', 'search' );
			$item->privacy 	= $privacyValue;

			if ($item->privacy == SOCIAL_PRIVACY_PUBLIC) {
				$access = 1;
			} else if ($item->privacy == SOCIAL_PRIVACY_MEMBER) {
				$access = 2;
			} else {
				// this is not public / member items. let set the access to 'special'
				$access = 3;
			}
		}

		$user = FD::user( $item->user_id );

		// check if the user's profile has the community access or not. if not, set access = 3 so that only admin can search.
		if (! $user->hasCommunityAccess()) {
			$access = 3;
		}

		$userAlias 	= $user->getAlias( false );

		$contentSnapshot	= array();

		$userName	= $user->getName( $config->get( 'users.indexer.name' ) );
		$userEmail  = $config->get( 'users.indexer.email' ) ? $user->email : '';

		$contentSnapshot[] = $userName;
		if ($userEmail) {
			// we need to check for the email field privacy
			if ($config->get('users.indexer.privacy', 1)) {

				$privacyModel = FD::model('Privacy');
				$fieldPrivacyValue = $privacyModel->getFieldValue('field.joomla_email', $user->id);

				if( $fieldPrivacyValue == SOCIAL_PRIVACY_PUBLIC || $fieldPrivacyValue == SOCIAL_PRIVACY_MEMBER ) {
					$contentSnapshot[] = $userEmail;
				} else {
					$userEmail = '';
				}
			} else {
				$contentSnapshot[] = $userEmail;
			}
		}

		// get data from customfields
		// get customfields.
		// $fieldsLib		= FD::fields();
		// $fieldModel  	= FD::model( 'Fields' );
		// $fieldsResult 	= array();

		// $options = array();
		// $options['data'] 		= true;
		// $options['dataId'] 		= $user->user_id;
		// $options['dataType'] 	= SOCIAL_TYPE_USER;
		// $options['searchable'] 	= 1;

		// //todo: get customfields.
		// $fields = $fieldModel->getCustomFields( $options );

		// if( count( $fields ) > 0 )
		// {
		// 	//foreach( $fields as $item )
		// 	foreach( $fields as $field )
		// 	{
		// 		$userFieldData = isset( $field->data ) ? $field->data : '';

		// 		$args 			= array( $userFieldData );
		// 		$f 				= array( &$field );
		// 		$dataResult 	= @$fieldsLib->trigger( 'onIndexer' , SOCIAL_FIELDS_GROUP_USER , $f , $args );

		// 		if( $dataResult !== false && count( $dataResult ) > 0 )
		// 			$fieldsResult[]  	= $dataResult[0];
		// 	}

		// 	if( $fieldsResult )
		// 	{
		// 		$customFieldsContent 	= implode( ' ', $fieldsResult );
		// 		$contentSnapshot[] 		= $customFieldsContent;
		// 	}
		// }

		$content = implode( ' ', $contentSnapshot );


		// Build the necessary route and path information.
		// we need to pass in raw url so that if the site on sef, smart serach will not create new item.
		// index.php?option=com_easysocial&view=profile&id=84:jenny-siew
		// $item->url		= 'index.php?option=com_easysocial&view=profile&id=' . $userAlias;
		$item->url		= 'index.php?option=com_easysocial&view=profile&id=' . $user->id;

		$item->route 	= $user->getPermalink(true, false, false);
		$item->route 	= $this->removeAdminSegment($item->route);
		$item->path 	= FinderIndexerHelper::getContentPath($item->route);

		$userProfile 	= $user->getProfile();

		$metaKey = $userName;
		if ($userEmail) {
			$metaKey .= ', ' . $userEmail;
		}

		$item->title 		= $userName;
		$item->access		= $access;
		$item->alias		= $userAlias;
		$item->state		= 1;
		$item->start_date	= $user->registerDate;
		$item->created_by	= $item->user_id;
		$item->created_by_alias	= $userAlias;
		$item->modified		= $user->registerDate;
		$item->modified_by	= $item->user_id;
		$item->params		= '';
		$item->metakey		= $metaKey;
		$item->metadesc		= $content;
		$item->metadata		= '';
		$item->publish_start_date	= $user->registerDate;

		// let put user profile as category
		$item->catid		= $userProfile->id;
		$item->category		= $userProfile->getTitle();
		$item->cat_state	= 1;
		$item->cat_access	= 0;

		$item->summary          = $content;
		$item->body 			= $content;

		// Add the meta-author.
		$item->metaauthor 	= $userAlias;
		$item->author 		= $userAlias;

		// add image param
		$registry	= FD::registry();
		$registry->set( 'image' , $user->getAvatar() );

		$item->params = $registry;

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'EasySocial.Users');

		// Add the author taxonomy data.

		$item->addTaxonomy('Author', $userAlias );

		// Add the category taxonomy data.
		$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

		// Add the language taxonomy data.
		// $langParams 	= JComponentHelper::getParams('com_languages');
		// $item->language = $langParams->get( 'site', 'en-GB');
		//
		$item->language = '*';

		$item->addTaxonomy('Language', $item->language);

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		if( FD::isJoomla30() )
		{
			$this->indexer->index($item);
		}
		else
		{
			FinderIndexer::index( $item );
		}

	}

	private function removeAdminSegment( $url = '' )
	{
		if( $url )
		{
			$url 	= ltrim( $url , '/' );
			// $url 	= str_replace('administrator/index.php', 'index.php', $url );
			$url 	= str_replace('administrator/', '', $url );
		}

		return $url;
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 */
	protected function setup()
	{
		// Load dependent classes.
		require_once( JPATH_ROOT .  '/administrator/components/com_easysocial/includes/foundry.php' );
		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param   mixed  $sql  A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getListQuery($sql = null)
	{
		$db = JFactory::getDbo();
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);

		$sql->select( 'a.*');
        $sql->select('a.id AS ordering');
		$sql->select( 'b.user_id');
 		$sql->from('#__users AS a');
		$sql->join('INNER', '#__social_users AS b ON a.id = b.user_id');
		$sql->where( 'b.state = 1');
		$sql->where( 'a.block = 0');

		return $sql;
	}
}
