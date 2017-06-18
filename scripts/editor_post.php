<?php
/*
This script takes:

$ss -> SiteStrings
*/

switch($_POST['form']) {
	case "site_strings":
			$site_strings = parse_ini_file(SITE_PATH."/conf/site_strings.ini",true);
			// loop through different sections ([plaintext] and [html])
			try {
				foreach ($site_strings as $key => $val) {
					// loop through plaintext entries
					foreach ($val as $key => $org_val) {
						if (isset($_POST['string_'.$key]))
							$ss->set_value($key, $_POST['string_'.$key]);
					}
				}
			} catch (PDOException $e) {
				$post_result['status'] = 'error';
				$post_result['message'] = "An internal error occured :/";
				$post_result['details'] = $e->getMessage();
				break;
			}
			$post_result['status'] = 'okay';
			$post_result['message'] = "Your changes were saved successfully!";
		break;
	case "gallery_submission":
		$image_id = null;
		try {
			$image_id = $imgdb->add_from_post_request('file','Automatically Added','site_gallery');
		} catch (ImageDBException $e) {
			$post_result['status'] = 'error';
			$post_result['message'] = $e->getMessage();
			break;
		} catch (PDOException $e) {
			$post_result['status'] = 'error';
			$post_result['message'] = "An internal error occured while adding an entry to the images database. [".$e->getMessage()."]";
			break;
		}

		try {
			$sitedb->add_from_post_request($_POST['name'],$_POST['description'],$image_id);
		} catch (SiteDBException $e) {
			$post_result['status'] = 'error';
			$post_result['message'] = $e->getMessage();
			break;
		} catch (PDOException $e) {
			$post_result['status'] = 'error';
			$post_result['message'] = "An internal error occured while adding an entry to the images database. [".$e->getMessage()."]";
			break;
		}

		break;
	default:
		echo "Oops; something didn't get sent properly! :/";
}