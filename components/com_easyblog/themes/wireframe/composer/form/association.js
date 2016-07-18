
EasyBlog.require()
.script('composer/panels/association')
.done(function($) {
    $('[data-composer-association]').addController('Composer.Panels.Association');
});

EasyBlog.ready(function($){
    window.insertAssoc = function( id , name, codeid )
    {
        $('input#assoc-postname' + codeid).val(name);
        $('input#assoc-postid' + codeid).val(id);
        SqueezeBox.close();
    }
});
