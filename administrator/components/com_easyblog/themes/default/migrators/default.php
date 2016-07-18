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
?>
<form action="<?php echo JRoute::_('index.php');?>" method="post" name="adminForm" id="adminForm" data-migrate-article-form>
<?php if ($htmlcontent) {?>
    <?php echo $htmlcontent; ?>
<?php } else { ?>
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b>
                    <i class="fa fa-joomla"></i>&nbsp; <a href="index.php?option=com_easyblog&view=migrators&layout=joomla"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_ARTICLES');?></a>
                </b>
            </div>

            <div class="panel-body">
                <p><?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_ARTICLES_INFO');?></p>

                <br />
                <a href="index.php?option=com_easyblog&view=migrators&layout=joomla" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_VIEW_MIGRATOR');?></a>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b>
                    <i class="fa fa-joomla"></i>&nbsp; <a href="index.php?option=com_easyblog&view=migrators&layout=wordpress"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_WORDPRESS');?></a>
                </b>
            </div>

            <div class="panel-body">
                <p><?php echo JText::_('COM_EASYBLOG_MIGRATOR_WORDPRESS_INFO');?></p>

                <br />
                <span class="label label-important"><?php echo JText::_('COM_EASYBLOG_NOTE');?></span> <?php echo JText::_('COM_EASYBLOG_MIGRATOR_WORDPRESS_REQUIREMENTS');?>
                <br /><br />
                <a href="index.php?option=com_easyblog&view=migrators&layout=wordpress" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_VIEW_MIGRATOR');?></a>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b>
                    <i class="fa fa-joomla"></i>&nbsp; <a href="index.php?option=com_easyblog&view=migrators&layout=wordpressjoomla"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_WORDPRESSJOOMLA');?></a>
                </b>
            </div>

            <div class="panel-body">
                <p><?php echo JText::_('COM_EASYBLOG_MIGRATOR_WORDPRESSJOOMLA_INFO');?></p>

                <br /><br />
                <a href="index.php?option=com_easyblog&view=migrators&layout=wordpressjoomla" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_VIEW_MIGRATOR');?></a>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b>
                    <i class="fa fa-cube"></i>&nbsp; <a href="index.php?option=com_easyblog&view=migrators&layout=k2"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_K2');?></a>
                </b>
            </div>

            <div class="panel-body">
                <p><?php echo JText::_('COM_EASYBLOG_MIGRATOR_K2_INFO');?></p>

                <br />
                <a href="index.php?option=com_easyblog&view=migrators&layout=k2" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_VIEW_MIGRATOR');?></a>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b>
                    <i class="fa fa-cube"></i>&nbsp; <a href="index.php?option=com_easyblog&view=migrators&layout=zoo"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_ZOO');?></a>
                </b>
            </div>

            <div class="panel-body">
                <p><?php echo JText::_('COM_EASYBLOG_MIGRATOR_ZOO_INFO');?></p>

                <br />
                <a href="index.php?option=com_easyblog&view=migrators&layout=zoo" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_VIEW_MIGRATOR');?></a>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b>
                    <i class="fa fa-cube"></i>&nbsp; <a href="index.php?option=com_easyblog&view=migrators&layout=blogger"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGER');?></a>
                </b>
            </div>

            <div class="panel-body">
                <p><?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGER_INFO');?></p>

                <br />
                <a href="index.php?option=com_easyblog&view=migrators&layout=blogger" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_VIEW_MIGRATOR');?></a>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_easyblog" />
</form>
