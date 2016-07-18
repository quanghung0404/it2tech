<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.utilities.simplexml' );
jimport( 'joomla.utilities.arrayhelper' );

require_once( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php' );

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
//require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewMigrators extends EasyBlogAdminView
{
	var $err				= null;

	function migrateArticle($params)
	{

		$post	= EasyBlogStringHelper::ejaxPostToArray($params);

		if(isset($post['com_type']))
		{

			$migrateStat    = new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->comments	= 0;
			$migrateStat->images	= 0;
			$migrateStat->user      = array();

			$jSession = JFactory::getSession();
			$jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');

			$com_type   = $post['com_type'];

			switch($com_type)
			{
			    case 'com_blog':

					$migrateComment	= isset($post['smartblog_comment']) ? $post['smartblog_comment'] : '0';
					$migrateImage	= isset($post['smartblog_image']) ? $post['smartblog_image'] : '0';
					$imagePath		= isset($post['smartblog_imagepath']) ? $post['smartblog_imagepath'] : '';

					$this->_processSmartBlog($migrateComment, $migrateImage, $imagePath);

			        break;
			    case 'com_content':

					$authorId	= isset($post['authorId']) ? $post['authorId'] : '0';
					$stateId	= isset($post['stateId']) ? $post['stateId'] : '*';
					$catId		= isset($post['catId']) ? $post['catId'] : '0';
					$sectionId	= isset($post['sectionId']) ? $post['sectionId'] : '-1';
					$start		= 1;
					$myblogSection   = isset($post['$myblogSection']) ? $post['$myblogSection'] : '';

					$jomcomment		= isset( $post['content-jomcomment'] ) ? true : false;
					$this->_process($authorId, $stateId, $catId, $sectionId, $myblogSection , $jomcomment );

			        break;
			    case 'com_lyftenbloggie':
			    	//migrate lyftenbloggie tags
			    	$migrateComment	= isset($post['lyften_comment']) ? $post['lyften_comment'] : '0';

					$this->_migrateLyftenTags();
			        $this->_processLyftenBloggie( $migrateComment );
			        break;
			    case 'com_myblog':

		        	require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_myblog' . DIRECTORY_SEPARATOR . 'config.myblog.php');
		        	$myblogConfig	= new MYBLOG_Config();
		        	$myBlogSection	= $myblogConfig->get('postSection');

			    	$jomcomment		= isset( $post['myblog-jomcomment'] ) ? true : false;
			        $this->_processMyBlog( $myBlogSection , $jomcomment );
			        break;
			    case 'com_wordpress':

					$wpBlogId	= isset($post['wpBlogId']) ? $post['wpBlogId'] : '-1';

			        $this->_processWordPress( $wpBlogId );
			        break;
			    case 'xml_wordpress':

			        $fileName   = $post['wpxmlfiles'];
			        $authorId   = $post['authorid'];

			        $this->_processWordPressXML( $fileName, $authorId );
					break;

			    case 'xml_blogger':

			        $fileName   = $post['bloggerxmlfiles'];
			        $authorId   = $post['authorid'];
					$categoryId = $post['categoryid'];

			        $this->_processBloggerXML( $fileName, $authorId, $categoryId );
					break;

			    default:
			        break;

			}

		}

	}

	public function migrateK2( $params , $migrateComments = false )
	{

		if( !is_array( $params ) )
		{
			$data	= array( 'k2category' => $params );
		}
		else
		{
			$data	= EasyBlogStringHelper::ejaxPostToArray($params);
		}

		$db					= EasyBlogHelper::db();
	    $jSession 			= JFactory::getSession();
		$ejax				= new EJax();

		if( isset( $data['migrate_k2_comments'] ) )
		{
			$migrateComments	= true;
		}

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$k2Category		= $data[ 'k2category' ];

		$query	= 'SELECT * FROM `#__k2_items` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_k2');
		$query	.= ' )';
		$query	.= ' AND a.`catid` = ' . $db->Quote($k2Category);
		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{

			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status-k2', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ejax->script("scrollToBottomK2();");

			//update statistic
			$stat   = '========================================== <br />';
			$state .= JText::_( 'COM_EASYBLOG_MIGRATOR_TOTAL_BLOGS' ) . ' : ' . $migrateStat->blog . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $state .= JText::_( 'COM_EASYBLOG_MIGRATOR_TOTAL_USER_CONTRIBUTIONS' ) . ' : ' . count( $statUser ) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			    	$state	.= JText::_( 'COM_EASYBLOG_MIGRATOR_TOTAL_BLOG_USER' . ' \'' , $eachUser->name ) . ' : ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status-k2', $stat);

			$ejax->script("$( '#migrator-submit-k2' ).html('" . JText::_( 'COM_EASYBLOG_MIGRATOR_MIGRATION_COMPLETE' ) . "');");
			$ejax->script("$( '#migrator-submit-k2' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait-k2' ).css( 'display' , 'none' );");

		}
		else
		{
			// here we should process the migration

			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.

			$date           = EB::date();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			if(! empty($row->catid))
			{

			    $joomlaCat  = $this->_getK2Category($row->catid);

			    $eCat   	= $this->_isEblogCategoryExists($joomlaCat);

				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($joomlaCat);
				}

				$blogObj->category_id   = $eCat;
			}

			$profile	= EB::user($row->created_by);
			$blog		= EB::table('Blog');

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->created ) ? $row->created : $date->toMySQL();
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $row->title;
			$blogObj->permalink		= ( empty($row->alias) ) ? EasyBlogHelper::getPermalink($row->title) : $row->alias;

			if(empty($row->fulltext))
			{
				$blogObj->intro			= $row->introtext;
			}
			else
			{
				$blogObj->intro			= $row->introtext;
				$blogObj->content		= $row->fulltext;
			}

			//translating the article state into easyblog publish status.
			$blogState  = '';

			// Since K2 does not have `state` column, we need to map it back.
			$row->state	= $row->published;

			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
			    $blogState  = ($row->state == 2 || $row->state == -2) ? 0 : $row->state;
			}
			else
			{
			    $blogState  = ($row->state == -1) ? 0 : $row->state;
			}

			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $row->publish_up )? $row->publish_up : $date->toMySQL();
			$blogObj->publish_down	= !empty( $row->publish_down )? $row->publish_down : $date->toMySQL();

			$blogObj->ordering		= $row->ordering;
			$blogObj->hits			= $row->hits;
			$blogObj->frontpage     = 1;

			$blog->bind($blogObj);

			$this->_migrateK2Videos( $row , $blog , $profile );

			// Migrate K2 Images
			$this->_migrateK2Images( $row , $blog , $profile );


			$blog->store();

			//migrate meta description
			$this->_migrateContentMeta($row->metakey, $row->metadesc, $blog->id);

			// Map K2 tags into EasyBlog tags
			$query	= 'SELECT a.* FROM #__k2_tags AS a '
					. 'INNER JOIN #__k2_tags_xref AS b '
					. 'ON a.`id`=b.`tagID` '
					. 'WHERE b.`itemID`=' . $db->Quote( $row->id );
			$db->setQuery($query);

			$k2Tags	= $db->loadObjectList();

			if( $k2Tags )
			{
				foreach( $k2Tags as $item )
				{
				    $now    = EB::date();
					$tag	= EB::table('Tag');


					if( $tag->exists( $item->name ) )
					{
					    $tag->load( $item->name, true);
					}
					else
					{
						// Create tag if necessary
					    $tagArr = array();
					    $tagArr['created_by']  	= $this->_getSAUserId();
					    $tagArr['title']  		= $item->name;
					    $tagArr['alias']  		= $item->name;
					    $tagArr['published']  	= '1';
					    $tagArr['created']     	= $now->toMySQL();

                        $tag->bind($tagArr);
					    $tag->store();
					}

					$postTag	= EB::table('PostTag');
					$postTag->tag_id	= $tag->id;
					$postTag->post_id	= $blog->id;
					$postTag->created	= $now->toMySQL();
					$postTag->store();
				}
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
			$migrator->component    = 'com_k2';
			$migrator->store();

			$ejax->script( '$("#no-progress-k2").hide();' );
			$ejax->append('progress-status-k2', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_K2') . ': ' . $row->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $blog->id . '<br />');

			if( $migrateComments )
			{
				$this->migrateK2Comments( $row , $blog );
				$ejax->append('progress-status-k2', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_K2_COMMENTS') . ': ' . $row->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG_COMMENTS') . ': ' . $blog->id . '<br />');

				$return 	= array();
				$return['migrate_k2_comments' ] = 1;
				$return['k2category']			= $k2Category;

				require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

				$json 		= new Services_JSON();
				$return 	= $json->encode( $return );

				$ejax->script("ejax.load('migrators','migrateK2','" . $k2Category . "' , '1');");
			}
			else
			{
				$ejax->script("ejax.load('migrators','migrateK2','" . $k2Category . "');");
			}
		}

		$ejax->send();
		exit;
	}


	public function _migrateK2Videos( &$row , &$blog , $author )
	{
		$video 	= $row->video;

		$blog->content 	.= $video;
	}

	public function _migrateK2Images( &$row , &$blog , $author )
	{
		jimport( 'joomla.filesystem.file' );

		$name			= md5( 'Image' . $row->id );
		$path 			= JPATH_ROOT  . '/media/k2/items/src/' . $name . '.jpg';
		$config				= EasyBlogHelper::getConfig();
		$configStorage		= str_ireplace( '\\' , '/' , $config->get( 'main_image_path' ) );
		$newPath			= JPATH_ROOT . '/' . rtrim( $configStorage , '/' ) . '/' . $author->id;

		if( !JFolder::exists( $newPath ) )
		{
			JFolder::create( $newPath );
		}

		if( JFile::exists( $path ) )
		{
			// Copy the full scaled image
			$large				= JPATH_ROOT . '/media/k2/items/cache/' . $name . '_XL.jpg';
			$targetLarge		= $newPath . '/' . $name . '.jpg';
			$targetURL 			= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $configStorage ) . '/' . $author->id;
			$largeSrc			= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $configStorage ) . '/' . $author->id . '/' . $name . '.jpg';

			$file 				= getimagesize( $large );
			$file['name']		= basename( $large );
			$file[ 'tmp_name' ]	= $large;

			// ini_set( 'display_errors' , 1 );
			// error_reporting(E_ALL);

			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );
			$media 				= new EasyBlogMediaManager();
			$result 			= $media->upload($file, 'user:' . $author->id);
			$result 			= json_encode( $result );

			$blog->image 	= $result;
		}
	}


	function _processBloggerXML( $fileName, $authorId, $categoryId )
	{
	    $jSession 	= JFactory::getSession();

	    $fixedLocation  = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'xmlfiles' . DIRECTORY_SEPARATOR . 'blogger';
		$file 			= $fixedLocation . DIRECTORY_SEPARATOR . $fileName;

		if( JFile::exists( $file ) )
		{
// 			/* use for debugging - dun remove */
// 			libxml_use_internal_errors(true);
// 			echo '<pre>';
// 			print_r( simplexml_load_file( $file ) );
// 			echo '</pre>';
// 		    $errors = libxml_get_errors();
// 			var_dump($errors);
// 		    foreach ($errors as $error) {
// 				var_dump($error);
// 		    }
// 		    libxml_clear_errors();
// 			exit;

   			$parser = simplexml_load_file( $file );

			if( $parser )
			{
				$posts  		= array();
				$attachments    = array();

				$entries        = $parser->entry;
				// process each items
				foreach ( $entries as $item )
				{

					if( strpos( $item->id, '.post-' ) === false )
					{
						continue;
					}

					$post = array(
						'post_title' => (string) $item->title,
						'guid' => (string) $item->id,
					);


					$post['post_author'] = $authorId;

					$content = (string) $item->content;
					$post['post_content'] = $content;

					$post_id = stristr( $item->id, '.post-' );
					$post_id = str_replace( '.post-', '', $post_id);

					$post['post_id'] 				= $post_id;
					$post['post_date_gmt'] 			= (string) $item->published;
					$post['post_lastupdate_gmt'] 	= (string) $item->updated;

					$i  = 0;
					foreach ( $item->category as $tag )
					{
						if( $i == 0 )
						{
							$i++;
							continue;
						}
						$att = $tag->attributes();
						$post['tags'][] = (string) $att['term'];
					}

					$commentLink    	= '';
					$commentCountTitle  = '';
					$commentCount       = 0;
					foreach ( $item->link as $link )
					{
						$att = $link->attributes();
						if( $att['rel'] == 'replies' && $att['type'] == 'application/atom+xml' )
						{
							$commentLink    	= (string) $att['href'];
						}
						else if( $att['rel'] == 'replies' && $att['type'] == 'text/html' )
						{
							$commentCountTitle  = (string) $att['title'];
						}
					}

					if( $commentCountTitle )
					{
						$tmp    = explode(' ', $commentCountTitle);
						if( isset( $tmp[0] ) && is_numeric( $tmp[0] ) )
						{
							$commentCount   = $tmp[0];
						}
					}

					$post['comment_link']   = $commentLink;
					$post['images']         = '';

					//process images from blogger in content.
					$pattern	= '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
					preg_match_all( $pattern , $content , $matches );

					if( count( $matches[1] ) > 0 )
					{
						$postImages = array();
						foreach( $matches[1] as $img )
						{
							if( strpos($img, '.bp.blogspot.com') !== false )
							{
								$postImages[]   = $img;
							}
						}

						if( count($postImages) > 0 )
						{
							$post['images'] = $postImages;
						}
					}

					$postComments   = array();
					$this->logXMLData( $fileName,  $post_id, 'post-blogger',  $post, $postComments );

				} //end foreach

				$this->_migrateBloggerXML( $fileName, $authorId, $categoryId );
			}
			else
			{
			    $ejax		= new EJax();
			    $ejax->append('progress-status8', JText::_('COM_EASYBLOG_MIGRATOR_XML_PARSE_ERROR'));
				$ejax->script("$( '#migrator-submit8' ).attr('disabled' , '');");
				$ejax->script("$( '#icon-wait8' ).css( 'display' , 'none' );");
				$ejax->send();
			}// if parser
		}
		else
		{
		    $ejax		= new EJax();
		    $ejax->append('progress-status8', JText::_('COM_EASYBLOG_MIGRATOR_XML_PARSE_ERROR'));
			$ejax->script("$( '#migrator-submit8' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait8' ).css( 'display' , 'none' );");
			$ejax->send();
		}
	}

	function _migrateBloggerXML( $fileName, $authorId, $categoryId )
	{
	    $db			= EasyBlogHelper::db();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$posts	= $this->getXMLPostData( $fileName, 'post-blogger' );

		if( ! isset($posts->post_id) )
		{
		    $this->clearXMLData( $fileName );
			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status8', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ejax->script("divSrolltoBottomBloggerXML();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOGS') . ': ' . $migrateStat->blog . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOG_USER') . ' \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status8', $stat);

			$ejax->script("$( '#migrator-submit8' ).html('" . JText::_( 'COM_EASYBLOG_MIGRATOR_MIGRATION_COMPLETED' ) . "');");
			$ejax->script("$( '#migrator-submit8' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait8' ).css( 'display' , 'none' );");
			$ejax->send();
		}

		$data   	= $posts->data;
		$contentId 	= $data['post_id'];

		$data['comments']   = $posts->comments;

		if( empty( $contentId ) )
		{
			$this->clearXMLData( $fileName, true);
			$ejax->script("ejax.load('migrators','_migrateBloggerXML','$fileName','$authorId','$categoryId');");
			$ejax->send();
		}

		$query	= 'SELECT content_id FROM `#__easyblog_migrate_content` AS b';
		$query	.= ' WHERE b.`content_id` = '. $db->Quote( $contentId );
		$query	.= '  and `component` = ' . $db->Quote( 'xml_blogger' );
		$query	.= '  and `filename` = ' . $db->Quote( $fileName );

		$db->setQuery($query);
		$row	= $db->loadResult();

		if( is_null( $row ) )
		{
			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.
			$date           = EB::date();
			$blogObj    	= new stdClass();
			$adminId        = ( empty($authorId) ) ? EasyBlogHelper::getDefaultSAIds() : $authorId;

			//default
			$blogObj->category_id   = $categoryId;  //assume 1 is the uncategorized id.

			$wpCat  = '';
			$wpTag  = array();

			if( ! empty($data['tags']) )
			{
			    foreach( $data['tags'] as $tag )
				{
			        $tmpTag = new stdClass();
			        $tmpTag->title  = $tag;
			        $tmpTag->alias  = '';
			        $wpTag[] = $tmpTag;
				}
			}

			//load user profile
			$profile	= EB::user($adminId);

			$blog		= EB::table('Blog');

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $data['post_date_gmt'] ) ? $data['post_date_gmt'] : $date->toMySQL();
			$blogObj->modified		= !empty( $data['post_lastupdate_gmt'] ) ? $data['post_lastupdate_gmt'] : $date->toMySQL();

			$blogObj->title			= $data['post_title'];

			$addTail    = false;
			if( empty( $blogObj->title ) )
			{
				$tmpString 		= strip_tags( $data['post_content'] );
				$blogObj->title = substr( $tmpString, 0, 15 );
				$addTail    = true;
			}

			$blogObj->permalink		= EasyBlogHelper::getPermalink($data['post_title']);
			if( $addTail )
			{
				$blogObj->title = $blogObj->title . '...';
			}


			// process image upload and urll replacement.
			if( $data['images'] )
			{
				foreach( $data['images'] as $image )
				{
					$data['post_content'] = $this->_migrateBloggerImage($image, $authorId, $data['post_content'] );
				}
			}


			$data['post_content']	= nl2br( $data['post_content'] );

			$blogObj->intro			= $data['post_content'];
			$blogObj->content		= '';

			//translating the article state into easyblog publish status.
			$blogState  	= '1';
			$isPrivate		= '0';

			$blogObj->access       = $isPrivate;
			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $data['post_date_gmt'] )? $data['post_date_gmt'] : $date->toMySQL();
			$blogObj->publish_down	= '0000-00-00 00:00:00';

			$blogObj->ordering		= 0;
			$blogObj->hits			= 0;
			$blogObj->frontpage     = 1;
			$blogObj->allowcomment  = 1;

			$blog->bind($blogObj);
			$blog->store();

			// add tags.
			if( count($wpTag) > 0)
			{

			    foreach($wpTag as $item)
			    {
				    $now    = EB::date();
					$tag	= EB::table('Tag');

					if( $tag->exists( $item->title ) )
					{
					    $tag->load( $item->title, true);
					}
					else
					{
					    $tagArr = array();
					    $tagArr['created_by']  	= $adminId;
					    $tagArr['title']  		= $item->title;
					    $tagArr['published']  	= '1';
					    $tagArr['created']     	= $now->toMySQL();

                        $tag->bind($tagArr);
					    $tag->store();
					}

					$postTag	= EB::table('PostTag');
					$postTag->tag_id	= $tag->id;
					$postTag->post_id	= $blog->id;
					$postTag->created	= $now->toMySQL();
					$postTag->store();

			    }
			}

			// add comments
			if( isset($data['comment_link']) )
			{
				// we need to fetch the data from a external feed.
				$this->_migrateBloggerComment($data['comment_link'], $blog->id);
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

			$migrator->content_id	= $contentId;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'xml_blogger';
			$migrator->filename    	= $fileName;
			$migrator->store();

			//$this->clearXMLData( $fileName, $contentId);

			$ejax->append('progress-status8', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_BLOGGER_XML') . ': ' . $contentId . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $blog->id . '<br />');
		    $ejax->script("ejax.load('migrators','_migrateBloggerXML','$fileName','$authorId','$categoryId');");
		}
		else
		{
		    // skip, go to next item.
		    $ejax->script("ejax.load('migrators','_migrateBloggerXML','$fileName','$authorId','$categoryId');");
		}

		$ejax->send();
	}

	function _migrateBloggerImage( $image, $userid, $content )
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$config 			= EasyBlogHelper::getConfig();
		$main_image_path	= $config->get('main_image_path');
        $main_image_path 	= rtrim($main_image_path, '/');

		$rel_upload_path    = $main_image_path . '/' . $userid;
		$userUploadPath    	= JPATH_ROOT . DIRECTORY_SEPARATOR . str_ireplace('/', DIRECTORY_SEPARATOR, $main_image_path . DIRECTORY_SEPARATOR . $userid);
		$folder     		= JPath::clean($userUploadPath);

		$dir  	 			= $userUploadPath . DIRECTORY_SEPARATOR;
		$tmp_dir 			= JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

  		if(! JFolder::exists($dir))
  		{
  		    JFolder::create($dir);
  		}

		//now let get the image from remove url.
		$segments   = explode( '/', $image );
		$fileName   = $segments[ count($segments) - 1 ];

		$fileName	 = JFile::makesafe( $fileName );
		$tmpFileName = $tmp_dir . $fileName;

		$file['name']   	= $fileName;
		$file['tmp_name']   = $tmpFileName;

		// write to JOOMLA tmp folder
		file_put_contents( $tmpFileName, file_get_contents($image));

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );
		$media 				= new EasyBlogMediaManager();
		$result 			= $media->upload($file, 'user:' . $userid );

		@JFile::delete( $file['tmp_name'] );

		if( isset( $result->type ) )
		{
			$relativeImagePath = $rel_upload_path . '/' . $file['name'];

			// lets replace the image from the content to this uploaded one.
			$content     	= str_replace( $image , $relativeImagePath , $content);
		}

		return $content;
	}

	function _migrateBloggerComment( $link, $post_id)
	{
	    require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'connectors.php' );

	    $connector  = EB::connector();
		$connector->addUrl( $link );
		$connector->execute();
	    $content	= $connector->getResult( $link );

		$pattern    	= '/(.*?)<\?xml version/is';
		$replacement    = '<?xml version';
		$content		= preg_replace( $pattern , $replacement , $content, 1);

 		$parser = simplexml_load_string( $content );

		if( $parser )
		{
		    $lft = 1;
		    $rgt = 2;

			$entries        = $parser->entry;
			// process each items
			foreach ( $entries as $item )
			{
				if( strpos( $item->id, '.post-' ) === false )
				{
					continue;
				}

				$now	= EB::date();
				$db		= EasyBlogHelper::db();
				$commt	= EB::table('Comment');

				//we need to rename the esname and esemail back to name and email.
				$post               = array();
				$post['name']		= (string) $item->author->name;
				$post['email']		= (string) $item->author->email;
				$post['id']     	= $post_id;
				$post['comment']    = (string) $item->content;
				$post['title']      = (string) $item->title;
		        $post['url']        = (string) $item->author->uri;;
		        $post['ip']        	= '';
				$commt->bindPost($post);

				$commt->created_by  = '0';
				$commt->created		= (string) $item->published;
				$commt->modified	= (string) $item->published;
				$commt->published   = 1;
				$commt->parent_id   = '0';
				$commt->sent        = 1;
				$commt->lft	= $lft;
				$commt->rgt	= $rgt;

				if( $commt->store() )
				{
					$lft++;
					$rgt++;
				}
			}
		}

	}

	/* adapted from wordpress importer */
	function _processWordPressXML( $fileName, $authorId )
	{
	    $jSession 	= JFactory::getSession();

	    $fixedLocation  = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'xmlfiles';
		$file 			= $fixedLocation . DIRECTORY_SEPARATOR . $fileName;

		if( JFile::exists( $file ) )
		{
			/* use for debugging - dun remove */
			/*
			libxml_use_internal_errors(true);
			var_dump( simplexml_load_file( $file ) );
		    $errors = libxml_get_errors();
			var_dump($errors);
		    foreach ($errors as $error) {
				var_dump($error);
		    }
		    libxml_clear_errors();
			exit;
			*/

   			$parser = simplexml_load_file( $file );

			if( $parser )
			{

				$baseUrl = $parser->xpath('/rss/channel/wp:base_site_url');
				$baseUrl = (string) trim( $baseUrl[0] );

				$namespaces = $parser->getDocNamespaces();

				if ( ! isset( $namespaces['wp'] ) )
				{
					$namespaces['wp'] = 'http://wordpress.org/export/1.1/';
				}

				if ( ! isset( $namespaces['excerpt'] ) )
				{
					$namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';
				}

				$posts  		= array();
				$attachments    = array();

				// process each items
				foreach ( $parser->channel->item as $item )
				{
					$post = array(
						'post_title' => (string) $item->title,
						'guid' => (string) $item->guid,
						'link' => (string) $item->link,
					);

					$dc = $item->children( 'http://purl.org/dc/elements/1.1/' );
					$post['post_author'] = (string) $dc->creator;

					$content = $item->children( 'http://purl.org/rss/1.0/modules/content/' );
					$excerpt = $item->children( $namespaces['excerpt'] );

					$post['post_content'] = (string) $content->encoded;
					$post['post_excerpt'] = (string) $excerpt->encoded;

					$wp = $item->children( $namespaces['wp'] );

					$post['post_id'] 		= (int) $wp->post_id;
					$post['post_date_gmt'] 	= (string) $wp->post_date_gmt;

					$post['comment_status'] = (string) $wp->comment_status;

					$post['post_name'] 		= (string) $wp->post_name;
					$post['status'] 		= (string) $wp->status; // publish , draft

					$post['post_type'] 		= (string) $wp->post_type;
					$post['post_parent']	= (string) $wp->post_parent;
					$post['post_password'] 	= (string) $wp->post_password;
					$post['attachment_url'] = (string) $wp->attachment_url;


					if( ($post['post_type'] != 'post') && ($post['post_type'] != 'attachment') )
					    continue;

					if( $post['status'] == 'draft' )
					    continue;


					foreach ( $item->category as $terms )
					{
						$att = $terms->attributes();
						if ( isset( $att['nicename'] ) )
						{
							$post['terms'][] = array(
								'name' => (string) $terms,
								'slug' => (string) $att['nicename'],
								'domain' => (string) $att['domain']
							);
						}
					}

					foreach ( $wp->postmeta as $meta )
					{
						$post['postmeta'][] = array(
							'key' => (string) $meta->meta_key,
							'value' => (string) $meta->meta_value
						);
					}

					$postComments   = array();
					foreach ( $wp->comment as $comment )
					{
						if( empty( $comment->comment_content ) )
							continue;

						$postComments[] = array(
							'comment_id' => (int) $comment->comment_id,
							'comment_author' => (string) $comment->comment_author,
							'comment_author_email' => (string) $comment->comment_author_email,
							'comment_author_IP' => (string) $comment->comment_author_IP,
							'comment_author_url' => (string) $comment->comment_author_url,
							'comment_date' => (string) $comment->comment_date,
							'comment_date_gmt' => (string) $comment->comment_date_gmt,
							'comment_content' => (string) $comment->comment_content,
							'comment_approved' => (string) $comment->comment_approved,
							'comment_type' => (string) $comment->comment_type,
							'comment_parent' => (string) $comment->comment_parent,
							'comment_user_id' => (int) $comment->comment_user_id
						);
					}//end foreach

					if( $post['post_type'] == 'attachment' )
					{
					    $post_parant    = $post['post_parent'];
					    $this->logXMLData( $fileName,  $post_parant, 'attachment',  $post );
					}
					else
					{
					    $post_id    = $post['post_id'];

						if( count($postComments) > 150 )
						{
							$postComments = array_slice($postComments, 0, 150);
						}

					    $this->logXMLData( $fileName,  $post_id, 'post',  $post, $postComments );
					}

				} //end foreach

				$this->_migrateWPXML( $fileName, $authorId );
			}
			else
			{
			    $ejax		= new EJax();
			    $ejax->append('progress-status6', JText::_('COM_EASYBLOG_MIGRATOR_XML_PARSE_ERROR'));
				$ejax->script("$( '#migrator-submit6' ).attr('disabled' , '');");
				$ejax->script("$( '#icon-wait6' ).css( 'display' , 'none' );");
				$ejax->send();
			}// if parser
		}
		else
		{
		    $ejax		= new EJax();
		    $ejax->append('progress-status6', JText::_('COM_EASYBLOG_MIGRATOR_XML_PARSE_ERROR'));
			$ejax->script("$( '#migrator-submit6' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait6' ).css( 'display' , 'none' );");
			$ejax->send();
		}
	}

	function logXMLData( $fileName,  $postId, $source,  $data, $comments = array() )
	{
	    $jSession 	= JFactory::getSession();

		//log the entry into migrate table.
		$xml = EB::table('Xmldata');

		$xml->post_id		= $postId;
		$xml->session_id	= $jSession->getToken();
		$xml->source    	= $source;
		$xml->filename    	= $fileName;
		$xml->data			= serialize($data);
		$xml->comments		= (! empty($comments) ) ? serialize($comments) : '';

		$xml->store();
	}

	function getXMLPostData( $fileName, $sourceName = 'post' )
	{
	    $jSession 	= JFactory::getSession();
	    $db         = EasyBlogHelper::db();

	    $sessId     = $jSession->getToken();

	    $query  = 'select * from `#__easyblog_xml_wpdata`';
 	    $query  .= ' where `session_id` = ' . $db->Quote( $sessId );
 	    $query  .= ' and `filename` = ' . $db->Quote($fileName);
	    $query  .= ' and `source` = ' . $db->Quote( $sourceName );
	    $query  .= ' order by `id` limit 1';

	    $db->setQuery($query);
	    $result = $db->loadObject();

	    $contentId  = '';
	    if( isset($result->post_id) )
	    {
	        $contentId  = $result->post_id;
	        $result->data 		= unserialize($result->data);
			if( !empty($result->comments) )
				$result->comments 	= unserialize($result->comments);
	    }

		$this->clearXMLData( $fileName, $contentId);

	    return $result;
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

		if( count( $result ) > 0 )
		{
		    foreach( $result as $att)
		    {
		        $attachments[]  = unserialize( $att->data );
		    }
		}

	    return $attachments;
	}

	function clearXMLData( $fileName, $postid = '' )
	{
	    $jSession 	= JFactory::getSession();
	    $db         = EasyBlogHelper::db();

	    $sessId     = $jSession->getToken();


		if( $postid === true )
		{
		    $query  = 'delete from `#__easyblog_xml_wpdata`';
		    $query  .= ' where `session_id` = ' . $db->Quote( $sessId );
		    $query  .= ' and `filename` = ' . $db->Quote($fileName);
			$query  .= ' limit 1';
		}
		else
		{
		    $query  = 'delete from `#__easyblog_xml_wpdata`';
		    $query  .= ' where `session_id` = ' . $db->Quote( $sessId );
		    $query  .= ' and `filename` = ' . $db->Quote($fileName);
		    if( !empty( $postid ) )
		    	$query  .= ' and `post_id` = ' . $db->Quote($postid);
		}

	    $db->setQuery($query);
	    $db->query();

		return true;
	}

	function _migrateWPXML( $fileName, $authorId )
	{
	    $db			= EasyBlogHelper::db();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$posts	= $this->getXMLPostData( $fileName );

		if( ! isset($posts->post_id) )
		{
		    $this->clearXMLData( $fileName );
			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status6', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ejax->script("divSrolltoBottomWordPressXML();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOGS') . ': ' . $migrateStat->blog . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOG_USER') . ' \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status6', $stat);

			$ejax->script("$( '#migrator-submit6' ).html('" . JText::_( 'COM_EASYBLOG_MIGRATOR_MIGRATION_COMPLETED' ) . "');");
			$ejax->script("$( '#migrator-submit6' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait6' ).css( 'display' , 'none' );");
			$ejax->send();
		}

		$data   	= $posts->data;
		$contentId 	= $data['post_id'];

		$data['comments']   = $posts->comments;

		if( empty( $contentId ) )
		{
			$this->clearXMLData( $fileName, true);
			$ejax->script("ejax.load('migrators','_migrateWPXML','$fileName','$authorId');");
			$ejax->send();
		}

		$query	= 'SELECT content_id FROM `#__easyblog_migrate_content` AS b';
		$query	.= ' WHERE b.`content_id` = '. $db->Quote( $contentId );
		$query	.= '  and `component` = ' . $db->Quote( 'xml_wordpress' );
		$query	.= '  and `filename` = ' . $db->Quote( $fileName );

		$db->setQuery($query);
		$row	= $db->loadResult();

		if( is_null( $row ) )
		{
			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.
			$date           = EB::date();
			$blogObj    	= new stdClass();
			$adminId        = ( empty($authorId) ) ? EasyBlogHelper::getDefaultSAIds() : $authorId;

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			$wpCat  = '';
			$wpTag  = array();

			if( ! empty($data['terms']) )
			{
			    foreach( $data['terms'] as $term )
				{
				    if( $term['domain'] == 'category' && empty($wpCat) )
				    {
				        $wpCat  = new stdClass();
				        $wpCat->title  = $term['name'];
				        $wpCat->alias  = $term['slug'];
				    }
				    else if( $term['domain'] == 'post_tag' )
				    {
				        $tmpTag = new stdClass();
				        $tmpTag->title  = $term['name'];
				        $tmpTag->alias  = $term['slug'];
				        $wpTag[] = $tmpTag;
				    }
				}
			}

			if(isset($wpCat->title))
			{
			    $eCat   	= $this->_isEblogCategoryExists($wpCat);
				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($wpCat);
				}

				$blogObj->category_id   = $eCat;
			}

			//load user profile
			$profile	= EB::user($adminId);
			$blog		= EB::table('Blog');

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $data['post_date_gmt'] ) ? $data['post_date_gmt'] : $date->toMySQL();
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $data['post_title'];
			$blogObj->permalink		= ( empty($data['post_name']) ) ? EasyBlogHelper::getPermalink($data['post_title']) : $data['post_name'];

			// Migrate caption
			$pattern2   = '/\[caption.*caption="(.*)"\]/iU';
            $data['post_content'] 	= preg_replace( $pattern2 , '<div class="caption">$1</div>' , $data['post_content'] );
            $data['post_content']	= str_ireplace( '[/caption]' , '<br />' , $data['post_content'] );

			// process attachments.
