<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableCategory extends EasyBlogTable
{
	public $id = null;
	public $created_by = null;
	public $title = null;
	public $alias = null;
	public $avatar = null;
	public $parent_id = null;
	public $private = null;
	public $created = null;
	public $status = null;
	public $published = null;
	public $autopost = null;
	public $ordering = null;
	public $description = null;
	public $level = null;
	public $lft = null;
	public $rgt = null;
	public $default = null;
	public $language = null;
	public $params = null;

	/**
	 * The theme this category is assigned to.
	 * @var string
	 */
	public $theme 	= null;

	public function __construct(& $db )
	{
		parent::__construct('#__easyblog_category' , 'id' , $db );
	}


	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	public function delete($pk = null)
	{
		EB::loadLanguages(JPATH_ADMINISTRATOR);

		$config = EB::config();

		// If the table contains posts, do not allow them to delete the category.
		if ($this->getCount()) {
			$this->setError(JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_POST_NOT_EMPTY', $this->title));
			return false;
		}

		// If the table contains subcategories, do not allow them to delete the parent.
		if ($this->getChildCount()) {
			$this->setError(JText::sprintf('COM_EASYBLOG_CATEGORIES_DELETE_ERROR_CHILD_NOT_EMPTY', $this->title));
			return false;
		}

		// If the current user deleting this is the creator of the category, remove the points too.
		$my = JFactory::getUser();

		if ($this->created_by == $my->id) {
			EB::loadLanguages(JPATH_ROOT);

			// Integrations with EasyDiscuss.
			EB::easydiscuss()->log('easyblog.delete.category', $my->id, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_DELETE_CATEGORY', $this->title));
			EB::easydiscuss()->addPoint('easyblog.delete.category', $my->id);
			EB::easydiscuss()->addBadge('easyblog.delete.category', $my->id);

			// Integrations with EasySocial
			EB::easysocial()->assignPoints('category.remove', $this->created_by);

			// Integrations with JomSocial
			EB::jomsocial()->assignPoints('com_easyblog.category.remove', $this->created_by);

			// Integrations with AUP
			EB::aup()->assignPoints('plgaup_easyblog_delete_category', $this->created_by, JText::sprintf('COM_EASYBLOG_AUP_CATEGORY_DELETED', $this->title));
		}

		// Remove avatar if previously already uploaded.
		$this->removeAvatar();

		$state = parent::delete();

		return $state;
	}

	/**
	 * Removes a category avatar
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeAvatar()
	{
		if (empty($this->avatar)) {
			return false;
		}

		$config = EB::config();

		$path = $config->get('main_categoryavatarpath');
		$path = rtrim($path, '/');
		$path = JPATH_ROOT . '/' . $path;

		// Get the absolute path to the file.
		$file = $path . '/' . $this->avatar;
		$file = JPath::clean($file);

		if (JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	/**
	 * Determines if this category is the primary category for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPrimary()
	{
		if (!isset($this->primary)) {
			return false;
		}

		return $this->primary;
	}

	/**
	 * Determines if this category is the default category
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isDefault()
	{
		return $this->default;
	}

	/**
	 * Determines if this category is the public category
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isNotAssigned()
	{
		return $this->private == '0' || $this->private == '1';
	}

	/**
	 * Sets this category as the default category
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setDefault()
	{
		// Get the  model
		$model = EB::model('Category');

		// Remove all default categories
		$model->resetDefault();

		$this->default = true;

		return $this->store();
	}

	public function aliasExists()
	{
		static $_cache = array();
		$key = $this->alias . $this->id;


		$db		= $this->getDBO();

		if (! isset($_cache[$key])) {

			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_category' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $this->alias );

			if( $this->id != 0 )
			{
				$query	.= ' AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
			}
			$db->setQuery( $query );

			$_cache[$key] = $db->loadResult() > 0 ? true : false;

		}

		return $_cache[$key];
	}

	/**
	 * Overrides parent's bind method
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind( $data, $ignore = array() )
	{
		parent::bind( $data, $ignore );

		if (!$this->created) {
			$this->created = EB::date()->toSql();
		}

		// we check the alias only when the category is a new category or the alias is empty
		if (empty($this->id) || empty($this->alias)) {
			jimport( 'joomla.filesystem.filter.filteroutput');

			$i = 1;
			while ($this->aliasExists() || empty($this->alias)) {

				$this->alias = empty($this->alias) ? $this->title : $this->alias . '-' . $i;
				$this->alias = EBR::normalizePermalink($this->alias);
				$i++;
			}
		}
	}

	/**
	 * Retrieves rss link for the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRssLink()
	{
		return EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $this->id, false, 'category');
	}

	/**
	 * Retrieve a list of tags that is associated with this category
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultTags()
	{
		$params = new JRegistry($this->params);

		$tags = $params->get('tags');

		if (empty($tags)) {
			return array();
		}

		$tags = explode(',', $tags);

		return $tags;
	}

	/**
	 * Retrieve rss link for the category
	 *
	 * @deprecated	4.0
	 */
	public function getRSS()
	{
		return $this->getRssLink();
	}

	public function getAtom()
	{
		return EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $this->id, true, 'category');
	}

	/**
	 * Retrieve categories avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @return	string	The location to the avatar
	 */
	public function getAvatar()
	{
		$defaults 	= array('cdefault.png', 'default_category.png', 'components/com_easyblog/assets/images/default_category.png', 'components/com_easyblog/assets/images/cdefault.png');
		$link 		= 'components/com_easyblog/assets/images/default_category.png';

		if (!in_array($this->avatar, $defaults) && !empty($this->avatar)) {

			$link 	= EB::image()->getAvatarRelativePath('category') . '/' . $this->avatar;
		}

		return rtrim(JURI::root(), '/') . '/' . $link;
	}

	/**
	 * Retrieves the total number of posts in this category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	int
	 */
	public function getCount($bloggerId = '')
	{
		static $counts = array();

		$options = array();

		if ($bloggerId) {
			$options['bloggerId'] = $bloggerId;
		}

		if (!isset($counts[$this->id])) {
			$model = EB::model('Category');
			$counts[$this->id] = $model->getTotalPostCount($this->id, $options);
		}

		return $counts[$this->id];
	}

	/**
	 * Use getCount instead
	 *
	 * @deprecated	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostCount()
	{
		return $this->getCount();
	}

	/**
	 * Retrieves the total number of subcategories this category has.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getChildCount()
	{
		$db = EB::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_category' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Retrieve a list of active authors for this category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getActiveAuthors()
	{
		static $result = array();

		if (!isset($result[$this->id])) {

			//check if the active author already cached or not. if yes,
			//let retrieve those
			if (EB::cache()->exists($this->id, 'cats')) {
				$data = EB::cache()->get($this->id, 'cats');

				if (isset($data['author'])) {
					$result[$this->id] = $data['author'];
				} else {
					$result[$this->id] = array();
				}
			} else {
				$model = EB::model('Category');
				$result[$this->id] = $model->getActiveAuthors($this->id);
			}
		}

		return $result[$this->id];
	}

	/**
	 * Deprecated. Use @getActiveAuthors() instead
	 *
	 * @deprecated 4.0
	 */
	public function getActiveBloggers()
	{
		return $this->getActiveAuthors();
	}

	/**
	 * Override parent's implementation of store
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($updateNulls = false)
	{
		if (!$this->created) {
			$this->created = EB::date()->toSql();
		}

		// Generate an alias if alias is empty
		if (!$this->alias) {
			$this->alias = EBR::normalizePermalink($this->title);
		}

	    $my = JFactory::getUser();

	    // Add point integrations for new categories
	    if ($this->id == 0 && $my->id > 0) {

	    	EB::loadLanguages();

			// Integrations with EasyDiscuss
			EB::easydiscuss()->log( 'easyblog.new.category' , $my->id , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_CATEGORY' , $this->title ) );
			EB::easydiscuss()->addPoint( 'easyblog.new.category' , $my->id );
			EB::easydiscuss()->addBadge( 'easyblog.new.category' , $my->id );

			// AlphaUserPoints
			EB::aup()->assign('plgaup_easyblog_add_category', '', 'easyblog_add_category_' . $this->id, JText::sprintf('COM_EASYBLOG_AUP_NEW_CATEGORY_CREATED', $this->title));

			// JomSocial integrations
			EB::jomsocial()->assignPoints('com_easyblog.category.add', $my->id);

			// Assign EasySocial points
			EB::easysocial()->assignPoints('category.create', $my->id);
	    }

		// Figure out the proper nested set model
		if ($this->id == 0 && $this->lft == 0) {

			// No parent id, we use the current lft,rgt
			if ($this->parent_id) {
				$left = $this->getLeft( $this->parent_id );
				$this->lft = $left;
				$this->rgt = $this->lft + 1;

				// Update parent's right
				$this->updateRight($left);
				$this->updateLeft($left);
			} else {
				$this->lft = $this->getLeft() + 1;
				$this->rgt = $this->lft + 1;
			}
		}

		if ($this->id == 0) {
			// new cats. we need to store the ordering.
			$this->ordering = $this->getOrdering($this->parent_id) + 1;
		}

		$isNew 	= !$this->id ? true : false;
		$state 	= parent::store();

	    return $state;
	}

	public function saveACL( $post )
	{

		$catRuleItems	= EB::table('CategoryAclItem');
		$categoryRules  = $catRuleItems->getAllRuleItems();

		foreach( $categoryRules as $rule)
		{
			$key    = 'category_acl_'.$rule->action;
			if( isset( $post[ $key ] ) )
			{
				if( count( $post[ $key ] ) > 0)
				{
					foreach( $post[ $key ] as $joomla)
					{
						//now we reinsert again.
						$catRule	= EB::table('CategoryAcl');
						$catRule->category_id	= $this->id;
						$catRule->acl_id 		= $rule->id;
						$catRule->type 			= 'group';
						$catRule->content_id 	= $joomla;
						$catRule->status 		= '1';
						$catRule->store();
					} //end foreach

				} //end if
			}//end if
		}
	}

	public function deleteACL( $aclId = '' )
	{
		$db = EasyBlogHelper::db();

		$query  = 'delete from `#__easyblog_category_acl`';
		$query	.= ' where `category_id` = ' . $db->Quote( $this->id );
		if( !empty($aclId) )
			$query	.= ' and `acl_id` = ' . $db->Quote( $aclId );

		$db->setQuery( $query );
		$db->query();

		return true;
	}

	public function getAssignedACL()
	{
		$db = EasyBlogHelper::db();

		$acl    = array();

		$query  = 'SELECT a.`category_id`, a.`content_id`, a.`status`, b.`id` as `acl_id`';
		$query  .= ' FROM `#__easyblog_category_acl` as a';
		$query  .= ' LEFT JOIN `#__easyblog_category_acl_item` as b';
		$query  .= ' ON a.`acl_id` = b.`id`';
		$query  .= ' WHERE a.`category_id` = ' . $db->Quote( $this->id );
		$query  .= ' AND a.`type` = ' . $db->Quote( 'group' );

		//echo $query;

		$db->setQuery( $query );
		$result = $db->loadObjectList();


		$joomlaGroups    = EasyBlogHelper::getJoomlaUserGroups();

		if( EasyBlogHelper::getJoomlaVersion() < '1.6' )
		{
			$guest  = new stdClass();
			$guest->id		= '0';
			$guest->name	= 'Public';
			$guest->level	= '0';
			array_unshift($joomlaGroups, $guest);
		}

		$acl             = $this->_mapRules($result, $joomlaGroups);

		return $acl;

	}

	public function _mapRules( $catRules, $joomlaGroups)
	{
		$db 	= EasyBlogHelper::db();
		$acl    = array();

		$query  = 'select * from `#__easyblog_category_acl_item` order by id';
		$db->setQuery( $query );

		$result = $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $item )
		{
			$aclId 		= $item->id;
			$default    = $item->default;

			foreach( $joomlaGroups as $joomla )
			{
				$groupId    	= $joomla->id;
				$catRulesCnt    = count($catRules);

				if ( empty($acl[$aclId][$groupId]) )
				{
					$acl[$aclId][$groupId] = new stdClass();
				}

				//now match each of the catRules
				if( $catRulesCnt > 0)
				{
					$cnt    = 0;
					foreach( $catRules as $rule)
					{
						if($rule->acl_id == $aclId && $rule->content_id == $groupId)
						{
							$acl[$aclId][$groupId]->status  	= $rule->status;
							$acl[$aclId][$groupId]->acl_id  	= $aclId;
							$acl[$aclId][$groupId]->groupname	= $joomla->name;
							$acl[$aclId][$groupId]->groupid		= $groupId;
							break;
						}
						else
						{
							$cnt++;
						}
					}

					if( $cnt == $catRulesCnt)
					{
						//this means the rules not exist in this joomla group.
						$acl[$aclId][$groupId]->status  	= '0';
						$acl[$aclId][$groupId]->acl_id  	= $aclId;
						$acl[$aclId][$groupId]->groupname	= $joomla->name;
						$acl[$aclId][$groupId]->groupid		= $groupId;
					}
				}
				else
				{
					$acl[$aclId][$groupId]->status  	= $default;
					$acl[$aclId][$groupId]->acl_id  	= $aclId;
					$acl[$aclId][$groupId]->groupname	= $joomla->name;
					$acl[$aclId][$groupId]->groupid		= $groupId;
				}
			}
		}

		return $acl;
	}

	public function checkPrivacy()
	{
		$obj 			= new stdClass();
		$obj->allowed 	= true;
		$obj->message 	= '';

		$my 			= JFactory::getUser();

		if( $this->private == '1' && $my->id == 0)
		{
			$obj->allowed	= false;
			$obj->error		= EB::privacy()->getErrorHTML();
		}
		else
		{
			if( $this->private == '2')
			{
				$cats    = EasyBlogHelper::getPrivateCategories();

				if( in_array($this->id, $cats) )
				{
					$obj->allowed	= false;
					$obj->error		= JText::_( 'COM_EASYBLOG_PRIVACY_NOT_AUTHORIZED_ERROR' );
				}

			}
		}

		return $obj;
	}

	// category ordering with lft and rgt
	public function updateLeft( $left, $limit = 0 )
	{
		$db     = EasyBlogHelper::db();
		$query  = 'UPDATE ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . $db->nameQuote( 'lft' ) . '=' . $db->nameQuote( 'lft' ) . ' + 2 '
				. 'WHERE ' . $db->nameQuote( 'lft' ) . '>=' . $db->Quote( $left );

		if( !empty( $limit ) )
			$query  .= ' and `lft`  < ' . $db->Quote( $limit );

		$db->setQuery( $query );
		$db->Query();
	}

	public function updateRight( $right, $limit = 0 )
	{
		$db     = EasyBlogHelper::db();
		$query  = 'UPDATE ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . $db->nameQuote( 'rgt' ) . '=' . $db->nameQuote( 'rgt' ) . ' + 2 '
				. 'WHERE ' . $db->nameQuote( 'rgt' ) . '>=' . $db->Quote( $right );

		if( !empty( $limit ) )
			$query  .= ' and `rgt`  < ' . $db->Quote( $limit );

		$db->setQuery( $query );
		$db->Query();
	}

	public function getLeft( $parent = 0 )
	{
		$db     = EasyBlogHelper::db();

		if( $parent != 0 )
		{
			$query  = 'SELECT `rgt`' . ' '
					. 'FROM ' . $db->nameQuote( $this->_tbl ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $parent );
		}
		else
		{
			$query  = 'SELECT MAX(' . $db->nameQuote( 'rgt' ) . ') '
					. 'FROM ' . $db->nameQuote( $this->_tbl );
		}
		$db->setQuery( $query );

		$left   = (int) $db->loadResult();

		return $left;
	}

	public function getOrdering($parent = 0)
	{
		$db     = EB::db();
		$query = "select max(ordering)";
		$query .= " from `#__easyblog_category`";
		if ($parent) {
			$query .= " where (`id` = " . $db->Quote($parent) . " or `parent_id` = " . $db->Quote($parent) . ")";
		}

		$db->setQuery( $query );
		$maxordering   = (int) $db->loadResult();

		return $maxordering;
	}


	function move( $direction, $where = '' )
	{
		$db = EasyBlogHelper::db();

		if( $direction == -1) //moving up
		{
			// getting prev parent
			$query  = 'select `id`, `lft`, `rgt` from `#__easyblog_category` where `lft` < ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft desc limit 1';

			//echo $query;exit;
			$db->setQuery($query);
			$preParent  = $db->loadObject();

			// calculating new lft
			$newLft = $this->lft - $preParent->lft;
			$preLft = ( ($this->rgt - $newLft) + 1) - $preParent->lft;

			//get prevParent's id and all its child ids
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($preParent->lft) . ' and rgt <= ' . $db->Quote($preParent->rgt);
			$db->setQuery($query);

			// echo '<br>' . $query;
			$preItemChilds = $db->loadResultArray();
			$preChildIds   = implode(',', $preItemChilds);
			$preChildCnt   = count($preItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$itemChilds = $db->loadResultArray();

			$childIds   = implode(',', $itemChilds);
			$ChildCnt   = count($itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.
			//update current parent block
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' lft = lft - ' . $db->Quote($newLft);
			if( $ChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($newLft);
			}
			$query  .= ' where `id` in (' . $childIds . ')';

			//echo '<br>' . $query;
			$db->setQuery($query);
			$db->query();

			$query  = 'update `#__easyblog_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($preLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($preLft);
			$query  .= ' where `id` in (' . $preChildIds . ')';

			//echo '<br>' . $query;
			//exit;
			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` - 1';
			$query  .= ' where `id` = ' . $db->Quote($this->id);
			$db->setQuery($query);
			$db->query();

			//now update the previous parent's ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($preParent->id);
			$db->setQuery($query);
			$db->query();

			return true;
		}
		else //moving down
		{
			// getting next parent
			$query  = 'select `id`, `lft`, `rgt` from `#__easyblog_category` where `lft` > ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft asc limit 1';

			$db->setQuery($query);
			$nextParent  = $db->loadObject();


			$nextLft 	= $nextParent->lft - $this->lft;
			$newLft 	= ( ($nextParent->rgt - $nextLft) + 1) - $this->lft;


			//get nextParent's id and all its child ids
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($nextParent->lft) . ' and rgt <= ' . $db->Quote($nextParent->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$nextItemChilds = $db->loadResultArray();
			$nextChildIds   = implode(',', $nextItemChilds);
			$nextChildCnt   = count($nextItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__easyblog_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$itemChilds = $db->loadResultArray();
			$childIds   = implode(',', $itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.

			//update next parent block
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `lft` = `lft` - ' . $db->Quote($nextLft);
			if( $nextChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($nextLft);
			}
			$query  .= ' where `id` in (' . $nextChildIds . ')';

			//echo '<br>' . $query;
			$db->setQuery($query);
			$db->query();

			//update current parent
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($newLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($newLft);
			$query  .= ' where `id` in (' . $childIds. ')';

			//echo '<br>' . $query;
			//exit;

			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($this->id);

			//echo '<br>' . $query;

			$db->setQuery($query);
			$db->query();

			//now update the previous parent's ordering.
			$query  = 'update `#__easyblog_category` set';
			$query  .= ' `ordering` = `ordering` - 1';
			$query  .= ' where `id` = ' . $db->Quote($nextParent->id);

			//echo '<br>' . $query;

			$db->setQuery($query);
			$db->query();

			return true;
		}
	}

	public function rebuildOrdering($parentId = null, $leftId = 0 )
	{
		$db = EasyBlogHelper::db();

		$query  = 'select `id` from `#__easyblog_category`';
		$query  .= ' where parent_id = ' . $db->Quote( $parentId );
		$query  .= ' order by lft';

		$db->setQuery( $query );
		$children = $db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuildOrdering($node->id, $rightId );

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false) return false;
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$updateQuery    = 'update `#__easyblog_category` set';
		$updateQuery    .= ' `lft` = ' . $db->Quote( $leftId );
		$updateQuery    .= ', `rgt` = ' . $db->Quote( $rightId );
		$updateQuery    .= ' where `id` = ' . $db->Quote($parentId);

		$db->setQuery($updateQuery);

		// If there is an update failure, return false to break out of the recursion.
		if (! $db->query())
		{
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	public function updateOrdering()
	{
		$db = EasyBlogHelper::db();

		$query  = 'select `id` from `#__easyblog_category`';
		$query  .= ' order by lft';

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if( count( $rows ) > 0 )
		{
			$orderNum = '1';

			foreach( $rows as $row )
			{
				$query  = 'update `#__easyblog_category` set';
				$query  .= ' `ordering` = ' . $db->Quote( $orderNum );
				$query  .= ' where `id` = ' . $db->Quote( $row->id );

				$db->setQuery( $query );
				$db->query();

				$orderNum++;
			}
		}

		return true;
	}

	/**
	 * Determines if there are any fields binded to the category
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasCustomFields()
	{
		$fields = $this->getCustomFields();

		if ($fields === false) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves custom fields for this category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCustomFields()
	{
		static $loaded = array();

		if (!isset($loaded[$this->id])) {

			$group = null;
			$fields = null;

			if (EB::cache()->exists($this->id, 'categories')) {
				$cachedCategory = EB::cache()->get($this->id, 'categories');

				$group = $cachedCategory['group'];
				$fields = $cachedCategory['field'];

			} else {
				$model = EB::model('Categories');

				$group = $model->getCustomFieldGroup($this->id);
				$fields = $model->getCustomFields($this->id);
			}

			if (!$group->id && !$fields) {
				$obj = false;
			} else {
				$obj = new stdClass();
				$obj->group  = $group;
				$obj->fields = $fields;
			}

			$loaded[$this->id] = $obj;
		}

		return $loaded[$this->id];
	}

	/**
	 * Retrieves the custom field group for this category
	 *
	 * @since	4.0
	 * @access	public
	 * @return	mixed 	false if category is not associated with the group
	 */
	public function getCustomFieldGroup()
	{
		static $loaded = false;

		if (!isset($loaded[$this->id])) {
			$table 	= EB::table('CategoryFieldGroup');
			$state 	= $table->load(array('category_id' => $this->id));

			$loaded[$this->id] = $table;
		}


		return $loaded[$this->id];
	}

	/**
	 * Bind custom field group to the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFieldGroup()
	{
		// Delete existing mapping first
		$model = EB::model('Category');
		$state = $model->deleteExistingFieldMapping($this->id);

		return $state;
	}

	/**
	 * Bind custom field group to the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bindCustomFieldGroup($groupId = '')
	{
		if (!$groupId) {
			return false;
		}

		// Delete existing mapping first
		$model = EB::model('Category');
		$model->deleteExistingFieldMapping($this->id);

		// Create a new mapping
		$table 	= EB::table('CategoryFieldGroup');
		$table->category_id = $this->id;
		$table->group_id = $groupId;
		$state = $table->store();

		if (!$state) {
			$this->setError($table->getError());
		}

		return $state;
	}

	/**
	 * Retrieves the parameters for the menu
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenuParams()
	{

	}

	/**
	 * Retrieve a specific parameter value
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParam($key, $default = null)
	{
		static $params = array();

		if (!isset($params[$this->id])) {
			$params[$this->id] = $this->getParams();
		}

		$val = $params[$this->id]->get($key);

		$prefix = 'layout_';

		if ($val == '-1') {
			$config = EB::config();
			$val = $config->get($prefix . $key, $default);
		}

		// If the value is still null, probably not set in the category
		if ($val === null) {
			$val = $default;
		}

		return $val;
	}

	/**
	 * Retrieves the external permalink for this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExternalPermalink($format = null)
	{
		$link = EBR::getRoutedURL('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $this->id, false, true, true);

		$link = EBR::appendFormatToQueryString($link, $format);

		return $link;
	}

	/**
	 * Retrieves alias for the category
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		$config = EB::config();
		$alias = $this->alias;

		if ($config->get('main_sef_unicode') || !EBR::isSefEnabled()) {
			$alias = $this->id . ':' . $this->alias;
		}

		return $alias;
	}

	/**
	 * Retrieves the permalink for this category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = true)
	{
		$url = EB::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $this->id, $xhtml);

		return $url;
	}

	public function getMetaId()
	{
		$db = $this->_db;

		$query  = 'SELECT a.`id` FROM `#__easyblog_meta` AS a';
		$query  .= ' WHERE a.`content_id` = ' . $db->Quote($this->id);
		$query  .= ' AND a.`type` = ' . $db->Quote( META_TYPE_CATEGORY );

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public function createMeta()
	{
		$id		= $this->getMetaId();

		// @rule: Save meta tags for this entry.
		$meta		= EB::table('Meta');
		$meta->load( $id );

		$meta->set( 'keywords'		, '' );

		if( !$meta->description )
		{
			$meta->description 	= strip_tags( $this->description );
		}

		$meta->set( 'content_id'	, $this->id );
		$meta->set( 'type'			, META_TYPE_CATEGORY );
		$meta->store();
	}

	/**
	 * Retrieve a list of tags that is associated with this category
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultParams()
	{
		static $_cache = null;

		if (! $_cache) {

			// $manifest = JPATH_ROOT . '/components/com_easyblog/views/entry/tmpl/default.xml';
			$manifest = JPATH_ROOT . '/components/com_easyblog/views/categories/tmpl/listings.xml';
			$fieldsets = EB::form()->getManifest($manifest);

			$obj = new stdClass();

			foreach($fieldsets as $fieldset) {
				foreach($fieldset->fields as $field) {
					$obj->{$field->attributes->name} = $field->attributes->default;
				}
			}

			$_cache = new JRegistry($obj);
		}

		return $_cache;
	}


}
