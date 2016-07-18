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

##########################################
## Paths
##########################################
$files 			= array();

$files['admin']	= new stdClass();
$files['admin']->path 	= JPATH_ROOT . '/administrator/components';

$files['site']	= new stdClass();
$files['site']->path 	= JPATH_ROOT . '/components';

$files['tmp']	= new stdClass();
$files['tmp']->path 	= JPATH_ROOT . '/tmp';

$files['media']	= new stdClass();
$files['media']->path 	= JPATH_ROOT . '/media';

$files['user']	= new stdClass();
$files['user']->path 	= JPATH_ROOT . '/plugins/user';

$files['system']	= new stdClass();
$files['system']->path 	= JPATH_ROOT . '/plugins/system';

$files['user']	= new stdClass();
$files['user']->path 	= JPATH_ROOT . '/plugins/user';

$files['auth']	= new stdClass();
$files['auth']->path 	= JPATH_ROOT . '/plugins/authentication';


##########################################
## Determine states
##########################################
$hasErrors	= false;

foreach ($files as $file) {
	// The only proper way to test this is to not use is_writable
	$contents	= "<body></body>";
	$state 		= JFile::write( $file->path . '/tmp.html' , $contents );

	// Initialize this to false by default
	$file->writable 	= false;

	if ($state) {
		JFile::delete( $file->path . '/tmp.html' );

		$file->writable 	= true;
	}

	if (!$file->writable) {
		$hasErrors 		= true;
	}
}
?>
<script type="text/javascript">
jQuery( document ).ready( function(){

	jQuery( '[data-permissions-info]' ).bind( 'click' , function(){
		jQuery( this ).parents( 'td' ).find( '.permissions-info' ).toggle();
	});

	jQuery( '[data-installation-submit]' ).bind( 'click' , function(){

		<?php if( $hasErrors ){ ?>
			$( '[data-permissions-error]' ).show();
		<?php } else { ?>
			$( '[data-installation-form]' ).submit();
		<?php } ?>
	});

	jQuery( '[data-installation-reload]' ).bind( 'click' , function()
	{
		jQuery( '[data-installation-form]' ).submit();
	});

});
</script>

<form name="installation" method="post" data-installation-form>
<p class="section-desc">
	<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PERMISSIONS_DESC' ); ?>
</p>

<?php if( !$hasErrors ){ ?>
<hr />
<p class="text-success"><?php echo JText::_('COM_EASYSOCIAL_INSTALLATION_PERMISSIONS_SUCCESS');?></p>
<?php } ?>

<?php if( $hasErrors ){ ?>
<div class="alert alert-error" data-permissions-error style="display: none;">
	<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PERMISSIONS_ERROR' );?>
	<div class="mt-10">
		<a href="javascript:void(0);" class="btn btn-es-inverse" data-installation-reload><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_RELOAD' );?></a>
	</div>
</div>
<?php } ?>

<table class="table table-striped mt-20 stats">
	<thead>
		<tr>
			<td width="75%">
				<?php echo JText::_( 'Directory' ); ?>
			</td>
			<td class="center" width="25%">
				<?php echo JText::_( 'State' ); ?>
			</td>
		</tr>
	</thead>

	<tbody>
		<?php foreach( $files as $file ){ ?>
		<tr class="<?php echo !$file->writable ? 'error' : '';?>">
			<td>
				<div class="clearfix">
					<span><?php echo $file->path;?></span>

					<?php if( !$file->writable ){ ?>
					<a href="javascript:void(0);" class="btn btn-es-inverse btn-mini pull-right" data-permissions-info><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_INFO' ); ?></a>
					<a href="javascript:void(0);" class="btn btn-es-danger btn-mini pull-right mr-5"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_HOW_TO_FIX' ); ?></a>
					<?php } ?>
				</div>
			</td>
			<?php if( $file->writable ){ ?>
			<td class="center text-success">
				<i class="fa fa-check  mr-5"></i>
				<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PERMISSIONS_WRITABLE' );?>
			</td>
			<?php } else { ?>
			<td class="center text-error">
				<i class="fa fa-remove  mr-5"></i>
				<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_PERMISSIONS_UNWRITABLE' );?>
			</td>
			<?php } ?>
		</tr>
		<?php } ?>

	</tbody>
</table>

<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="active" value="<?php echo $active; ?>" />

<?php if( $reinstall ){ ?>
<input type="hidden" name="reinstall" value="1" />
<?php } ?>

<?php if( $update ){ ?>
<input type="hidden" name="update" value="1" />
<?php } ?>
</form>
