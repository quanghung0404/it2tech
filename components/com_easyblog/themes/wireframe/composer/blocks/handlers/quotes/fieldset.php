<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$styles = array(

    array(
        'label' => 'Default',
        'classname' => 'style-default'
    ),

    array(
        'label' => 'Minimal Light',
        'classname' => 'style-minimallight'
    ),

    array(
        'label' => 'Minimal Box',
        'classname' => 'style-minimalbox'
    )
);
?>
<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_QUOTES_STYLE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field" data-eb-composer-block-quotes-style>
            <div class="eb-swatch swatch-grid">
                <div class="row">
                    <?php foreach ($styles as $style) { ?>
                    <div class="col-xs-12">
                        <div class="eb-swatch-item eb-composer-quote-preview" data-style="<?php echo $style['classname']; ?>">
                            <div class="eb-swatch-preview">
                                <blockquote class="eb-quote <?php echo $style['classname']; ?>">
                                    <p><?php echo JText::_('COM_EASYBLOG_BLOCKS_QUOTES_PREVIEW_CONTENT');?></p>
                                    <cite><?php echo JText::_('COM_EASYBLOG_BLOCKS_QUOTES_PREVIEW_CITE');?></cite>
                                </blockquote>
                            </div>
                            <div class="eb-swatch-label">
                                <span><?php echo $style['label']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_QUOTES_CITATION'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field mb-10">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'citation', $data->citation, 'citation', 'data-quotes-citation'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
