EasySocial
    .require()
    .script('admin/grid/grid')
    .done(function($){
        $('[data-table-grid]').implement(EasySocial.Controller.Grid);
    });
