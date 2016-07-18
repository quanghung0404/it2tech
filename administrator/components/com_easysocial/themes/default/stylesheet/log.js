try {

var task = <?php echo $task->toJSON(); ?>,
	method = {
		"success": "info",
		"info": "info",
		"warning": "warn",
		"error": "warn"
	};

$.each(task.details, function(i, detail){
	console[method[detail.type]](detail.message);
});

if (task.failed) {
	console.info("If you are switching between development & 1.2 branch, remove 'styles' folder from 'admin/default', 'site/wireframe' & 'media/com_easysocial' and perform a 'hg update -C'.");
	console.info("If you are using Solo, just do 'solo syncProjectFiles easysocial --clear'.");
}

console.log("Total time: " + (Math.round(task.time_total * 1000) / 1000) + "s");
console.log("Peak memory usage: " + (task.mem_peak/1048576).toFixed(2) + "mb");
console.log("View complete log: ", task);


} catch(e) {};
