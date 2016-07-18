<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($featured && $this->params->get('featured_slider', true)) { ?>
<div class="eb-featured eb-responsive">
    <div id="eb-showcases" class="eb-showcases carousel slide mootools-noconflict"  data-featured-posts>
        <?php if ($this->params->get('featured_bottom_navigation', true) && count($featured) > 1) { ?>
            <ol class="eb-showcase-indicators carousel-indicators reset-list text-center">
                <?php for ($i = 0; $i < count($featured); $i++) { ?>
                    <li data-target=".eb-showcases" data-bp-slide-to="<?php echo $i;?>" class="<?php echo $i == 0 ? 'active' : '';?>"></li>
                <?php } ?>
            </ol>
        <?php } ?>



        <div class="carousel-inner">
            <?php $i = 0; ?>
            <?php foreach ($featured as $post) { ?>
            <?php ++$i;?>
            <div class="item<?php echo $i == 1 ? ' active' : '';?>">
                <div class="eb-showcase">
                    <?php if ($post->image && $this->params->get('featured_post_image', true) || (!$post->image && $this->params->get('post_image_placeholder', false) && $this->params->get('post_image', true))) { ?>
                        <?php $post->image == null ? $post->image = $post->getImage() : '' ; ?>
                            <div class="eb-showcase-thumb eb-post-thumb<?php echo " is-" . $this->config->get('cover_featured_alignment');?>">
                                <?php if (!$this->config->get('cover_featured_crop', false)) { ?>
                                    <a href="<?php echo $post->getPermalink();?>" class="eb-post-image" 
                                        style="width: <?php echo $this->config->get('cover_featured_width');?>px;"
                                    >
                                        <img src="<?php echo $post->image;?>" alt="<?php echo $this->escape($post->title);?>" />
                                    </a>
                                <?php } ?>

                                <?php if ($this->config->get('cover_featured_crop', false)) { ?>
                                    <a href="<?php echo $post->getPermalink();?>" class="eb-post-image-cover" 
                                        style="
                                            background-image: url('<?php echo $post->image;?>'); 
                                            width: <?php echo $this->config->get('cover_featured_width');?>px;
                                            height: <?php echo $this->config->get('cover_featured_height');?>px;"
                                    ></a>
                                <?php } ?>
                            </div>
                    <?php } ?>

                    <div class="eb-showcase-content">
                        <?php if ($this->params->get('featured_post_author_avatar', true)) { ?>
                            <!--TODO: pull-left & pull-right settings for author avatar-->
                            <a href="<?php echo $post->author->getProfileLink(); ?>" class="eb-avatar-sm pull-right">
                                <img src="<?php echo $post->author->getAvatar();?>" class="eb-avatar eb-avatar-sm" width="30" height="30" alt="<?php echo $this->html('string.escape', $post->author->getName());?>" />
                            </a>
                        <?php } ?>

                        <?php if ($this->params->get('featured_post_title', true)) { ?>
                        <h2 class="eb-showcase-title reset-heading">
                            <a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
                        </h2>
                        <?php } ?>

                        <div class="eb-showcase-meta text-muted">
                            <?php if ($this->params->get('featured_post_author', true) && $this->params->get('featured_post_date', true)) { ?>
                            <div class="eb-post-author" itemprop="author" itemscope="" itemtype="http://schema.org/Person">
                                <i class="fa fa-user"></i>
                                <span itemprop="name">
                                    <a href="<?php echo $post->author->getProfileLink(); ?>">
                                        <?php echo $post->author->getName(); ?>
                                    </a>
                                </span>
                            </div>
                            <?php } ?>
                            
                            <?php if ($this->params->get('featured_post_author', true) && !$this->params->get('featured_post_date', true)) { ?>
                            <div class="eb-post-date">
                                <i class="fa fa-clock-o"></i>
                                <?php echo JText::sprintf('<time>' . $post->getDisplayDate($this->params->get('featured_post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')) . '</time>'); ?>
                            </div>
                            <?php } ?>
                            
                            <?php if ($this->params->get('featured_post_category', true)) { ?>    
                            <div class="eb-post-category comma-seperator">
                                <i class="fa fa-folder-open"></i>

                                <?php foreach ($post->getCategories() as $category) { ?>
                                <span>
                                    <a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
                                </span>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
                        

                        <?php if ($this->params->get('featured_post_content', true)) { ?>
                        <div class="eb-showcase-article">
                            <?php echo $this->html('string.truncater', $post->getIntro(EASYBLOG_STRIP_TAGS), $this->params->get('featured_post_content_limit', 250));?>
                        </div>
                        <?php } ?>

                        <!--TODO: .eb-post-more should have specific height to cover .eb-showcase-control-->
                        <div class="eb-showcase-more">
                            <?php if ($this->params->get('featured_post_readmore', true)) { ?>
                            <a class="btn btn-default" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php if ($this->config->get('listing_featured_bottom_navigation')) { ?>
            <div class="eb-showcase-control btn-group">
                <a class="btn btn-default btn-sm" href="#eb-showcases" role="button" data-bp-slide="prev">
                    <span class="fa fa-angle-left"></span>
                </a>
                <a class="btn btn-default btn-sm" href="#eb-showcases" role="button" data-bp-slide="next">
                    <span class="fa fa-angle-right"></span>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
