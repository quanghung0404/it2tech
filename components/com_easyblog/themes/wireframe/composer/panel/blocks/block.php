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
?>
<div class="eb-composer-subpanel eb-composer-blocks-block-subpanel" data-eb-composer-blocks-block-subpanel>

    <div class="eb-composer-subpanel-content" data-scrolly="y">
        <div data-scrolly-viewport>

            <?php echo $this->output('site/composer/blocks/tree'); ?>

            <div class="eb-composer-blocks-prop-group" data-eb-composer-blocks-prop-group data-type="specific"></div>

            <div class="eb-composer-fieldset" data-name="font">
                <div class="eb-composer-fieldset-header">
                    <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_FONT_HEADER'); ?></strong>
                </div>
                <div class="eb-composer-fieldset-content">
                    <?php echo $this->output('site/composer/fields/font'); ?>
                    <?php
                        $layout = array(
                            array(
                                'class' => 'group-basic',
                                'actions' => array('bold', 'italic', 'underline', 'strikethrough')
                            ),
                            array(
                                'class' => 'group-alignment',
                                'actions' => array('alignleft', 'aligncenter', 'alignright', 'justify')
                            )
                        );
                        echo $this->output('site/composer/fields/font_formatting', array('classname' => 'section-text', 'layout' => $layout));
                    ?>
                    <?php
                        $layout = array(
                            array(
                                'class' => 'group-list',
                                'actions' => array('orderedlist', 'unorderedlist')
                            ),
                            array(
                                'class' => 'group-indent',
                                'actions' => array('outdent', 'indent')
                            )
                        );
                        echo $this->output('site/composer/fields/font_formatting', array('classname' => 'section-list', 'layout' => $layout));
                    ?>
                </div>
            </div>

            <div class="eb-composer-fieldset" data-name="dimensions">
                <div class="eb-composer-fieldset-header">
                    <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_DIMENSIONS'); ?></strong>
                </div>
                <div class="eb-composer-fieldset-content">
                    <div class="eb-composer-field eb-dimensions style-bordered" data-type="dimensions">
                        <?php
                            echo $this->output('site/composer/fields/numslider', array(
                                'name' => 'width',
                                'label' => JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_WIDTH'),
                                'attributes' => 'data-eb-block-dimensions-field',
                                'container' => 'data-eb-block-dimensions-field-container'
                            ));
                        ?>
                        <?php
                            echo $this->output('site/composer/fields/numslider', array(
                                'name' => 'height',
                                'label' => JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_HEIGHT'),
                                'attributes' => 'data-eb-block-dimensions-field',
                                'container' => 'data-eb-block-dimensions-field-container',
                                'units' => array(
                                    array(
                                        'title'   => 'COM_EASYBLOG_PIXEL',
                                        'type'    => 'pixel',
                                        'unit'    => '%',
                                        'default' => true
                                    )
                                )
                            ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="eb-composer-fieldset" data-name="text">
                <div class="eb-composer-fieldset-header">
                    <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_SELECTION'); ?></strong>
                </div>
                <div class="eb-composer-fieldset-content">
                    <?php echo $this->output('site/composer/fields/font'); ?>
                    <?php
                        $layout = array(
                            array(
                                'class' => 'group-basic',
                                'actions' => array('bold', 'italic', 'underline', 'strikethrough', 'code')
                            ),
                            array(
                                'class' => 'group-subsup',
                                'actions' => array('subscript', 'superscript')
                            ),
                            array(
                                'class' => 'group-clear',
                                'actions' => array('clear')
                            )
                        );
                        echo $this->output('site/composer/fields/font_formatting', array('classname' => 'section-text', 'layout' => $layout));
                    ?>
                    <?php
                        $layout = array(
                            array(
                                'class' => 'group-list',
                                'actions' => array('orderedlist', 'unorderedlist')
                            ),
                            array(
                                'class' => 'group-indent',
                                'actions' => array('outdent', 'indent')
                            )
                        );
                        echo $this->output('site/composer/fields/font_formatting', array('classname' => 'section-list', 'layout' => $layout));
                    ?>
                </div>
            </div>

            <div class="eb-composer-fieldset" data-name="links">
                <div class="eb-composer-fieldset-header">
                    <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_FORMATTER_LINKS'); ?></strong>
                </div>
                <div class="eb-composer-fieldset-content">
                    <?php echo $this->output('site/composer/fields/links'); ?>
                </div>
            </div>

            <div class="eb-hint style-gray layout-overlay hint-empty">
                <div>
                    <i class="eb-hint-icon fa fa-cube"></i>
                    <span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_SELECT'); ?></span>
                </div>
            </div>

        </div>
    </div>
</div>
