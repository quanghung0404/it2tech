<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include main views file.
FD::import('admin:/views/views');

class EasySocialViewStream extends EasySocialAdminView
{
    public function confirmDelete()
    {
        $ajax = FD::ajax();
        $theme  = FD::themes();

        $output = $theme->output( 'admin/stream/dialog.delete' );
        return $ajax->resolve( $output );
    }

    public function confirmTrash()
    {
        $ajax = FD::ajax();
        $theme  = FD::themes();

        $output = $theme->output( 'admin/stream/dialog.trash' );
        return $ajax->resolve( $output );
    }

    public function confirmArchive()
    {
        $ajax = FD::ajax();
        $theme  = FD::themes();

        $output = $theme->output( 'admin/stream/dialog.archive' );
        return $ajax->resolve( $output );
    }

    public function confirmRestore()
    {
        $ajax = FD::ajax();
        $theme  = FD::themes();

        $output = $theme->output( 'admin/stream/dialog.restore' );
        return $ajax->resolve( $output );
    }
}
