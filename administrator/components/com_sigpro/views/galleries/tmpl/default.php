<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="sigTopWhiteFix"></div>
<div class="sigGrayFix"></div>

<div id="sigProModal">
   <a class="sig-icon sig-icon-cancel-circled sigProModalCloseButton"><i class="hidden"><?php echo JText::_('COM_SIGPRO_CLOSE'); ?></i></a>
   <?php if($this->type == 'k2'): ?>
   <h3 class="sigProNote"><?php echo JText::_('COM_SIGPRO_SELECT_A_K2_ITEM_TO_CREATE_THE_GALLERY'); ?></h3>
   <?php else: ?>
   <h3 class="sigProNote"><?php echo JText::_('COM_SIGPRO_ADD_A_GALLERY'); ?></h3>
   <span class="sigProSubNote"><?php echo JText::_('COM_SIGPRO_TYPE_A_FOLDER_NAME_TO_CREATE_THE_GALLERY'); ?></span>
   <?php endif; ?>
   <div class="sigProModalInner">
   	<?php if($this->permissions->create): ?>
     <iframe src="<?php echo $this->frameSrc; ?>" width="800" height="<?php echo $this->frameHeight; ?>" class="<?php echo $this->frameClass; ?>" frameborder="0"></iframe>
   	<?php endif; ?>
   </div>
</div>

