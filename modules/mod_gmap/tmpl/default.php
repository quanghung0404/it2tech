<?php defined('_JEXEC') or die('Restircted access'); ?>
<div id="gmap" style="width:<?php echo $width; ?>px;height:<?php echo $height; ?>px"></div>
<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $api_key; ?>&amp;hl=en"></script>
<script type="text/javascript">
//<![CDATA[
var gMap = {};
gMap.cLat = <?php echo $cLat; ?>;
gMap.cLng = <?php echo $cLng; ?>;
gMap.zoom = <?php echo $zoom; ?>;
gMap.initialize = function() {
    if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("gmap"));
        map.setCenter(new GLatLng(gMap.cLat, gMap.cLng), gMap.zoom);
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());

        var baseIcon = new GIcon();
        baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
        baseIcon.iconSize = new GSize(20, 34);
        baseIcon.shadowSize = new GSize(37, 34);
        baseIcon.iconAnchor = new GPoint(9, 34);
        baseIcon.infoWindowAnchor = new GPoint(9, 2);
        baseIcon.infoShadowAnchor = new GPoint(18, 25);

        function createMarker(point, index, info) {
            var letter = String.fromCharCode("A".charCodeAt(0) + index);
            var letteredIcon = new GIcon(baseIcon);
            letteredIcon.image = "http://www.google.com/mapfiles/marker" + letter + ".png";
            var marker = new GMarker(point, {icon:letteredIcon});
            GEvent.addListener(marker, "click", function() {marker.openInfoWindowHtml(info)});
            return marker;
        }

        map.addOverlay(createMarker(new GLatLng(<?php echo $p1_lat; ?>, <?php echo $p1_lng; ?>), 0, '<?php echo $p1_info; ?>'));
        map.addOverlay(createMarker(new GLatLng(<?php echo $p2_lat; ?>, <?php echo $p2_lng; ?>), 1, '<?php echo $p2_info; ?>'));
        map.addOverlay(createMarker(new GLatLng(<?php echo $p3_lat; ?>, <?php echo $p3_lng; ?>), 2, '<?php echo $p3_info; ?>'));

    }
}

window.addEvent('domready', gMap.initialize);
window.onunload = GUnload();
//]]>
</script>