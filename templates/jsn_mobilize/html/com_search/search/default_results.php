<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();

?>
<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
<dl class="search-results<?php echo $this->pageclass_sfx; ?>">
<?php foreach($this->results as $result) : ?>
	<dt class="result-title">
		<?php echo $this->pagination->limitstart + $result->count.'. ';?>
		<?php if ($result->href) :?>
			<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_blank"<?php endif;?>>
				<?php echo $this->escape($result->title);?>
			</a>
		<?php else:?>
			<?php echo $this->escape($result->title);?>
		<?php endif; ?>
	</dt>
	<?php if ($result->section) : ?>
		<dd class="result-category">
			<span class="small<?php echo $this->pageclass_sfx; ?>">
				(<?php echo $this->escape($result->section); ?>)
			</span>
		</dd>
	<?php endif; ?>
	<dd class="result-text">
		<?php echo $result->text; ?>
	</dd>
	<?php if ($this->params->get('show_date')) : ?>
		<dd class="result-created<?php echo $this->pageclass_sfx; ?>">
			<?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?>
		</dd>
	<?php endif; ?>
<?php endforeach; ?>
</dl>

<div class="center">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php else : ?>
<table class="contentpaneopen<?php echo $this->escape($this->pageclass_sfx); ?>">
	<tr>
		<td><?php
		foreach( $this->results as $result ) : ?>
			<fieldset>
				<div> <span class="small<?php echo $this->escape($this->pageclass_sfx); ?>"> <?php echo $this->pagination->limitstart + $result->count.'. ';?> </span>
					<?php if ( $result->href ) :
						if ($result->browsernav == 1 ) : ?>
					<a href="<?php echo JRoute::_($result->href); ?>" target="_blank">
					<?php else : ?>
					<a href="<?php echo JRoute::_($result->href); ?>">
					<?php endif;

						echo $this->escape($result->title);

						if ( $result->href ) : ?>
					</a>
					<?php endif;
						if ( $result->section ) : ?>
					<br />
					<span class="small<?php echo $this->escape($this->pageclass_sfx); ?>"> (<?php echo $this->escape($result->section); ?>) </span>
					<?php endif; ?>
					<?php endif; ?>
				</div>
				<div> <?php echo $result->text; ?> </div>
				<?php
					if ( $this->params->get( 'show_date' )) : ?>
				<div class="small<?php echo $this->escape($this->pageclass_sfx); ?>"> <?php echo $result->created; ?> </div>
				<?php endif; ?>
			</fieldset>
			<?php endforeach; ?></td>
	</tr>
	<tr>
		<td colspan="3"><div align="center"> <?php echo $this->pagination->getPagesLinks( ); ?> </div></td>
	</tr>
</table>
<?php endif; ?>
