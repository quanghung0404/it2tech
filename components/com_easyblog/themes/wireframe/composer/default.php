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
<!-- this  hack is to fix the scrolly in safari browser -->
<style type="text/css">
html, body { width: 100%; padding: 0; margin: 0;}
body {overflow-x: hidden;}
</style>

<div id="fd" class="eb eb-composer-frame is-loading view-document
    <?php echo $post->isBlank() && !$post->isLegacy() && $this->config->get('composer_templates') && $postTemplates && $this->config->get('layout_editor') == 'composer' ? ' show-templates' : '';?>
    <?php echo $post->doctype !== 'ebd' ? ' is-legacy' : 'not-legacy'; ?>"
    data-eb-composer-frame
    data-eb-composer-keepalive-enabled="<?php echo $this->config->get('main_keepalive') ? 1 : 0;?>"
    data-eb-composer-keepalive-interval="<?php echo $this->config->get('main_keepalive_interval') * 1000; ?>"
    data-eb-composer-autosave-enabled="<?php echo $this->config->get('main_autodraft') ? 1 : 0;?>"
    data-eb-composer-autosave-interval="<?php echo $this->config->get('main_autodraft_interval') * 1000; ?>"
    data-eb-composer-tags-enabled="<?php echo $this->config->get('layout_composer_tags') ? '1' : '0';?>"
    data-post-id="<?php echo $post->id; ?>"
    data-post-uid="<?php echo $post->uid; ?>"
    data-author-id="<?php echo $this->my->id;?>"

    data-post-doctype="<?php echo $post->doctype; ?>">

    <?php echo $this->output('site/composer/alerts'); ?>

    <?php echo $composer->renderManager($post->uid); ?>

    <div class="eb-composer-ghosts" data-eb-composer-ghosts>
        <div class="ebd-workarea show-guide is-ghost" data-ebd-workarea-ghosts></div>
    </div>
</div>
