
EasyBlog.require()
.library("ace")
.done(function($){

    var editor = ace.edit("<?php echo $uid;?>");
    editor.setTheme("<?php echo $data->theme;?>");
    editor.getSession().setMode("ace/mode/<?php echo $data->mode;?>");

    editor.renderer.setShowGutter(<?php echo $data->show_gutter ? 'true' : 'false';?>);
    editor.setFontSize("<?php echo $data->fontsize;?>");
    editor.setReadOnly(true);
    editor.setTheme("<?php echo $data->theme;?>");

    $('#<?php echo $uid;?>').height('200px');
    editor.resize();

});