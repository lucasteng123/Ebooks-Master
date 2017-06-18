 <?php
 function get_res_folder() {
	if ($_SERVER['HTTP_HOST'] == "kdhq.dubedev.com"
		|| $_SERVER['HTTP_HOST'] == "dubedev.com"
		|| $_SERVER['HTTP_HOST'] == "127.0.0.1"
		) {
		return 'http://'.$_SERVER['HTTP_HOST'].'/bestebooks/res/';
	}
	else {
		return "./res/";
	}
}
?>