// 			$attachments    = $this->getXMLAttachmentData( $fileName, $contentId);
// 			if( count($attachments) > 0 )
// 			{
// 				$data['post_excerpt'] = $this->_processWPXMLAttachment($contentId, $data['post_excerpt'], $attachments, $authorId);
// 				$data['post_content'] = $this->_processWPXMLAttachment($contentId, $data['post_content'], $attachments, $authorId);
// 			}

			$data['post_excerpt']	= nl2br( $data['post_excerpt'] );
			$data['post_content']	= nl2br( $data['post_content'] );

			$blogObj->intro			= $data['post_excerpt'];
			$blogObj->content		= $data['post_content'];

			//translating the article state into easyblog publish status.
			$blogState  	= '0';
			$isPrivate		= '0';
			if( $data['status'] == 'private' )
			{
                $isPrivate  = '1';
                $blogState  = '1';
			}
			else if( $data['status'] == 'publish' )
			{
                $isPrivate  = '0';
                $blogState  = '1';
			}

			$blogObj->blogpassword  = $data['post_password'];
			$blogObj->access       = $isPrivate;
			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $data['post_date_gmt'] )? $data['post_date_gmt'] : $date->toMySQL();
			$blogObj->publish_down	= '0000-00-00 00:00:00';

			$blogObj->ordering		= 0;
			$blogObj->hits			= 0;
			$blogObj->frontpage     = 1;
			$blogObj->allowcomment  = ($data['comment_status'] == 'open') ? 1 : 0;

			$blog->bind($blogObj);
			$blog->store();

			// add tags.
			if( count($wpTag) > 0)
			{

			    foreach($wpTag as $item)
			    {
				    $now    = EB::date();
					$tag	= EB::table('Tag');

					if( $tag->exists( $item->title ) )
					{
					    $tag->load( $item->title, true);
					}
					else
					{
					    $tagArr = array();
					    $tagArr['created_by']  	= $adminId;
					    $tagArr['title']  		= $item->title;
					    $tagArr['alias']  		= $item->alias;
					    $tagArr['published']  	= '1';
					    $tagArr['created']     	= $now->toMySQL();

                        $tag->bind($tagArr);
					    $tag->store();
					}

					$postTag	= EB::table('PostTag');
					$postTag->tag_id	= $tag->id;
					$postTag->post_id	= $blog->id;
					$postTag->created	= $now->toMySQL();
					$postTag->store();

			    }
			}

			// add comments

			if( !empty( $data['comments'] ) && is_array( $data['comments'] ) )
			{
				// Sort first
				usort( $data['comments'], array( $this, '_sortWPXMLComments' ) );

				foreach( $data['comments'] as $citem )
				{
					$item = JArrayHelper::toObject( $citem );

					if( $item->comment_parent == 0 )
					{
						$this->_migrateWPComments('xml', $contentId, $blog->id, 0, $item, $data['comments']);
					}
				}
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

			$migrator->content_id	= $contentId;
			$migrator->post_id		= $blog->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'xml_wordpress';
			$migrator->filename    	= $fileName;
			$migrator->store();

			//$this->clearXMLData( $fileName, $contentId);

			$ejax->append('progress-status6', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_WORDPRESS_XML') . ': ' . $contentId . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $blog->id . '<br />');
		    $ejax->script("ejax.load('migrators','_migrateWPXML','$fileName','$authorId');");
		}
		else
		{
		    // skip, go to next item.
		    $ejax->script("ejax.load('migrators','_migrateWPXML','$fileName','$authorId');");
		}

		$ejax->send();
	}

	function _sortWPXMLComments( $a, $b )
	{
		$date1 = new DateTime($a['comment_date']);
		$date2 = new DateTime($b['comment_date']);

		return $date1 < $date2 ? -1 : 1;
	}

	function _processWPXMLAttachment( $wpPostId, $content, $attachments, $authorId)
	{
	    require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'connectors.php' );

		foreach( $attachments as $attachment)
		{
			$link    		= $attachment['link'];
			$attachementURL = $attachment['attachment_url'];

			if( EasyImageHelper::isImage($attachementURL) )
			{
			    $filname    = EasyImageHelper::getFileName($attachementURL);
			    $extension  = EasyImageHelper::getFileExtension($attachementURL);

			    $folder   = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'blogs' . DIRECTORY_SEPARATOR . $wpPostId;
			    if( !JFolder::exists( $folder ) )
			    {
			    	JFolder::create( $folder );
				}

				// new image location
				$newFile    = $folder . DIRECTORY_SEPARATOR . $filname;

			    $connector  = EB::connector();
				$connector->addUrl( $attachementURL );
				$connector->execute();
			    $imageraw	= $connector->getResult( $attachementURL );

			    if( $imageraw )
			    {
			        if( JFile::write($newFile, $imageraw ) )
			        {
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

	function _processWordPress( $wpBlogId )
	{
	    $db			= EasyBlogHelper::db();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$wpTableNamePrex    = ($wpBlogId == '1') ? '' : $wpBlogId . '_';
		$wpComponentName    = 'com_wordpress' . $wpBlogId;


		$query	= 'SELECT a.`ID` as `id`, a.* FROM `#__wp_' . $wpTableNamePrex . 'posts` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote( $wpComponentName );
		$query	.= ' )';
		$query  .= ' AND `post_type` = ' . $db->Quote( 'post' );
		$query  .= ' AND `post_status` != ' . $db->Quote( 'auto-draft' );
		$query	.= ' ORDER BY a.`id` LIMIT 1';


		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{

			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status5', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ejax->script("divSrolltoBottomWordPress();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOGS') . ': ' . $migrateStat->blog . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOG_USER') . ' \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status5', $stat);

			$ejax->script("$( '#migrator-submit5' ).html('" . JText::_( 'COM_EASYBLOG_MIGRATOR_MIGRATION_COMPLETED' ) . "');");
			$ejax->script("$( '#migrator-submit5' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait5' ).css( 'display' , 'none' );");

		}
		else
		{
			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.

			$date           = EB::date();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			$wpCat  = $this->_getWPTerms( $wpTableNamePrex, $row->id, 'category');
			if(isset($wpCat->title))
			{
			    $eCat   	= $this->_isEblogCategoryExists($wpCat);
				if($eCat === false)
				{
				    $eCat   = $this->_createEblogCategory($wpCat);
				}

				$blogObj->category_id   = $eCat;
			}

			//load user profile
			$profile	= EB::user($row->post_author);
			$blog		= EB::table('Blog');

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->post_date ) ? $row->post_date : $date->toMySQL();
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $row->post_title;
			$blogObj->permalink		= ( empty($row->post_name) ) ? EasyBlogHelper::getPermalink($row->post_title) : $row->post_name;

			/* replacing [caption] and [gallery] */

			// Migrate caption
			$pattern2   = '/\[caption.*caption="(.*)"\]/iU';
            $row->post_content  = preg_replace( $pattern2 , '<div class="caption">$1</div>' , $row->post_content );
            $row->post_content	= str_ireplace( '[/caption]' , '<br />' , $row->post_content );

			// Migrate galleries
			$pattern	= '/\[gallery(.*)/i';
			preg_match( $pattern , $row->post_content , $matches );
			if( !empty( $matches ) )
			{
			    $folder   = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'blogs' . DIRECTORY_SEPARATOR . $row->id;
			    if( !JFolder::exists( $folder ) )
			    {
			    	JFolder::create( $folder );
				}

			    // Now fetch items
				$query	= 'SELECT a.guid FROM `#__wp_' . $wpTableNamePrex . 'posts` AS a';
				$query  .= ' WHERE `post_type` = ' . $db->Quote( 'attachment' );
				$query  .= ' AND `post_mime_type` LIKE "%image%"';
				$query	.= ' AND `post_parent`=' . $db->Quote( $row->id );

				//http://maephim.se/piccolina/wp-content/uploads/2011/04/Thailand-Apr-2010-080-Large.jpg
				//http://easyblog.localhost.com/components/com_wordpress/wp/wp-content/uploads/2011/08/262131_1791775084596_1546222768_31409359_5180181_n.jpg-540×720-pixels.jpg
				$db->setQuery( $query );
				$cibais	= $db->loadObjectList();

				$images 	= array();
				$siteRoot   = JURI::root();

				foreach( $cibais as $cibai )
				{
				    $image  = $cibai->guid;

					$image  = str_ireplace( $siteRoot , '' , $image );
					$image  = str_ireplace( '/' , DIRECTORY_SEPARATOR , $image );

					$imageFull  = JPATH_ROOT . DIRECTORY_SEPARATOR . $image;
					$parts= explode( DIRECTORY_SEPARATOR , $imageFull );
					JFile::copy( $imageFull , JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'blogs' . DIRECTORY_SEPARATOR . $row->id . DIRECTORY_SEPARATOR .  $parts[ count( $parts ) - 1 ] );
				}


				// Replace content with the proper gallery tag
				//{gallery}4745732{/gallery}
				$row->post_content	= JString::str_ireplace( $matches[0] , '{gallery}' . $row->id . '{/gallery}' , $row->post_content );
			}

			/* end replacing [caption] and [gallery] */

			$row->post_excerpt	= nl2br( $row->post_excerpt );
			$row->post_content	= nl2br( $row->post_content );

			$blogObj->intro			= $row->post_excerpt;
			$blogObj->content		= $row->post_content;


			//translating the article state into easyblog publish status.
			$blogState  	= '0';
			$isPrivate		= '0';
			if( $row->post_status == 'private' )
			{
                $isPrivate  = '1';
                $blogState  = '1';
			}
			else if( $row->post_status == 'publish' )
			{
                $isPrivate  = '0';
                $blogState  = '1';
			}

			$blogObj->blogpassword  = $row->post_password;
			$blogObj->access       = $isPrivate;
			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $row->post_date )? $row->post_date : $date->toMySQL();
			$blogObj->publish_down	= '0000-00-00 00:00:00';

			$blogObj->ordering		= 0;
			$blogObj->hits			= 0;
			$blogObj->frontpage     = 1;
			$blogObj->allowcomment  = ($row->comment_status == 'open') ? 1 : 0;

			$blog->bind($blogObj);
			$blog->store();

			// add tags.
			$wpPostTag  = $this->_getWPTerms( $wpTableNamePrex, $row->id, 'post_tag');
			if( count($wpPostTag) > 0)
			{

			    foreach($wpPostTag as $item)
			    {
				    $now    = EB::date();
					$tag	= EB::table('Tag');

					if( $tag->exists( $item->title ) )
					{
					    $tag->load( $item->title, true);
					}
					else
					{
					    $tagArr = array();
					    $tagArr['created_by']  	= $this->_getSAUserId();
					    $tagArr['title']  		= $item->title;
					    $tagArr['alias']  		= $item->alias;
					    $tagArr['published']  	= '1';
					    $tagArr['created']     	= $now->toMySQL();

                        $tag->bind($tagArr);
					    $tag->store();
					}

					$postTag	= EB::table('PostTag');
					$postTag->tag_id	= $tag->id;
					$postTag->post_id	= $blog->id;
					$postTag->created	= $now->toMySQL();
					$postTag->store();

			    }
			}


			// add comments
			$query	= 'SELECT * FROM `#__wp_' . $wpTableNamePrex . 'comments` AS a';
			$query	.= ' where `comment_post_ID` = ' . $db->Quote( $row->id );
			$query  .= ' and `comment_approved` = ' . $db->Quote('1');
			$query  .= ' and `comment_parent` = ' . $db->Quote('0');
			$query  .= ' order by `comment_date` ASC';

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if( count($result) > 0)
			{
			    foreach( $result as $item)
			    {
					$this->_migrateWPComments($wpTableNamePrex, $row->id, $blog->id, '0', $item);
			    }
			}
		    //end adding comments



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
			$migrator->component    = $wpComponentName;
			$migrator->store();

			$ejax->append('progress-status5', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_WORDPRESS_BLOG') . ': ' . $row->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_processWordPress','$wpBlogId');");

		}

		$ejax->send();
	}

	function _migrateWPComments($wpTableNamePrex, $postId, $blogId, $parentId, $item, $comments = array())
	{
		$now	= EB::date();
		$db		= EasyBlogHelper::db();
		$commt	= EB::table('Comment');

		//we need to rename the esname and esemail back to name and email.
		$post               = array();
		$post['name']		= ( isset( $item->comment_author ) ) ? $item->comment_author : '';
		$post['email']		= ( isset( $item->comment_author_email ) ) ? $item->comment_author_email : '';
		$post['id']     	= $blogId;
		$post['comment']    = ( isset( $item->comment_content ) ) ? $item->comment_content : '';
		$post['title']      = '';
        $post['url']        = ( isset( $item->comment_author_url ) ) ? $item->comment_author_url : '';
        $post['ip']        	= ( isset( $item->comment_author_IP ) ) ? $item->comment_author_IP : '';
		$commt->bindPost($post);

		$commt->created_by  = ( $wpTableNamePrex == 'xml' ) ? '0' : $item->user_id;
		$commt->created		= ( isset( $item->comment_date ) ) ? $item->comment_date : '';
		$commt->modified	= ( isset( $item->comment_date ) ) ? $item->comment_date : '';
		$commt->published   = 1;
		$commt->parent_id   = $parentId;
		$commt->sent        = 1;

		$commt->store();

		if( $wpTableNamePrex != 'xml' )
		{
			//check to see if there is any child comments or not.
			$query	= 'SELECT a.* FROM `#__wp_' . $wpTableNamePrex . 'comments` AS a';
			$query	.= ' where `comment_post_ID` = ' . $db->Quote( $postId );
			$query  .= ' and `comment_approved` = ' . $db->Quote('1');
			$query  .= ' and `comment_parent` = ' . $db->Quote ( $item->comment_ID );
			$query  .= ' order by `comment_date` ASC';

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if( count($result) > 0)
			{
			    foreach( $result as $citem)
			    {
			        $this->_migrateWPComments($wpTableNamePrex, $postId, $blogId, $commt->id, $citem);
			    }
			}
		}
		else
		{
			foreach( $comments as $citem )
			{
				$child = JArrayHelper::toObject( $citem );

				if( $child->comment_parent == $item->comment_id )
				{
					$this->_migrateWPComments('xml', $postId, $blogId, $commt->id, $child, $comments);
				}
			}
		}

        return true;
	}

	function _updateEasyBlogCommentSibling($blogId, $nodeValue)
	{
		$db	= EasyBlogHelper::db();

		$query	= 'UPDATE `#__easyblog_comment` SET `rgt` = `rgt` + 2';
		$query	.= ' WHERE `rgt` > ' . $db->Quote($nodeValue);
		$query	.= ' AND `post_id` = ' . $db->Quote($blogId);
		$db->setQuery($query);
		$db->query();

		$query	= 'UPDATE `#__easyblog_comment` SET `lft` = `lft` + 2';
		$query	.= ' WHERE `lft` > ' . $db->Quote($nodeValue);
		$query	.= ' AND `post_id` = ' . $db->Quote($blogId);
		$db->setQuery($query);
		$db->query();
	}


	function _getEasyBlogLatestComment($blogId, $parentId = 0)
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT `id`, `lft`, `rgt` FROM `#__easyblog_comment`';
		$query	.= ' WHERE `post_id` = ' . $db->Quote($blogId);
		$query	.= ' AND `parent_id` = ' . $db->Quote($parentId);
		$query	.= ' ORDER BY `lft` DESC LIMIT 1';

		$db->setQuery($query);
		$result	= $db->loadObject();

		return $result;
	}

	function _getWPTerms( $wpTBPrex = '', $postId, $type)
	{
	    $db		= EasyBlogHelper::db();

		$query   = 'select distinct a.`name` as `title`, a.`slug` as `alias`, 1 as `published` from `#__wp_'.$wpTBPrex.'terms` as a';
		$query  .= '  inner join `#__wp_term_'.$wpTBPrex.'taxonomy` as b on a.`term_id` = b.`term_id`';
		$query  .= '  inner join `#__wp_term_'.$wpTBPrex.'relationships` as c on b.`term_taxonomy_id` = c.`term_taxonomy_id`';
		$query  .= ' where c.`object_id` = ' . $db->Quote($postId);
		$query  .= ' and b.`taxonomy` = ' . $db->Quote($type);

		$db->setQuery($query);

		$result = '';
		if( $type == 'category')
		{
			// always load one category bcos easyblog only support one category.
			$result = $db->loadObject();
		}
		else
		{
		    //tags
		    $result = $db->loadObjectList();
		}
	    return $result;
	}

	function _processMyBlog( $myBlogSection , $jomcomment = false )
	{
	    $db			= EasyBlogHelper::db();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');

		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$query	= 'SELECT * FROM `#__content` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_myblog');
		$query	.= ' )';
		$query	.= ' AND a.`sectionid` = ' . $db->Quote($myBlogSection);
		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{

			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status4', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ejax->script("divSrolltoBottomMyblog();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOGS') . ': ' . $migrateStat->blog . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOG_USER') . ' \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status4', $stat);

			$ejax->script("$( '#migrator-submit4' ).html('" . JText::_( 'COM_EASYBLOG_MIGRATOR_MIGRATION_COMPLETED' ) . "');");
			$ejax->script("$( '#migrator-submit4' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait4' ).css( 'display' , 'none' );");

		}
		else
		{
			// here we should process the migration

			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.

			$date           = EB::date();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			if(! empty($row->catid))
			{

			    $joomlaCat  = $this->_getJoomlaCategory($row->catid);

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
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $row->title;
			$blogObj->permalink		= ( empty($row->alias) ) ? EasyBlogHelper::getPermalink($row->title) : $row->alias;

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

			//translating the article state into easyblog publish status.
			$blogState  = '';
			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
			    $blogState  = ($row->state == 2 || $row->state == -2) ? 0 : $row->state;
			}
			else
			{
			    $blogState  = ($row->state == -1) ? 0 : $row->state;
			}

			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $row->publish_up )? $row->publish_up : $date->toMySQL();
			$blogObj->publish_down	= !empty( $row->publish_down )? $row->publish_down : $date->toMySQL();

			$blogObj->ordering		= $row->ordering;
			$blogObj->hits			= $row->hits;
			$blogObj->frontpage     = 1;

			$blog->bind($blogObj);
			$blog->store();

			// Run jomcomment migration here.
			if( $jomcomment )
			{
				$this->migrateJomcomment( $row->id , $blog->id , 'com_myblog' );
			}

			//migrate meta description
			$this->_migrateContentMeta($row->metakey, $row->metadesc, $blog->id);

			//map myblog tags into EasyBlog tags.
			$query  = 'SELECT a.*, b.`name`, b.`slug` FROM `#__myblog_content_categories` as a INNER JOIN `#__myblog_categories` as b';
			$query  .= ' ON a.`category` = b.`id`';
			$query  .= ' WHERE a.`contentid` = ' . $db->Quote($row->id);
			$db->setQuery($query);

			$myblogTags = $db->loadObjectList();

			if(count($myblogTags) > 0)
			{
			    foreach($myblogTags as $item)
			    {
				    $now    = EB::date();
					$tag	= EB::table('Tag');

					if( $tag->exists( $item->name ) )
					{
					    $tag->load( $item->name, true);
					}
					else
					{
					    $tagArr = array();
					    $tagArr['created_by']  	= $this->_getSAUserId();
					    $tagArr['title']  		= $item->name;
					    $tagArr['alias']  		= $item->slug;
					    $tagArr['published']  	= '1';
					    $tagArr['created']     	= $now->toMySQL();

                        $tag->bind($tagArr);
					    $tag->store();
					}

					$postTag	= EB::table('PostTag');
					$postTag->tag_id	= $tag->id;
					$postTag->post_id	= $blog->id;
					$postTag->created	= $now->toMySQL();
					$postTag->store();

			    }
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
			$migrator->component    = 'com_myblog';
			$migrator->store();

			$ejax->append('progress-status4', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_MYBLOG') . ': ' . $row->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_processMyBlog','$myBlogSection','$jomcomment');");

		}

		$ejax->send();

	}

	function _processLyftenBloggie( $migrateComment )
	{
	    $db			= EasyBlogHelper::db();
	    $jSession 	= JFactory::getSession();
		$ejax		= new EJax();

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
			$this->_migrateContentMeta($row->metakey, $row->metadesc, $blog->id);


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


	function _processSmartBlog($migrateComment, $migrateImage, $imagePath)
	{

		$db			= EasyBlogHelper::db();
		$jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		//check if com_blog installed.
		if(! JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_blog' . DIRECTORY_SEPARATOR . 'blog.php'))
		{
		    $ejax->append('progress-status2', JText::_('COM_EASYBLOG_MIGRATOR_SMARTBLOG_ERROR'));
			$ejax->script("$( '#migrator-submit2' ).html('" . JText::_( 'COM_EASYBLOG_MIGRATOR_ABORTED' ) . "');");
			$ejax->script("$( '#migrator-submit2' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait2' ).css( 'display' , 'none' );");
			$ejax->send();
			exit;
		}

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

		$query	= 'SELECT * FROM `#__blog_postings` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_blog');
		$query	.= ' )';
		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{
			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status2', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ejax->script("divSrolltoBottomSmartBlog();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOGS') . ': ' . $migrateStat->blog . '<br />';
			$stat  .= 'Total comments migrated : ' . $migrateStat->comments . '<br />';
			$stat  .= 'Total images migrated : ' . $migrateStat->images . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat  .= 'Total user\'s contribution: ' . count($statUser) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_BLOG_USER') . ' \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status2', $stat);

			$ejax->script("$( '#migrator-submit2' ).html('" . JText::_( 'COM_EASYBLOG_MIGRATOR_MIGRATION_COMPLETED' ) . "');");
			$ejax->script("$( '#migrator-submit2' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait2' ).css( 'display' , 'none' );");

		}
		else
		{
			// here we should process the migration
			// step 1 : create user if not exists in eblog_users - create user through profile jtable load method.
			// step 2 : migrate image files.
			//      step 2.1: create folder if not exist.
			// step 3: migrate comments if needed.

			$date           = EB::date();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			//load user profile
			$profile	= EB::user($row->user_id);

			$blog		= EB::table('Blog');

			//assigning blog data
			$blogObj->created_by	= $profile->id;
			$blogObj->created 		= !empty( $row->post_date ) ? $row->post_date : $date->toMySQL();
			$blogObj->modified		= !empty( $row->post_update ) ? $row->post_update : $date->toMySQL();

			$blogObj->title			= $row->post_title;
			$blogObj->permalink		= EasyBlogHelper::getPermalink( $row->post_title );


			$blogObj->intro			= '';
			$blogObj->content		= $row->post_desc;


			$blogObj->published		= $row->published;
			$blogObj->publish_up 	= !empty( $row->post_date ) ? $row->post_date : $date->toMySQL();
			$blogObj->publish_down	= '0000-00-00 00:00:00';

			$blogObj->hits			= $row->post_hits;
			$blogObj->frontpage     = 1;

			$blog->bind($blogObj);

			//step 2
			$imageMigrated  = false;
			if($migrateImage)
			{
			    $newImagePath   = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images';
			    if(! empty($imagePath))
			    {
			        $tmpimagePath	= str_ireplace('/', DIRECTORY_SEPARATOR,  $imagePath);
			        $newImagePath   .= DIRECTORY_SEPARATOR . $tmpimagePath;
			        $newImagePath   = JFolder::makeSafe($newImagePath);
			    }

			    if(! JFolder::exists($newImagePath))
			    {
			        JFolder::create($newImagePath);
			    }

			    $src	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_blog' . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . 'blogimages' . DIRECTORY_SEPARATOR . 'th'.$row->post_image;
			    $dest	= $newImagePath . DIRECTORY_SEPARATOR . $row->post_image;


			    if(JFile::exists($src))
			    {
			        $imageMigrated	= JFile::copy($src, $dest);
			    }
			}

			if($imageMigrated)
			{
			    $destSafeURL	= str_ireplace(DIRECTORY_SEPARATOR, '/',  $imagePath);
			    $destSafeURL    = 'images/' . $destSafeURL . '/' . $row->post_image;

			    $imageContent	= '<p><img style="padding:0px 10px 10px 0px;" align="left" src="' . $destSafeURL. '" border="0" /> </p>';
			    $blog->content  = $imageContent . $blog->content;
			    $migrateStat->images++;
			}

			$blog->store();

			// step 3
			if($migrateComment)
			{

			    //required frontend model file.
			    require_once (JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'comment.php');
				$model	= new EasyBlogModelComment();

				$queryComment  = 'SELECT * FROM `#__blog_comment` WHERE `post_id` = ' . $db->Quote($row->id);
				$queryComment  .= ' ORDER BY `id`';
				$db->setQuery($queryComment);
				$resultComment  = $db->loadObjectList();


				if(count($resultComment) > 0)
				{
				    foreach($resultComment as $itemComment)
				    {
				    	//load user profile
						$commentor	= EB::user($itemComment->user_id);

						$user   = JFactory::getUser($itemComment->user_id );

	    				$now	= EB::date();
						$commt	= EB::table('Comment');


						$commt->post_id      = $blog->id;
						$commt->comment      = $itemComment->comment_desc;
						$commt->title        = $itemComment->comment_title;

						$commt->name         = $user->name;
						$commt->email        = $user->email;
						$commt->url          = $commentor->url;
						$commt->created_by   = $itemComment->user_id;
						$commt->created      = $itemComment->comment_date;
						$commt->published    = $itemComment->published;


						//adding new comment
						$latestCmmt	= $model->getLatestComment($blog->id, '0');
						$lft	= 1;
						$rgt	= 2;

						if(! empty($latestCmmt))
						{
						 	$lft	= $latestCmmt->rgt + 1;
						 	$rgt	= $latestCmmt->rgt + 2;

						 	$model->updateCommentSibling($blog->id, $latestCmmt->rgt);
						}

						$commt->lft          = $lft;
						$commt->rgt          = $rgt;

						$commt->store();

						//update state
						$migrateStat->comments++;

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
			$migrator->component    = 'com_blog';
			$migrator->store();

			$ejax->append('progress-status2', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_SMARTBLOG') . ': ' . $row->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ' :' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_processSmartBlog','$migrateComment', '$migrateImage', '$imagePath');");

		}

		$ejax->send();

	}

	function _process($authorId, $stateId, $catId, $sectionId, $myblogSection , $jomcomment = false )
	{
		$db			= EasyBlogHelper::db();
		$jSession 	= JFactory::getSession();
		$ejax		= new EJax();

		$migrateStat	= $jSession->get('EBLOG_MIGRATOR_JOOMLA_STAT', '', 'EASYBLOG');
		if(empty($migrateStat))
		{
			$migrateStat    		= new stdClass();
			$migrateStat->blog  	= 0;
			$migrateStat->category	= 0;
			$migrateStat->user      = array();
		}

		$query	= 'SELECT * FROM `#__content` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_content');
		$query	.= ' )';
		if($authorId != '0')
			$query	.= ' AND a.`created_by` = ' . $db->Quote($authorId);

		if($stateId != '*')
		{
			switch($stateId)
			{
				case 'P':
					$query	.= ' AND a.`state` = ' . $db->Quote('1');
					break;
				case 'U':
					$query	.= ' AND a.`state` = ' . $db->Quote('0');
					break;
				case 'A':
					$query	.= ' AND a.`state` = ' . $db->Quote('-1');
					break;

				// joomla 1.6 compatibility
				case '1': // publish
					$query	.= ' AND a.`state` = ' . $db->Quote('1');
					break;
				case '0': //unpublish
					$query	.= ' AND a.`state` = ' . $db->Quote('0');
					break;
				case '2': // archive
					$query	.= ' AND a.`state` = ' . $db->Quote('2');
					break;
				case '-2': // trash
					$query	.= ' AND a.`state` = ' . $db->Quote('-2');
					break;

				default:
					break;
			}
		}
		if($sectionId != '-1')
			$query	.= ' AND a.`sectionid` = ' . $db->Quote($sectionId);

		// we do not want the myblog post process here.
		if($myblogSection != '')
			$query	.= ' AND a.`sectionid` != ' . $db->Quote($myblogSection);

		if($catId != '0')
			$query	.= ' AND a.`catid` = ' . $db->Quote($catId);

		$query	.= ' ORDER BY a.`id` LIMIT 1';

		$db->setQuery($query);
		$row	= $db->loadObject();

		if(is_null($row))
		{
			//at here, we check whether there are any records processed. if yes,
			//show the statistic.
			$ejax->append('progress-status', JText::_('COM_EASYBLOG_MIGRATOR_FINISHED'));
			$ejax->script("divSrolltoBottom();");

			//update statistic
			$stat   = '========================================== <br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_TOTAL_ARTICLE') . ' : ' . $migrateStat->blog . '<br />';
			$stat  .= JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_TOTAL_CATEGORY') . ' : ' . $migrateStat->category . '<br />';

			$statUser   = $migrateStat->user;
			if(! empty($statUser))
			{
			    $stat  .= '<br />';
			    $stat .= JText::_( 'COM_EASYBLOG_MIGRATOR_TOTAL_USER_CONTRIBUTIONS' ) . ' : ' . count( $statUser ) . '<br />';

			    foreach($statUser as $eachUser)
			    {
			        $stat   .= JText::_('COM_EASYBLOG_MIGRATOR_TOTAL_ARTICLES_USER') . ' \'' . $eachUser->name . '\': ' . $eachUser->blogcount . '<br />';
			    }
			}
			$stat   .= '<br />==========================================';
			$ejax->assign('stat-status', $stat);

			$ejax->script("$( '#migrator-submit' ).html('" . JText::_( 'COM_EASYBLOG_MIGRATOR_MIGRATION_COMPLETED' ) . "');");
			$ejax->script("$( '#migrator-submit' ).attr('disabled' , '');");
			$ejax->script("$( '#icon-wait' ).css( 'display' , 'none' );");
		}
		else
		{
			// here we should process the migration

			// step 1 : create categery if not exist in eblog_categories
			// step 2 : create user if not exists in eblog_users - create user through profile jtable load method.

			$date           = EB::date();
			$blogObj    	= new stdClass();

			//default
			$blogObj->category_id   = 1;  //assume 1 is the uncategorized id.

			if(! empty($row->catid))
			{

			    $joomlaCat  = $this->_getJoomlaCategory($row->catid);

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
			$blogObj->modified		= $date->toMySQL();

			$blogObj->title			= $row->title;
			$blogObj->permalink		= $row->alias;

			// Need to remap the access.
			$access					= 0;

            if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
            {
				switch($row->access)
				{
				    case 1:
				        $access = 0;
				        break;
				    default:
				        $access = 1;
				        break;
				}
			}
			else
			{
			   	$access = ($row->access == 2) ? 1 : $row->access;
			}

			$blogObj->access		= $access;
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

			// joomla 3.0 intro imag properties.
			if( isset($row->images) && !empty($row->images) )
			{
				$joomlaImages  = json_decode( $row->images );

				if(isset($joomlaImages->image_intro) and !empty($joomlaImages->image_intro))
				{
					$imgTag = '<img';
					if ($joomlaImages->image_intro_caption)
					{
						$imgTag .= ' class="caption" title="' . htmlspecialchars($joomlaImages->image_intro_caption) .'"';
					}
					$imgTag .= ' src="' . htmlspecialchars($joomlaImages->image_intro) . '" alt="' . htmlspecialchars($joomlaImages->image_intro_alt) . '"/>';

					$blogObj->intro = $imgTag . $blogObj->intro;
				}

				if(isset($joomlaImages->image_fulltext) and !empty($joomlaImages->image_fulltext))
				{
					$imgTag = '<img';
					if ($joomlaImages->image_fulltext_caption)
					{
						$imgTag .= ' class="caption" title="' . htmlspecialchars($joomlaImages->image_fulltext_caption) .'"';
					}
					$imgTag .= ' src="' . htmlspecialchars($joomlaImages->image_fulltext) . '" alt="' . htmlspecialchars($joomlaImages->image_fulltext_alt) . '"/>';

					$blogObj->content = $imgTag . $blogObj->content;
				}

			}


			//translating the article state into easyblog publish status.
			$blogState  = '';
			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
			    $blogState  = ($row->state == 2 || $row->state == -2) ? 0 : $row->state;
			}
			else
			{
			    $blogState  = ($row->state == -1) ? 0 : $row->state;
			}

			$blogObj->published		= $blogState;
			$blogObj->publish_up 	= !empty( $row->publish_up )? $row->publish_up : $date->toMySQL();
			$blogObj->publish_down	= !empty( $row->publish_down )? $row->publish_down : $date->toMySQL();

			$blogObj->ordering		= $row->ordering;
			$blogObj->hits			= $row->hits;
			$blogObj->frontpage     = 1;

			$blog->bind($blogObj);
			$blog->store();

			// Run jomcomment migration here.
			if( $jomcomment )
			{
				$this->migrateJomcomment( $row->id , $blog->id , 'com_content' );
			}

			//migrate meta description
			$this->_migrateContentMeta($row->metakey, $row->metadesc, $blog->id);


			if ($row->featured) {
		      	// just call the model file will do as we do not want to create stream on featured action at this migration.
				$modelF = EB::model('Featured');
				$modelF->makeFeatured('post', $blog->id);
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
			$migrator->component    = 'com_content';
			$migrator->store();


			$ejax->append('progress-status', JText::_('COM_EASYBLOG_MIGRATOR_MIGRATED_JOOMLA_ARTICLE') . ': ' . $row->id . JText::_('COM_EASYBLOG_MIGRATOR_EASYBLOG') . ': ' . $blog->id . '<br />');
			$ejax->script("ejax.load('migrators','_process','$authorId', '$stateId', '$catId', '$sectionId', '$myblogSection','$jomcomment');");
		}
		$ejax->send();
	}

	function _getJoomlaCategory( $catId )
	{
	    $db = EasyBlogHelper::db();

	    $query  = 'select * from `#__categories` where `id` = ' . $db->Quote($catId);
	    $db->setQuery($query);
	    $result = $db->loadObject();

	    return $result;
	}

	public function _getK2Category( $catId )
	{
		$db = EasyBlogHelper::db();

		$query  = 'SELECT * FROM `#__k2_categories` where `id` = ' . $db->Quote($catId);
		$db->setQuery($query);
		$result = $db->loadObject();

		// Mimic Joomla's category behavior
		if( $result )
		{
			$result->title	= $result->name;
		}
		return $result;
	}

	function _isEblogCategoryExists( $joomlaCatObj )
	{
	    $db = EasyBlogHelper::db();

	    $query  = 'select id from `#__easyblog_category`';
		$query	.= ' where lower(`title`) = ' . $db->Quote(JString::strtolower($joomlaCatObj->title));
		$query  .= ' OR lower(`alias`) = ' . $db->Quote(JString::strtolower($joomlaCatObj->alias));
		$query  .= ' LIMIT 1';

	    $db->setQuery($query);
	    $result = $db->loadResult();

	    if(empty($result))
	        return false;
	    else
	        return $result;
	}

	function _createEblogCategory($joomlaCatObj)
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

	    $category	= EB::table('Category');

	    $arr    = array();
	    $arr['created_by']  = $this->_getSAUserId();
	    $arr['title']  		= $joomlaCatObj->title;
	    $arr['alias']  		= $joomlaCatObj->alias;
	    $arr['published']  	= ( isset($joomlaCatObj->published) ) ? $joomlaCatObj->published : 1;

	    $category->bind($arr);
	    $category->store();

	    //update session value
	    $migrateStat->category++;
	    $jSession->set('EBLOG_MIGRATOR_JOOMLA_STAT', $migrateStat, 'EASYBLOG');

	    return $category->id;
	}

	function _getSAUserId()
	{
		$saUserId   = '62';
		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsers	= EasyBlogHelper::getSAUsersIds();

			$saUserId = '42';
			if(count($saUsers) > 0)
			{
			    $saUserId = $saUsers['0'];
			}
		}
		return $saUserId;
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

	function _migrateContentMeta($metaKey, $metaDesc, $blogId)
	{
	    $db 	= EasyBlogHelper::db();

	    if(empty($metaKey) && empty($metaDesc))
	    {
			return true;
	    }

	    $meta				= EB::table('Meta');
	    $meta->keywords		= $metaKey;
	    $meta->description	= $metaDesc;
	    $meta->content_id	= $blogId;
	    $meta->type			= 'post';
		$meta->store();

		return true;
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

	public function migrateK2Comments( $k2obj , $blog )
	{
		$db			= EasyBlogHelper::db();
		$jSession 	= JFactory::getSession();

		$query	= 'SELECT * FROM `#__k2_comments` AS a';
		$query	.= ' WHERE NOT EXISTS (';
		$query	.= ' SELECT content_id FROM `#__easyblog_migrate_content` AS b WHERE b.`content_id` = a.`id` and `component` = ' . $db->Quote('com_k2.comments');
		$query	.= ' ) ';
		$query	.= 'AND a.' . $db->nameQuote( 'itemID' ) . ' = ' . $db->Quote( $k2obj->id ) . ' ORDER BY a.`id` ASC';

		$db->setQuery( $query );

		$comments	= $db->loadObjectList();

		if( !$comments )
		{
			return;
		}

		$lft		= 1;
		$rgt		= 2;

		foreach( $comments as $comment )
		{
	        $post				= array();
	        $post['id'] 		= $blog->id;
	        $post['comment']    = $comment->commentText;
			$post['name']       = $comment->userName;

			// @rule: Since K2 does not store any title for comments, we just leave this blank.
			$post['title']      = '';

			$post['email']      = $comment->commentEmail;
			$post['url']        = $comment->commentURL;

            $table		= EB::table('Comment');
            $table->bindPost($post);

            //the rest info assign here.
            $table->lft   		= $lft;
			$table->rgt			= $rgt;

			$table->created_by 	= $comment->userID;
			$table->created    	= $comment->commentDate;
			$table->modified	= $comment->commentDate;
			$table->published  	= $comment->published;

            $table->store();

			//log the entry into migrate table.
			$migrator = EB::table('Migrate');

			$migrator->content_id	= $comment->id;
			$migrator->post_id		= $table->id;
			$migrator->session_id	= $jSession->getToken();
			$migrator->component    = 'com_k2.comments';
			$migrator->store();

	        //do not touch this settings!
	        $lft    = $rgt + 1;
	        $rgt    = $lft + 1;
		}
	}

	public function migrateJomcomment( $contentId , $blogId , $option )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__jomcomment' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'contentid' ) . ' = ' . $db->Quote( $contentId ) . ' '
				. 'AND ' . $db->nameQuote( 'option' ) . ' = ' . $db->Quote( $option ) . ' '
				. 'ORDER BY `id` ASC';

		$db->setQuery( $query );
		$comments	= $db->loadObjectList();


		if( !$comments )
		{
			return;
		}


		$lft		= 1;
		$rgt		= 2;

		foreach( $comments as $comment )
		{
	        $post				= array();

	        $post['id'] 		= $blogId;
	        $post['comment']    = $comment->comment;
			$post['name']       = $comment->name;
			$post['title']      = $comment->title;
			$post['email']      = $comment->email;
			$post['url']        = $comment->website;

            $table		= EB::table('Comment');
            $table->bindPost($post);

            //the rest info assign here.
            $table->lft   		= $lft;
            $table->rgt			= $rgt;
            $table->ip   		= $comment->ip;
            $table->created_by 	= $comment->user_id;
            $table->created    	= $comment->date;
            $table->modified	= $comment->date;
            $table->published  	= $comment->published;
            $table->ordering   	= $comment->ordering;
            $table->vote       	= $comment->voted;

            $table->store();

	        //do not touch this settings!
	        $lft    = $rgt + 1;
	        $rgt    = $lft + 1;
		}
	}

	function _migrateJCommentIntoEasyBlog($contentId, $blogId, $contentGroup)
	{
        $db 	= EasyBlogHelper::db();

        $query  = 'select * from `#__jcomments`';
		$query	.= ' where `object_id` = ' . $db->Quote($contentId);
		$query  .= ' and `object_group` = ' . $db->Quote($contentGroup);
		$query  .= ' order by `id` asc';

		$db->setQuery($query);

		$results    = $db->loadObjectList();

		$lft    = 1;
		$rgt    = 2;

		for($i = 0; $i < count($results); $i++)
		{
		    $itemComment   = $results[$i];

			$commt		= EB::table('Comment');
			$now		= EB::date();

			$commt->post_id      = $blogId;
			$commt->comment      = $itemComment->comment;
			$commt->title        = $itemComment->title;

			$commt->name         = $itemComment->name;
			$commt->email        = $itemComment->email;
			$commt->url          = $itemComment->homepage;
			$commt->created_by   = $itemComment->userid;
			$commt->created      = $itemComment->date;
			$commt->published    = $itemComment->published;

			$commt->lft          = $lft;
			$commt->rgt          = $rgt;

			$commt->store();

			//update state
			$migrateStat->comments++;

		    // next set of siblings
		    $lft    = $rgt + 1;
		    $rgt    = $lft + 1;
		}

		return true;
	}

	function _migrateSimpleJACommentIntoEasyBlog( $contentId, $blogId, $contentGroup )
	{
		$db = EasyBlogHelper::db();

		$query	= 'SELECT * FROM `#__jacomment_itmes`';
		$query	.= ' WHERE `option` = ' . $db->quote( $contentGroup );
		$query	.= ' AND `contentid` = ' . $db->quote( $contentId );
		$query	.= ' ORDER BY `date`';
		$db->setQuery($query);
		$results	= $db->loadObjectList();

		$lft	= 1;
		$rgt	= 2;

		foreach( $results as $result )
		{
			$new = EB::table('Comment');

			$new->post_id		= $blogId;
			$new->comment		= $parent->comment;
			$new->title			= $parent->contenttitle;

			$new->name			= $parent->name;
			$new->email			= $parent->email;
			$new->url			= $parent->website;
			$new->created_by	= $parent->userid;
			$new->created		= $parent->date;
			$new->published		= $parent->published;
			$new->ip			= $parent->ip;

			$new->lft			= $lft;
			$new->rgt			= $rgt;

			$new->store();

			$lft = $rgt + 1;
			$rgt = $lft + 1;
		}

		return true;
	}

	function _migrateJACommentIntoEasyBlog( $contentId, $blogId, $contentGroup )
	{
		// contentId is the id from JA Comment table
		// blogId is the id from EasyBlog
		// contentGroup is the target component in JA Comment table

		$db = EasyBlogHelper::db();

		// get all parent first
		$query = 'SELECT * FROM `#__jacomment_itmes`';
		$query .= ' WHERE `option` = ' . $db->quote( $contentGroup );
		$query .= ' AND `contentid` = ' . $db->quote( $contentId );
		$query .= ' AND `parentid` = ' . $db->quote( 0 );
		$query .= ' ORDER BY `date`';
		$db->setQuery($query);
		$parents	= $db->loadObjectList();

		$break = 0;

		// migrate parent by parent
		foreach( $parents as $parent  )
		{
			$insertNode = false;

			$base = 1;

			if( $break == 0 )
			{
				// check for insertion node based on date
				$query  = 'SELECT * FROM `#__easyblog_comment`';
				$query .= ' WHERE `post_id` = ' . $db->quote( $blogId );
				$query .= ' AND `created` > ' . $db->quote( $parent->date );
				$query .= ' AND `parent_id` = ' . $db->quote( 0 );
				$query .= ' ORDER BY `created` LIMIT 1';
				$db->setQuery( $query );
				$insertNode = $db->loadObject();

				// if insert node is found, means that there are existing comment with date earlier than current comment, and hence need to insert current comment before the existing comment
				if( $insertNode )
				{
					// set the base of the current comment to the existing comment
					$base = $insertNode->lft;
					$diff = 2;

					// push all subsequent comment downwards
					$this->pushCommentDown( $blogId, $base, $diff );
				}
				else
				{
					// no insert node found, means that all comments are now later than existing comments
					// set break == 1, this means all subsequent parents does not need to check insert node
					$break = 1;

					// get the last rgt in kmt to append subsequent comments
					$model = EB::model( 'comment' );
					$latestComment = $model->getLatestComment( $blogId );

					if( $latestComment )
					{
						$base = $latestComment->rgt + 1;
					}
				}
			}

			// if ultimately no insertion and no latest comment found on that blog post, which means no comment exist on that blog post, then $base will be defaulted to 1

			// reset it to parent_id = 0 since this section is migrating all parent comment
			$parent->parentid = 0;

			// set lft rgt appropriately based on deduced $base
			$parent->lft = $base;
			$parent->rgt = $base + 1;

			// store the comement
			$new = EB::table('Comment');
			$new->post_id		= $blogId;
			$new->comment		= $parent->comment;
			$new->title			= $parent->contenttitle;
			$new->parent_id		= $parent->parentid;
			$new->name			= $parent->name;
			$new->email			= $parent->email;
			$new->url			= $parent->website;
			$new->created_by	= $parent->userid;
			$new->created		= $parent->date;
			$new->published		= $parent->published;
			$new->ip			= $parent->ip;
			$new->lft			= $parent->lft;
			$new->rgt			= $parent->rgt;

			$new->store();

			// oldId is $parent->id
			$oldId = $parent->id;
			// newId is $new->id
			$newId = $new->id;

			// start migrating children for this parent block
			// the oldId will be used to find the direct child comments with oldId as parent id
			// the newId will be used as the child's new parent id
			$this->_migrateJACommentChildIntoEasyBlog( $blogId, $oldId, $newId );
		}
	}

	private function _migrateJACommentChildIntoEasyBlog( $blogId, $oldId, $newId )
	{
		$db = EasyBlogHelper::db();

		// get all direct child comments with $oldId as parent
		$query = 'SELECT * FROM `#__jacomment_itmes`';
		$query .= ' WHERE `parentid` = ' . $db->quote( $oldId );
		$query .= ' ORDER BY `date`';
		$db->setQuery( $query );
		$children = $db->loadObjectList();

		// migrate child by child
		foreach( $children as $child )
		{
			// set the node to the parent as default
			$parent = EB::table('Comment');
			$parent->load( $newId );
			$node = $parent->lft;

			// check and see if the parent comment (has already migrated to easyblog) contains any child comment existing or not
			// if this parent comment contains child comment, then use the latest child comment's as node
			$model = EB::model( 'comments' );
			$latest = $model->getLatestComment( $blogId, $newId );
			if( !empty( $latest ) )
			{
				$node = $latest->rgt;
			}

			// update all sibling to make room for this comment
			$model->updateCommentSibling( $blogId, $node );

			// set the lft rgt value for this comment
			$child->lft = $node + 1;
			$child->rgt = $node + 2;

			// store the comment
			$new		= EB::table('Comment');
			$new->post_id		= $blogId;
			$new->parent_id		= $newId;
			$new->comment		= $child->comment;
			$new->title			= $child->contenttitle;
			$new->name			= $child->name;
			$new->email			= $child->email;
			$new->url			= $child->website;
			$new->created_by	= $child->userid;
			$new->created		= $child->date;
			$new->published		= $child->published;
			$new->ip			= $child->ip;
			$new->lft			= $child->lft;
			$new->rgt			= $child->rgt;
			$new->store();

			// oldId is $child->id
			$oldId = $parent->id;
			// newId is $new->id
			$newId = $new->id;

			// check for any direct child comment for this child
			// the oldId will be used to find the direct child comments with oldId as parent id
			// the newId will be used as the child's new parent id
			$this->_migrateJACommentChildIntoEasyBlog( $blogId, $oldId, $newId );
		}
	}

	private function pushCommentDown( $blogId, $base, $diff )
	{
		// this will push comments down to make space for inserting comments above the lft = $base comment
		$db = EasyBlogHelper::db();

		$query  = 'UPDATE `#__easyblog_comment`';
		$query .= ' SET `lft` = `lft` + ' . $diff . ', `rgt` = `rgt` + ' . $diff;
		$query .= ' WHERE `post_id` = ' . $db->quote( $blogId );
		$query .= ' AND `lft` >= ' . $base;

		$db->setQuery( $query );
		return $db->query();
	}
}
