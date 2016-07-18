<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewMigrators extends EasyBlogAdminView
{
	var $err = null;

	public function migrateArticle()
	{
		$component = $this->input->get('component', '', 'string');

		if (!$component) {
			die('Invalid migration');
		}

		if ($component == 'xml_wordpress') {
	        $fileName = $this->input->get('xmlFile', '', 'string');
	    	$authorId = $this->input->get('authorId', '', 'int');

	    	$migrator = EB::migrator()->getAdapter('wordpress_xml');
	        $migrator->migrate($fileName, $authorId);

	        return;
		}


		switch($component)
		{
		    case 'com_blog':

				$migrateComment	= $this->input->get('migrateComment', 0, 'int');
				$migrateImage	= $this->input->get('migrateImage', 0, 'int');
				$imagePath		= $this->input->get('imagepath', '', 'string');

				$migrator = EB::migrator()->getAdapter('smartblog');

				$migrator->migrate($migrateComment, $migrateImage, $imagePath);

		        break;
		    case 'com_content':

		    	$authorId 	= $this->input->get('authorId', '', 'int');
				$catId = $this->input->get('categoryId', 0, 'int');
				$stateId = $this->input->get('state', '', 'int');
				$ebcategory = $this->input->get('ebcategory', 0, 'int');
				$myblogSection = $this->input->get('myblog', '', 'int');
				$jomcomment = $this->input->get('migrateComment', 0, 'int');
				$start		= 1;
				$sectionId	= '';

				$migrator = EB::migrator()->getAdapter('content');

				$migrator->migrate($authorId, $stateId, $catId, $sectionId, $ebcategory, $myblogSection , $jomcomment);

		        break;
		    case 'com_lyftenbloggie':
		    	//migrate lyftenbloggie tags
		    	$migrateComment	= isset($post['lyften_comment']) ? $post['lyften_comment'] : '0';

				$this->_migrateLyftenTags();
		        $this->_processLyftenBloggie( $migrateComment );
		        break;
		    case 'com_wordpress':

				$wpBlogId	= $this->input->get('blogId', '', 'int');

				$migrator = EB::migrator()->getAdapter('wordpress');
		        $migrator->migrate( $wpBlogId );
		        break;

		    case 'xml_blogger':
		    	$fileName 	= $this->input->get('xmlFile', '', 'string');
		    	$authorId 	= $this->input->get('authorId', '', 'int');
		    	$categoryId 	= $this->input->get('categoryId', '', 'int');

		    	$migrator = EB::migrator()->getAdapter('blogger_xml');

		    	$migrator->migrate( $fileName, $authorId, $categoryId );
				break;

			case 'com_k2':
		    	$migrateComment	= $this->input->get('migrateComment', '', 'bool');
		    	$migrateAll		= $this->input->get('migrateAll', '', 'bool');
		    	$catId	= $this->input->get('categoryId', 0, 'int');

		    	$migrator = EB::migrator()->getAdapter('k2');
		    	$migrator->migrate($migrateComment, $migrateAll, $catId);

				break;

			case 'com_zoo':
		    	$applicationId 	= $this->input->get('applicationId', '', 'int');

		    	$migrator = EB::migrator()->getAdapter('zoo');
		    	$migrator->migrate($applicationId);
				break;

		    default:
		        break;
		}
	}


	function getXMLAttachmentData( $fileName, $postid )
	{
	    $jSession 	= JFactory::getSession();
	    $db         = EasyBlogHelper::db();

	    $sessId     = $jSession->getToken();

	    $query  = 'select * from `#__easyblog_xml_wpdata`';
	    $query  .= ' where `session_id` = ' . $db->Quote( $sessId );
	    $query  .= ' and `filename` = ' . $db->Quote($fileName);
	    $query  .= ' and `source` = ' . $db->Quote('attachment');
	    $query  .= ' and `post_id` = ' . $db->Quote($postid);

	    $db->setQuery($query);

	    $result = $db->loadObjectList();

	    $attachments    = array();

		if ( count( $result ) > 0 ) {
		    foreach( $result as $att) {
		        $attachments[]  = unserialize( $att->data );
		    }
		}

	    return $attachments;
	}

	function _processWPXMLAttachment( $wpPostId, $content, $attachments, $authorId)
	{
	    require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'connectors.php' );

		foreach( $attachments as $attachment)
		{
			$link    		= $attachment['link'];
			$attachementURL = $attachment['attachment_url'];

			if (EasyImageHelper::isImage($attachementURL)) {
			    $filname    = EasyImageHelper::getFileName($attachementURL);
			    $extension  = EasyImageHelper::getFileExtension($attachementURL);

			    $folder   = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'blogs' . DIRECTORY_SEPARATOR . $wpPostId;
			    if(!JFolder::exists($folder)) {
			    	JFolder::create( $folder );
				}

				// new image location
				$newFile    = $folder . DIRECTORY_SEPARATOR . $filname;

			    $connector  = EB::connector();
				$connector->addUrl( $attachementURL );
				$connector->execute();
			    $imageraw	= $connector->getResult( $attachementURL );

			    if ($imageraw) {
			        if (JFile::write($newFile, $imageraw)) {
					    //replace the string in the content.
					    $absImagePath   = rtrim( JURI::root(), '/' ) . '/images/blogs/' . $wpPostId . '/' . $filname;
					    $content		= str_ireplace( 'href="' . $link . '"'  , 'href="' . $absImagePath . '"' , $content );

					    $pattern 		= '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';
					    $content		= preg_replace( $pattern  , 'src="'.$absImagePath.'"' , $content );
			        }
				}

// 				if( file_put_contents( $newFile, file_get_contents($attachementURL) ) !== false )
// 				{
// 				    //replace the string in the content.
// 				    $absImagePath   = rtrim( JURI::root(), '/' ) . '/images/blogs/' . $wpPostId . '/' . $filname;
// 				    $content		= JString::str_ireplace( 'href="' . $link . '"'  , 'href="' . $absImagePath . '"' , $content );
//
// 				    $pattern 		= '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';
// 				    $content		= preg_replace( $pattern  , 'src="'.$absImagePath.'"' , $content );
// 				}
			}
		}

		return $content;
	}

	function _processLyftenBloggie( $migrateComment )
	{
	    $db			= EasyBlogHelper::db();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrator	= EB::migrator()->getAdapter('k2');

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->comments	= 0;
			$migrateStat->images	= 0;
			$migrateStat->user      = array();
		}

		$query	= 'SELECT * FROM `#__bloggies_entries` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_lyftenbloggie');
		$query	.= ' )';
		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{
		    // now we migrate the remaining categories
     		$this->_migrateLyftenCategories();

			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status3', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ejax->script("divSrolltoBottomLyften();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOGS') . ': ' . $migrateStat->blog . '<br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_COMMENTS') . ': ' . $migrateStat->comments . '<br />';
			//$stat  .= 'Total images migrated : ' . $migrateStat->images . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_USERS_CONTRIBUTIONS') . ': ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOG_USER') . ' \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status3', $stat);

			$ejax->script("$( '#migrator-submit3' ).html('" . JText::_( 'COM_EASYBLOG_MIGRATOR_MIGRATION_COMPLETED' ) . "');");
			$ejax->script("$( '#migrator-submit3' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait3' ).css( 'display' , 'none' );");

		}
		else
		{
			// here we should process the migration
			// step 1 : create user if not exists in eblog_users - create user through profile jtable load method.
			// step 2: create categories / tags if needed.
			// step 3: migrate comments if needed.

			$date           = EB::date();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			if(! empty($row->catid))
			{

			    $joomlaCat  = $this->_getLyftenCategory($row->catid);

			    $eCat   	= $this->_isEblogCategoryExists($joomlaCat);
				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($joomlaCat);
				}

				$blogObj->category_id   = $eCat;
			}

			//load user profile
			$profile	= EB::user($row->created_by);

			$blog		= EB::table('Blog');

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->created ) ? $row->created : $date->toMySQL();
			$blogObj->modified		= !empty( $row->modified ) ? $row->modified : $date->toMySQL();

			$blogObj->title			= $row->title;
			$blogObj->permalink		= EasyBlogHelper::getPermalink( $row->title );

			if(empty($row->fulltext))
			{
				$blogObj->intro			= '';
				$blogObj->content		= $row->introtext;
			}
			else
			{
				$blogObj->intro			= $row->introtext;
				$blogObj->content		= $row->fulltext;
			}


			$blogObj->published		= ($row->state == '1') ? '1' : '0'; // set to unpublish for now.
			$blogObj->publish_up 	= !empty( $row->created ) ? $row->created : $date->toMySQL();
			$blogObj->publish_down	= '0000-00-00 00:00:00';

			$blogObj->hits			= $row->hits;
			$blogObj->frontpage     = 1;
			$blogObj->allowcomment  = 1;
			$blogObj->subscription  = 1;

			$blog->bind($blogObj);
			$blog->store();

			//add meta description
			$migrator->migrateContentMeta($row->metakey, $row->metadesc, $blog->id);


			//step 2: tags
			$query  = 'insert into `#__easyblog_post_tag` (`tag_id`, `post_id`, `created`)';
			$query  .= ' select a.`id`, ' . $db->Quote($blog->id) . ', ' . $db->Quote($date->toMySQL());
			$query  .= ' from `#__easyblog_tag` as a inner join `#__bloggies_tags` as b';
			$query  .= ' on a.`title` = b.`name`';
			$query  .= ' inner join `#__bloggies_relations` as c on b.`id` = c.`tag`';
			$query  .= ' where c.`entry` = ' . $db->Quote($row->id);

			$db->setQuery($query);
			$db->query();


			// migrate Jcomments from lyftenbloggie into EasyBlog
			// $this->_migrateJCommentIntoEasyBlog($row->id, $blog->id, 'com_lyftenbloggie');
			// step 3
			if($migrateComment)
			{

			    //required frontend model file.
			    require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'comment.php');
				$model	= new EasyBlogModelComment();

				$queryComment  = 'SELECT * FROM `#__bloggies_comments` WHERE `entry_id` = ' . $db->Quote($row->id);
				$queryComment  .= ' ORDER BY `id`';
				$db->setQuery($queryComment);
				$resultComment  = $db->loadObjectList();


				if(count($resultComment) > 0)
				{

					$lft    = 1;
					$rgt    = 2;

				    foreach($resultComment as $itemComment)
				    {
	    				$now	= EB::date();
						$commt	= EB::table('Comment');


						$commt->post_id      = $blog->id;
						$commt->comment      = $itemComment->content;
						$commt->title        = '';

						$commt->name         = $itemComment->author;
						$commt->email        = $itemComment->author_email;
						$commt->url          = $itemComment->author_url;
						$commt->created_by   = $itemComment->user_id;
						$commt->created      = $itemComment->date;
						$commt->published    = ($itemComment->state == '1') ? '1' : '0';

						$commt->lft          = $lft;
						$commt->rgt          = $rgt;

						$commt->store();

						//update state
						$migrateStat->comments++;

					    // next set of siblings
					    $lft    = $rgt + 1;
					    $rgt    = $lft + 1;

				    }//end foreach

				}//end if count(comment)

			}


			//update session value
			$migrateStat->blog++;
			$statUser   	= $migrateStat->user;
			$statUserObj    = null;
			if(! isset($statUser[$profile->id]))
			{
			    $statUserObj    = new stdClass();
			    $statUserObj->name  		= $profile->nickname;
			    $statUserObj->blogcount		= 0;
			}
			else
			{
			    $statUserObj    = $statUser[$profile->id];
			}
			$statUserObj->blogcount++;
			$statUser[$profile->id] = $statUserObj;
			$migrateStat->user  	= $statUser;


			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');


			//log the entry into migrate table.
			$migrator = EB::table('Migrate');

			$migrator->content_id	= $row->id;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'com_lyftenbloggie';
			$migrator->store();

			$ejax->append('progress-status3', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_LYFTEN') . ':' . $row->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_processLyftenBloggie', '$migrateComment');");

		}//end if else isnull

		$ejax->send();
	}

	function _getLyftenCategory($catId)
	{
	    $db = EasyBlogHelper::db();

	    $query  = 'select *, slug as `alias` from `#__bloggies_categories` where `id` = ' . $db->Quote($catId);
	    $db->setQuery($query);

	    $result = $db->loadObject();
	    $result->alias  = JFilterOutput::stringURLSafe( trim( $result->slug ) );

	    return $result;
	}

	function _migrateLyftenTags()
	{
	    //this will plot all lyften bloggie tags into easyblog's tags
	    // no relations created for each blog vs tag

	    $db 	= EasyBlogHelper::db();
	    $suId   = $this->_getSAUserId();
	    $now	= EB::date();

	    $query  = 'insert into `#__easyblog_tag` (`created_by`, `title`, `alias`, `created`, `published`)';
		$query  .= ' select ' . $db->Quote($suId) . ', `name`, `slug`, '. $db->Quote($now->toMySQL()).', ' . $db->Quote('1');
		$query  .= ' from `#__bloggies_tags`';
		$query  .= ' where `name` not in (select `title` from `#__easyblog_tag`)';

		$db->setQuery($query);
		$db->query();

		return true;
	}

	function _migrateLyftenCategories()
	{
		$jSession 		= JFactory::getSession();
		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

	    $db 	= EasyBlogHelper::db();
	    $suId   = $this->_getSAUserId();
	    $now	= EB::date();

		$query  = ' select `title`, `slug`, `published`';
		$query  .= ' from `#__bloggies_categories`';
		$query  .= ' where `title` != \'\' and `title` not in (select `title` from `#__easyblog_category`)';

		$db->setQuery($query);
		$results    = $db->loadObjectList();

		$suId       = $this->_getSAUserId();

		for($i = 0; $i < count($results); $i++)
		{
		    $catObj     = $results[$i];

		    $category	= EB::table('Category');

		    $arr    = array();
		    $arr['created_by']  = $suId;
		    $arr['title']  		= $catObj->title;
		    $arr['alias']  		= JFilterOutput::stringURLSafe(trim($catObj->slug));
		    $arr['published']  	= $catObj->published;

		    $category->bind($arr);
		    $category->store();

		    //update session value
		    $migrateStat->category++;

		}

		if(count($results) > 0)
		{
			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');
		}

		return true;
	}

}

