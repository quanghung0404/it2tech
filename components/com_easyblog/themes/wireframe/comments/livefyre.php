<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="comments-livefyre">
    <div id="livefyre-comments"></div>
    <script type="text/javascript" src="http://zor.livefyre.com/wjs/v3.0/javascripts/livefyre.js"></script>
    <script type="text/javascript">
    (function () {
        var articleId = fyre.conv.load.makeArticleId(null);
        fyre.conv.load({}, [{
            el: 'livefyre-comments',
            network: "livefyre.com",
            siteId: "<?php echo $siteId;?>",
            articleId: "<?php echo $blog->id;?>",
            signed: false,
            collectionMeta: {
                articleId: "<?php echo $blog->id;?>",
                url: fyre.conv.load.makeCollectionUrl(),
            }
        }], function() {});
    }());
    </script>
</div>