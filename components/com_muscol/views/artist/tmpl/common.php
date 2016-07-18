<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

$user = JFactory::getUser();
	
	if($this->params->get('showletternavigation')){
		echo MusColHelper::letter_navigation($this->artist->letter);
	}
	$itemid = $this->params->get('itemid');
	if($itemid != "") $itemid = "&Itemid=" . $itemid;
	
	$view = JRequest::getVar('view') ;

echo MusColHelper::edit_button_artist($this->artist->id); ?>


<div class="page-header">
<div class="pull-right">
  <?php if($this->params->get('showpdficon')){ ?>
  <?php 

			$attr = array("title" => "PDF");
			$link_pdf = JRoute::_('index.php?option=com_muscol&view=artist&format=ownpdf&id=' . $this->artist->id);
			$pdf_icon = JHTML::image('components/com_muscol/assets/images/page_white_acrobat.png',"PDF", array("title" => "PDF"));

			?>
  <a href="<?php echo $link_pdf; ?>" target="_blank"><?php echo $pdf_icon; ?></a>
  <?php }  ?>
  <?php if($this->params->get('showchangetemplate') && $view == 'artist') echo $this->change_layout(); 

  ?> 
  </div>
  <h1 class="nom_artista">
    <?php if($this->artist->image) : 
					$image_attr = array(
								"style" => "max-height:60px;" ,
								"title" => $this->artist->artist_name
								);
					$artist_image = JHTML::image('images/artists/' . $this->artist->image , $this->artist->artist_name , $image_attr );
			?>
    <?php echo $artist_image; ?>
    <?php else : echo $this->artist->artist_name; endif; 

    ?>
  </h1>
  
</div>
<div class="row-fluid">
  <div class="span4">
    <?php
    //new plugin access
    $dispatcher = JDispatcher::getInstance();
    $plugin_ok = JPluginHelper::importPlugin('muscol');
    $results = $dispatcher->trigger('onDisplayArtist', array ($this->artist->id));
    ?>

    <?php if($this->artist->picture || $this->artist->city || $this->artist->country || isset($this->artist->genre_name) || $this->artist->years_active || $this->artist->url ){ ?>
    <div class=" well well-small">
      <?php 

		if($this->artist->picture) echo MusColHelper::createThumbnailArtist($this->artist->picture, $this->artist->artist_name, $this->params->get('thumb_size_artist_profile',200), array("class" => "artist_picture_profile span12") ) ;
 ?>
      <br />
      <?php if($this->artist->genre_name){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'GENRE' ); ?></strong> <span class="value_detailed_album"><a href="<?php echo MusColHelper::get_genre_link($this->artist->genre_id); ?>"><?php echo $this->artist->genre_name; ?></a></span><br />
      <?php } ?>
      <?php if($this->artist->creation_year){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'CREATION_YEAR' ); ?></strong> <span class="value_detailed_album"><?php echo $this->artist->creation_year; ?></span><br />
      <?php } ?>
      <?php if($this->artist->city){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'CITY' ); ?></strong> <span class="value_detailed_album"><?php echo $this->artist->city; ?></span><br />
      <?php } ?>
      <?php if($this->artist->country){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'COUNTRY' ); ?></strong> <span class="value_detailed_album"><?php echo $this->artist->country; ?></span><br />
      <?php } ?>
      <?php if($this->artist->years_active){ ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'YEARS_ACTIVE' ); ?></strong> <span class="value_detailed_album"><?php echo $this->artist->years_active; ?></span><br />
      <?php } ?>
      <?php if($this->artist->url){ 
		
		if(substr($this->artist->url, 0, 4) != "http") $theurl = "http://" . $this->artist->url ;
		else $theurl =  $this->artist->url ;
		
		?>
      <strong class="label_detailed_album"><?php echo JText::_( 'WEB' ); ?></strong> <span class="value_detailed_album"><a href="<?php echo $theurl; ?>" rel="nofollow" target="_blank"><?php echo $this->artist->url; ?></a></span><br />
      <?php } ?>
      <?php if($this->params->get('showalbum_tags', 1) == 1 ){ 
       
        ?>
      <strong class="label_detailed_album"><?php echo JText::_( 'TAGS' ); ?></strong> <span class="value_detailed_album"><?php                     
      for($k=0;$k < count($this->artist->tags); $k++){ 
        if(isset($this->artist->tags[$k]->tag_name)){
         
          echo MusColHelper::renderTag($this->artist->tags[$k])." ";
        }
      
     } ?></span><br />
     <?php } ?>

    </div>
    <?php } ?>
    <div class="disc_details">
      <?php 		
		$modules = JModuleHelper::getModules("muscol_artist_stats");
		$document	=JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$attribs 	= array();
		$attribs['style'] = 'xhtml';
		foreach ( @$modules as $mod ) 
		{
			echo $renderer->render($mod, $attribs);
		}
		?>
    </div>
    <?php if($this->params->get('showartistbookmarks', 1) ){ // show the bookmark system ?>
    <div class="disc_details">
      <h3><?php echo JText::_('BOOKMARK'); ?></h3>
      <div class="album_rating"><?php echo MusColHelper::show_bookmarks(); ?></div>
    </div>
    <?php } ?>
  </div>
  <div class="span8">
    <?php if( $this->artist->review ): ?>
    <div class="review"> <?php echo $this->artist->review; ?> </div>
    <?php endif; ?>
    <?php if( $this->related ): ?>
    <div class="related">
      <?php
		$related = array();
		for ($i=0, $n=count( $this->related ); $i < $n; $i++){
			$related_link= JRoute::_( 'index.php?option=com_muscol&view=artist&id='. $this->related[$i]->id . $itemid);
			$related[] = "<a title='".$this->related[$i]->artist_name."' href='".$related_link."'>". $this->related[$i]->artist_name."</a>";
		}
		$related_string = implode(", ",$related);?>
      <?php echo JText::_( 'RELATED' ); ?>: <?php echo $related_string; ?> </div>
    <?php endif; ?>
    <?php if( $this->also_related ): ?>
    <div class="related">
      <?php
		$related = array();
		for ($i=0, $n=count( $this->also_related ); $i < $n; $i++){
			$related_link= JRoute::_( 'index.php?option=com_muscol&view=artist&id='. $this->also_related[$i]->id . $itemid);
			$related[] = "<a title='".$this->also_related[$i]->artist_name."' href='".$related_link."'>". $this->also_related[$i]->artist_name ."</a>";
		}
		$related_string = implode(", ",$related);?>
      <?php echo JText::_( 'ALSO_RELATED' ); ?>: <?php echo $related_string; ?> </div>
    <?php endif; ?>
    <?php 
	  
	  if($view == 'artist'){
	  
	  echo MusColHelper::artist_tabs($this->artist->id, 'albums'); ?>
    <div class="artist_main_content">
      <?php
			for ($i=0, $n=count( $this->albums ); $i < $n; $i++)	{
				$row =$this->albums[$i];
				if(count( $row->albums ) > 0){
					$image_attr = array(
										"title" => $row->format_name
										);
					$format_image = JHTML::image('images/formats/' . $row->icon , $row->format_name , $image_attr );
				?>
      <div class="format_title"><?php echo $format_image; ?> <span><?php echo $row->format_name; ?></span></div>
      <?php
					if($this->_layout == "grid") echo "<ul class='albums_grid'>" ;
					for ($j=0, $m=count( $row->albums ); $j < $m; $j++)	{
						$this->detail_album = $row->albums[$j];
						$this->i = $j ;
						echo $this->loadTemplate('album');
					}
					if($this->_layout == "grid") echo "</ul>" ;
					?>
      <br />
      <?php
				} // end if
			}

	  } // END if view == artist
	  else{
		  // we are seeing SONGS list
		  echo MusColHelper::artist_tabs($this->artist->id, 'songs');
		  ?>
      <div class="artist_main_content">
        <?php
		  
		  echo $this->loadTemplate('songs');
	  }
