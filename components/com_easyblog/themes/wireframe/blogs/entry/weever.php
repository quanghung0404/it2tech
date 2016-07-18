<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="ezblog-body" itemscope itemtype="http://schema.org/Blog">
	<h1 id="title-<?php echo $blog->id; ?>" class="blog-title" itemprop="name"><?php echo $blog->title; ?></h1>

	<div>
	<?php if( $this->getParam( 'show_author') ){ ?>
		<!-- Author information -->
		<span class="blog-author">
			<?php echo JText::_( 'COM_EASYBLOG_POSTED_BY' );?>
			<?php echo $blogger->getName(); ?>
		</span>
	<?php } ?>

	<?php if( $this->getParam( 'show_created_date' ) ){ ?>
		<!-- Creation date -->
		<span class="blog-created">
			<?php echo JText::_( 'COM_EASYBLOG_ON' ); ?>
			<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $blog->created ); ?>">
				<span><?php echo $this->formatDate( $system->config->get('layout_dateformat') , $blog->created ); ?></span>
			</time>
		</span>
	<?php } ?>

	<?php if( $this->getParam( 'show_category' ) ){ ?>
		<!-- Category info -->
		<span class="blog-category">
			<?php echo $blog->getCategoryName();?>
		</span>
	<?php } ?>
	</div>

	<div>
		<?php if( $blog->getImage() ){ ?>
		<div><?php echo $blog->getImage()->getSource( 'large' , true ); ?></div>
		<?php } ?>
		
		<div>
			<?php echo $blog->intro; ?>
			<?php echo $blog->content; ?>
		</div>
	</div>
</div>
