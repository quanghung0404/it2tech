EasySocial.require().script('site/events/browser').done(function($) {
    $('[data-events]').addController('EasySocial.Controller.Events.Browser', {
        <?php if ($filter === 'nearby') { ?>
        distance: '<?php echo $distance; ?>',
        <?php } ?>

        hasLocation: <?php echo $hasLocation ? 1 : 0; ?>,
        userLatitude: '<?php echo $hasLocation ? $userLocation['latitude'] : ''; ?>',
        userLongitude: '<?php echo $hasLocation ? $userLocation['longitude'] : ''; ?>',
        delayed: <?php echo $delayed ? 1 : 0; ?>,
        includePast: <?php echo $includePast ? 1 : 0; ?>,
        ordering: '<?php echo $ordering; ?>'
    });
});