<div id="sigPro" class="J<?php echo $this->version; if($this->tmpl == 'component')  { echo ' sigProModalSite'; } ?> sigProGalleries">
	<!--[if lt IE 8]>
	<div id="deprecatedOverlay"><div id="browserUpdateWrapper"><div id="frownie">:(</div><h1 id="sorry">We Are Sorry.</h1><h2 id="notSupported">Your web browser is not supported.</h2><div>Please <strong>upgrade</strong> your browser to it's latest version<br/>or <a href="http://browsehappy.com/" title="Upgrade your browser today!" target="_blank">install another one</a>.</div><div class="clr"></div><div id="modernBrowsers"><a href="http://www.mozilla.org/" title="Mozilla Firefox" target="_blank" id="ff"><span>Firefox</span></a><a href="https://www.google.com/chrome" title="Google Chrome" target="_blank" id="gc"><span>Chrome</span></a><a href="http://www.apple.com/safari/" title="Safari" target="_blank" id="as"><span>Safari</span></a><a href="http://www.opera.com/" title="Opera" target="_blank" id="op"><span>Opera</span></a><a href="http://windows.microsoft.com/en-US/internet-explorer/download-ie" title="Internet Explorer" target="_blank" id="ie"><span>IE</span></a></div></div></div>
	<![endif]-->

   	<div class="sigProHeader sigLight sigStartMenu sigProClearFix">
   		<div class="sigProTopRow"></div>
   		<div class="sigProLogo sigFloatLeft">
   			<i class="hidden"><?php echo JText::_('COM_SIGPRO'); ?></i>
   		</div>
   		<span class="sigMenuVersion"><?php echo JText::_('COM_SIGPRO_VERSION'); ?></span>
   		<?php echo $this->menu; ?>
   	</div>
   	    
    <div class="sigProClearFix"></div>
    
    <div id="adminFormContainer">
	    <form action="index.php" method="post" name="adminForm" id="adminForm">
			 
			<?php echo $this->sidebar; ?>
			 
		   	<div class="sigProToolbar sigTransition sigBoxSizing sigSlidingItem"> 
	  	  		<div class="sigProUpperToolbar">
	  	  			<h3 class="sigProPageTitle sigPurple"><?php echo strip_tags($this->title); ?></h3>
	  	  			<?php if($this->permissions->create): ?>
	  	  			<a href="#" onclick="Joomla.submitbutton('add'); return false;" class="sigProBtnadd"><?php echo JText::_('COM_SIGPRO_ADD_NEW'); ?></a>
	  	  			<?php endif; ?>
	  	  			<div class="sigFloatRight sigProOrdering">
	  	  				<label for="sorting"><?php echo JText::_('COM_SIGPRO_SORT'); ?>:</label>
	  	  				<?php echo $this->lists['sorting']; ?>
					</div>
				</div>
				
				<div class="sigProClearFix"></div>
				
				<div class="sigProLowerToolbar">
					<div class="sigFloatLeft sigThin sigPro50">
						<span id="selectedCount" class="sigPurple"></span> <?php echo JText::_('COM_SIGPRO_SELECTED'); ?> 
						<span id="selectedItem">
							<span id="sigSel1" class="hidden"><?php echo JText::_('COM_SIGPRO_ITEM'); ?></span>
							<span id="sigSel2" class="hidden"><?php echo JText::_('COM_SIGPRO_ITEMS'); ?></span>
						</span>
					</div>
					<?php if($this->permissions->delete): ?>
					<div class="sigFloatLeft sigPro50 sigProItemDeleteLinks">
						<a href="#" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('COM_SIGPRO_NO_ROWS_SELECTED', true); ?>');}else{if (confirm('<?php echo JText::_('COM_SIGPRO_YOU_ARE_GOING_TO_DELETE_PERMANENTLY_THE_SELECTED_FOLDERS_FROM_THE_SERVER_ARE_YOU_SURE', true); ?>')){Joomla.submitbutton('delete'); return false;}}" class="sig-icon-trash">Delete</a>
					</div>
					<?php endif; ?>
				</div>
			</div>
			
			<div class="sigProClearFix"></div>
	
	    	<div class="sigProGrid sigProClearFix sigTransition sigBoxSizing sigSlidingItem">
	    	     
	    	    <?php if ( $this->total == '0' ): ?>
	    	    <div class="sigProGalleryEmpty">
				   	<div class="sigProGalleryEmptyMessage blockIt">
				   		<?php if($this->permissions->create): ?>
				    	<h2 class="sigTextCenter"><?php echo JText::_('COM_SIGPRO_CLICK_ON_THE_ADD_GALLERY_BUTTON_TO_UPLOAD_SOME_GALLERIES'); ?></h2>
				    	<?php endif; ?>
					    <div class="sigProGalleryEmptyBody sigTextCenter">
					    	<div class="sig-icon hugeNoGalleryIcon sig-icon-picture"></div>
					    	<?php echo JText::_('COM_SIGPRO_THIS_SITE_IS_EMPTY'); ?>
					    </div>
				    </div>
				</div>
	    	   	<?php endif; ?>
	    	     
	            <?php $counter = 0; foreach ($this->rows as $row): ?>
	            <div class="sigProGallery sigProGridColumn">
	            	
	                <div class="sigProGalleryInner">
	                   <div class="sigProImageContainer">
	                   		<div class="sigProNumOfImages"><?php echo $row->numOfImages;?></div>
	                   		
	                   		<?php if($this->editorName): ?>
	                       	<div class="sigProGalleryPreviewImage sigCover sigProGalleryImageLink" style="background-image:url('<?php echo $row->url; ?>');"></div>
		                    
		                    <div class="sigProGalleryActions">	
		                    	<a class="sigProGalleryAction sigProInsertButton" title="<?php echo JText::_('COM_SIGPRO_INSERT'); ?>" data-path="<?php echo $row->insertPath; ?>" href="<?php echo $row->insertPath; ?>">
		                    		<span class="sigIcon sig-icon-login"></span>
		                    		<?php echo JText::_('COM_SIGPRO_INSERT'); ?>
		                    	</a>
	                   			<a class="sigProGalleryAction" href="<?php echo $row->link; ?>">
	                   				<span class="sigIcon sig-icon-eye"></span>
	                   				<?php echo JText::_('COM_SIGPRO_VIEW'); ?>
	                   			</a>
		                   	</div>
	                    	<?php else: ?>
	                     	<a class="sigProGalleryImageLink sigCover sigSafeTransition" href="<?php echo $row->link; ?>" style="background-image:url('<?php echo $row->url; ?>');">
	                    	</a>                   		                    		
	                    	<?php endif; ?>
	                    </div>
	                    <?php if($this->permissions->delete): ?>
	                   <div class="sigCheckWrapper">
		                   <label class="sigCheckbox"  for="cb<?php echo $counter; ?>">
		                   	<span class="sigIcon"></span><span class="sigIconFade sig-icon-check"></span>
		                   	<?php echo JHTML::_('grid.id', $counter, $row->folder, false, 'folder'); ?>
		                   </label>
	                   </div>
	                   <?php endif; ?>
	                   <div class="sigProGalleryTextWrapper">
		                   <div class="sigProGalleryTitle sigProCollapse">
		                        <a class="sigProGalleryLink" href="<?php echo $row->link; ?>"><?php echo $row->title; ?></a>
		                    </div>
		 
		                    <div class="sigProGalleryInfo">
		                        <div class="sigProGalleryPath sigProCollapse"><span><?php echo JText::_('COM_SIGPRO_PATH'); ?>:</span> <?php echo $row->folder;?></div>
		                    </div>
	                    </div>
	                </div>
	            </div>
	            <?php $counter++; endforeach; ?>
	            
	            <div class="sigProClearFix"></div>
	            		        
		        <?php if($this->tmpl == 'component' && $this->type == 'site'): ?>
		        <div class="sigProGallerySettings">
		        	<div class="sigProBtmRow"></div>
		            <div class="sigProGallerySettingsInner">
		            	<h3><?php echo JText::_('COM_SIGPRO_GALLERY_SETTINGS'); ?>:</h3>
		            	<div class="innerSettingsBlock">
		                	<label><?php echo JText::_('COM_SIGPRO_THUMBNAIL_WIDTH'); ?></label>
		                	<input type="text" name="width" value="" size="4" />
		                </div>
		                <div class="innerSettingsBlock">
		                	<label><?php echo JText::_('COM_SIGPRO_THUMBNAIL_HEIGHT'); ?></label>
		                	<input type="text" name="height" value="" size="4" />
		                </div>
		                <div class="innerSettingsBlock">
		               		<label><?php echo JText::_('COM_SIGPRO_DISPLAY_MODE'); ?></label>
		                	<?php echo $this->displayMode; ?>
		                </div>
		                <div class="innerSettingsBlock">
		                	<label><?php echo JText::_('COM_SIGPRO_CAPTIONS_MODE'); ?></label>
		                	<?php echo $this->captionsMode; ?>
		               	</div>
		            </div>
		        </div>
		        <?php endif; ?>
	
	        </div>
	        
	    	<div class="sigProPagination sigProClearFix sigBoxSizing">
	        	<div class="sigProPagiWrap">
		        	<div class="sigFloatLeft sigProPageItem">
		        		<input id="jToggler" type="checkbox" name="toggle" value="" onclick="<?php echo (version_compare(JVERSION, '1.6.0', 'ge'))? 'Joomla.checkAll(this);':'checkAll('.count($this->rows).');'; ?>" />
		        		<label for="jToggler" class="sigProLabel"><?php echo JText::_('COM_SIGPRO_SELECT_ALL'); ?></label>
					</div>
					
					<?php echo $this->pagination; ?>
		        	
		        	<div class="sigFloatRight sigProPageItem">
		        		<strong><?php echo $this->total; ?></strong> <span class="sigPurple thin"><?php echo JText::_('COM_SIGPRO_GALLERIES'); ?></span>
		        	</div>
	        	</div>
	        </div>
	
	       	<input type="hidden" name="option" value="com_sigpro" />
	    	<input type="hidden" name="view" value="galleries" />
	    	<input type="hidden" name="task" value="" />
	    	<input type="hidden" name="tmpl" value="<?php echo $this->tmpl; ?>" />
	    	<input type="hidden" name="editorName" value="<?php echo $this->editorName; ?>" />
	    	<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
	    	<input type="hidden" name="boxchecked" value="0" />
	    	<input type="hidden" name="newFolder" value="" />
	    	<?php if($this->template): ?>
	    	<input type="hidden" name="template" value="<?php echo $this->template; ?>" />
	    	<?php endif; ?>
	    	<?php echo JHTML::_('form.token'); ?>
	    </form>
	</div>
</div>
<div class="sigBtmWhiteFix"></div>
