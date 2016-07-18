EasySocial
.require()
.script('groups/suggest')
.done(function($){

    var groupsList = $('[data-profile-groups]');

    // Implement the controller
    $('[data-groups-suggest]').addController(EasySocial.Controller.Groups.Suggest, {
        "name" : "group_ids[]",
        "exclusion": <?php echo json_encode($excludeGroups);?>
    });

    // Bind the insert group button
    $(document).on('click.profile.insert.group', '[data-insert-groups]', function() {

        var selected = $('[data-textboxlist-itemcontent] input[name=group_ids\\[\\]]');
        var groups = [];

        selected.each(function(i, input) {
            groups.push($(input).val());
        });

        EasySocial.ajax('admin/views/profiles/getGroupTemplate', {
            "groups": groups
        }).done(function(output) {

            // Remove the empty block
            groupsList.removeClass('is-empty');

            // Append the new data
            $('[data-profile-groups]').append(output);
        });

    });

    // Bind the remove group button
    $(document).on('click.profile.remove.group', '[data-groups-remove]', function() {
        var elem = $(this);
        var parent = $(this).parents('[data-groups-item]');

        // Remove the parent
        parent.remove();

        var items = $('[data-groups-item]');

        if (items.length < 1) {
            groupsList.addClass('is-empty');
        }
    });
});