?>
      </div>
    
    <?php if($this->params->get('showartistcomments') ){ // show the comments 
		switch($this->params->get('commentsystem')){ 
			
			default: 
				if( $quants = count( $this->comments )){ ?>
    <div class="comments_title"><?php echo JText::_('COMMENTS'). " (". $quants .")"; ?></div>
    <div class="comments">
      <?php $k = 0; 
						foreach($this->comments as $comment){ ?>
      <div class="comment comment_<?php echo $k; ?>">
        <div class="comment_name"><?php echo $comment->username; ?></div>
        <div class="date"><?php echo JHTML::_('date', $comment->date, JText::_('DATE_FORMAT_LC2')); ?></div>
        <div class="comment_text"><?php echo $comment->comment; ?></div>
      </div>
      <?php $k = 1 - $k;
						} ?>
    </div>
    <?php } ?>
    <?php if($user->id){ ?>
    <div class="well well-small">
      <h3 class="post_comment_title"><?php echo JText::_('POST_A_COMMENT'); ?></h3>
    
      <?php $uri = JFactory::getURI(); ?>
      <form action="<?php echo JRoute::_('index.php'); ?>" method="post">
        <textarea name="comment" class="span12" rows="5"></textarea>
        <br />
        <input type="submit" class="btn" value="<?php echo JText::_('POST_COMMENT'); ?>" />
        <input type="hidden" name="album_id" value="<?php echo $this->artist->id; ?>" />
        <input type="hidden" name="task" value="save_comment" />
        <input type="hidden" name="comment_type" value="artist" />
        <input type="hidden" name="option" value="com_muscol" />
      </form>
    </div>
    <?php } ?>
    <?php break;
		}?>
    <?php } // end of show comments IF ?>
    <?php
		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('muscol');
		$results = $dispatcher->trigger('onCommentsArtist', array ($this->artist->id));
		?>
  </div></div>

<?php if($this->params->get('showhits') ){ ?>
<div align="center"> <?php echo JHTML::image('components/com_muscol/assets/images/hits.png',JText::_('HITS'), array("title" => JText::_('HITS'))); ?> <span class="num_hits"><?php echo $this->artist->hits; ?></span> </div>
<?php } ?>
<div align="center"><?php echo MusColHelper::showMusColFooter(); ?></div>
