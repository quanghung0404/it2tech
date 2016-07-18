<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<table class="es-theme-compiler-status table table-striped table-bordered table-condensed" data-status>
<?php foreach($status as $file) { ?>
	<tr class="is-<?php echo $file->state; ?>">
		<td><span class="label"><?php echo $file->state; ?></span></td>
		<td width="100%">
			<a href="<?php echo $file->uri; ?>" target="_blank"><?php echo $file->name; ?></a>
			<?php if (!is_null($file->size)) { ?>
				&bull; <strong><?php echo round(FD::math()->convertUnits($file->size, 'B', 'KB'), 2); ?>kb</strong>
				<?php if ($file->rules > 0) { ?>
				&bull; <strong <?php echo ($file->rules >= 4096) ? 'style="color: red;"' : ''; ?>><?php echo JText::sprintf('COM_EASYSOCIAL_THEMES_COMPILER_CSS_RULECOUNT', $file->rules); ?></strong>
				<?php } ?>
				<time class="pull-right" datetime="<?php echo $file->modified; ?>"><?php echo FD::date($file->modified)->toLapsed(); ?></time>
			<?php } ?>
			<br/>
			<small><?php echo $file->path; ?></small>
		</td>
	</tr>
<?php } ?>
</table>
