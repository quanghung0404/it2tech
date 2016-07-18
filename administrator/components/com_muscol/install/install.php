<?php

/**
 * @version		1.5.0
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

//new for Joomla 3.0
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

?>

<h2>Music Collection Installation</h2>
<img src="http://www.joomlathat.com/images/icons/mc_icon_128_h.png" /><br /><br />
Music Collection has been installed. Before do anything, follow the next steps:<br /><br />

Check that the following hierachy of folder inside your IMAGES directory has been correctly create, on the root of you Joomla! installation. If they haven't, create them yourself.
<ul>
	<li>albums</li>
    	<ul>
        	<li>thumbs_40</li>
            <li>thumbs_115</li>
        </ul>
    <li>artists</li>
    <li>album_extra</li>
    	<ul>
        	<li>album_name</li>
            <li>artist_name</li>
        </ul>
    <li>formats</li>
    <li>tags</li>
</ul>

<?php if(!JFolder::create(JPATH_SITE . DS . 'images'. DS .'albums'. DS .'thumbs_115')){ ?>
<div class="notice"><?php echo JText::_('Could not create folder /images/albums due to permission restriction. Please create it manually'); ?></div>
<div class="notice"><?php echo JText::_('Could not create folder /images/albums/thumbs_115 due to permission restriction. Please create it manually'); ?></div>
<?php } else { ?>
<div class="message"><?php echo JText::_('Folder /images/albums succesfully created'); ?></div>
<div class="message"><?php echo JText::_('Folder /images/albums/thumbs_115 succesfully created'); ?></div>
<?php 
} 

if(!JFolder::create(JPATH_SITE . DS . 'images'. DS .'albums'. DS .'thumbs_40')){ ?>
<div class="notice"><?php echo JText::_('Could not create folder /images/albums/thumbs_40 due to permission restriction. Please create it manually'); ?></div>
<?php } else { ?>
<div class="message"><?php echo JText::_('Folder /images/albums/thumbs_40 succesfully created'); ?></div>
<?php 
} 

if(!JFolder::create(JPATH_SITE . DS . 'images'. DS .'album_extra'. DS .'album_name')){ ?>
<div class="notice"><?php echo JText::_('Could not create folder /images/album_extra due to permission restriction. Please create it manually'); ?></div>
<div class="notice"><?php echo JText::_('Could not create folder /images/album_extra/album_name due to permission restriction. Please create it manually'); ?></div>
<?php } else { ?>
<div class="message"><?php echo JText::_('Folder /images/album_extra succesfully created'); ?></div>
<div class="message"><?php echo JText::_('Folder /images/album_extra/album_name succesfully created'); ?></div>
<?php 
} 

if(!JFolder::create(JPATH_SITE . DS . 'images'. DS .'album_extra'. DS .'artist_name')){ ?>
<div class="notice"><?php echo JText::_('Could not create folder /images/album_extra/artist_name due to permission restriction. Please create it manually'); ?></div>
<?php } else { ?>
<div class="message"><?php echo JText::_('Folder /images/album_extra/artist_name succesfully created'); ?></div>
<?php 
} 

if(!JFolder::create(JPATH_SITE . DS . 'images'. DS .'album_extra'. DS .'artist_name')){ ?>
<div class="notice"><?php echo JText::_('Could not create folder /images/album_extra/artist_name due to permission restriction. Please create it manually'); ?></div>
<?php } else { ?>
<div class="message"><?php echo JText::_('Folder /images/album_extra/artist_name succesfully created'); ?></div>
<?php 
} 

if(!JFolder::create(JPATH_SITE . DS . 'images'. DS .'artists' )){ ?>
<div class="notice"><?php echo JText::_('Could not create folder /images/artists due to permission restriction. Please create it manually'); ?></div>
<?php } else { ?>
<div class="message"><?php echo JText::_('Folder /images/artists succesfully created'); ?></div>
<?php 
} 

if(!JFolder::create(JPATH_SITE . DS . 'images'. DS .'formats' )){ ?>
<div class="notice"><?php echo JText::_('Could not create folder /images/formats due to permission restriction. Please create it manually'); ?></div>
<?php } else { ?>
<div class="message"><?php echo JText::_('Folder /images/formats succesfully created'); ?></div>
<?php 
} 

if(!JFolder::create(JPATH_SITE . DS . 'images'. DS .'tags' )){ ?>
<div class="notice"><?php echo JText::_('Could not create folder /images/tags due to permission restriction. Please create it manually'); ?></div>
<?php } else { ?>
<div class="message"><?php echo JText::_('Folder /images/tags succesfully created'); ?></div>
<?php 
} 

?>

Check that a folder named "<b>songs</b>" has been created in the root your Joomla! installation. If not, create it yourself<br />

<?php
if(!JFolder::create(JPATH_SITE . DS . 'songs' )){ ?>
<div class="notice"><?php echo JText::_('Could not create folder /songs due to permission restriction. Please create it manually'); ?></div>
<?php } else { ?>
<div class="message"><?php echo JText::_('Folder /songs succesfully created'); ?></div>
<?php 
} 

?>

You can change the folder where songs are stored on "Parameters" icon, in the backend of Music Collection component<br />

All folder must have writing permissions.<br />

Copy the file "cd.png", located in /components/com_muscol/ to the "<b>formats</b>" folder you just created in the "images" directory. <br /><br />

Go to Music Collection administor area and click on "<b>Options</b>" icon to set up basic configuration for the component. Be sure to do that before you start working with Music Collection.<br /><br />

PHP variable "short_open_tag" must be set to "On" (on php.ini file) to allow MC to work properly.<br />

Install and enable the plugin JW PLAYER, downloadable from the site <a href="http://www.joomlathat.com">www.joomlathat.com</a> (free)<br />

Enjoy!